<?php
namespace Home\Controller;
use Think\Controller;
class TraingController extends CommonController {
    public function _initialize(){
        $safety_pwd=session('safety_pwd');
        if(!isset($safety_pwd) || empty($safety_pwd)){
           $this->redirect('Index/index'); 
        }

        parent::_initialize();
    }


    /**
     * 交易中心-出售
     */
    public function TraingSell(){
        $config=D('config');
        $fee=$config->getValue('TARGET_FEE');//手续费5%
        $this->assign('fee',$fee);
        $this->display();
    }

    public function SaveSell(){
        if(!IS_AJAX){
            return false;
        }
        $postdata['buy_account']=I('post.account');
        $postdata['buy_username']=I('post.username');
        $postdata['num']=I('post.num');
        $table=D('Traing');
        $data        = $table->create($postdata);
        if(!$data){
            ajaxReturn($table->getError());
            return ;
        }
        //验证买家账号是否正确
        $where['account']=$data['buy_account'];
        $where['username']=$data['buy_username'];
        $buy_id=D('User')->where($where)->getField('userid');
        if(!isset($buy_id) || empty($buy_id)){
           ajaxReturn('买家账号或姓名错误'); 
        }
        $data['buy_id']=$buy_id;
        $sell_num=$data['num'];
        //出售手续费
        $config=D('config');
        $fee=$config->getValue('TARGET_FEE');//手续费5%
        $fee_num=$sell_num*$fee/100;//手续费
        $total_num=$sell_num+$fee_num;

        //扣减仓库数量
        $store=D('Store');
        if(!$store->DesNum($total_num)){
            ajaxReturn($store->getError());
        }
        $data['fee_num']=$fee_num;

        $res=$table->add($data);
        if($res){
            ajaxReturn('出售成功',1); 
        }else{
            ajaxReturn($table->getError());
        }
    }

    /**
     * [TraingBuy 购买]
     */
    public function TraingBuy(){
        $table=D('Traing');
        $where['buy_id']=get_userid();
        $where['status']=0;
        $info=$table->field('create_time,num,fee_num,id,sell_account,sell_username,status')->where($where)->order('id desc')->select();
        $this->assign('info',$info);
        $this->display();
    }

    /**
     * [SureBuy 确认购买]
     */
    public function SureBuy(){
        if(!IS_AJAX){
            return false;
        }
        $old_status=0;
        $new_status=1;
        $Traing=D('Traing');
        $where['buy_id']=get_userid();
        $res=$Traing->setStatus($old_status,$new_status,$where);
        if($res){
            ajaxReturn('购买成功，等待卖家确认收米，快去联系卖家转账吧~',1);  
        }else{
            ajaxReturn($Traing->getError());  
        }
    }

    /**
     * [QuitBuy 取消交易]
     */
    public function QuitBuy(){
        if(!IS_AJAX){
            return false;
        }
        $old_status=0;
        $new_status=3;
        $Traing=D('Traing');
        $where['buy_id']=get_userid();
        $res=$Traing->setStatus($old_status,$new_status,$where);
        //将矿石退回给出售者
        if($res){
            $id=I('post.id',0,'intval');
            $id=safe_replace($id);
            if(!isset($id) || empty($id)){
                ajaxReturn('取消失败');  
                return false;
            }
            $where['id']=$id;
            $info=$Traing->field('num,fee_num,sell_id')->where($where)->find();
            $sell_id=$info['sell_id'];
            $total_num=$info['num']+$info['fee_num'];
            $res=D('Store')->IncNum($total_num,array('uid' => $sell_id));
        }

        if($res){
            ajaxReturn('交易已取消！',1);  
        }else{
            ajaxReturn('取消失败');  
        }
    }


    /**
     * [TraingWait 等待收米]
     */
    public function TraingWait(){
        $table=D('Traing');
        $where='(sell_id = '.get_userid().' AND status IN (0,1)) OR (buy_id ='.get_userid().' AND status=1 )';

        $info=$table->where($where)->order('id desc')->select();
        $this->assign('info',$info);
        $this->assign('userid',get_userid());

        $this->display();
    }


    /**
     * [QuitBuy 确认收米]
     */
    public function SureSell(){
        if(!IS_AJAX){
            return false;
        }
        $old_status=1;
        $new_status=2;
        $Traing=D('Traing');
        $where['sell_id']=get_userid();
        $res=$Traing->setStatus($old_status,$new_status,$where);
        //将矿石转给购买者
        if($res){
            $where['id']=$res;
            $info=$Traing->field('num,buy_id')->where($where)->find();
            $buy_id=$info['buy_id'];
            $total_num=$info['num'];
            $res=D('Store')->IncNum($total_num,array('uid' => $buy_id));
        }

        if($res){
            ajaxReturn('已确认收米，交易完成，可在交易记录中查看交易详情~',1);  
        }else{
            ajaxReturn('操作失败');  
        }
    }


    /**
     * [QuitBuy 取消收米]
     */
    public function QuitSell(){
        if(!IS_AJAX){
            return false;
        }
        $old_status=0;
        $new_status=3;
        $Traing=D('Traing');
        $res=$Traing->setStatus($old_status,$new_status);
        //将矿石退回给自己
        if($res){
            $where['id']=$res;
            $info=$Traing->field('num,fee_num')->where($where)->find();
            $total_num=$info['num']+$info['fee_num'];
            $res=D('Store')->IncNum($total_num);
        }

        if($res){
            ajaxReturn('交易已取消！',1);  
        }else{
            ajaxReturn('操作失败');  
        }
    }



    /**
     * 交易中心-自由交易
     */
    public function FreeSell(){
        $config=D('config');
        $fee=$config->getValue('FREE_FEE');//手续费10%
        $this->assign('fee',$fee);
        $this->display();
    }

    /**
     * [SaveFreeSell 保存自由交易]
     */
    public function SaveFreeSell(){
        if(!IS_AJAX){
            return false;
        }
        $table=D('TraingFree');
        $data        = $table->create();
        if(!$data){
            ajaxReturn($table->getError());
            return ;
        }
       
        $sell_num=$data['num'];
        //出售手续费
        $config=D('config');
        $fee=$config->getValue('FREE_FEE');//手续费10%
        $fee_num=$sell_num*$fee/100;//手续费
        $total_num=$sell_num+$fee_num;

        //扣减仓库数量
        $store=D('Store');
        if(!$store->DesNum($total_num)){
            ajaxReturn($store->getError());
        }
        $data['fee_num']=$fee_num;

        $res=$table->add($data);
        if($res){
            ajaxReturn('出售成功',1); 
        }else{
            ajaxReturn($table->getError());
        }
    }

    public function FreeBuy(){
        $table=D('TraingFree');
        $where['sell_id']=array('neq',get_userid());
        $where['status']=0;
        $info=$table->field('create_time,num,fee_num,id,sell_account,sell_username,status')->where($where)->order('id desc')->limit(100)->select();
        $this->assign('info',$info);

        $this->display();
    }

    
    /**
     * [SureFreeBuy 确认抢购]
     */
    public function SureFreeBuy(){
        if(!IS_AJAX){
            return false;
        }
        $old_status=0;
        $new_status=1;
        $Traing=D('TraingFree');
        //添加抢购人信息
        $res=$Traing->setFreeUser();
        if($res){
            ajaxReturn('抢购成功',1);  
        }else{
            ajaxReturn($Traing->getError());  
        }
    }

    public function FreeWait(){

        $table=D('TraingFree');
        $where='(sell_id = '.get_userid().' AND status IN (0,1)) OR (buy_id ='.get_userid().' AND status=1 )';

        $info=$table->where($where)->order('id desc')->select();
        $this->assign('info',$info);
        $this->assign('userid',get_userid());

        $this->display();
    }

    /**
     * [QuitBuy 确认收米]
     */
    public function SureFreeSell(){
        if(!IS_AJAX){
            return false;
        }
        $old_status=1;
        $new_status=2;
        $Traing=D('TraingFree');
        $where['sell_id']=get_userid();
        $res=$Traing->setStatus($old_status,$new_status,$where);
        //将矿石转给购买者
        if($res){
            $where['id']=$res;
            $info=$Traing->field('num,buy_id')->where($where)->find();
            $buy_id=$info['buy_id'];
            $total_num=$info['num'];
            $res=D('Store')->IncNum($total_num,array('uid' => $buy_id));
        }

        if($res){
            ajaxReturn('已确认收米，交易完成，可在交易记录中查看交易详情~',1);  
        }else{
            ajaxReturn('操作失败');  
        }
    }

     /**
     * [QuitFreeSell 取消抢购]
     */
    public function QuitFreeSell(){
        if(!IS_AJAX){
            return false;
        }
        $old_status=0;
        $new_status=3;
        $Traing=D('TraingFree');
        $res=$Traing->setStatus($old_status,$new_status);
        //将矿石退回给自己
        if($res){
            $where['id']=$res;
            $info=$Traing->field('num,fee_num')->where($where)->find();
            $total_num=$info['num']+$info['fee_num'];
            $res=D('Store')->IncNum($total_num);
        }

        if($res){
            ajaxReturn('您已成功取消挂卖！',1);  
        }else{
            ajaxReturn('操作失败');  
        }
    }



    /**
     * 出售记录
     */
    public function SellRecord(){
        $this->display();
    }

    /**
     * 购买记录
     */
    public function BuyRecord(){
        $this->display();
    }
}