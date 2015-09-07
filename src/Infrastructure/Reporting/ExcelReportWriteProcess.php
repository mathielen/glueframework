<?php
namespace Infrastructure\Reporting;

use Doctrine\Common\Inflector\Inflector;
use Doctrine\Common\Proxy\Exception\InvalidArgumentException;
use Mcs\Reporting\CoreBundle\ValueObject\Report;
use Psr\Log\LoggerInterface;

class ExcelReportWriteProcess
{

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var \PHPExcel
     */
    private $template;

    /**
     * @var \PHPExcel_Worksheet
     */
    private $templateSheet;

    /**
     * @var \PHPExcel
     */
    private $output;

    /**
     * @var \PHPExcel_Worksheet
     */
    private $outputSheet;

    private $currentRowNum;

    /**
     * @var array
     */
    private $data;

    /**
     * @var array
     */
    private $namedRangeNames;

    public function __construct(array $reportData, \PHPExcel $template, LoggerInterface $logger)
    {
        $this->logger = $logger;
        $this->template = $template;
        $this->data = $reportData;
        $this->data = array_change_key_case($this->data, CASE_LOWER);

        if (!isset($this->data['root'])) {
            throw new \InvalidArgumentException("Cannot write ExcelReport. Missing 'root' entry in Report data!");
        }
        if (!$this->template->getSheetByName('TEMPLATE')) {
            throw new \InvalidArgumentException("A sheet named 'TEMPLATE' must exist.");
        }

        $this->namedRangeNames = array_keys(array_change_key_case($this->template->getNamedRanges(), CASE_LOWER));

        $this->logger->debug('Populated named ranges', $this->namedRangeNames);
    }

    /**
     * @return \PHPExcel
     */
    public function run()
    {
        $this->prepare();

        $this->currentRowNum += $this->writeRange($this->template->getNamedRange('HEADER'), $this->data);
        $this->currentRowNum--;

        $this->loop($this->data['root']);

        $this->finish();

        $this->clean();

        return $this->output;
    }

    private function prepare()
    {
        $this->output = new \PHPExcel();
        $this->outputSheet = $this->output->getActiveSheet();
        $this->outputSheet->setTitle('Report');
        $this->templateSheet = $this->output->addExternalSheet($this->template->getSheetByName('TEMPLATE'));

        foreach ($this->templateSheet->getColumnDimensions() as $col=>$columnDimension) {
            $this->outputSheet->getColumnDimension($col)->setWidth($columnDimension->getWidth());
        }

        $this->currentRowNum = 1;
    }

    private function finish()
    {
        $footer = $this->template->getNamedRange('FOOTER');
        if ($footer) {
            $this->writeRange($footer, $this->data);
        }
    }

    private function clean()
    {
        $this->output->setActiveSheetIndexByName('TEMPLATE');
        $this->output->removeSheetByIndex($this->output->getActiveSheetIndex());
    }

    /**
     * @return int
     */
    private function writeRange(\PHPExcel_NamedRange $namedRange, array $currentData=[])
    {
        $rangeData = $this->templateSheet->rangeToArray($namedRange->getRange(), null, false, true, true);

        $i = 0;
        foreach ($rangeData as $rangeRowNum => $rangeCols) {
            foreach ($rangeCols as $rangeColNum => $rangeCellValue) {
                $templateCor = $rangeColNum.$rangeRowNum; //A1...
                $outputCor = $rangeColNum.($this->currentRowNum+$i); //A1...

                if (!empty($rangeCellValue)) {
                    $cellValue = empty($currentData) ? $rangeCellValue : $this->translate($rangeCellValue, $currentData);

                    //is formula
                    if (substr($rangeCellValue, 0, 1) == '=') {
                        $rowDelta = $this->currentRowNum + $i - $rangeRowNum;

                        //has ref to field - add row-offset
                        $cellValue = preg_replace_callback(
                            '/([A-Z]+)([0-9])+/',
                            function ($matches) use ($rowDelta) {
                                $offsettedY = ($matches[2] + $rowDelta);

                                return $matches[1] . $offsettedY;
                            },
                            $cellValue);
                    }

                    //set value
                    $this->outputSheet->getCell($outputCor)->setValue($cellValue);
                }

                //set style
                $this->setStyle($this->templateSheet->getStyle($templateCor),$outputCor);
            }

            $i++;
        }

        return $i;
    }

    /**
     * Like PHPExcel's Duplicate Style but without checking for existing style (all styles do exist)
     */
    private function setStyle(\PHPExcel_Style $pCellStyle, $pRange)
    {
        // make sure we have a real style and not supervisor
        $style = $pCellStyle->getIsSupervisor() ? $pCellStyle->getSharedComponent() : $pCellStyle;
        $xfIndex = $style->getIndex();

        // Calculate range outer borders
        list($rangeStart, $rangeEnd) = \PHPExcel_Cell::rangeBoundaries($pRange . ':' . $pRange);

        // Make sure we can loop upwards on rows and columns
        if ($rangeStart[0] > $rangeEnd[0] && $rangeStart[1] > $rangeEnd[1]) {
            $tmp = $rangeStart;
            $rangeStart = $rangeEnd;
            $rangeEnd = $tmp;
        }

        // Loop through cells and apply styles
        for ($col = $rangeStart[0]; $col <= $rangeEnd[0]; ++$col) {
            for ($row = $rangeStart[1]; $row <= $rangeEnd[1]; ++$row) {
                $this->outputSheet->getCell(\PHPExcel_Cell::stringFromColumnIndex($col - 1) . $row)->setXfIndex($xfIndex);
            }
        }
    }

    private function loop(array $data, \PHPExcel_NamedRange $namedRange=null)
    {
        $numItems = count($data);
        $i = 0;
        foreach ($data as $key=>$currentData) {
            if (!is_array($currentData)) {
                throw new \InvalidArgumentException();
            }

            $currentData = array_change_key_case($currentData, CASE_LOWER);

            $rowsAdvanced = 0;
            if ($namedRange) {
                $rowsAdvanced = $this->writeRange($namedRange, $currentData);
            }

            //Recursion
            $recursionProperties = $this->getRecursionProperties($currentData);
            if (count($recursionProperties) > 0) {
                $this->logger->debug("Recursing properties", $recursionProperties);

                foreach ($recursionProperties as $property) {
                    $subData = $currentData[$property];
                    $nr = $this->template->getNamedRange(strtoupper($property));

                    //Calculate the Y offset between the 2 named ranges
                    if ($namedRange) {
                        $offset = $this->calculateRangeYOffset($namedRange->getRange(), $nr->getRange());
                    } else {
                        $offset = 1;
                    }

                    $this->currentRowNum += $offset;
                    $this->loop($subData, $nr);
                }
            }

            if (++$i !== $numItems && $rowsAdvanced > 0) {
                $this->currentRowNum += $rowsAdvanced;
            }
        }
    }

    private function calculateRangeYOffset($range1, $range2)
    {
        preg_match('/([A-Z]+)([0-9])+:/', $range1, $matches1);
        preg_match('/([A-Z]+)([0-9])+:/', $range2, $matches2);

        return $matches2[2]-$matches1[2];
    }

    private function getRecursionProperties($currentData)
    {
        if (!is_array($currentData)) {
            return [];
        }

        $curDataKeys = array_keys($currentData);

        return array_values(array_intersect($curDataKeys, $this->namedRangeNames));
    }

    private function translate($templateValue, array $data)
    {
        $translated = preg_replace_callback('/"?{{(.+)}}"?/', function ($matches) use ($data) {
            $property = $matches[1];

            //use rootdata instead of scope data
            if (substr($property, 0, 2) == '//') {
                $property = substr($property, 2);
                $data = $this->data['root'][0];
            }

            $propertyPath = strtolower(Inflector::camelize(strtolower($property)));

            return isset($data[$propertyPath])?$data[$propertyPath]:null;
        }, $templateValue);

        $this->logger->debug("Translated '$templateValue' to '$translated'");

        return $translated;
    }

}
