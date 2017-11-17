<?php
namespace Home\Model;
use Common\Model\ModelModel;

class OrderBuyModel extends ModelModel
{
	protected $tableName = 'orderbuy';

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


    protected function setDataStatus(&$result, $options) {

		$arr=explode(',',$result['pay_type']);

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
		}elseif ($result['status']=='4') {
			$result['_status_class']='btwzcc_ok';
			$result['_status_ico']='&#xe62e;';
			$result['_status_text']='交易成功';
		}

		$result['pay_text']=implode('，',$pay_text);

		//加上a_mid字段甩单显示
		$result['_receipt']=D('Order')->where(array('a_id'=>$result['id'],'a_uid'=>$result['uid']))->select();
		return $result;
	}

	protected function _after_select(&$result, $options) {
		foreach ($result as &$record) {

			$this->setDataStatus($record, $options);
		}
		return $result;
	}



	public function AddData($params = array()){
		$fp = fopen("data/lock.txt", "w+");
		if(!flock($fp,LOCK_EX | LOCK_NB)){
			$this->error='系统繁忙，请稍后再试';
			return false;
		}
		if(empty($params['uid'])){
			$this->error='用户不能为空';
			return false;
		}
		$where['status']=1;
		$where['userid']=$params['uid'];
		$user=D('User')->where($where)->field('activate,userid,username,account')->find();
		if(!$user){
			$this->error='非法的用户';
			return false;
		}
		//资料未完善不能认筹
		if($user['activate']!=2){

			$this->error='请完善资料后再抢单';
			return false;
		}



		if(empty($params['money'])){
			$this->error='金额不能为空';
			return false;
		}


		if(!in_array($params['money'],array('1000','2000','3000','5000','10000'))){
			$this->error='非法的金额';

			return false;
		}

		//验证认筹时间
		if(!check_order_time()){
		   $this->error='非认筹时间';
		   return false;
		}


		//验证每天成功排单次数
		$ok_count=get_config('m_ok_p_count');
		if(!empty($ok_count)){

			$sqlmap=array();
			$sqlmap['uid']=$user['userid'];
			$sqlmap['status']='0';
			$sqlmap['datestr']=date('Ymd');
			$pin_num= $this->where($sqlmap)->count(1);

			if($pin_num >= (int) $ok_count){
				$this->error='抢单失败，每天只能抢'.$ok_count.'单';
				return false;
			}

		}

		if(!check_money_num($params['money'])){
			$this->error='金额被抢完！';
			return false;
		}

		$store=D('Store');
		$buy_num=$store->where(array('uid'=>$user['userid']))->getField('buy_num');
		//认筹币
		$p_count=get_config('m_p_count');
		if($p_count){
			$pin_count=$params['money']*($p_count/1000);
			if($buy_num<$pin_count){
				$this->error='财富币不足'.$pin_count.'个';
				return false;
			}

			//认筹币明细
			$detail=M('buy_num_detail');
			$f_detail=array();
	        $f_detail['uid']=$user['userid'];
	        $f_detail['mobile']= $user['account'];
	        $f_detail['num']=$pin_count;
	        $f_detail['to_mobile']='';
	        $f_detail['to_id']=0;
	        $f_detail['type']=2;
	        $f_detail['datetime']=time();
	        $res=$detail->add($f_detail);

			//扣除认筹币
			if($res)
			$buy_num=$store->where(array('uid'=>$user['userid']))->setDec('buy_num',$pin_count);

		}





		$params['account']=$user['account'];
		$params['username']=$user['username'];
		$params['status']=0;
		$params['datetime']=time();
		$params['datestr']=date('Ymd');

		if(!$insert=$this->data($params)->add()){

			$this->error='抢单失败';
			return false;
		}


		//添加认筹记录
		$daywhere['money']=$params['money'];
	    $daywhere['datestr']=date('Ymd');
	    $daywork=M('daywork');
	    $count=$daywork->where($daywhere)->count(1);//已抢数量
	    if($count==0){
	    	$daywork->where(array('money'=>$params['money']))->save(array('num'=>1,'datestr'=>date('Ymd')));
	    }else{
	    	$daywork->where($daywhere)->setInc('num',1);
	    }


		//认筹明细
		$data=array();
		$data['uid']=$user['userid'];
		$data['account']=$user['account'];
		$data['username']=$user['username'];
		$data['type']='play';
		$data['content']='抢单'.$params['money'];
		$data['datetime']=time();
		$data['money']=json_encode(array('money' => sprintf('%.2f', '-'.$params['money']),));
		D('money_detail')->data($data)->add();

		//修改排单时间
		D('User')->where(array('userid'=>$user['userid']))->setField('paidang_time',time());

		flock($fp,LOCK_UN);
		fclose($fp);

		return true;
	}









	//确认打米
	public function send($params = array()){

		if(empty($params['order_id'])){
			$this->error='操作失败';
			return false;
		}

		if(empty($params['play_pic'])){
			$this->error='请上传打米凭证';
			return false;
		}

		//验证是否合法数据
		$order_service=D('Order');
		$order_info=$order_service->field('id,a_id,r_id')->where(array('id'=>$params['order_id'],'a_uid'=>$params['uid'],'status'=>'0'))->find();
		if(!$order_info){
			$this->error='操作失败';
			return false;
		}



		$data=array();
		$data['id']=$params['order_id'];
		$data['play_pic']=$params['play_pic'];
		$data['status']='1';
		$data['play_datetime']=time();

		if(!$order_service->save($data)){
			$this->error = '操作失败';
			return false;
		}


		//全部订单都打米了修改主订单状态
		$sub_count=$order_service->where(array('a_id'=>$order_info['a_id']))->count(1);
		$status_sub_count=$order_service->where(array('a_id'=>$order_info['a_id'],'status'=>array('neq','0')))->count(1);

		if($sub_count==$status_sub_count){
			$this->where(array('id'=>$order_info['a_id']))->data(array('status'=>'2'))->save();
		}

		$receipt_model=D('OrderReceipt');
		$sub_count=$receipt_model->where(array('r_id'=>$order_info['r_id']))->count(1);

		$status_sub_count=$order_service->where(array('r_id'=>$order_info['r_id'],'status'=>'1'))->count(1);

		if($sub_count==$status_sub_count){

			//提现状态
			$receipt_model->where(array('id'=>$order_info['r_id']))->data(array('status'=>'2'))->save();
		}



		return true;

	}


	public function getAboveUsersByUserId($userid){
		static $userList = array();

		$userModel = D("User");
		$path = $userModel->where(array('userid'=>$userid))->getField('path');//上级ID
		$array = explode("-",$path);
		dump($array);die();

	}



}

?>