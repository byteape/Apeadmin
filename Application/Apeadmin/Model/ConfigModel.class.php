<?php
namespace Apeadmin\Model;

use Think\Model;

class ConfigModel extends Model {
    /**
     * 设置键值
     * @param $k 键名
     * @param $v 键值
     * @return bool|mixed
     */
    public function set($k, $v) {
        $exist = $this->where(Array('k' => $k))->count();
        if ($exist > 0) {
            if (get_magic_quotes_gpc()) {
                $v = stripslashes($v);
            }
            $flag = $this->where(Array('k' => $k))->setField('v', $v);
        } else {
            $data['k'] = $k;
            $data['v'] = $v;
            $flag = $this->data($data)->add();
        }
        return $flag;
    }

    /**
     * 获取键值
     * @param $k 键名
     * @return mixed
     */
    public function get($k) {
        return $this->getFieldByK($k, 'v');
    }

    /**
     * 获取所有配置参数名值对
     * @return array
     */
    public function getAll() {
        $result = $this->select();
        $returnArray = array();
        foreach ($result as $re) {
            $returnArray[$re['k']] = $re['v'];
        }
        return $returnArray;
    }
}