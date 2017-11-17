<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends CommonController {


    public function building()
    {
      $this->display();
    }
    
    public function index(){
        $news=M('news');
        $news_info=$news->where('status=1')->order('id desc')->find();
        $this->assign('news_info',$news_info);

        $userid=get_userid();
        $u_info=D('User')->field('mobile')->find($userid);
        $this->assign('u_info',$u_info);
        $this->display();
    }


    

    //验证二级密码
    public function checkpassword(){
        if(!IS_AJAX)
            return false;

        $password=I('password');
        if(D('User')->CheckPwd($password)){
            session('safety_pwd',$password,10800);//保存三个小时
            ajaxReturn('密码正确',1,U('Traing/TraingSell'));
        }else{
            ajaxReturn('密码错误');
        }
    }

    /**
     * 修改头像
     */
    public function uplodeimg(){
        $data=img_uploading();
        if($data['status']==1){
            $userid=get_userid();
            $where['userid']=$userid;
            D('User')->where($where)->setField('head_img',$data['res']);
        }
        $this->ajaxReturn($data);
    }



}