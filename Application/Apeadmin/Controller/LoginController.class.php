<?php
namespace Apeadmin\Controller;

use Think\Controller;
use Apeadmin\Form;

/**
 * 管理员登录控制器不继承总管理控制器
 */
class LoginController extends Controller {
    /**
     * 会员登录
     */
    public function index() {
        $Model = D('Admin');
        if (IS_POST) {
            /*验证码检测*/
            if (!check_verify(I('code'))) {
                $this->error('验证码输入错误！');
            }
            /*登录数据检测*/
            $data = array(
                'admin_name' => I('admin_name'),
                'password' => I('password', '', 'md5'),
            );
            $result = $Model->where($data)->find();
            if ($result) {
                session('admin_id', $result['admin_id']);
                session('admin_name', $result['admin_name']);
                session('admin_group_id', $result['group_id']);
                /*cookie记录*/
                $cookie_key_login_code = $result['admin_id'] . $result['group_id'] . time();
                if (I('remember') == '1') {
                    $expire = 604800;
                    cookie('admin_id', $arr['admin_id'], $expire);
                    cookie('key_login_code', $cookie_key_login_code, $expire);
                }

                $Model->where(array('admin_id' => $result['admin_id']))->data(array('login_time' => time(), 'login_ip' => get_client_ip(), 'key_login_code' => $cookie_key_login_code))->save();
                $this->success('登录成功', U('Index/index'));
            } else {
                $this->error('用户名或密码错误');
            }
        } else {
            if (session('admin_id')) {
                $this->redirect('Index/index');
            } else {
                $LoginForm = new Form\LoginForm();
                $this->LoginForm = $LoginForm;
                $this->display();
            }
        }
    }

    /**
     * 显示验证码
     */
    public function verify() {
        layout(false);
        ob_clean();
        $Verify = new \Think\Verify();
        $Verify->fontSize = 20;
        $Verify->useImgBg = false;
        $Verify->length = 4;
        $Verify->useNoise = false;
        $Verify->useCurve = false;
        $Verify->codeSet = '0123456789';
        $Verify->imageW = 158;
        $Verify->imageH = 48;
        $Verify->entry();
    }

    /**
     * 退出登录
     */
    public function logout() {
        session('admin_id', null);
        session('admin_name', null);
        session('admin_group_id', null);
        $this->redirect('Login/index');
    }
}