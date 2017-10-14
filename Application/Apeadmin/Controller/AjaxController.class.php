<?php
namespace Apeadmin\Controller;

use Think\Controller;
use Common\Controller\ApeadminController;

/**
 * Ajax处理 类型：Ajax无页面输出
 */
class AjaxController extends ApeadminController {
    /**
     * 初始化
     */
    function __construct() {
        parent::__construct();
        //网站开关设置保存文件
        $web_open_file = C('WEB_OPEN_FILE');
        $this->web_open_file = $web_open_file;
    }

    /**
     * 删除缓存
     */
    public function delRuntime() {
        import("Org.Util.FileDir");
        $file = new\FileDir();
        $fileList = $file->getFiles(RUNTIME_PATH);
        foreach ($fileList as $k => $v) {
            unlink($v);
        }
        $this->success('清除缓存成功');
    }

    /**
     * 管理员修改密码
     */
    public function editPassword() {
        $admin_id = session('admin_id');
        $old_password = I('old_password', '', 'md5');
        $password = I('password', '', 'md5');
        $sql_password = D('Admin')->getFieldByAdminId($admin_id, 'password');
        if ($old_password == $sql_password) {
            $re = D('Admin')->where(array('admin_id' => $admin_id))->setField('password', $password);
            if ($re) {
                $this->success('修改成功', U('Login/logout'));
            } else {
                $this->error('修改失败');
            }
        } else {
            $this->error('旧密码错误');
        }
    }

    /**
     * 网站关闭设置
     */
    public function webcontroll() {
        $open_web = I('open_web') ? I('open_web') : 2; //1为关闭2为打开
        $close_web_detail = I('close_web_detail', '', 'addslashes'); //给特殊字符加上转义
        $web_open_file = $this->web_open_file;
        $content = "<?php return array('open_web'=>" . $open_web . ",'close_web_detail'=>'" . $close_web_detail . "');?>";
        $handle = fopen($web_open_file, 'w');
        $result = fwrite($handle, $content);
        fclose($handle);
        $result ? $this->success('保存成功') : $this->error('保存失败');
    }

}