<?php
namespace Home\Controller;
use Think\Controller;
class RecyleController extends CommonController {
    /**
     * 回收
     */
    public function recycling(){
        $fee=D('config')->getValue('BACK_FEE');//回收手续费
        $this->assign('fee',$fee);
        $count=$this->recyclecount();
        $this->assign('count',$count);
        $this->display();
    }

    //获取回收次数
    private function recyclecount(){
        $table=D('Recyle');
        $where['uid']=get_userid();
        $where['Year']=date("Y");
        $where['Week']=date("W");
        $count=$table->where($where)->count(1);
        return $count;

    }

    //每周六9:00-凌晨2:00回收
    private function istime(){
        $week=(int)date('w');
        $hour=(int)date('H'); 
        if(($week==6 && $hour>=9 && $hour <24 ) || ($week==0 && $hour>=0 && $hour<2)){
            return true;
        }
        else{
            return false;
        }
    }


    /**
     * 保存 
     */
    public function save(){

        //每周只能回收一次
        $count=$this->recyclecount();
        if($count>=1){
             ajaxReturn('本周已回,每周只能回收一次哦');
        }

        //每周六9:00-凌晨2:00回收
        if(!$this->istime()){
            ajaxReturn('不在回收时间内');
        }

        //接收数据
        $postdata['pay_num']=I('post.num');
        $postdata['pay_no']=I('post.no');
        $postdata['pay_way']=I('post.way');
        $postdata['pay_name']=I('post.name');

        $table=D('Recyle');
        $data        = $table->create($postdata);
        if(!$data){
            ajaxReturn($table->getError());
            return ;
        }
        $pay_num=$data['pay_num'];
        $config=D('config');
        
        //出售限制每次只能出售仓库总数的30%
         $limit=$config->getValue('BACK_LIMIT');
         $store=D('Store');
         $cangku_num=$store->CangkuNum();
         $limit_num=$cangku_num*$limit/100;
         if($limit_num<$pay_num){
          ajaxReturn('每次最多回收仓库的30%');
        }
        //回收手续费
        $fee=$config->getValue('BACK_FEE');//回收手续费
        $fee_num=$pay_num*$fee/100;//手续费
        $total_num=$pay_num+$fee_num;
        
        //扣减仓库数量
        if(!$store->DesNum($total_num)){
            ajaxReturn($store->getError());
        }
        //添加数据
        $data['pay_fee']=$fee_num;//手续费
        if($table->add($data))
            ajaxReturn('已回收',1);
        else
            ajaxReturn('回收失败');  
    }

    
}