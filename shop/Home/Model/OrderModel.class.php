<?php 

namespace Home\Model;
use Common\Model\ModelModel;

class OrderModel extends ModelModel{

  
	protected function setStatus(&$result, $options) {
		
		
		switch($result['status']){
			case 0;
				$result['_status_text']='等待打款';
				$result['_datetime']=$result['datetime'];
			break;				
			case 1;
				$result['_status_text']='全部打款';
				$result['_datetime']=$result['play_datetime'];
			break;				
			case 2;
				$result['_status_text']='全部收款';
				$result['_datetime']=$result['receipt_datetime'];
			break;			
			
			
		}
		
		//收米人帐户信息
		$receipt_info=D('User')->field('bank_no,bank_name,bank_username,zhifubao,weixin')->where(array('userid'=>$result['r_uid']))->find();
		$result['r_bank_account']=$receipt_info['bank_no'].'('.$receipt_info['bank_name'].')';
		$result['r_alipay']=$receipt_info['zhifubao'];
		$result['r_wechat']=$receipt_info['weixin'];
	
		
		return $result;
	}

	protected function _after_select(&$result, $options) {
		
		foreach ($result as &$record) {
			
			$this->setStatus($record, $options);
		}
		return $result;
	}
  
  
}

?>