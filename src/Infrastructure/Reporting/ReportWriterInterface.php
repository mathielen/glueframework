<?php
namespace Infrastructure\Reporting;

interface ReportWriterInterface
{

    public function write(array $reportData, $templateId);

}
