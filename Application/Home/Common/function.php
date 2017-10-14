<?php
/*
 *系统自带普通数据分页 分页函数
 * $Model表的实例化模型
 * $number每页条数
 * $map数据查询条件
 * $order带引号的字符串，排序规则
 * 返回$array['list']为一页数据
 * 返回$array['page']为返回的分页数代码
 * */
function  mypage_sys($Model, $number, $map, $order) {
    $count = $Model->where($map)->count();
    $Page = new \Think\Page($count, $number);
    $show = $Page->show();
    // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
    $list = $Model->where($map)->order($order)->limit($Page->firstRow . ',' . $Page->listRows)->select();
    $array['list'] = $list;
    $array['page'] = $show;
    $array['maxPage'] = ceil($count / $number);
    return $array;
}

/**
 * 对数据集进行分页
 * @param $dataList
 * @param $number
 * @return mixed
 */
function data_pages_sys($dataList, $number) {
    $count = count($dataList);
    $Page = new \Think\Page($count, $number);
    $show = $Page->show();
    // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
    $list = array_slice($dataList, $Page->firstRow, $Page->listRows);
    $array['list'] = $list;
    $array['page'] = $show;
    $array['maxPage'] = ceil($count / $number);
    return $array;
}

/**
 * 多个数组合并
 * @param $dataList
 * @return array
 */
function arrays_merge($dataList) {
    $arr = array();
    for ($i = 0; $i < count($dataList); $i++) {
        for ($j = 0; $j < count($dataList[$i]); $j++) {
            if ($dataList[$i][$j]) {
                $arr[] = $dataList[$i][$j];
            }
        }
    }
    return $arr;
}

/**
 * 获取内容列表
 * @param $model 模型
 * @param string $keywords 关键词
 * @param int $page 第几页数据
 * @param int $length 一页的长度
 * @param string $order 排列顺序
 * @param array $where 条件
 * @return mixed
 */
function get_content_list($model, $keywords = '', $page = 0, $length = 5, $order = 'order_id', $where = array()) {
    if ($keywords) {
        $where['title'] = array('like', '%' . $keywords . '%');
    }
    $where['is_show'] = 1;
    $count = $model->where($where)->count();
    $max_page = ceil($count / $length);
    $start = $page * $length;
    $list = $model->where($where)->order($order)->limit($start, $length)->select();
    $contentList['max_page'] = $max_page;
    $contentList['list'] = $list;
    return $contentList;
}

/**
 * 获取上一条下一条数据数组，注意变量$Mobdel表模型,category_id有类别区别
 * @param $id 当前id值
 * @param $order 排列顺序
 * @param $Mobdel 当前模型
 * @param int $map 自带查找条件
 * @param bool $isCategory 是否带类别
 * @return mixed
 */
function get_prev_next($id, $order, $Mobdel, $map = 1, $isCategory = true) {
    if ($isCategory) {
        $thiscontent = $Mobdel->find($id);
        $map['category_id'] = $thiscontent['category_id'];
    }
    $contentlist = $Mobdel->where($map)->order($order)->select();
    for ($i = 0; $i < count($contentlist); $i++) {
        if ($contentlist[$i]['id'] == $id) {
            $mid = $i;
            break;
        }
    }
    $data['prev'] = ($mid - 1) > -1 ? $contentlist[$mid - 1] : "";
    $data['next'] = ($mid + 1) < count($contentlist) ? $contentlist[$mid + 1] : "";
    return $data;
}

/**
 * 把数组分割成几个数组
 * @param $content
 * @param $num
 * @return array
 */
function cut_array($content, $num) {
    $allnum = count($content);
    $page = ceil($allnum / $num);
    for ($i = 0; $i < $page; $i++) {
        $newcontent[] = array_slice($content, $i * $num, $num, false);
    }
    return $newcontent;
}

/**
 * 搜索结果飘红替换
 * @param $html
 * @param $q
 * @return mixed
 */
function search_replace($html, $q) {
    return str_ireplace($q, '<span style="color:red">' . $q . '</span>', $html);
}

/**
 * 路由生成url链接,目前只支持正则路由
 * @param $url
 * @param $vars
 * @param bool $suffix
 * @return string
 */
function urlrotue($url, $vars, $suffix = true) {
    if (!C('URL_ROUTER_ON')) {
        return U($url, $vars, $suffix);
        exit();
    }
    $rotueRules = C('URL_ROUTE_RULES');
    $rulesArr = array_values($rotueRules); //路由规则键值数组
    $varsArr = array_values($vars); //参数键值数组
    $patternArr = array(); //字符串子匹配数组
    $params = '';
    //生成需要查找的解析字符串
    $i = 1;
    foreach ($vars as $k => $v) {
        $params .= $k . '=:' . $i . '&';
        $i++;
        $patternArr[] = '/\(.*?\)/';
    }
    $params = $params ? substr($params, 0, -1) : ''; //解析参数字符串

    $parater = ''; //匹配到的原路由规则键值
    foreach ($rulesArr as $k => $v) {
        if ($url . ($params ? '?' : '') . $params == $v) {
            $parater = $v;
            break;
        }
    }
    if ($parater) {
        $key = array_search($parater, $rotueRules); //获取匹配的路由规则键名
        $str = substr('/' . substr($key, 2), 0, -2); //把路由规则键名的正则规则变成纯字符串
        $result = preg_replace($patternArr, $varsArr, $str, 1);
        $suffix = $suffix ? '.' . C('URL_HTML_SUFFIX') : '';
        $result = stripslashes($result);
        return __ROOT__ . $result . (substr($result, strlen($result) - 1, 1) == '/' ? '' : $suffix);
    } else {
        //如果没有写相应的路由规则则使用原方式
        return U($url, $vars, $suffix);
    }
}