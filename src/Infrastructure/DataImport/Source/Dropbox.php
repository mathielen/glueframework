<?php
namespace Infrastructure\DataImport\Source;

use Ddeboer\DataImport\Source\SourceInterface;

class Dropbox implements SourceInterface
{
	protected $filters = array();

	/**
	 * @var \Dropbox_API
	 */
	private $dropboxApi;

	private $uri;
	private $tempDirectory;

	public function __construct(
		\Dropbox_API $dropboxApi,
		$uri,
		$tempDirectory = '/tmp')
	{
		$this->dropboxApi = $dropboxApi;
		$this->uri = $uri;
		$this->tempDirectory = $tempDirectory;
	}

	/**
	 *
	 * @return \SplFileObject
	 */
	public function getFile()
	{
		$file = $this->downloadFile();
		foreach ($this->filters as $filter) {
			$file = $filter->filter($file);
		}

		return $file;
	}

	/**
	 * Download the file from dropbox to a temporary location
	 *
	 * @return \SplFileObject
	 */
	public function downloadFile($target = null)
	{
		if (!$target) {
			$target = tempnam($this->tempDirectory, 'data_import');
		}

		$content = $this->dropboxApi->getFile($this->uri);
		@file_put_contents($target, $content);

		return new \SplFileObject($target);
	}

	public function addFilter($filter)
	{
		$this->filters[] = $filter;
	}
}