<?php 
//excel表导入
vendor('PHPExcel.PHPExcelUser');
$file=__ROOT__.'/cs.xlsx';//这里路径前面不用加点，会自动判断测试环境中的两点和正式环境中的一点
$excel=new \PHPExcelUser($file);
$data=$excel->excelImport(1);
//excel表导出
ob_end_clean();//清除缓冲区,避免乱码
header("Content-type: text/html; charset=utf-8");
vendor('PHPExcel.PHPExcelUser');
$file='cs.xlsx';
$excel=new \PHPExcelUser($file);
$data=array();//数据二维数组
$title=array();//数据标题一维数组
$excel->excelExport($data,$title);