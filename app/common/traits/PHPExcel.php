<?php
// +----------------------------------------------------------------------
// | CoolCms [ DEVELOPMENT IS SO SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2018-2019 http://www.coolcms.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +---------------------------------------------------------------------
// | Author: Alan <251956250@qq.com>
// +----------------------------------------------------------------------
// | DateTime: 2020/9/24 10:36
// +----------------------------------------------------------------------
// | Desc: 
// +----------------------------------------------------------------------

namespace app\common\traits;

use app\common\base\ErrorCode;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

/**
 * Trait PHPOffice
 * @dec:
 * @author: Alan <251956250@qq.com>
 * @package app\common\traits
 */
trait PHPExcel
{
    /**
     * @param null $tableData
     * @desc 数据格式   array $tableData
     *                       tableName => '要生成的表名称'
     *                       lists      =>  [
     *                                  0 => [
     *                                          'sheetName'=>'表下面工作区的名称'
     *                                          'sheetData'=>[
     *                                                     0 => []//第一行可以填标题、或者直接填表头
     *                                                     1 => []//数据 每行数据个数需保持一致
     *                                              ]
     *                                       ]
     *                                   ]
     * @param string $format
     * @return array
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public static function exportExcel($tableData=null,$format = 'Xlsx')
    {
        $excelSheet = new Spreadsheet();//创建一个新的excel文档
        if(empty($tableData)){
            return ['code'=>400,'data'=>'暂无数据'];
        }
        $fileName = $tableData['tableName'];
        $fullName = $fileName.'-'.date('Y-m-d-H-i-s').'.'.strtolower($format);

        //循环全部表格
        foreach ($tableData['lists'] as $key=>$value){
            $excelSheet->createSheet($key);
            $excelSheet->setActiveSheetIndex($key);
            $excelSheet->getActiveSheet()->setTitle($value['sheetName']);
            //循环每个表格中的数据的行数
            foreach ($value['sheetData'] as $k=>$v){
                //循环每个表格中的列数
                foreach ($v as $kk=>$vv){
                    $excelSheet->getActiveSheet()->setCellValueByColumnAndRow($kk+1,$k+1,$vv);
                }
            }
        }
//        die;
        //根据数据结构，判断要生成的表格数量，即要$excelSheet->createSheet(1)的个数
//        $objSheet = $excelSheet->getActiveSheet();//获取当前操作的sheet对象
//        $objSheet->setTitle('工作记录表');//设置当前sheet的标题
//        dump($excelSheet->getSheet(0));
//        dump($excelSheet->createSheet(1));
//        dump($excelSheet->createSheet(2));
//        die;
        //设置列宽度为true，避免宽度太宽
//        $objSheet->getColumnDimension('A')->setAutoSize(true);
//        $objSheet->getColumnDimension('B')->setAutoSize(true);
//        $objSheet->getColumnDimension('C')->setAutoSize(true);
//        $objSheet->getColumnDimension('D')->setAutoSize(true);

        //设置第一栏的标题
//        $objSheet->setCellValue('A1','大家一起来找茬')
//            ->setCellValue('B1','')
//            ->setCellValue('C1','')
//            ->setCellValue('D1','');
        /**
         * 另外一种写法
         * $objSheet->setCellValueByColumnAndRow(1,1,'大家一起来找茬');//该方法第一个参数：所在列数；第二个参数：所在行数；第三个参数：要展示的内容
         */

//        $objSheet->mergeCells('A1:D1');

//        $styleArray = [
//            'alignment' =>  [
//                'horizontal'    => Alignment::HORIZONTAL_CENTER,
//            ]
//        ];
//        $objSheet->getStyle('A1:D1')->applyFromArray($styleArray);

        //设置第二行
//        $objSheet->setCellValue('A2','id')
//            ->setCellValue('B2','用户名')
//            ->setCellValue('C2','密码')
//            ->setCellValue('D2','时间');
//
//        $excelSheet->createSheet(1);
        /**
         * 另外一种写法
        $objSheet->setCellValueByColumnAndRow(1,2,'id')
        ->setCellValueByColumnAndRow(2,2,'用户名')
        ->setCellValueByColumnAndRow(3,2,'密码')
        ->setCellValueByColumnAndRow(4,2,'时间');
         */
        //从第二行起，每一行的值，setCellValueExplicit是用来导出文本格式的。
//        for ($i=3;$i<15;$i++){
//            $objSheet->setCellValue('A'.$i,'id'.$i)
//                ->setCellValue('B'.$i,'用户名'.$i)
//                ->setCellValue('C'.$i,'密码'.$i)
//                ->setCellValue('D'.$i,'时间'.$i);
//        }

        //可以将以下内容拆看，作为下载方法
        ob_end_clean();
        ob_start();
        if($format == 'Xlsx'){
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        }else{
            header('Content-Type: application/vnd.ms-excel;charset=UTF-8');
        }
        header('pragma:public');
        //设置完整文件名及后缀
        header('Content-Type: application/octet-stream');//声明下载一个文件
        header('Content-Disposition:attachment;filename='.$fullName);
        header('Content-Transfer-Encoding: binary');
        header('Cache-Control:max-age=0');
// header('Content-Type: application/vnd.ms-excel;charset=UTF-8');
// header('Content-Disposition: attachment;filename="订单汇总表(' . date('Ymd-His') . ').xlsx"');
// header('Cache-Control: max-age=0');
        $objWriter = IOFactory::createWriter($excelSheet,$format);
//        $objWriter->setIncludeCharts(true);
//        $objWriter->save('php://output'); //直接下载的方法
//        $objWriter->save('php://output');
        $objWriter->save(UPLOAD_PATH.'/'.$fullName); //保存到指定目录
        return '/uploads/'.$fullName;
    }

}