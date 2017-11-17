<?php
namespace Home\Controller;
use Think\Controller;
class TurntableController extends CommonController {
    /**
     * 直推奖励 
     */
    public function index(){
        $where['bill_type']='1';
        $info= M('nzbill')->field('bill_time,bill_username,bill_name')->where($where)->order('bill_id desc')->limit(50)->select();
        $this->assign('info',$info);
        $this->display();
    }

    /**
     * 转盘
     */
    public function turn(){
        if(!IS_AJAX){
            return false;
        }

        $userid=get_userid();
        $gailv=F('turntable_data','','./Public/data/');

        $prize_arr = array( 
            '0' => array( 'id'=>1, 'min'=>318,  'max'=>355, 'prize'=>'0',  'name'=>'奖励iPhone7plus',  'v'=>$gailv['one']), 
            '1' => array( 'id'=>2, 'min'=>3,    'max'=>40,  'prize'=>'2',   'name'=>'奖励2矿石',   'v'=>$gailv['two']), 
            '2' => array( 'id'=>3, 'min'=>48,   'max'=>83,  'prize'=>'5',   'name'=>'奖励5矿石',   'v'=>$gailv['three']), 
            '3' => array( 'id'=>4, 'min'=>93,   'max'=>130, 'prize'=>'10',  'name'=>'奖励10矿石',   'v'=>$gailv['four']), 
            '4' => array( 'id'=>5, 'min'=>138,  'max'=>175, 'prize'=>'30',  'name'=>'奖励30矿石',   'v'=>$gailv['five']), 
            '5' => array( 'id'=>6, 'min'=>183,  'max'=>220, 'prize'=>'50',  'name'=>'奖励50矿石',   'v'=>$gailv['six']), 
            '6' => array( 'id'=>7, 'min'=>228,  'max'=>265, 'prize'=>'100', 'name'=>'奖励100矿石',   'v'=>$gailv['seven']), 
            '7' => array( 'id'=>8, 'min'=>273,  'max'=>310, 'prize'=>'0',   'name'=>'谢谢参与',   'v'=>$gailv['eight']), //谢谢参与
        );

        foreach ($prize_arr as $key => $val) { 
            $arr[$val['id']] = $val['v']; 
        } 
    
      $rid = $this->getRand($arr); //根据概率获取奖项id 
      $res = $prize_arr[$rid-1]; //中奖项 
      $bidb = M('nzbill');

      //每天第一次抽奖不需要手续费
      $date=date('Ymd');
      $where['bill_uid']=$userid;
      $where["FROM_UNIXTIME(bill_time,'%Y%m%d')"]=$date;
      $count=$bidb->where($where)->count(1);
      $store=D('store');
      if($count>0)
      {
        $cangku_num=$store->CangkuNum();
        if($cangku_num<1){
            ajaxReturn('矿石不足',0);
        }

        //扣减仓库数量
        if(!$store->DesNum(1)){
            ajaxReturn('抽奖失败',0);
        }

        $bidata['bill_uid']=$userid;
        $bidata['bill_num']=-1;
        $bidata['bill_name']='扣除1矿石';
        $bidata['bill_reason']='转盘抽奖扣除';
        $bidata['bill_time']=time();
        $bidata['bill_type']=0;
        $msg = $bidb->add($bidata);//扣除记录
      }
      // 给用户添加记录
      $num=$res['prize'];
      $id=$res['id'];
      if($num>0)
        $cangku_num=$store->IncNum($num);

      //添加中奖记录
      $u_info=D('User')->UserInfo(null,'username,account');

      $data['bill_uid']=$userid;
      $data['bill_num']=$num;
      $data['bill_name']=$res['name'];
      $data['bill_reason']='转盘抽奖'.$res['name'];
      $data['bill_time']=time();
      $data['bill_type']=1;
      $data['bill_username']=$u_info['username'];
      $data['bill_account']=$u_info['account'];
      $add_res = $bidb->add($data);//获得记录

      if($add_res==false){
         ajaxReturn('抽奖失败',0);
      }


      $min = $res['min']; 
      $max = $res['max']; 
      $result['angle'] = mt_rand($min,$max); //随机生成一个角度 
      $result['prize'] = $res['prize']; 
      $result['prize_name'] = $res['name']; 
      $result['status'] = 1; 
      echo json_encode($result); 
    }

    // 抽奖转盘
    private function getRand($proArr) { 
        $result = ''; 
        //概率数组的总概率精度 
        $proSum = array_sum($proArr); 
        //概率数组循环 
        foreach ($proArr as $key => $proCur) { 
          $randNum = mt_rand(1, $proSum); 
          if ($randNum <= $proCur) { 
            $result = $key; 
            break; 
          } else { 
            $proSum -= $proCur; 
          } 
        } 
        unset ($proArr); 
        return $result; 
    }
}