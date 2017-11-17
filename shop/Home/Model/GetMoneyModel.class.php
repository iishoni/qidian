<?php
namespace Home\Model;
use Common\Model\ModelModel;

class GetMoneyModel extends ModelModel
{

    /**
     * 冻结发放
     */
    public function GetFreeze(){
        $money_freeze=M('money_freeze');
        $store=M('store');
        $member_details=M('money_detail');
        $play_model=D('OrderBuy');

        $list = $money_freeze->where(array('isok'=>'0'))->select();
        
        
        $field=array('money'=>'common_num','val'=>'wealth_num','lx'=>'income_num','tj'=>'recommen_num','team'=>'recommen_num');
        foreach($list as $k=>$v){
            
            if(array_key_exists($v['type'],$field)){

                if(time()>=$v['endtime']){
                    $f=$field[$v['type']];
                    $money=$store->where(array('uid'=>$v['uid']))->getField($f);
                    $money_freeze->where(array('id'=>$v['id']))->data(array('isok'=>'1'))->save();
                    $store->where(array('uid'=>$v['uid']))->setInc($f,$v['money']);
                    
                    //修改状态,表示已到账
                    $play_model->where(array('id'=>$v['a_id']))->data(array('status'=>'4'))->save();
                    
                    //明细
                    $datas=array();
                    $datas['uid']=$v['uid'];
                    $datas['account']=$v['account'];
                    $datas['username']=$v['username'];
                    $datas['type']=$v['type'];
                    $datas['content']=$v['content'];
                    $datas['datetime']=time();
                    
                    $money_detail=json_encode(
                        array(
                            'old_money' => sprintf('%.2f', $money),
                            'money' => sprintf('%.2f', '+'.$v['money']),
                            'new_money' => sprintf('%.2f', $money + $v['money']),
                        )
                    );
                    
                    $datas['money']=$money_detail;
                    $member_details->data($datas)->add();   
                }
            }   
        }
        
    }
    
    
    
    
    
    //7天未成功排单清空所有收益
    public function SevenDayClear(){

        $where=array();
        $where['activate']=array('gt',0);
        $time=7*60*60*24;
        $user=M('user');
        $info=$user->field('userid,username,account,unix_timestamp(now())-order_time as limit_time')->where($where)->having('limit_time > '.$time)->select();

        $store=M('store');
        $money_freeze=M('money_freeze');
        $money_detail=M('money_detail');
        //清空所有收益
        $data=array();
        $data['common_num']='0';
        $data['income_num']='0';
        $data['recommen_num']='0';
        $data['wealth_num']='0';
        foreach ($variable as $key => $value) {

            $userid=$value['userid'];
            $data['uid']=$userid;
            $res=$store->where(array('uid'=>$userid))->data($data)->save();
            //清除冻结的金额
            $money_freeze->where(array('uid'=>$userid,'isok'=>'0'))->delete();

            if($res){
                //明细
                $datas=array();
                $datas['uid']=$userid;
                $datas['account']=$value['account'];
                $datas['username']=$value['username'];
                $datas['type']='clear';
                $datas['content']='7天未成功排单清空所有收益';
                $datas['datetime']=time();
                $money=json_encode(
                    array(
                        'old_money' => sprintf('%.2f', '0'),
                        'money' => sprintf('%.2f', '0'),
                        'new_money' => sprintf('%.2f', '0'),
                    )
                );
                $datas['money']=$money;
                $money_detail->data($datas)->add();
            }
        }
          
    }
    
    
    //超时封号
    public function not_login($username){
        
        $user=M('user');
        $money_detail=M('money_detail');

        //===============7天未激活帐号将封号=======S===========
        $where=array();
        $where['activate']=0;
        $where['status']=1;
        $time=7*60*60*24;
        $member_list=$user->field('userid,username,account,unix_timestamp(now())-reg_date as limit_time')->where($where)->having('limit_time > '.$time)->select();
        foreach($member_list as $k=>$v){
            $user->where(array('userid'=>$v['userid']))->data(array('status'=>'0'))->save();
            //明细
            $datas=array();
            $datas['uid']=$v['userid'];
            $datas['account']=$v['account'];
            $datas['username']=$v['username'];
            $datas['type']='not_login';
            $datas['info']='7天未激活帐号封号';
            $datas['datetime']=time();
            $money=json_encode(
                array(
                    'old_money' => sprintf('%.2f', '0'),
                    'money' => sprintf('%.2f', '0'),
                    'new_money' => sprintf('%.2f', '0'),
                )
            );
            
            $datas['money']=$money;
            $money_detail->data($datas)->add();
                
        }
        //=========7天未激活帐号将封号======E=======


        //=========15天未排单封号=======S===========
        $where=array();
        $where['activate']=array('gt',0);
        $where['status']=1;
        $time=15*60*60*24;
        $user_list=$user->field('userid,username,account,unix_timestamp(now())-order_time as limit_time')->where($where)->having('limit_time > '.$time)->select();

        foreach ($user_list as $k => $val) {
           $user->where(array('userid'=>$v['userid']))->data(array('status'=>'0'))->save();
            //明细
            $datas=array();
            $datas['uid']=$v['id'];
            $datas['username']=$v['username'];
            $datas['account']=$v['account'];
            $datas['type']='not_login';
            $datas['info']='15天未排单封号';
            $datas['datetime']=time();
            $money=json_encode(
                array(
                    'old_money' => sprintf('%.2f', '0'),
                    'money' => sprintf('%.2f', '0'),
                    'new_money' => sprintf('%.2f', '0'),
                )
            );
            $datas['money']=$money;
            $money_detail->data($datas)->add();
        }
        //=========15天未排单封号=======E===========
        
    }


    //24小时未打款甩单给上级
    public function push_top_parent(){
    
        $fp = fopen("data/lock_push_play_order.txt", "w+");  
        if(!flock($fp,LOCK_EX | LOCK_NB)){  
            //防止处理异常
            //'系统繁忙，请稍后再试';
        }else{
            
            $order_model=M('order');
            $order_play_model=M('orderbuy');
            $usermodel=M('user');
            $order_receipt=M('orderreceip');
            $money_detail_table=M('money_detail');
            
            //24小时后甩单给上级
            $sqlmap=array();
            $sqlmap['status']='1';//匹配成功
            $result=$order_play_model->where($sqlmap)->order('id asc')->select();
            $order=$result;

            foreach($order as $k=>$v){

                $start_time=(time()-$v['match_datetime']);
                //24小时下级没打款
                if(empty($v['is_push'])){
                    
                    $end_time=(24*60*60);
                    if($end_time<=$start_time){
                
                        $order_count=$order_model->where(array('a_id'=>$v['id']))->count(1); 
                        $order_status_count=$order_model->where(array('a_id'=>$v['id'],'status'=>'0'))->count(1); 
                        
                        //取父级用户信息
                        $pid=$usermodel->where(array('userid'=>$v['uid']))->getField('pid');
                        $user=$usermodel->field('userid,username,account,pid')->where(array('userid'=>$pid))->find();
                        
                        
                        if($order_count==$order_status_count){  //说明整个订单都没有打款，整个订单甩给上级
                            
                            if($user){
                                $data=array();
                                $data['a_uid']=$user['id'];
                            
                                $data['a_username']=$user['username'];
                                $data['a_account']=$user['account'];
                                
                                $top_top_user=$usermodel->field('id,username,account,pid,mobile')->where(array('userid'=>$user['pid']))->find();
                                
                                if(!$top_top_user){
                                    $top_top_user['mobile']='';
                                    $top_top_user['username']='';
                                }
                                
                                $data['a_push_mobile']=$top_top_user['mobile'];
                                $data['a_push_username']=$top_top_user['username'];
                                
                                $data['datetime']=time();
                                $data['is_push']=1;
                                
                                $order_model->where(array('a_id'=>$v['id']))->data($data)->save();
                                
                                //修改order_play打款人
                                $data=array();
                                $data['uid']=$user['userid'];
                                $data['username']=$user['username'];
                                $data['account']=$user['account'];
                                $data['is_push']='1';
                                $data['match_datetime']=time();
                                $order_play_model->where(array('id'=>$v['id']))->data($data)->save();
                                
                            }
                            
                            
                        }else{
                            
                            //部分打款，进行拆分
                            $order_status_list=$order_model->where(array('a_id'=>$v['id'],'status'=>'0'))->select(); //没有打款数据
                            
                            $count_money=0;
                            foreach($order_status_list as $key=>$val){
                                
                                $data=array();
                                $data['uid']=$user['userid'];
                                $data['username']=$user['username'];
                                $data['account']=$user['account'];
                                $data['money']=$val['money'];
                                $data['pay_type']='1,2,3';
                                $data['status']='1';
                                $data['is_push']='1';
                                $data['datetime']=$v['datetime'];
                                $data['match_datetime']=time();
                                
                                $a_insert=$order_play_model->data($data)->add();
                                
                                //修改a_id字段
                                
                                $data=array();
                                $data['a_id']=$a_insert;
                                $data['a_uid']=$user['userid'];
                                $data['a_username']=$user['username'];
                                $data['a_account']=$user['account'];
                                
                                $top_top_user=$usermodel->field('userid,username,account,pid,mobile')->where(array('userid'=>$user['pid']))->find();
                                
                                if(!$top_top_user){
                                    $top_top_user['mobile']='';
                                    $top_top_user['username']='';
                                }
                                
                                $data['a_push_mobile']=$top_top_user['mobile'];
                                $data['a_push_username']=$top_top_user['username'];
                                
                                $data['datetime']=time();
                                $data['is_push']=1;
                                
                                $order_model->where(array('id'=>$val['id']))->data($data)->save();
                                //累计未打款金额
                                $count_money+=$val['money'];
                            }
                            
                            
                            //修改order_play全部打米状态和价格
                            $order_play_model->where(array('id'=>$v['id']))->data(array('status'=>'2'))->save();
                            $order_play_model->where(array('id'=>$v['id']))->setDec('money',$count_money);
                            
                        }
                        
                        
                        //封号
                        $usermodel->where(array('userid'=>$v['uid']))->data(array('status'=>'0'))->save();
                        
                        $data=array();
                        $data['uid']=$v['uid'];
                        $data['account']=$v['account'];
                        $data['username']=$v['username'];
                        $data['type']='not_login';
                        $data['content']='超过24小时未打款封号';
                        $data['datetime']=time();
                        
                        $money_detail=json_encode(
                            array(
                                'money' => sprintf('%.2f', '0'),
                            )
                        );
                        
                        $data['money']=$money_detail;
                        $money_detail_table->data($data)->add();
                    }
                
                }else{

                    //上级没打款
                    $end_time=(24*60*60);
                    if($end_time<=$start_time){
                        
                        $order_r=$order_model->where(array('a_id'=>$v['id']))->field('r_id,r_uid,r_account,r_username')->find(); 
                        $order_r_count=$order_model->where(array('r_id'=>$order_r['r_id']))->count(1); 
                        
                        $order_status_count=$order_model->where(array('r_id'=>$order_r['r_id'],'status'=>'0'))->count(1); 
                        
                        if($order_r_count > 1){ //收款一对多

                            if($order_r_count==$order_status_count){  //N个订单所有上级都没有打款
                                
                                $r_in_id=$order_model->where(array('a_id'=>$v['id']))->getField('r_id',true); 
                                $order_play_model->where(array('id'=>$v['id']))->delete();
                                $order_model->where(array('a_id'=>$v['id']))->delete();
                            
                                //重置提现状态
                                if($r_in_id){
                                   $order_receipt->where(array('id'=>array('in',$r_in_id)))->data(array('status'=>'0'))->save();
                                }
                                
                            }else{
                                
                                $order_receipt->where(array('id'=>$order_r['r_id']))->setDec('money',$v['money']);
                                //获取提现类型
                                $re_row=$order_receipt->where(array('id'=>$order_r['r_id']))->field('id,pay_type')->find();
                                
                                //自动提现
                                $data=array();
                                $data['uid']=$order_r['r_uid'];
                                $data['account']=$order_r['r_account'];
                                $data['username']=$order_r['r_username'];
                                $data['pay_type']=(int)$re_row['pay_type'];
                                $data['receipt_type']='1,2,3';
                                $data['money']=trim($v['money']);
                                $data['datetime']=time();
                                
                                $order_receipt->data($data)->add();
                                
                                //自动提现明细
                                $data=array();
                                $data['uid']=$order_r['r_uid'];
                                $data['account']=$order_r['r_account'];
                                $data['username']=$order_r['r_username'];
                                $data['type']='money';
                                $data['content']='自动提现'.$v['money'];
                                $data['datetime']=time();
                                
                                $money_detail=json_encode(
                                    array(
                                        'money' => sprintf('%.2f', $v['money']),
                                    )
                                );
                                
                                $data['money']=$money_detail;
                                
                                $money_detail_table->data($data)->add();
                                
                                
                                
                                
                                //明细
                                $data=array();
                                $data['uid']=$order_r['r_uid'];
                                $data['account']=$order_r['r_account'];
                                $data['username']=$order_r['r_username'];
                                $data['type']='push';
                                $data['content']='上级没打款自动提现'.$v['money'];
                                $data['datetime']=time();
                                
                                $money_detail=json_encode(
                                    array(
                                        'money' => sprintf('%.2f', $v['money']),
                                    )
                                );
                                
                                $data['money']=$money_detail;
                                
                                $money_detail_table->data($data)->add();
                                
                                
                                //删除此打款和匹配数据
                                $order_play_model->where(array('id'=>$v['id']))->delete();
                                $order_model->where(array('a_id'=>$v['id']))->delete();
                                
                                //提现状态
                                $order_r_count=$order_model->where(array('r_id'=>$order_r['r_id']))->count(1); 
                                
                                $order_status_count=$order_model->where(array('r_id'=>$order_r['r_id'],'status'=>2))->count(1); 
                                
                                if($order_status_count==$order_r_count){
                                    
                                    $order_receipt->where(array('id'=>$order_r['r_id']))->data(array('status'=>3))->save();
                                    
                                }
                            
                            }
                            
                        }else{
                            
                            $r_in_id=$order_model->where(array('a_id'=>$v['id']))->getField('r_id',true); 
                            $order_play_model->where(array('id'=>$v['id']))->delete();
                            $order_model->where(array('a_id'=>$v['id']))->delete();
                            //重置提现状态
                        
                            if($r_in_id){
                                $order_receipt->where(array('id'=>array('in',$r_in_id)))->data(array('status'=>'0'))->save();
                            }
                        }
                        
                        
                        //封号
                        $usermodel->where(array('userid'=>$v['uid']))->data(array('status'=>'0'))->save();
                        
                        $data=array();
                        $data['uid']=$v['uid'];
                        $data['account']=$v['account'];
                        $data['username']=$v['username'];
                        $data['type']='not_login';
                        $data['content']='下级甩单超过24小时未打款封号';
                        $data['datetime']=time();
                        
                        $money_detail=json_encode(
                            array(
                                'money' => sprintf('%.2f', '0'),
                            )
                        );
                        
                        $data['money']=$money_detail;
                        $money_detail_table->data($data)->add();
                        
                    }
                    
                }
            
            }
            
        }
        
        fclose($fp);
    }
    
}
