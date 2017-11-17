<?php
namespace Home\Model;

use Common\Model\ModelModel;

/**
 * 用户模型
 *
 */
class TraingModel extends ModelModel
{
    

    
   /**
     * 自动验证规则
     * 
     */
    protected $_validate = array(
        array('buy_account', 'require', '买家账号不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_INSERT),
        array('buy_username', 'require', '买家姓名不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_INSERT),
        array('num', 'require', '出售数量不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_INSERT),
        array('num', '/^[1-9]\d*$/', '出售数量只能为整数', self::MUST_VALIDATE, 'regex', self::MODEL_INSERT),
    );

     /**
     * 自动完成规则
     * 
     */
    protected $_auto = array(
        array('sell_id', 'get_userid', self::MODEL_INSERT, 'function'),
        array('create_time', 'time', self::MODEL_INSERT, 'function'),
        array('status', '0', self::MODEL_INSERT),
        array('sell_account', 'getAccount', self::MODEL_INSERT,'callback'),
        array('sell_username', 'getUsername', self::MODEL_INSERT,'callback'),
    );

    /**
     * [getAccount 获取用户头像]
     * @param  [type] $value [description]
     * @return [type]        [description]
     */
    protected function getAccount(){
    	$userid=get_userid();
    	$where['userid']=$userid;
    	return D('User')->where($where)->getField('account');
    }

   protected function getUsername(){
        $userid=get_userid();
        $where['userid']=$userid;
        return D('User')->where($where)->getField('username');
    }

    public function setStatus($old_status,$new_status,$where=null){

        $id=I('post.id',0,'intval');
        $id=safe_replace($id);
        if(!isset($id) || empty($id)){
            $this->error="参数错误";
            return false;
        }
        if($where==null)
            $where['sell_id']=get_userid();//购买者
        $where['id']=$id;
        $status=$this->where($where)->getField('status');
        if($status==$old_status){
            if($this->where($where)->setField('status',$new_status)){
                $this->error="修改成功";
                return $id;
            }else{
                $this->error="修改失败";
                return false;
            }
        }
    }

}
