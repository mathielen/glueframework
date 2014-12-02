<?php
namespace Infrastructure\Reporting;

use Mcs\Reporting\CoreBundle\ValueObject\Report;

interface ReportWriterInterface
{

    public function write(Report $report, $templateId);

}
