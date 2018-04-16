<?php
namespace Home\Controller;
use Think\Controller;
class UserCenterController extends CommonController {

    public function index(){
        $userid=get_userid();
        $u_info=D('User')->field('head_img,username,level,mobile')->find($userid);
        $s_info=D('Store')->field('common_num,income_num,recommen_num,fee_num,wealth_num')->find($userid);
        $this->assign('u_info',$u_info);
        $this->assign('s_info',$s_info);
        $this->display();
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

     //激活码
    public function ActivateCode(){
       $s_info=D('Store')->StoreInfo('activate_num');
       $this->assign('num',$s_info['activate_num']);

       //激活码明细
        $where=array();
        $where['uid']=get_userid();
        $detail=M('activate_num')->where($where)->order('id desc')->select();
        $this->assign('info', $detail);

       $this->display();
    }


    /**
     * [savecode 转激活码]
     * @return [type] [description]
     */
    public function SaveActivate(){
        $to_mobile=I('post.to_mobile');
        $num=I('post.to_num',0,'intval');
        $pwd_two=I('post.pwd_two');
        $code=I('post.code');

        if(!isset($to_mobile) || empty($to_mobile)){
            ajaxReturn('请输入接收人手机号');
        }
        if(!isset($num) || empty($num)){
            ajaxReturn('请输入赠送数量');
        }
        if(!isset($pwd_two) || empty($pwd_two)){
            ajaxReturn('请输入二级密码');
        }
        if(!isset($code) || empty($code)){
            ajaxReturn('请输入验证码');
        }

        $user=D('User');
        //验证手机号码
        $u_where['mobile']=$to_mobile;
        $u_where['status']=array('gt',0);
        $to_info=$user->where($u_where)->field('userid,mobile,username')->find();//对方id
        $to_id=$to_info['userid'];
        if(!isMobile($to_mobile) || !$to_info){
            ajaxReturn('接收人手机号错误或不存在');
        }
        if(!preg_match("/^[1-9]\d*$/", $num)){
           ajaxReturn('赠送数量错误');
        }
        
        //验证码
        if(!check_verify($code)){
            ajaxReturn('验证码错误',0);
        }
        //验证二级密码
        if(!$user->CheckPwd($pwd_two)){
            ajaxReturn('二级密码错误');
        }

        //扣减自己的激活码
        $userid=get_userid();
        $store=D('Store');
        if(!$store->DesNum($num,'activate_num')){
            ajaxReturn($store->getError());
        }
        //添加对方激活码
        $where['uid']=$to_id;
        if(!$store->IncNum($num,'activate_num',$where)){
            ajaxReturn($store->getError());
        }

        $table=M('activate_num');
        $u_info=$user->UserInfo('username,mobile');
        //出售者明细
        $data['num']=$num;
        $data['uid']=$userid;
        $data['username']=$u_info['username'];
        $data['mobile']=$u_info['mobile'];
        $data['type']=1;
        $data['to_username']=$to_info['username'];
        $data['to_mobile']=$to_info['mobile'];
        $data['create_time']=time();
        $res=$table->add($data);
         //购买者明细
        $data['num']=$num;
        $data['uid']=$to_id;
        $data['username']=$to_info['username'];
        $data['mobile']=$to_info['mobile'];
        $data['type']=0;
        $data['to_username']=$u_info['username'];
        $data['to_mobile']=$u_info['mobile'];
        $data['create_time']=time();
        $res=$table->add($data);

        if($res)
            ajaxReturn('操作成功',1);
        else
            ajaxReturn('操作失败');
    }


    #++++++++++++会员预警+++++++S++++++++++++
    public function UserWarn(){
        $user=D('User');
        $u_info=$user->UserInfo('userid,deep,path');
        $where['path']   =   array('like','%-'.$u_info['userid'].'-%');
        //$where['deep']   =   array('between',array($u_info['deep']+1,$u_info['deep']+2));//只取两代

        $info=$user->field('userid,deep,username,mobile,status,activate')->where($where)->order('deep,userid')->limit(0,10)->select();
        
        //状态配置
         $status=array(
                0=>'未激活',
                1=>'资料未完善',
                2=>'<span class="gery">正常</span>',
            );

        $this->assign('info', $info);
        $this->assign('deep', $u_info['deep']);
        $this->assign('status', $status);

        $this->display();
    }
    #++++++++++++会员预警+++++++E++++++++++++
    
    public function getUserWarn(){

        $user=D('User');
        $u_info=$user->UserInfo('userid,deep,path');
        $where['path']   =   array('like','%-'.$u_info['userid'].'-%');
        $p = I('p','0','intval');
        $page=$p*10;
        $info=$user->field('userid,deep,username,mobile,status,activate')->where($where)->limit($page,10)->order('deep,userid')->select();
        
        //状态配置
         $status=array(
                0=>'未激活',
                1=>'资料未完善',
                2=>'<span class="gery">正常</span>',
            );

      
      $deep=$u_info['deep'];
      $str='';
      $tb=M('orderbuy');
      foreach ($info as $key => $v) {
          $str.='<tr>';
          $str.='<td>'.($v['deep']-$deep).'级</td>';
          $str.='<td>'.$v['username'].'</td>';
          $str.='<td>'.$v['mobile'].'</td>';
          if($v['status']==0)
            $status_name='冻结';
          else
            $status_name=$status[$v['activate']];

          $str.='<td class="red" >'.$status_name.'</td>';
          $str.='<td class="red">';

          $time=$tb->where(array('uid'=>$v['userid']))->order('id desc')->getField('datetime');
          if($time)
            $time=date('Y-m-d H:i',$time);
          else
            $time = "-";
          $str.=$time.'</td></tr>';
      }
      if(empty($info)){
            $str=null;
      }
      $this->ajaxReturn($str);
    }





    #++++++++++++我的团队+++++++S++++++++++++
    public function MyTeam(){
       $team_info=$this->getteam();
       $this->assign('team_info', $team_info);
        $this->display();
    }

    private function getteam(){
        $team_info=array();
        $user=D('User');
        $u_info=$user->UserInfo('userid,deep,path');
        //一代
        $where['path']=array('like','%-'.$u_info['userid'].'-%');
        $where['deep']=$u_info['deep']+1;

        $team_one=$user->where($where)->count(1);
        $team_info[1]=$team_one; 
        
        //二代
        $where['deep']=$u_info['deep']+2;

        $team_one=$user->where($where)->count(1);
        $team_info[2]=$team_one; 

         //三到七代
         unset($where['deep']);
        $where['_string']='deep > '.($u_info['deep']+2).' AND deep <= '.($u_info['deep']+7);

        $team_one=$user->where($where)->count(1);
        $team_info[3]=$team_one; 
        
        return $team_info;

    }
     #++++++++++++我的团队+++++++E++++++++++++
     
    /**
     * [BuyCode 认筹币]
     */
    public function BuyCode(){
        //扣减自己的激活码
        $userid=get_userid();
        $store=D('Store');
        $s_where['uid']=$userid;
        $pin_code=$store->where($s_where)->getField('buy_num');
        $this->assign('count', $pin_code);

        //激活码明细
        $where=array();
        $where['uid']=$userid;
        $detail=M('buy_num_detail')->where($where)->order('id desc')->select();
        $this->assign('info', $detail);
        $this->display();
    }

    
    /**
     * [savecode 转激活码]
     * @return [type] [description]
     */
    public function SaveCode(){

        $to_mobile=I('post.to_mobile');
        $num=I('post.to_num',0,'intval');
        $pwd_two=I('post.pwd_two');
        $code=I('post.code',0,'intval');
        $to_mobile=safe_replace($to_mobile);

        if(!isset($to_mobile) || empty($to_mobile)){
            ajaxReturn('请输入接收人手机号');
        }
        if(!isset($num) || empty($num)){
            ajaxReturn('请输入赠送数量');
        }
        if(!isset($pwd_two) || empty($pwd_two)){
            ajaxReturn('请输入二级密码');
        }
        if(!isset($code) || empty($code)){
            ajaxReturn('请输入验证码');
        }

        $user=D('User');
        //验证手机号码
        $u_where['mobile']=$to_mobile;
        $u_where['status']=array('gt',0);
        $to_info=$user->where($u_where)->field('userid,mobile,username')->find();//对方id
        $to_id=$to_info['userid'];

        if(!isMobile($to_mobile) || !$to_info){
            ajaxReturn('接收人手机号错误或不存在');
        }
        if(!preg_match("/^[1-9]\d*$/", $num)){
           ajaxReturn('赠送数量错误');
        }

        //验证码
        if(!check_verify($code)){
            ajaxReturn('验证码错误');
        }
        //验证二级密码
        if(!$user->CheckPwd($pwd_two)){
            ajaxReturn('二级密码错误');
        }

        //扣减自己的认筹币
        $userid=get_userid();
        $store=D('Store');
        if(!$store->DesNum($num,'buy_num')){
            ajaxReturn($store->getError());
        }
        //添加对方认筹币
        $where['uid']=$to_id;
        if(!$store->IncNum($num,'buy_num',$where)){
            ajaxReturn($store->getError());
        }



         $detail=M('buy_num_detail');
         $u_info=$user->UserInfo('username,mobile');
         //出售者明细
         $f_detail['uid']=$userid;
         $f_detail['mobile']= $u_info['mobile'];
         $f_detail['num']=$num;
         $f_detail['to_mobile']=$to_mobile;
         $f_detail['to_id']=$to_id;
         $f_detail['type']=1;
         $f_detail['datetime']=time();
         $res=$detail->add($f_detail);
         //购买者明细
         $f_detail['uid']=$to_id;
         $f_detail['mobile']=$to_mobile;
         $f_detail['num']=$num;
          $f_detail['to_mobile']= $u_info['mobile'];
         $f_detail['to_id']=$userid;
         $f_detail['type']=0;
         $f_detail['datetime']=time();
         $res=$detail->add($f_detail);
         if($res)
            ajaxReturn('操作成功','',1);
         else
            ajaxReturn('操作失败');
    }
    

    //头像上传
    public function headimg() {
        
        if (!empty($_FILES['face']['name'])) {
            $img_data=img_uploading();
            if($img_data['status']==1){
                $data['head_img']=$img_data['res'];
            }else{
                ajaxReturn('上像上传失败'); 
            }
            $data['userid']=get_userid();
            $row=D('User')->data($data)->save();
            if($row){
                
                ajaxReturn('上像上传成功',1,$data['head_img']);
            }else{
                ajaxReturn('上像上传失败');
            }
         }else{
             
            ajaxReturn('上像上传失败');
         }
        
    }

    public function sign() {
        $userid = get_userid();
        if(IS_AJAX) {
            $user = M("User")->field("sign_week")->where(array('userid'=>$userid))->find();

            if(date('Ymd', time()) === date('Ymd', $user['sign_week'])) {
                ajaxReturn('今天已经签到了，明天再来哦！');
            } else {
                // 更新签到时间
                D('User')->where(array('userid'=>$userid))->setField('sign_week',time());
                // 更新收益积分
                D('Store')->where(array('uid' => $userid))->setInc('income_num', 5);
                ajaxReturn('签到成功！');
            }
        }
    }

}