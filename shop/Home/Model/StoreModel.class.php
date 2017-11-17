<?php
namespace Home\Model;

use Common\Model\ModelModel;

/**
 * 用户模型
 *
 */
class StoreModel extends ModelModel
{
    

    public function StoreInfo($field){
        $userid=get_userid();
        $where['uid']=$userid;
        return $this->where($where)->field($field)->find();
    }

    //扣减仓库数量
    public function DesNum($num,$field,$where=null){
    	if(empty($num))
    		return false;
        if($where==null){
            $userid=get_userid();
            $where['uid']=$userid;
        }
        $cangku_num=$this->where($where)->getField($field);
        if($cangku_num<$num){
            $this->error="原有数量不足";
            return false;
        }
    	$res= $this->where($where)->setDec($field,$num);
    	if($res)
        {
            return true;
        }else{
            $this->error="数量扣减失败";
            return false; 
        }
    }

    //增加仓库数量
    public function IncNum($num,$field,$where=null){
        if(empty($num))
            return false;

       if($where==null){
            $userid=get_userid();
            $where['uid']=$userid;
        }
        $res= $this->where($where)->setInc($field,$num);
        return $res;
    }

    //增加拆分累计
    public function IncHuaFei($num,$where=null){
        if(empty($num))
            return false;

       if($where==null){
            $userid=get_userid();
            $where['uid']=$userid;
        }
        $res= $this->where($where)->setInc('huafei_num',$num);
        if($res)
            return $this->where($where)->getField('huafei_num'); 
        else
            return false;
    }

    //创建仓库
    public function CreateCangku($uid){
    	if(empty($uid))
    		return false;
    	$data['common_num']=0;
        $data['uid']=$uid;
    	$data['income_num']=0;
    	$res=$this->add($data);
        return $res;
    }


    //增加总矿石
    public function IncTotal($num){
        if(empty($num))
            return false;

        $userid=get_userid();
        $where['uid']=$userid;
        $res= $this->where($where)->setInc('total_num',$num);
        return $res;
    }



}
