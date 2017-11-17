<?php
/**
 * 本程序仅供娱乐开发学习，如有非法用途与本公司无关，一切法律责任自负！
 */
namespace Home\Controller;
use Think\Controller;
class CommonController extends Controller {
	public function _initialize(){
    
       // $mm=CONTROLLER_NAME;
       //  if($mm!='Panic'){
       //    R('GetMoney/getAll');
       //  }

        //判断网站是否关闭
        $close=is_close_site();
        if($close['value']==0){
          success_alert($close['tip'],U('Login/logout'));
        }

        //验证用户登录
        $this->is_user();
        
        #+++配置网站信息+++
        $this->webinfo();

        //在线人数
        get_online_num();
    }

    public function webinfo(){
        // $config=D('Config');
        // $where['group']=1;
        
    }
    
  

  protected function is_user(){
      $userid=user_login();
      $user=M('user');
      if(!$userid){
          $this->redirect('Login/login');
          exit();
       }

       //判断12后必须重新登录
       $in_time=session('in_time');
       $time_now=time();
       $between=$time_now-$in_time;
       $limit_time=60*30;
       if($between > $limit_time){
           $this->redirect('Login/logout');
       }

       $where['userid']=$userid;
       $u_info=$user->where($where)->field('status,session_id')->find(); 
       //判断用户是否锁定 
       $login_from_admin=session('login_from_admin');//是否后台登录
       if($u_info['status']==0 && $login_from_admin!='admin'){
          success_alert('你账号已锁定，请联系管理员',U('Login/logout'));
          exit();
       }

       //判断用户是否在他处已登录
       $session_id=session_id();
       if($session_id != $u_info['session_id'] && empty($login_from_admin)){
          success_alert('您的账号在他处登录，您被迫下线',U('Login/logout'));
          exit();
       }

         //记录操作时间
         session('in_time',time());

  }


}

