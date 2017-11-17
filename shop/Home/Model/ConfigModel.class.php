<?php
namespace Home\Model;

use Common\Model\ModelModel;

/**
 * 用户模型
 *
 */
class ConfigModel extends ModelModel
{
    

    protected $tableName = 'config';
    
    public function getValue($field){
        $where['name']=$field;
        return $this->where($where)->getField('value');
    }

    //获取三种车的拆分概率
    public function getCarFee(){
    	$where="name='CAR_COMMON_FEE' OR name='CAR_SILVER_FEE' OR name='CAR_GOLD_FEE'";
    	$data=$this->where($where)->order('id')->getField('value',true);
    	
    	return $data;
    }


}
