<?php
namespace Home\Behaviors;
class compressBehavior extends \Think\Behavior {
    //行为执行入口
    public function run(&$content) {
        /* 去除html空格与换行 */
        $find = '/>\s+</';
        $replace = '><';
        $content = preg_replace($find, $replace, $content);
    }
}
