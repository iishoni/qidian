<?php
namespace Home\Controller;
use Think\Controller;
class GrowthController extends CommonController {
    /**
     *  
     */
    public function index(){

        $sqlmap=array();
        $sqlmap['_string']=' type="tj" or type="team" ';
        $userid=get_userid();
        $sqlmap['uid']=$userid;
        $result=D('MoneyFreeze')->where($sqlmap)->select();

        $info=M('order')->where(array('a_uid'=>get_userid(),'status'=>2))->order('id desc')->find();
        
        if($info){
            $day=7;
            $paidang_time=M('user')->where(array('userid'=>$userid))->getField('paidang_time');
            $info['end_time']=date('Y/m/d H:i:s',$paidang_time+$day*60*60*24);
        }

        $this->assign('list',$result)->assign('end_time',$info['end_time'])->display();
    }

//冻结收益
public function djmoney(){
    $money_freeze=M('money_freeze');
    $where['uid']=get_userid();
   // $where['type']=array('neq','tj');
  $where['_string'] = "type != 'tj' and type != 'val'";
   $info= $money_freeze->where($where)->select();

   $this->assign('info',$info);
   $this->display();
}
    /**
     *  
     */
    public function UserPay(){
        $sqlmap=array();
        $userid=get_userid();
        $sqlmap['path']=array('like','%-'.$userid.'-%');
        $sqlmap['islock']='0';

        $uid_in=M('user')->where($sqlmap)->order('userid asc')->getField('userid',true);
        $sqlmap=array();
        if($uid_in){
            $sqlmap['uid']=array('in',implode(',',$uid_in));
            $sqlmap['status']='1';
            $result=D('OrderBuy')->where($sqlmap)->select();
        }
        
        $this->assign('list',$result)->display();
    }

    
}