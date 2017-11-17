<?php
namespace Home\Controller;
use Think\Controller;
class LoginController extends Controller
{


    /**
     * 后台登陆
     */
    public function login()
    {
        //判断网站是否关闭
        $close=is_close_site();
        if($close['value']==0){
            $this->assign('message',$close['tip'])->display('closesite');
        }else{
            $this->display();
        }
    }

    public function checkLogin(){
        if (IS_AJAX) {

            $account = I('account');
            $password = I('password');
            $code=I('post.code',0,'intval');
            if(empty($code)){
               ajaxReturn('验证码不能为空',0); 
            }
            //验证验证码
            // if(!check_verify($code)){
            //     ajaxReturn('验证码错误',0);
            // }
            // 验证用户名密码是否正确
            $user_object = D('Home/User');
            $user_info   = $user_object->login($account, $password);
            if (!$user_info) {
                //账号未激活
                if($user_object->getError()=='activate'){
                    ajaxReturn('账号未激活,请激活',2,U('Login/activate',array('mobile'=>$account)));
                }
                ajaxReturn($user_object->getError(),0);
            }
            // 设置登录状态
            $uid = $user_object->auto_login($user_info);
            // 跳转
            if (0 < $uid && $user_info['userid'] === $uid) {
                session('in_time',time());
                //登录成功计算解冻奖金
                // $this->
                ajaxReturn('登录成功',1,U('Index/index'));
            }else{
                ajaxReturn('签名错误',0);
            }
        }
    }

    /**
     * 注销
     * @author jry <598821125@qq.com>
     */
    public function logout()
    {   
        session('user_login',null);
        session('user_login_sign',null);
        session('in_time',null);
        $this->redirect('Login/login');
    }

    /**
     * 图片验证码生成，用于登录和注册
     * 
     */
    public function verify()
    {
        $vid = 1;
        set_verify($vid);
    }


    //找回密码
    public function getpsw(){
        
        $this->display();
    }

    public function setpsw(){
        if(!IS_AJAX)
            return ;

        $mobile=I('post.mobile');
        $code=I('post.code');
        $password=I('post.password');
        $reppassword=I('post.pwdconfirm');
        if(empty($mobile)){
            ajaxReturn('手机不能为空');
        }
        if(empty($code)){
            ajaxReturn('验证码不能为空');
        }
        if(empty($password)){
            ajaxReturn('密码不能为空');
        }
        if($password  !== $reppassword){
            ajaxReturn('两次输入的密码不一致');
        }
        $user=D('User');
        $mwhere['mobile']=$mobile;
        $userid=$user->where($mwhere)->getField('userid');
        if(empty($userid) || !isMobile($mobile)){
            ajaxReturn('手机号码不出存在或错误');
        }

        //短信验证
        if(empty($code) || !check_sms($code,$mobile)){
            ajaxReturn('验证码不正确或已过期',0);
        }

        $where['userid']=$userid;
        //密码加密
        $salt=user_salt();
        $data['login_pwd']=$user->pwdMd5($password,$salt);
        $data['login_salt']=$salt;
        $res=$user->field('login_pwd,login_salt')->where($where)->save($data);
        if($res){
            ajaxReturn('修改成功',1,U('Login/logout'));
        }
        else{
            ajaxReturn('修改失败');
        }

    }




     /**
   * [cmoneydetail 推广二维码]
   * @return [type] [description]
   */
      public function qrcode(){
        $userid=I('get.userid',0,'intval');
        $userid=safe_replace($userid);
        if(empty($userid))
        {
          return false;
        };
        $mobile=M('user')->where(array('userid'=>$userid))->getField('mobile');
        $this->assign('mobile',$mobile);
        $this->display();
      } 


      public function RegUser(){
        $mobile=I('mobile');
        if($mobile){
          $this->assign('mobile',$mobile);
        }
          $this->display();
      } 



      public function SaveReg(){
        if(IS_AJAX){
            //获取上级
            $post_data=I('post.');
            $cdata['mobile']=$post_data["mobile"];
            $cdata['code']=intval($post_data["code"]);
            $cdata['login_pwd']=$post_data["password"];
            $cdata['login_pwdrep']=$post_data["pwdconfirm"];
            $cdata['safety_pwd']=$post_data["two_password"];
            $cdata['safety_pwdrep']=$post_data["two_pwdconfirm"];
            $cdata['username']=$post_data["realname"];
            $cdata['pid']=$post_data["p_mobile"];
            $cdata['agree']=$post_data["agree"];

            $user=D('User');
            $data = $user->create($cdata);
            if(!$data){
                ajaxReturn($user->getError(),0);
                return ;
            }

            //短信验证
            // if(empty($cdata['code']) || !check_sms($cdata['code'],$data['mobile'])){
            //     ajaxReturn('验证码不正确或已过期',0);
            // }


            //密码加密
            $salt=user_salt();
            $data['login_pwd']=$user->pwdMd5($data['login_pwd'],$salt);
            $data['login_salt']=$salt;
            $data['safety_pwd']=$user->pwdMd5($data['safety_pwd'],$salt);
            $data['safety_salt']=$salt;
            $data['account']=$data['mobile'];
            $data['head_img']='/Public/home/images/tx.jpg';

            //添加层级
            $data['deep']=$user->getdeep($data['pid']);
            //添加路径
            $data['path']=$user->getpath($data['pid']);
            $uid=$user->add($data);
            //为新会员创建仓库和土地
            if(!D('Store')->CreateCangku($uid)){
                ajaxReturn('数据创建失败，请联系管理员',0);
            }

            if($uid){
                ajaxReturn('注册成功，请激活账号',1,U('Login/activate',array('mobile'=>$data['mobile'])));
            }
            else
                ajaxReturn('注册失败',0);
        }
      }

      //发送短信
      public function SendSMS(){
        if(IS_AJAX){
            $mobile=I('post.mobile');
            $return=sendMsg($mobile);
            $this->ajaxReturn($return);
        }
      }

      /**
     * [activate 激活用户]
     * @return [type] [description]
     */
    
    public function activate(){
       $mobile=I('mobile');
       $this->assign('mobile',$mobile);
       $code=$this->ActivateCode();
       if($code>0)
       {
            $code=think_encrypt(getCode(4,1));
            $this->assign('code',$code);
        }
       
       $this->display(); 
    }

    public function ActivateUser(){
        if(!IS_AJAX)
            return false;

        $mobile=trim(I('post.mobile'));
        $mobile=safe_replace($mobile);
        //判断用户是否已激活
        $m_where['mobile']=$mobile;
        $m_where['activate']=0;
        $member=D('User');
        //isactvate=0 未激活 1=已激活
        $u_info=$member->where($m_where)->find();
        if(empty($u_info)){
             ajaxReturn('该账户不存在或已激活');
        }
        $userid=$u_info['userid'];
        //判断是否有激活码
        $store=D('Store');
        $where['uid']=$userid;
        $code=$store->where($where)->getField('activate_num');
        if($code<=0){
            ajaxReturn('无可用激活码');
        }

        //扣减激活码
        $s_res=$store->where($where)->setDec('activate_num',1);
        if($s_res==false){
            ajaxReturn('激活失败');
        }
        //激活用户
        $res=$member->where($m_where)->setField('activate',1);  
        //添加激活明细
        // $data['num']=1;
        // $data['uid']=$userid;
        // $data['username']=$u_info['username'];
        // $data['mobile']=$u_info['mobile'];
        // $data['type']=0;
        // $data['to_mobile']="激活帐号";
        // $data['create_time']=time();
        // M('activate_num')->add($data);

        if($res){
            $login_id=$member->auto_login($u_info);
            if($login_id){
                
                session('in_time',time());
                $session_id=session_id();
                $member->where(array('userid'=>$userid))->setField('session_id',$session_id);
                ajaxReturn('激活成功,请完善个人信息',1, U('UserInfo/Userinfo'));
            }

            ajaxReturn('激活成功',1, U('Login/login'));
           
        }else{
            ajaxReturn('激活失败');
        }

        

    }

    //获取验证码
    public function ActivateCode(){

            $mobile=I('mobile');
            $store=D('Store');
            $userid=D('User')->where(array('mobile'=>$mobile,'activate'=>0))->getField('userid');
            if(!$userid){
                IS_AJAX ? ajaxReturn('用户不存在或已激活') : error_alert('用户不存在或已激活');
            }
            
            $where['uid']=$userid;
            $code=$store->where($where)->getField('activate_num');
            if(!IS_AJAX)
                return $code;

            if(!isset($code) || empty($code)){
               ajaxReturn('暂无可用激活码');
            }

            $data['status']=1;
            $data['message']=think_encrypt(getCode(4,1));
            $this->ajaxReturn($data);
        

    }

}
