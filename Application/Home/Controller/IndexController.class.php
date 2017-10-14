<?php
namespace Home\Controller;

use Think\Controller;
use Common\Controller\HomeController;

class IndexController extends HomeController {
    public function index() {
        $this->display();
    }
}