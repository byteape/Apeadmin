<?php
namespace Fwadmin\Service;

class DbsearchService {
    /**
     * 获取数据库查询条件值
     */
    function get_condition($conditions, $dataFields, $searchFields) {
        $condition = array();
        foreach ($conditions as $k => $v) {
            if (in_array($k, $dataFields) && $v != '') {
                if (strtolower($searchFields[$k]) == 'between' && strstr($v, '-') != false) {
                    $arr=explode(' - ',$v);
                    foreach ($arr as $m => $n) {
                        $arr[$m] = strtotime($n) ? strtotime($n) : 0;
                    }
                    if ($arr[0] && $arr[1]) {
                        $condition[$k] = array($searchFields[$k], is_array($arr) ? implode(',', $arr) : $arr);
                    } elseif ($arr[0] && !$arr[1]) {
                        $condition[$k] = array('egt', $arr[0]);
                    } elseif (!$arr[0] && $arr[1]) {
                        $condition[$k] = array('elt', $arr[1]);
                    }
                } elseif (in_array(strtolower($searchFields[$k]), array('like', 'notlike'))) {
                    $condition[$k] = array($searchFields[$k], '%' . $v . '%');
                } else {
                    $condition[$k] = array($searchFields[$k], is_array($v) ? implode(',', $v) : $v);
                }
            }
        }
        $condition = $condition ? $condition : 1;
        return $condition;
    }
}