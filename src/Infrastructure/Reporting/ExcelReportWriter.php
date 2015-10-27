<?php
namespace Infrastructure\Reporting;

use Doctrine\Common\Proxy\Exception\InvalidArgumentException;
use Infrastructure\Exception\ResourceNotFoundException;
use Infrastructure\Persistence\Repository;
use Mcs\Reporting\CoreBundle\ValueObject\Report;
use Psr\Log\LoggerInterface;

class ExcelReportWriter implements ReportWriterInterface
{

    /**
     * @var Repository
     */
    private $templateRepository;

    /**
     * @var LoggerInterface
     */
    private $logger;

    private $saveDir;

    public function __construct(Repository $templateRepository, $saveDir, LoggerInterface $logger)
    {
        if (!is_dir($saveDir) || !is_writable($saveDir)) {
            throw new \InvalidArgumentException("saveDir is not a writable directory! Was: " . $saveDir);
        }

        $this->templateRepository = $templateRepository;
        $this->saveDir = $saveDir;
        $this->logger = $logger;
    }

    private function save(\PHPExcel $output, $id)
    {
        $filename = $this->saveDir . '/' . $id . '.xlsx';
        $this->logger->debug("Saving report to $filename");

        $writer = new \PHPExcel_Writer_Excel2007($output);
        $writer->save($filename);

        return $filename;
    }

    /**
     * @return \PHPExcel
     * @throws ResourceNotFoundException
     */
    private function getTemplate($templateId)
    {
        $this->logger->debug("Fetch and load template with id $templateId");

        $file = $this->templateRepository->get($templateId);
        if (is_null($file)) {
            throw new ResourceNotFoundException('Template', $templateId);
        }

        $inputFileType = \PHPExcel_IOFactory::identify($file);
        $objReader = \PHPExcel_IOFactory::createReader($inputFileType);

        return $objReader->load($file);
    }

    public function write(array $reportData, $templateId, $id = null)
    {
        $id = is_null($id)?uniqid():$id;

        $template = $this->getTemplate($templateId);

        $process = new ExcelReportWriteProcess($reportData, $template, $this->logger);

        $output = $process->run();

        return $this->save($output, $id);
    }

}
