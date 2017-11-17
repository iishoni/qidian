<?php 

namespace Home\Model;
use Common\Model\ModelModel;

class MoneyFreezeModel extends ModelModel{

	protected $tableName = 'money_freeze';
  
	
  	protected function setStatus(&$result, $options) {
		
		
		if($result['isok']=='1'){
		
			$time=date('Y-m-d',$result['endtime']);
			
		}else{
			
			if(time()<$result['endtime']){
				$s_time=$result['endtime']-time();	
				 $f=array(
					'86400'=>'天',
					'3600'=>'小时',
					'60'=>'分钟',
					'1'=>'秒'
				);
				
				foreach($f as $kk=>$vv){
					
					if (0 !=$c=floor($s_time/(int)$kk)) {
						$time='剩余：'. $c.$vv;
						break;
					}
				}
	
			}
			
			
		}
	
		$result['_type']=$this->type($result['type']);
		$result['time']=$time;
	
		return $result;
	}

	protected function _after_select(&$result, $options) {
		foreach ($result as &$record) {
			$this->setStatus($record, $options);
		}
		return $result;
	}
	
	
	
	
	
	
	public function type($k=''){
		
	
		
		$arr=array(
			'val'=>'诚信值',
			'money'=>'本金',
			'lx'=>'利息',
			'tj'=>'推广',
			'team'=>'团队奖',
		);
		
		if($k){
			
			return $arr[$k];
		}else{
			return $arr;
		}
		
	}
  
}

?>