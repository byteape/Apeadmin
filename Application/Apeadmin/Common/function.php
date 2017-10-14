<?php
/**
 * 删除文件
 * @param $file
 * @return bool
 */
function delfile($file) {
    $file = $_SERVER['DOCUMENT_ROOT'] . __ROOT__ . $file;
    if (file_exists($file)) {
        $result = unlink($file);
        return $result;
    } else {
        return false;
    }
}

/**
 * 创建目录
 * @param $dir 目录路径
 * @param int $mode 读取权限级别
 * @return bool
 */
function mkdirs($dir, $mode = 0777) {
    if (is_dir($dir) || @mkdir($dir, $mode)) return TRUE;
    if (!mkdirs(dirname($dir), $mode)) return FALSE;
    return @mkdir($dir, $mode);
}

/**
 * 编辑页文件上传初始化参数
 * @param $files
 * @return string
 */
function getEditFiles($files) {
    $filesArr = explode(',', $files);
    $resultArr = array();
    import("Org.Util.File");
    foreach ($filesArr as $k => $v) {
        if ($v) {
            $file = new \File($v);
            if ($file != 0) {
                $result = array(
                    'id' => md5($v),
                    'name' => $file->getBaseName(),
                    'type' => $file->getMimeType(),
                    'size' => $file->getSize(false),
                    'origSize' => $file->getSize(false),
                    'lastModifiedDate' => $file->getFileTime(),
                    'src' => $v,
                );
                $resultArr[] = $result;
            }
        }
    }
    return json_encode($resultArr);
}

/**
 * 在模板中输出多少个空格
 * @param $num
 * @return string
 */
function echo_nbsp($num) {
    $str = "";
    $num = ($num == 1) ? 0 : $num;
    for ($i = 1; $i < $num * 5; $i++) {
        $str .= "&nbsp;";
    }
    return $str;
}

/**
 * 判断后台列表节点是否显示
 * @param $name
 * @return mixed
 */
function isadmin($name) {
    if (in_array(session('admin_id'), array('1', '2')) || !C('POWER_ROUTE')) {
        return 1;
    } else {
        $nodes = S('nodes');
        $re = D('AdminNode')->where(array('node_id' => array('in', $nodes), 'name' => $name))->find();
        return $re;
    }
}