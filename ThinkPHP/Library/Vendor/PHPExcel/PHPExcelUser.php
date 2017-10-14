<?php

/**
 * PHPExcel使用类
 * +------------------------------------使用------------------------------------+
 * 导入excel表
 * vendor('PHPExcel.PHPExcelUser');
 * $file='./cs.xlsx';
 * $excel=new \PHPExcelUser($file);
 * $data=$excel->excelImport(1);//从第一行读取,如果有非字符串和函数的话则会返加一个错误的数组
 * 导出excel表
 * vendor('PHPExcel.PHPExcelUser');
 * $file='./cs.xlsx';
 * $excel=new \PHPExcelUser($file);
 * $excel->excelExport($data=array(),$title=array());
 * +------------------------------------使用------------------------------------+
 * Class FileTemp
 */
class  PHPExcelUser {
    public $excelFilename; //excel导入或导出的文件的名称
    public $strCheck; //是否进行导入全部字符串验证
    public $funCheck; //是否进行导入函数验证
    public $errorArr = array(); //当需要检查并且有错误时返回的错误统计数组

    /**
     * 构造函数
     * @param $excelFilename 文件名称
     * @param bool $strCheck 是否进行字符串验证检测
     * @param bool $funCheck 是否进行函数验证检测
     */
    public function __construct($excelFilename, $strCheck = false, $funCheck = false) {
        if (!$excelFilename) {
            try {
                $error = '请输入Excel文件名称';
                throw new Exception($error);
            } catch (Exception $e) {
                echo $e->getMessage();
            }
        }
        $this->excelFilename = $excelFilename;;
        $this->strCheck = $strCheck;
        $this->funCheck = $funCheck;
    }

    /**
     * 导出成Excel文件
     * @param array $data 一个二维数组,结构如同从数据库查出来的数组
     * @param array $title excel的第一行标题,一个数组,如果为空则没有标题
     * @exapme
     * $arr = $Model -> select();
     * excelExport($arr,array('id','账户','密码','昵称'),'文件名');
     */
    function excelExport($data = array(), $title = array()) {
        require_once('PHPExcel.php');
        error_reporting(E_ALL);
        date_default_timezone_set('Europe/London');
        $objPHPExcel = new PHPExcel();

        /*以下是一些设置 ，什么作者  标题啊之类的*/
        $objPHPExcel->getProperties()->setCreator($this->excelFilename)
            ->setLastModifiedBy($this->excelFilename)
            ->setTitle("数据EXCEL导出")
            ->setSubject("数据EXCEL导出")
            ->setDescription("备份数据")
            ->setKeywords("excel")
            ->setCategory("result file");
        /*以下就是对处理Excel里的数据， 横着取数据，主要是这一步，其他基本都不要改*/
        $model = $objPHPExcel->setActiveSheetIndex(0);
        $char = 65; //只有A-Z
        foreach ($title as $k => $v) {
            $model->setCellValue(chr($char) . '1', $v);
            $char++;
        }

        foreach ($data as $k => $v) {
            $num = $k + 2;
            $char = 65;
            foreach ($v as $m => $n) {
                //$model->setCellValue(chr($char) . $num, $n);
				$model->setCellValueExplicit(chr($char).$num, $n, PHPExcel_Cell_DataType::TYPE_STRING);
                $char++;
            }
        }
        $objPHPExcel->getActiveSheet()->setTitle('siteape');
        $objPHPExcel->setActiveSheetIndex(0);
        header('Content-Type: applicationnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $this->excelFilename . '.xls"');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
        exit;
    }

    /**
     * Excel读取
     * @param $begin 开始读取的行数
     * @return array|string
     */
    function excelImport($begin) {
        require_once('PHPExcel.php');
        $filename = $this->getRealFile($this->excelFilename);
        //建立reader对象
        $PHPReader = new PHPExcel_Reader_Excel2007();
        if (!$PHPReader->canRead($filename)) {
            $PHPReader = new PHPExcel_Reader_Excel5();
            if (!$PHPReader->canRead($filename)) {
                return array();
            }
        }
        //建立excel对象，此时你即可以通过excel对象读取文件，也可以通过它写入文件
        $PHPExcel = $PHPReader->load($filename);
        /*读取excel文件中的第一个工作表*/
        $currentSheet = $PHPExcel->getSheet(0);
        /*取得最大的列号*/
        $allColumn = $currentSheet->getHighestColumn();
        /*取得一共有多少行*/
        $allRow = $currentSheet->getHighestRow();
        $returnCell = '';
        //循环读取每个单元格的内容。注意行从1开始，列从A开始
        for ($rowIndex = $begin; $rowIndex <= $allRow; $rowIndex++) {
            for ($colIndex = 'A'; $colIndex <= $allColumn; $colIndex++) {
                $addr = $colIndex . $rowIndex;
                $cell = $currentSheet->getCell($addr)->getCalculatedValue();
                if ($cell instanceof PHPExcel_RichText) {
                    //富文本转换字符串
                    $returnCell[$rowIndex][$colIndex] = $cell->__toString();
                } else {
                    $returnCell[$rowIndex][$colIndex] = $cell;
                }
            }
        }
        return $returnCell;
    }

    /**
     * 获取正确的路径
     * @param $file
     * @return string
     */
    public function getRealFile($file) {
        $file = $file ? $file : $this->excelFilename;
        $error = '';
        if (substr($file, 0, 4) == 'http') {
            $realfile = $file;
        } elseif (!$file) {
            $error = '请指定文件路径';
        } elseif (file_exists($file)) {
            $realfile = $file;
        } elseif (file_exists('.' . $file)) {
            $realfile = '.' . $file;
        } elseif (file_exists('..' . $file)) {
            $realfile = '..' . $file;
        } else {
            $error = '您输入的文件不存在';
        }
        if ($error != '') {
            try {
                throw new Exception($error);
            } catch (Exception $e) {
                echo $e->getMessage();
            }
        } else {
            return $realfile;
        }
    }
}

?>