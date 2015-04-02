<?php
namespace Infrastructure\Reporting;

use Doctrine\Common\Inflector\Inflector;
use Doctrine\Common\Proxy\Exception\InvalidArgumentException;
use Infrastructure\Exception\ResourceNotFoundException;
use Infrastructure\Persistence\Repository;
use Mcs\Reporting\CoreBundle\ValueObject\Report;

class ExcelReportWriter implements ReportWriterInterface
{

    /**
     * @var Repository
     */
    private $templateRepository;

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

    /**
     * @var array
     */
    private $data;

    public function __construct(Repository $templateRepository, $saveDir)
    {
        if (!is_dir($saveDir) || !is_writable($saveDir)) {
            throw new \InvalidArgumentException("saveDir is not a writable directory! Was: ".$saveDir);
        }

        $this->templateRepository = $templateRepository;
        $this->saveDir = $saveDir;
    }

    private function clean()
    {
        $this->output->setActiveSheetIndexByName('TEMPLATE');
        $this->output->removeSheetByIndex($this->output->getActiveSheetIndex());
    }

    private function save()
    {
        $filename = $this->saveDir.'/'.uniqid().'.xls';
        $writer = new \PHPExcel_Writer_Excel5($this->output);
        $writer->save($filename);

        return $filename;
    }

    public function write(Report $report, $templateId)
    {
        $this->data = $report->getData();
        if (!array_key_exists('root', $this->data)) {
            throw new \InvalidArgumentException("Cannot write ExcelReport. Missing 'root' entry in Report data!");
        }

        $currentRowNum = $this->prepare($templateId, $this->data);

        $currentRowNum = $this->loop(1+$currentRowNum, $this->data['root']);

        $this->finish($currentRowNum, $this->data);

        $this->clean();

        return $this->save();
    }

    private function finish($currentRowNum, array $data)
    {
        $footer = $this->template->getNamedRange('FOOTER');
        if ($footer) {
            $this->writeRange($currentRowNum, $footer, $data);
        }
    }

    private function prepare($templateId, array $data)
    {
        $file = $this->templateRepository->get($templateId);
        if (is_null($file)) {
            throw new ResourceNotFoundException('Template', $templateId);
        }

        $inputFileType = \PHPExcel_IOFactory::identify($file);
        $objReader = \PHPExcel_IOFactory::createReader($inputFileType);
        $this->template = $objReader->load($file);

        if (!$this->template->getSheetByName('TEMPLATE')) {
            throw new \InvalidArgumentException("A sheet named 'TEMPLATE' must exist.");
        }

        $this->output = new \PHPExcel();
        $this->outputSheet = $this->output->getActiveSheet();
        $this->outputSheet->setTitle('Report');
        $this->templateSheet = $this->output->addExternalSheet($this->template->getSheetByName('TEMPLATE'));

        foreach ($this->templateSheet->getColumnDimensions() as $col=>$columnDimension) {
            $this->outputSheet->getColumnDimension($col)->setWidth($columnDimension->getWidth());
        }

        return $this->writeRange(1, $this->template->getNamedRange('HEADER'), $data);
    }

    private function writeRange($currentRowNum, \PHPExcel_NamedRange $namedRange, array $currentData=array())
    {
        $rangeData = $this->templateSheet->rangeToArray($namedRange->getRange(), null, false, true, true);

        $i = 0;
        foreach ($rangeData as $rangeRowNum => $rangeCols) {
            foreach ($rangeCols as $rangeColNum => $rangeCellValue) {
                $templateCor = $rangeColNum.$rangeRowNum; //A1...
                $outputCor = $rangeColNum.($currentRowNum+$i); //A1...

                if (!empty($rangeCellValue)) {
                    $cellValue = empty($currentData) ? $rangeCellValue : $this->translate($rangeCellValue, $currentData);

                    //is formula
                    if (substr($rangeCellValue, 0, 1) == '=') {
                        $rowDelta = $currentRowNum + $i - $rangeRowNum;

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
                $this->outputSheet->duplicateStyle($this->templateSheet->getStyle($templateCor),$outputCor);
            }

            $i++;
        }

        return $i;
    }

    private function loop($rowNum, array $data, \PHPExcel_NamedRange $namedRange=null)
    {
        $numItems = count($data);
        $i = 0;
        foreach ($data as $key=>$currentData) {
            $rowsAdvanced = 0;
            if ($namedRange) {
                $rowsAdvanced = $this->writeRange($rowNum, $namedRange, $currentData);
            }

            //Recursion
            $recursionProperties = $this->getRecursionProperties($currentData);
            if (count($recursionProperties) > 0) {
                foreach ($recursionProperties as $property) {
                    $subData = $currentData[$property];
                    $nr = $this->template->getNamedRange(strtoupper($property));

                    //Calculate the Y offset between the 2 named ranges
                    if ($namedRange) {
                        $offset = $this->calculateRangeYOffset($namedRange->getRange(), $nr->getRange());
                    } else {
                        $offset = 1;
                    }

                    $rowNum = $this->loop($rowNum+$offset, $subData, $nr);
                }
            }

            if (++$i !== $numItems && $rowsAdvanced > 0) {
                $rowNum += $rowsAdvanced;
            }
        }

        return $rowNum;
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
        $namedRangeLowerNames = array_keys(array_change_key_case($this->template->getNamedRanges(), CASE_LOWER));

        return array_intersect($curDataKeys, $namedRangeLowerNames);
    }

    private function translate($templateValue, array $data)
    {
        return preg_replace_callback('/"?{{(.+)}}"?/', function ($matches) use ($data) {
            $property = $matches[1];

            //use rootdata instead of scope data
            if (substr($property, 0, 2) == '//') {
                $property = substr($property, 2);
                $data = $this->data['root'][0];
            }

            $propertyPath = Inflector::camelize(strtolower($property));
            $propertyPath = explode('.', $propertyPath);

            return $this->resolvePropertyPath($propertyPath, $data);
        }, $templateValue);
    }

    private function resolvePropertyPath(array $propertyPath, $data)
    {
        if (empty($propertyPath) || !is_array($data)) {
            return $data;
        }

        $property = array_shift($propertyPath);

        if (!array_key_exists($property, $data)) {
            return null;
        }

        $data = $data[$property];

        return $this->resolvePropertyPath($propertyPath, $data);
    }

}
