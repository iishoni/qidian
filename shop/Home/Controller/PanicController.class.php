<?php
namespace Home\Controller;
use Think\Controller;
class PanicController extends CommonController {
    /**
     * [index 认筹]
     * @return [type] [description]
     */
    public function index(){

        $news=M('news');
        $info=$news->where('status=1')->order('id desc')->find();
        $this->assign('news_list',$info);

        $where['status']='0';
        $orderbuy=M('orderbuy')->field('account,username,money')->where($where)->order('rand()')->limit(10)->select();
        $onests=time()-60;
        $time= date('Y/m/d H:i:s',$onests);
        $this->assign('send_time',$time);
        $this->assign('orderbuy',$orderbuy);
        $this->display();
    }

public function return_ti(){
    $times=time()-60;
    $time= date('Y/m/d H:i:s',$times);
    echo $time;
}


    protected function randPaiMoney(){
         //查询数据库剩余排单
        $pin_money=get_config('m_pin_money');//金额
        $pin_money_arr=explode(",",$pin_money);
        array_push($pin_money_arr,0);
        $pin_money_count = count($pin_money_arr);
        $rand = rand(0,$pin_money_count-1);

        return $pin_money_arr[$rand];
    }



    public function getRandOrderMoney(){
        if(IS_AJAX){
            $u_info=session('user_login');
            $money = $this->randPaiMoney();

            $orderbuy = M("orderbuy");

            //验证认筹时间
            if(!check_order_time()){
                $result["status"] = "0";
                $result["data"] ='非认筹时间';
                echo  json_encode($result);
                exit;
            }


            //验证每天成功排单次数
            $ok_count=get_config('m_ok_p_count');
            if(!empty($ok_count)){

                $sqlmap=array();
                $sqlmap['uid']=$u_info['userid'];
                $sqlmap['status']='0';
                $sqlmap['datestr']=date('Ymd');
                $pin_num= $orderbuy->where($sqlmap)->count(1);

                if($pin_num >= (int) $ok_count){
                    $result["status"] = "0";
                    $result["data"] ='抢单失败，每天只能抢'.$ok_count.'单';
                    echo  json_encode($result);
                    exit;
                }

            }

            if($money>0){

            }else{
                $result["status"] = "1";
                $result["data"] = array("i"=>"3","c"=>"180","tip"=>"没抢到哦。");
                echo  json_encode($result);
                 exit;
            }


            if(!check_money_num($money)){
                $result["status"] = "0";
                $result["data"] ='抢单失败。'.$money.'额度已被抢完！';
                echo  json_encode($result);
                exit;
            }

            if($money>0){
                session("paiMoney",$money);
            }


            $award = array('1000'=>array("i"=>"5","c"=>"300","tip"=>"1000"),'2000'=>array("i"=>"0","c"=>"0","tip"=>"2000"),'3000'=>array("i"=>"4","c"=>"240","tip"=>"3000"),'5000'=>array("i"=>"1","c"=>"60","tip"=>"5000"),'10000'=>array("i"=>"2","c"=>"120","tip"=>"10000"),'0'=>array("i"=>"3","c"=>"180","tip"=>"没抢到哦。"));
            $result["status"] = "1";
            $result["data"] =$award[$money];

            echo  json_encode($result);
        }


    }






    public function GoPay(){
        if(IS_AJAX){

            $u_info=session('user_login');
            //$money=I('post.money',0,'intval');
            $money = session("paiMoney");
            $data=array();
            $data['uid']=$u_info['userid'];
            $data['pay_type']='1,2,3';
            $data['money']=$money;

            $orderbuy=D('OrderBuy');
            if(!$orderbuy->AddData($data)){
                ajaxReturn($orderbuy->getError());
            }
            ajaxReturn('认筹成功',1,U('Order/GiveHelp'));
        }
    }

    public function CheckTime(){

        $config_data=check_order_time();//奖金配置
        $ac=I('ac');
        if($ac=='time'){
            //验证认筹时间
            if(!check_order_time()){

                ajaxReturn('非认筹时间');
                return false;
            }
            else{

               ajaxReturn('',1);
            }
        }elseif($ac=='money'){
            //验证单数是否抢完
            $money=I('money');
            if(!check_money_num($money)){
                ajaxReturn($money.'金额已抢完！');
            }
            ajaxReturn('',1);
        }
    }

    //获得帮助
    public function GetMoney(){
        if(IS_AJAX){
            $post=I('post.');
            if(empty($post['money'])){
                 ajaxReturn('金额不能为空');
            }
            if(empty($post['password'])){
                 ajaxReturn('二级密码不能为空');
            }
            if(empty($post['code'])){
                 ajaxReturn('验证码不能为空');
            }
            //验证码
            if(!check_verify($post['code'])){
                ajaxReturn('验证码错误',0);
            }

            $data=array();
            $data['pay_type']=(int)$post['pay_type'];
            $data['receipt_type']='1,2,3';
            $data['money']=intval($post['money']);
            $data['password']=trim($post['password']);

            $model=D('OrderReceipt');
            if(!$model->AddData($data)){
                ajaxReturn($model->getError());
            }
            ajaxReturn('提现成功',1,U('Order/ReceiptHelp'));
        }else{
            $s_info=D('Store')->StoreInfo('common_num,income_num,recommen_num');
            $this->assign('s_info',$s_info)->display();
        }
    }

}