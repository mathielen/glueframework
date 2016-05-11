<?php

namespace Infrastructure\Cache;

use Doctrine\Common\Cache\Cache;

class FileContentCache implements Cache
{
    /**
     * The cache directory.
     *
     * @var string
     */
    protected $directory;

    /**
     * The cache file extension.
     *
     * @var string|null
     */
    protected $extension;

    /**
     * Constructor.
     *
     * @param string      $directory The cache directory.
     * @param string|null $extension The cache file extension.
     *
     * @throws \InvalidArgumentException
     */
    public function __construct($directory, $extension = null)
    {
        if (!is_dir($directory) && !@mkdir($directory, 0777, true)) {
            throw new \InvalidArgumentException(sprintf(
                    'The directory "%s" does not exist and could not be created.',
                    $directory
            ));
        }

        if (!is_writable($directory)) {
            throw new \InvalidArgumentException(sprintf(
                    'The directory "%s" is not writable.',
                    $directory
            ));
        }

        $this->directory = realpath($directory);
        $this->extension = $extension ?: $this->extension;
    }

    private function getFilename($id)
    {
        $filename = preg_replace('/[^A-Za-z0-9 ]/', '', $id);

        return $this->directory.DIRECTORY_SEPARATOR.$filename;
    }

    public function fetch($id)
    {
        if (!$this->contains($id)) {
            return;
        }

        return file_get_contents($this->getFilename($id));
    }

    public function contains($id)
    {
        return is_file($this->getFilename($id));
    }

    public function save($id, $data, $lifeTime = 0)
    {
        return file_put_contents($this->getFilename($id), $data);
    }

    public function delete($id)
    {
        return @unlink($this->getFilename($id));
    }

    public function getStats()
    {
        return;
    }
}
