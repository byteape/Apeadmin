<?php
/**
 * 获取文件夹及其子录目下的所有的文件名/文件数组
 * +------------------------------------说明------------------------------------+
 * +------------------------------------使用------------------------------------+
 * $File=new FileDir();
 * $info=$File->getFiles($dir,true);
 * +------------------------------------使用------------------------------------+
 * Class FileDir
 */
class  FileDir{
    /**
     * 获取某个目录下所有文件
     * @param $path 目录路径
     * @param bool $one 是否返回一个一维文件数组
     * @param bool $child 是否读取子目录
     * @return array|null
     */
    public  function getFiles($path,$one=true,$child=true){
        $files=array();        
        if(!$child){
            if(is_dir($path)){
                $dp = dir($path); 
            }else{
                return null;
            }
            while ($file = $dp ->read()){  
                if($file !="." && $file !=".." && is_file($path.$file)){  
                   $files[] = $file;
                }  
            }           
            $dp->close();
        }else{
            $this->scanfiles($files,$path);
        }
        if($one){
            //将得到的多维数组转换成一维文件数组
            return $this->getoneArr($path,$files);
        }
        return $files;
    }

    /**
     * @param $files 结果
     * @param $path 目录路径
     * @param bool $childDir 子目录名称
     */
    public function scanfiles(&$files,$path,$childDir=false){
        $dp = dir($path); 
        while ($file = $dp ->read()){  
            if($file !="." && $file !=".."){ 
                if(is_file($path.$file)){//当前为文件
                     $files[]= $file;
                }else{//当前为目录  
                     $this->scanfiles($files[$file],$path.$file.DIRECTORY_SEPARATOR,$file);
                }               
            } 
        }
        $dp->close();
    }

    /**
     * 将深度多维数组处理成一个一维文件数组
     * @param $rooturl
     * @param $dirArr
     * @param array $fileList
     * @return array
     */
    public function getoneArr($rooturl,$dirArr,&$fileList=array()){
        $thisroot=$rooturl;
        foreach($dirArr as $k=>$v){
            if(is_array($v)){
                $thisroot=$rooturl.$k.'/';
                $this->getoneArr($thisroot,$v,$fileList);
            }else{
				$thisroot=$rooturl;
                $file=$thisroot.$v;
                if(is_file($file)){$fileList[]=$file;}
            }
        }
        return $fileList;
    }
}
?>