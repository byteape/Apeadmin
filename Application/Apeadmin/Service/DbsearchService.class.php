<?php
namespace Apeadmin\Service;

class DbsearchService {
    /**
     * 获取数据库查询条件值
     */
    function get_condition($conditions, $dataFields, $searchFields) {
        $condition = array();
        foreach ($conditions as $k => $v) {
            if (in_array($k, $dataFields) && $v != '') {
                if (strtolower($searchFields[$k]) == 'between' && count($v) == 2) {
                    foreach ($v as $m => $n) {
                        $v[$m] = strtotime($n) ? strtotime($n) : 0;
                    }
                    if ($v[0] && $v[1]) {
                        $condition[$k] = array($searchFields[$k], is_array($v) ? implode(',', $v) : $v);
                    } elseif ($v[0] && !$v[1]) {
                        $condition[$k] = array('egt', $v[0]);
                    } elseif (!$v[0] && $v[1]) {
                        $condition[$k] = array('elt', $v[1]);
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