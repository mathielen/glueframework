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

    public function __construct(Repository $templateRepository, $saveDir)
    {
        if (!is_dir($saveDir) || !is_writable($saveDir)) {
            throw new \InvalidArgumentException("saveDir is not a writable directory! Was: ".$saveDir);
        }

        $this->templateRepository = $templateRepository;
        $this->saveDir = $saveDir;
    }

    private function prepare($templateId)
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

        return $this->writeRange(1, $this->template->getNamedRange('HEADER'));
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
        $currentRowNum = $this->prepare($templateId);

        $this->loop(1+$currentRowNum, $report->getData());

        $this->clean();

        return $this->save();
    }

    private function writeRange($currentRowNum, \PHPExcel_NamedRange $namedRange, array $currentData=array())
    {
//echo "writing range ".$namedRange->getName()."\n";

        $rangeData = $this->templateSheet->rangeToArray($namedRange->getRange(), null, false, true, true);

        $i = 0;
        foreach ($rangeData as $rangeRowNum => $rangeCols) {
//echo "row: ".($currentRowNum+$i)."\n";
            foreach ($rangeCols as $rangeColNum => $rangeCellValue) {
                $templateCor = $rangeColNum.$rangeRowNum; //A1...
                $outputCor = $rangeColNum.($currentRowNum+$i); //A1...

                //is formula
                if (substr($rangeCellValue, 0, 1) == '=') {
                    $rowDelta = $currentRowNum+$i-$rangeRowNum;

                    //has ref to field
                    $cellValue = preg_replace_callback(
                        '/([A-Z]{1,2})([0-9])+/',
                        function ($matches) use ($rowDelta) {
                            return $matches[1] . ($matches[2]+$rowDelta);
                        },
                        $rangeCellValue);
                } else {
                    $cellValue = empty($currentData)?$rangeCellValue:$this->translate($rangeCellValue, $currentData);
                }
                //set value
                $this->outputSheet->getCell($outputCor)->setValue($cellValue);

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
///echo "recurse for ".$property."\n";
                    $subData = $currentData[$property];
                    //$subData[Inflector::singularize($property)] = $currentData;
                    $nr = $this->template->getNamedRange(strtoupper($property));

                    $rowNum = $this->loop($rowNum, $subData, $nr);
                }
            }

            if (++$i !== $numItems && $rowsAdvanced > 0) {
                $rowNum += $rowsAdvanced;
            }
        }

        return $rowNum;
    }

    private function getRecursionProperties($currentData)
    {
        $curDataKeys = array_keys($currentData);
        $namedRangeLowerNames = array_keys(array_change_key_case($this->template->getNamedRanges(), CASE_LOWER));

        return array_intersect($curDataKeys, $namedRangeLowerNames);
    }

    private function translate($templateValue, array $data)
    {
        if (preg_match('/{{(.+)}}/', $templateValue, $matches)) {
            $propertyPath = Inflector::camelize(strtolower($matches[1]));

            $propertyPath = explode('.', $propertyPath);

            return $this->resolvePropertyPath($propertyPath, $data);
        }

        return $templateValue;
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
