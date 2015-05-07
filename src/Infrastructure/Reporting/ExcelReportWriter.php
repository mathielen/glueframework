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
            throw new \InvalidArgumentException("saveDir is not a writable directory! Was: ".$saveDir);
        }

        $this->templateRepository = $templateRepository;
        $this->saveDir = $saveDir;
        $this->logger = $logger;
    }

    private function save(\PHPExcel $output)
    {
        $filename = $this->saveDir.'/'.uniqid().'.xls';
        $this->logger->debug("Saving report to $filename");

        $writer = new \PHPExcel_Writer_Excel5($output);
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

    public function write(Report $report, $templateId)
    {
        $template = $this->getTemplate($templateId);

        $process = new ExcelReportWriteProcess($report, $template, $this->logger);

        $output = $process->run();

        return $this->save($output);
    }

}
