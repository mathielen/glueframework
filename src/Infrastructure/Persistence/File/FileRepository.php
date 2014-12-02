<?php
namespace Infrastructure\Persistence\File;

use Infrastructure\Exception\NotImplementedException;
use Infrastructure\Persistence\Repository;
use Symfony\Component\Finder\Finder;

class FileRepository implements Repository
{

    /**
     * @var Finder
     */
    private $finder;

    public function __construct(Finder $finder)
    {
        $this->finder = $finder;
    }

    /**
     * @return Finder
     */
    public function getConnection()
    {
        return $this->finder;
    }

    public function save($object)
    {
        throw new NotImplementedException();
    }

    public function get($id)
    {
        $id = strtolower($id);

        /** @var \SplFileInfo $file */
        foreach ($this->finder as $file) {
            if (strtolower($file->getFilename()) == $id) {
                return $file;
            }
        }

        return null;
    }

    public function delete($id)
    {
        throw new NotImplementedException();
    }

}
