<?php
/**
 * 检测输入的验证码是否正确，$code为用户输入的验证码字符串
 * @param $code
 * @param string $id
 * @return bool
 */
function check_verify($code, $id = '') {
    $verify = new \Think\Verify();
    return $verify->check($code, $id);
}

/*
 * 截取字符串
 * $string 原字符串
 * $length 需要保留的字符长度
 * $rephtml 是否保留字符串中的 HTML 标签 0为不保留1为保留
 * $dot 截取后的连接符号
 * $pagechar 字符串编码
 * */
function str_cut($string, $length, $rephtml = 0, $dot = '...', $pagechar = 'utf8') {
    //保留转译后的HTML字符
    $rep_str1 = array('&nbsp;', '&amp;', '&quot;', '&lt;', '&gt;', '&#039;', '&ldquo;', '&rdquo;', '&middot;', '&mdash;');
    $rep_str2 = array(' ', '&', '"', '<', '>', "'", "“", "”", "·", "—");
    if ($rephtml == 0) {
        $string = htmlspecialchars_decode($string);
        $string = str_replace($rep_str1, $rep_str2, $string);
        $string = strip_tags($string);
    }
    $string = preg_replace("/\s/", " ", $string);
    $string = preg_replace("/^ +| +$/", '', $string);
    $string = preg_replace("/ {2,}/is", "  ", $string);
    $strlen = strlen($string);
    if ($strlen <= $length) {
        return $string;
    }
    $strcut = '';
    if ($pagechar == 'utf8') {
        $n = $tn = $noc = 0;
        while ($n < $strlen) {
            $t = ord($string[$n]);
            if ($t == 9 || $t == 10 || (32 <= $t && $t <= 126)) {
                $tn = 1;
                $n++;
                $noc++;
            } elseif (194 <= $t && $t <= 223) {
                $tn = 2;
                $n += 2;
                $noc += 2;
            } elseif (224 <= $t && $t < 239) {
                $tn = 3;
                $n += 3;
                $noc += 2;
            } elseif (240 <= $t && $t <= 247) {
                $tn = 4;
                $n += 4;
                $noc += 2;
            } elseif (248 <= $t && $t <= 251) {
                $tn = 5;
                $n += 5;
                $noc += 2;
            } elseif ($t == 252 || $t == 253) {
                $tn = 6;
                $n += 6;
                $noc += 2;
            } else {
                $n++;
            }
            if ($noc >= $length) {
                break;
                $isdot = 1;
            }
        }
        if ($noc > $length) {
            $n -= $tn;
        }
        $strcut = substr($string, 0, $n);
        if ($n < $strlen) {
            $strcut = $strcut . $dot;
        }
    } else {
        for ($i = 0; $i < $length; $i++) {
            $strcut .= ord($string[$i]) > 127 ? $string[$i] . $string[++$i] : $string[$i];
        }
    }
    if ($rephtml == 0) {
        $string = str_replace($rep_str2, $rep_str1, $string);
    }
    return $strcut;
}

/**
 * 把返回的数据集转换成Tree
 * @param $list 要转换的数据集
 * @param string $pk 主键标记字段
 * @param string $pid parent标记字段
 * @param string $child level标记字段
 * @param int $root
 * @return array
 */
function list_to_tree($list, $pk = 'category_id', $pid = 'parent_id', $child = '_child', $root = 0) {
    // 创建Tree
    $tree = array();
    if (is_array($list)) {
        // 创建基于主键的数组引用
        $refer = array();
        foreach ($list as $key => $data) {
            $refer[$data[$pk]] =& $list[$key];
        }
        foreach ($list as $key => $data) {
            // 判断是否存在parent
            $parentId = $data[$pid];
            if ($root == $parentId) {
                $tree[] =& $list[$key];
            } else {
                if (isset($refer[$parentId])) {
                    $parent =& $refer[$parentId];
                    $parent[$child][] =& $list[$key];
                }
            }
        }
    }
    return $tree;
}

/**
 * 将list_to_tree的树还原成列表
 * @param  array $tree 原来的树
 * @param  string $child 孩子节点的键
 * @param  string $order 排序显示的键，一般是主键 升序排列
 * @param  array $list 过渡用的中间数组，
 * @return array        返回排过序的列表数组
 */
function tree_to_list($tree, $child = '_child', $order = 'category_id', &$list = array()) {
    if (is_array($tree)) {
        $refer = array();
        foreach ($tree as $key => $value) {
            $reffer = $value;
            if (isset($reffer[$child])) {
                unset($reffer[$child]);
                tree_to_list($value[$child], $child, $order, $list);
            }
            $list[] = $reffer;
        }
        $list = list_sort_by($list, $order, $sortby = 'asc');
    }
    return $list;
}

/**
 * 富文本编辑器详情内容输出图片根目录替换(建议替换使用函数htmlspecialchars_decode)
 * @param $html 富文本编辑器保存的内容
 * @param bool $diff 是否区分大小写，默认替换区分大小写
 * @return string
 */
function htmldecode($html, $diff = true) {
    $WEB_LOCK_FILE = C('WEB_LOCK_FILE');
    $DETAIL_PIC_ROOT = include($WEB_LOCK_FILE);
    if (__ROOT__ == '' && $DETAIL_PIC_ROOT['DETAIL_PIC_ROOT'] != '' && C('DETAIL_PIC_RESET')) {
        return htmlspecialchars_decode($diff ? str_replace($DETAIL_PIC_ROOT['DETAIL_PIC_ROOT'], '', $html) : str_ireplace($DETAIL_PIC_ROOT['DETAIL_PIC_ROOT'], '', $html));
    } else {
        return htmlspecialchars_decode($html);
    }
}

/**
 * 移动端判断 可在conf配置文件中用三元运算符定义默认模块http://detectmobilebrowsers.com/
 * @return bool
 */
function is_mobile() {
    $useragent = $_SERVER['HTTP_USER_AGENT'];
    if (preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i', $useragent) || preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i', substr($useragent, 0, 4))) {
        return true;
    } else {
        return false;
    }
}