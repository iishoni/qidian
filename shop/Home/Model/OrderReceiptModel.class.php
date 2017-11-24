<?php

namespace Home\Model;
use Common\Model\ModelModel;

class OrderReceiptModel extends ModelModel{


    protected $tableName = 'orderreceipt';


    /**
     * [select 列表]
     * @return [type]            [description]
     */
    public function getList($sqlmap = array(), $limit = 20, $page = 1, $order = 'id desc') {

        $count = $this->where($sqlmap)->count();
        $pages = new \Think\Page($count, $limit);
        $page = $page ? $page : 1;

        if (isset($_GET['p'])) {
            $page = (int) $_GET['p'];
        }

        if ($limit != '') {

            $limits = (($page - 1) * $limit) . ',' . $limit;
        }

        $lists = $this->where($sqlmap)->order($order)->limit($limits)->select();


        return array('count' => $count, 'limit' => $limit, 'lists' => dhtmlspecialchars($lists), 'page' => $pages->show());
    }



    public function AddData($params = array()){

        $params['money'] = (int) $params['money'];
        $fp = fopen("data/lock_add_receipt.txt", "w+");
        if(!flock($fp,LOCK_EX | LOCK_NB)){
            $this->error='系统繁忙，请稍后再试';
            return false;
        }


        $is_open_w=get_feeconfig('m_is_open_w');
        if(empty($is_open_w)){
            $this->error='提现未开放！';
            return false;
        }
        $user=D('User');
        $member=$user->UserInfo('userid,username,account,activate');
        if(empty($member)){
            $this->error='非法用户';
            return false;
        }
        //资料未完善不能提现
        if($member['activate']!='2'){
            $this->error='请完善资料后再提现';
            return false;
        }

        if(empty($params['money'])){
            $this->error='金额不能为空';
            return false;
        }


        $pay_array=array('1'=>'common_num','2'=>'income_num','3'=>'recommen_num');
        if(!array_key_exists($params['pay_type'],$pay_array)){
            $this->error='钱包类型错误';
            return false;
        }

        $w_money=get_feeconfig('m_w_money');
        if($w_money){
            if(($params['money']%$w_money)!='0'){

                $this->error='金额请输入'.$w_money.'的倍数';
                return false;
            }
        }

        //验证二级密码
        if(!$user->CheckPwd($params['password'])){
            $this->error='二级密码不正确';
            return false;
        }

        //验证提现时间
        if (!check_tx_time()) {
            $this->error='非提现时间';
            return false;
        }

        //验证每天成功提现次数
        $ok_count=get_feeconfig('m_tj_count');
        if(!empty($ok_count)){
            $sqlmap=array();
            $sqlmap['uid']=$member['userid'];
            $sqlmap['status']='0';
            $sqlmap['datastr']=date('Ymd');

            $tx_num= $this->where($sqlmap)->count(1);
            if( (int)$tx_num >= (int) $ok_count){
                $this->error='提现失败，每天只能提'.$ok_count.'次';
                return false;
            }
        }

        $store=D('Store');
        $wealth_num=$store->StoreInfo('wealth_num');
        //推荐奖验证诚信值
        if($params['pay_type']=='3'){

            if($wealth_num<20){
                $this->error="诚信值小于20不可提现";
                return false;
            }

            if($params['money'] > get_tx($wealth_num['wealth_num'])){

                $this->error="最大金额只能提".get_tx($wealth_num['wealth_num']);
                return false;
            }

        }

        $field=$pay_array[$params['pay_type']];//字段要修改的字段
        $s_info=$store->StoreInfo($field);
        $old_money=$s_info[$field];
        //扣除金额
        if($old_money<$params['money']){
            $this->error='金额不足';
            return false;
        }


        $params['uid']=$member['userid'];
        $params['account']=$member['account'];
        $params['username']=$member['username'];
        $params['status']=0;
        $params['datetime']=time();
        $params['datastr']=date('Ymd');
        if(!$insert=$this->data($params)->add()){

            $this->error='提现失败';
            return false;
        }

        //扣减金额
        if(!$store->where(array('uid'=>$member['userid']))->setDec($field,$params['money'])){
            $this->error='提现失败';
            return false;
        }


        //明细
        switch($params['pay_type']){

            case '1':
                $money_type_text='认筹股金';
                break;

            case '2':
                $money_type_text='股金收益';
                break;

            case '3':
                $money_type_text='业绩红利';
                break;
        }


        //明细记录
        $data=array();
        $data['uid']=$member['userid'];
        $data['account']=$member['account'];
        $data['username']=$member['username'];
        $data['type']='withdraw';
        $data['content']=$money_type_text.'提现'.$params['money'];
        $data['datetime']=time();
        $money_detail=json_encode(
            array(
                'old_money' => sprintf('%.2f', $old_money),
                'money' => sprintf('%.2f', '-'.$params['money']),
                'new_money' => sprintf('%.2f', $old_money-abs($params['money'])),
            )
        );
        $data['money']=$money_detail;
        D('money_detail')->data($data)->add();

        flock($fp,LOCK_UN);
        fclose($fp);

        return true;
    }





    //确认收米
    public function send($params = array()){

        $fp = fopen("data/lock_receipt.txt", "w+");
        if(!flock($fp,LOCK_EX | LOCK_NB)){
            $this->error='系统繁忙，请稍后再试';
            return false;
        }

        if(empty($params['order_id'])){
            $this->error='操作失败';
            return false;
        }

        if(empty($params['uid'])){
            $this->error='用户不能为空';
            return false;
        }

        //验证是否合法数据
        $order_service=D('order');
        $order_info=$order_service->field('id,a_id,r_id,money,datetime')->where(array('id'=>$params['order_id'],'r_uid'=>$params['uid'],'status'=>'1'))->find();
        if(!$order_info){
            $this->error='操作失败';
            return false;
        }



        $data=array();
        $data['id']=$params['order_id'];
        $data['status']='2';
        $data['receipt_datetime']=time();

        if(!$order_service->save($data)){
            $this->error = '操作失败';
            return false;
        }



        $play_model=D('OrderBuy');
        //全部订单都收米了修改打米状态
        $sub_count=$order_service->where(array('a_id'=>$order_info['a_id']))->count(1);

        $status_sub_count=$order_service->where(array('a_id'=>$order_info['a_id'],'status'=>'2'))->count();

        if($sub_count==$status_sub_count){

            $play_model->where(array('id'=>$order_info['a_id']))->data(array('status'=>'3'))->save();
        }



        $sub_count=$order_service->where(array('r_id'=>$order_info['r_id']))->count(1);

        $status_sub_count=$order_service->where(array('r_id'=>$order_info['r_id'],'status'=>'2'))->count(1);

        if($sub_count==$status_sub_count){

            $this->where(array('id'=>$order_info['r_id']))->data(array('status'=>'3'))->save();
        }


        $pay_info=$play_model->where(array('id'=>$order_info['a_id']))->find();

        $pay_info['money']=$order_info['money']; //金额

        //修改排单时间
        D('User')->where(array('userid'=>$pay_info['uid']))->setField('order_time',time());

        //本金
        $bj_time=get_config('m_bj_time');//本金冻结时间

        $data=array();

        //金额表
        $member_model=D('Store');
        $details_model=D('money_detail');
        $freeze_model=M('money_freeze');

        if(!empty($bj_time)){
            $data['endtime']=time()+($bj_time*60*60);
        }else{
            $data['isok']=1;
            $data['endtime']=time();

            //本金转给用户
            $member_model->where(array('uid'=>$pay_info['uid']))->setInc('common_num',$pay_info['money']);
            //修改状态,表示已到账
            $play_model->where(array('id'=>$order_info['a_id']))->data(array('status'=>'4'))->save();

            //明细
            $datas=array();
            $datas['uid']=$pay_info['uid'];
            $datas['account']=$pay_info['account'];
            $datas['username']=$pay_info['username'];
            $datas['type']='money';
            $datas['content']='本金'.$pay_info['money'];
            $datas['datetime']=time();
            $money_detail=json_encode(
                array(
                    'money' => sprintf('%.2f', '+'.$pay_info['money']),
                )
            );
            $datas['money']=$money_detail;
            $details_model->data($datas)->add();

        }

        $data['uid']=$pay_info['uid'];
        $data['account']=$pay_info['account'];
        $data['username']=$pay_info['username'];
        $data['type']='money';
        $data['count_money']=$pay_info['money'];
        $data['money']=$pay_info['money'];
        $data['info']='本金';
        $data['a_id']=$pay_info['id'];
        $data['r_id']=$order_info['r_id'];
        $data['datetime']=time();
        $freeze_model->data($data)->add();




        //诚信值
        $val_time=get_lixinconfig('m_val_time');//诚信值冻结时间
        $data=array();
        $get_cx_val=set_cx_val($pay_info['money']);
        if(!empty($val_time)){
            $data['endtime']=time()+($val_time*60*60);
        }else{
            $data['isok']=1;
            $data['endtime']=time();

            $member_model->where(array('uid'=>$pay_info['uid']))->setInc('wealth_num',$get_cx_val);

            //明细
            $datas=array();
            $datas['uid']=$pay_info['uid'];
            $datas['account']=$pay_info['account'];
            $datas['username']=$pay_info['username'];
            $datas['type']='val';
            $datas['content']='诚信值'.$get_cx_val;
            $datas['datetime']=time();

            $money_detail=json_encode(
                array(
                    'money' => sprintf('%.2f', '+'.$get_cx_val),
                )
            );

            $datas['money']=$money_detail;
            $details_model->data($datas)->add();

        }

        $data['uid']=$pay_info['uid'];
        $data['account']=$pay_info['account'];
        $data['username']=$pay_info['username'];
        $data['type']='val';
        $data['count_money']=$pay_info['money'];
        $data['money']=$get_cx_val;
        $data['content']='诚信值';
        $data['a_id']=$pay_info['id'];
        $data['r_id']=$order_info['r_id'];
        $data['datetime']=time();
        $freeze_model->data($data)->add();



        //=================限时打款利息==========================
        $lx_arr=get_lixinconfig(); //格式5小时内打款=15(%)，24小时内打款=10(%)

        $limit_pay_time=explode(',',$lx_arr['m_lx_hour']);//限制打款时间
        $limit_pay_time_lv=explode(',',$lx_arr['m_lx_num']);//限制打款奖励

        $start_time = time() - $order_info['datetime'];//已用时间
        $lv=0;//获得利息比例
        foreach($limit_pay_time as $k=>$v){
            $end_time = ($v * 60 * 60);
            if ($start_time < $end_time){
                $lv=$limit_pay_time_lv[$k];
                break;
            }
        }
        $lx_time=$lx_arr['m_lx_time'];//利息冻结时间240小时
        //添加奖励
        if($lv>0){
            $lx_pr=$lv/100;
            $lx_money=$pay_info['money']*$lx_pr;
            $data=array();
            if(!empty($lx_time)){

                $data['endtime']=time()+($lx_time*60*60);
            }else{
                $data['isok']=1;
                $data['endtime']=time();
                //股金收益
                $member_model->where(array('uid'=>$pay_info['uid']))->setInc('income_num',$lx_money);

                //明细
                $datas=array();
                $datas['uid']=$pay_info['uid'];
                $datas['account']=$pay_info['account'];
                $datas['username']=$pay_info['username'];
                $datas['type']='lx';
                $datas['content']='利息'.$lx_money;
                $datas['datetime']=time();

                $money_detail=json_encode(
                    array(
                        'money' => sprintf('%.2f', '+'.$lx_money),
                    )
                );

                $datas['money']=$money_detail;
                $details_model->data($datas)->add();

            }

            $data['uid']=$pay_info['uid'];
            $data['account']=$pay_info['account'];
            $data['username']=$pay_info['username'];
            $data['type']='lx';
            $data['count_money']=$pay_info['money'];
            $data['money']=$lx_money;
            $data['content']='利息';
            $data['a_id']=$pay_info['id'];
            $data['r_id']=$order_info['r_id'];
            $data['datetime']=time();

            $freeze_model->data($data)->add();
        }

        //++++++++++++++烧伤奖++++++++++++++++++

        //动态推广奖:1代2% 2代1%;

        $user=D('User');
        $tj_list=$user->AllParent($pay_info['uid']);//获取上级推荐人

        $m_tj_time=get_lixinconfig('m_tj_time'); //冻结时间
        $m_team_rem = get_lixinconfig('m_team_rem');
        $m_team_level = get_lixinconfig('m_team_level');
        $m_team_lv = get_lixinconfig('m_team_lv');

        $m_levels = explode(",",$m_team_level);
        $m_rems = explode(",",$m_team_rem);
        $m_lvs = explode(",",$m_team_lv);
        $tj_lv = array();
        $tj_rems = array();
        foreach ($m_levels as $kl => $lev) {
            $tj_lv[$lev-1] = floatval($m_lvs[$kl])/100;
            $tj_rems[$lev-1] =$m_rems[$kl];
        }

        //$tj_lv=array( 0=>0.14, 2=>0.04,4=>0.02,);
        foreach($tj_lv as $k=>$v){
            $tj_vv=0;
            $tj_id=$tj_list[$k];//上级ID
            $tj_vv=$v;//收益


            //满足直推人数才有收益
            $tuiMap = array();
            $tuiMap["status"] = array("eq","1");
            $tuiMap["activate"] = array("eq","2");
            $tuiMap["pid"] = $tj_id;
            $tuiCount = $user->where($tuiMap)->count("userid");
            $tj_count  = intval($tj_rems[$k]);


            if($tj_id && $tj_vv &&$tuiCount>=$tj_count){  //不封号的推荐人才有收益

                $price = $pay_info['money'];

                //烧伤订单
                $fireOrder = M("order")->where(array("status"=>"2","a_uid"=>$tj_id))->order("play_datetime desc")->find();
                if($fireOrder){
                    $price = $fireOrder["money"];
                }else{
                    $price = 0;
                    continue;
                }


                $tj_money=$price*$tj_vv;

                $uinfo=$user->field('userid,username,account')->find($tj_id);

                $data=array();

                $time=time();

                if(empty($m_tj_time)){
                    $data['isok']=1;
                    $data['endtime']=$time;
                    //加入收益表
                    $member_model->where(array('uid'=>$uinfo['userid']))->setInc('recommen_num',$tj_money);

                    //明细
                    $datas=array();
                    $datas['uid']=$uinfo['userid'];
                    $datas['username']=$uinfo['username'];
                    $datas['account']=$uinfo['account'];
                    $datas['type']='tj';
                    $datas['content']=($k+1).'代推广奖';
                    $datas['datetime']=time();
                    $money_detail=json_encode(
                        array(
                            'money' => sprintf('%.2f', '+'.$tj_money),
                        )
                    );
                    $datas['money']=$money_detail;

                    $details_model->data($datas)->add();

                }else{
                    $data['endtime']=$time+($m_tj_time*60*60);
                }

                $data['uid']=$uinfo['userid'];
                $data['username']=$uinfo['username'];
                $data['account']=$uinfo['account'];
                $data['type']='tj';
                $data['count_money']=$pay_info['money'];
                $data['money']=$tj_money;
                $data['content']=($k+1).'代推广奖';
                $data['a_id']=$pay_info['id'];
                $data['r_id']=$order_info['r_id'];
                $data['datetime']=$time;

                $freeze_model->data($data)->add();

            }

        }



        //++++++++++++++++团队奖金+++++++++++++++++++++++++++++++
        // $chlidren=$user->AllParent($pay_info['uid']);
        // $team_time = get_lixinconfig('m_team_time'); //团队冻结时间

        // //直推几人,隔代拿奖
        // foreach ($chlidren as $kl => $vo) {
        // 	$pid=$vo;
        // 	if($pid){
        // 		$team_money = '';
        // 		$team_money = 0.002 * $pay_info['money'];
        // 		if ($team_money) {

        // 			$uinfo=$user->field('userid,account,username')->find($pid);
        // 			$data=array();
        // 			$time=time();
        // 			if(empty($team_time)){//冻结时间
        // 				$data['isok']=1;
        // 				$data['endtime']=$time;

        // 				//业绩红利
        // 				$recommen_num=$member_model->where(array('uid'=>$uinfo['userid']))->getField('recommen_num');
        // 				$member_model->where(array('uid'=>$uinfo['userid']))->setInc('recommen_num',$team_money);
        // 				//明细
        // 				$datas=array();
        // 				$datas['uid']=$uinfo['userid'];
        // 				$datas['username']=$uinfo['username'];
        // 				$datas['account']=$uinfo['account'];
        // 				$datas['type']='team';
        // 				$datas['content']=($kl+1).'代-'.$pay_info['username'];
        // 				$datas['datetime']=time();

        // 				$money_detail=json_encode(
        // 					array(
        // 						'old_money' => sprintf('%.2f', $recommen_num),
        // 						'money' => sprintf('%.2f', '+'.$team_money),
        // 						'new_money' => sprintf('%.2f', $recommen_num + $team_money),
        // 					)
        // 				);
        // 				$datas['money']=$money_detail;
        // 				$details_model->data($datas)->add();

        // 			}else{
        // 				$data['endtime']=$time+($team_time*60*60);
        // 			}

        // 			$data['uid']=$uinfo['userid'];
        // 			$data['username']=$uinfo['username'];
        // 			$data['account']=$uinfo['account'];
        // 			$data['type']='team';
        // 			$data['count_money']=$pay_info['money'];
        // 			$data['money']=$team_money;
        // 			$data['content']=($kl+1).'代-'.$pay_info['username'];
        // 			$data['a_id']=$pay_info['id'];
        // 			$data['r_id']=$order_info['r_id'];
        // 			$data['datetime']=$time;

        // 			$freeze_model->data($data)->add();
        // 		}
        // 	}

        // }

        fclose($fp);
        return true;
    }









    protected function setDataStatus(&$result, $options) {

        $arr=explode(',',$result['receipt_type']);

        foreach($arr as $k=>$v){

            if($v=='1') $pay_text[]='银行卡';
            if($v=='2') $pay_text[]='微信';
            if($v=='3') $pay_text[]='支付宝';

        }



        if(empty($result['status'])){

            $result['_status_class']='btwz';
            $result['_status_ico']='&#xe61e;';
            $result['_status_text']='提交成功';

        }elseif($result['status']=='1'){
            $result['_status_class']='btwzbb';
            $result['_status_ico']='&#xe62d;';
            $result['_status_text']='匹配成功';

        }elseif($result['status']=='2'){
            $result['_status_class']='btwzbb';
            $result['_status_ico']='&#xe62d;';
            $result['_status_text']='全部打款';
        }elseif($result['status']=='3'){
            $result['_status_class']='btwzcc';
            $result['_status_ico']='&#xe62e;';
            $result['_status_text']='交易成功';
        }


        $result['pay_text']=implode('，',$pay_text);
        $result['pay_type']=pay_type($result['pay_type']);

        $result['_play']=D('Order')->where(array('r_id'=>$result['id']))->select();


        return $result;
    }

    protected function _after_select(&$result, $options) {



        foreach ($result as &$record) {
            $this->setDataStatus($record, $options);
        }
        return $result;
    }




}

?>