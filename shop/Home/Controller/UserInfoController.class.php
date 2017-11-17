<?php
namespace Home\Controller;
use Think\Controller;
class UserInfoController extends CommonController {

    public function index(){
        
        $this->display();
    }

    /**
     * [Userinfo 用户信息]
     */
    public function Userinfo(){
        if(IS_AJAX){
            $data=array();
            $post = I('post.u_info');

            $data['idcard'] = safe_replace(trim($post['idcard']));
            $data['zhifubao'] = safe_replace(trim($post['zhifubao']));
            $data['weixin'] = safe_replace(trim($post['weixin']));
            $data['bank_name'] = safe_replace(trim($post['bank_name']));
            $data['bank_no'] = safe_replace(trim($post['bank_no']));
            $data['bank_username'] = safe_replace(trim($post['bank_username']));
            $user=D('User');
            if($user->SaveUserInfo($data)){
                 ajaxReturn('修改成功',1);
            }else{
                ajaxReturn($user->getError());
            }
            
        }else{
            $u_info=D('User')->UserInfo();
            $this->assign('u_info',$u_info);
            $this->display('base'); 
        }
    }

    /**
     * [Userinfo 一级密码]
     */
    public function PassWord(){
        if(IS_AJAX){
            $post_data=I('post.');
            $data['old_pwd']=$post_data['pwd_old'];
            $data['new_pwd']=$post_data['pwd'];
            $data['rep_pwd']=$post_data['pwd_com'];

            $user=D('User');
            if($user->Password($data)){
                ajaxReturn('修改成功',1);
            }else{
                ajaxReturn($user->getError(),0);
            }
        }else{
         $this->display('pwd');
        }
    }


    /**
     * [Userinfo 二级密码]
     */
    public function PassWordSafe(){
        if(IS_AJAX){
            $post_data=I('post.');
            $data['old_pwdt']=$post_data['pwd_old'];
            $data['new_pwdt']=$post_data['pwd'];
            $data['rep_pwdt']=$post_data['pwd_com'];

            if(!isset($data['old_pwdt']) || empty($data['old_pwdt'])){
                ajaxReturn('旧密码不能为空',0);
            }
            if(!isset($data['new_pwdt']) || empty($data['new_pwdt'])){
                ajaxReturn('新密码不能为空',0);
            }
            if(!isset($data['rep_pwdt']) || empty($data['rep_pwdt'])){
                ajaxReturn('确认密码不能为空',0);
            }

            $user=D('User');
            if($user->Password($data)){
                ajaxReturn('修改成功',1,U('Login/logout'));
            }else{
                ajaxReturn($user->getError(),0);
            }
        }else{
            $this->display('pwdtwo');
        }
    }
    


}