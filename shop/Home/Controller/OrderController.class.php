<?php
namespace Home\Controller;
use Think\Controller;

class OrderController extends CommonController {
	
	//提供帮助列表
	public function GiveHelp(){
		$play_service=D('OrderBuy');
		if(IS_POST){

			//上传支付凭证
			$data=array();
	        if (!empty($_FILES['picture']['name'])) {
	            $img_data=img_uploading();
	            if($img_data['status']==1){
	                $data['play_pic']=$img_data['res'];
	            }else{
	                error_alert('凭证上传失败'); 
	            }
	        }
	        $data['order_id']=I('order_id',0,'intval');
	        $data['uid']=get_userid();
	        
	        $play_service=D('OrderBuy'); 
	        if(!$play_service->send($data)){
	            error_alert($play_service->getError());
	        }   
	        success_alert('打款成功',U('GiveHelp'));
			
		}else{
			
			$where=array();
			$where['uid']=get_userid();
			$where['is_push']='0';
			$result=$play_service->getList($where);
			$where['is_push']='1';
			$result2=$play_service->getList($where);
			$this->assign('list',$result['lists'])->assign('list2',$result2['lists'])->display();
		}
		

	}
    
	//接受帮助列表
	public function ReceiptHelp(){
		
		if(IS_POST){
			$data=array();
			$data['order_id']=I('order_id',0,'intval');
			$data['uid']=get_userid();
			
			$receipt_service=D('OrderReceipt');
			if(!$receipt_service->send($data)){
				error_alert($receipt_service->getError());
			}	
			
			success_alert('收款成功',U('ReceiptHelp'));
			
		}else{
			
			$where=array();
			$where['uid']=get_userid();
			$result=D('OrderReceipt')->getList($where);
			
			$this->assign('list',$result['lists'])->display();
			
		}

		
	}


}
