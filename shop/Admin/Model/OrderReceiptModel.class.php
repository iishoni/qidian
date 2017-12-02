<?php 

namespace Admin\Model;
use Common\Model\ModelModel;

class OrderReceiptModel extends ModelModel{
	
	
	protected $tableName = 'orderreceipt';


	//匹配
	public function match($params = array()){
		$info=$this->where(array('id'=>$params['id'],'status'=>0))->find();
		if(!$info){
			$this->error='操作失败';
			return false;
		}
		
		if(empty($params['ids'])){
			$this->error='请选择要匹配数据';
			return false;
		}
		
		if(empty($info['money'])){
			
			$this->error='匹配金额不正确';
			return false;
		}
		
		
		$play_model=D('OrderBuy');
		$match_count_money=$play_model->where(array('id'=>array('in',$params['ids'],'uid'=>array('neq',$info['uid'])),'status'=>0))->sum('money');
		
		if($match_count_money!=$info['money']){
			$this->error='匹配金额不相等';
			return false;
		}
		
		$user=D('user');
		$order=M('order');
		$r_user=$user->uid($info['uid'])->push()->output();
		//拆分
		$realname_arr=array();
		foreach($params['ids'] as $k=>$v){
			$play_info=$play_model->where(array('id'=>$v))->field('uid,account,money,username')->find();
			
			$a_user=$user->uid($play_info['uid'])->push()->output();
			
			 
			$data=array();
			$data['a_id']=$v;//提供数据表ID
			$data['r_id']=$info['id'];//接受数据表ID
			
			$data['a_uid']=$play_info['uid'];//提供用户ID
			$data['a_account']=$play_info['account']; //提供用户
			$data['a_username']=$a_user['username'];//提供用户姓名
			$data['a_push_username']=$a_user['_push']['username'];//提供用户的推荐人姓名 
			$data['a_push_mobile']=$a_user['_push']['mobile'];//提供用户的推荐人手机
			
			$data['r_uid']=$info['uid'];//接受用户ID
			$data['r_account']=$info['account']; //接受用户
			$data['r_username']=$info['username'];//接受用户姓名
			$data['r_push_username']=$r_user['_push']['username'];//接受用户的推荐人姓名
			$data['r_push_mobile']=$r_user['_push']['mobile'];//接受用户的推荐人手机
			$data['money']=$play_info['money'];//金额
			$data['datetime']=time();
			
			if(!$insert=$order->data($data)->add()){
				//回滚
				$order->where(array('id'=>$insert))->delete();
				
				//提供状态操作
				$play_model->where(array('id'=>$v))->data(array('status'=>0))->save();
			}else{
				//提供状态操作
				$play_model->where(array('id'=>$v))->data(array('status'=>1,'match_datetime'=>time()))->save();
			}
			
			
			$realname_arr[]=$play_info['username'];
			
			//短信
			if($play_info['account']){
				
//				$tg_info='尊敬的用户,您提供的帮助已确认，请及时登录系统查询。';
				setmyCode($play_info['account'],'2',' ');
			}		
				
			if($info['account']){
				
//				$js_info='尊敬的用户，您的请求已给予帮助，请及时登录系统查询。';
				setmyCode($info['account'],'3',' ');
			}
			 
		}
		/*
		if($info['user']){
			
			$js_info='尊敬的丰亿农业会员，您好，您申请的'.$info['money'].'收米匹配成功，匹配会员信息为：姓名：'.implode(',',$realname_arr).'，请登录网站核实信息。';	
			send_sms($info['user'],$js_info);
		}	
		*/
		
		
		//接受状态操作
		$this->where(array('id'=>$info['id']))->data(array('status'=>1))->save();
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
		
		// $result['_play']=D('Match/Order','Service')->lists(array('r_id'=>$result['id']));
		
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