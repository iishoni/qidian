<?php
namespace Admin\Controller;

use Think\Page;

/**
 * 排单码
 * 
 */
class ActivateNumController extends AdminController
{


    /**
     * 用户列表
     * 
     */
    public function index()
    {

        // 搜索
        $keyword                                  = I('keyword', '', 'string');
        if($keyword){
            $condition                                = array('like', '%' . $keyword . '%');
            $map['manage_name|username|account'] = array(
                $condition,
                $condition,
                $condition,
                '_multi' => true,
            );
        }
        
        $map['type']='activate_num';
        #++++转账明细++++++
        $adminzgz=M('admin_zhuangz');
         //分页
        $p=getpage($adminzgz,$map,10);
        $page=$p->show();  

        $data_list     = $adminzgz
            ->where($map)
            ->order('id desc')
            ->select();

        #+++++平台总数据+++++
        $total=$adminzgz->where(array('type'=>'activate_num'))->sum('num');
        #+++++平台总数据+++++
        
        $this->assign('total',$total);   
        $this->assign('list',$data_list);
        $this->assign('table_data_page',$page);
        $this->display();
    }

    /**
     * 新增用户
     * 
     */
    public function add()
    {
        if (IS_POST) {
              
           $account=I('post.account');
           $num=I('post.num');
           if(empty($account)){
                $this->error('用户账号不能为空');
           }
           if(empty($num)){
                $this->error('数量不能为空');
           }
           if(!preg_match('/^[1-9]\d*$/',$num)){
               $this->error('请输入整数');
           }


           $dbst=M('store');
           $dbazg=M('admin_zhuangz'); // 播发给用户记录表
           $u_info=D('User')->where(array('account'=>$account))->field('account,username,userid')->find();
           if(empty($u_info)){
                 $this->error('账号错误或用户不存在');
           }

           $uid=$u_info['userid'];
           $before_cangku_num=$dbst->where('uid='.$uid)->getField('activate_num');
           $up=$dbst->where('uid='.$uid)->setInc('activate_num',$num);
          
           //把数据记录到金猫崽流水明细
             $m_info=session('user_auth');
             $manage_id=$m_info['uid'];
             $data['manage_id']=$manage_id;//管理者ID
             $data['manage_name']=$m_info['username'];
             $data['uid']=$uid; //用户ID
             $data['num']=$num; //转账数量
             $data['create_time']=time();
             $data['before_num']=$before_cangku_num; //转账前仓库数量
             $data['after_num']=$before_cangku_num+$num; //转账后仓库数量
             $data['ip']=get_client_ip();
             $data['type']='activate_num';
             $data['content']=I('content');
             $data['username']=$u_info['username'];
             $data['account']=$u_info['account'];
             $res=$dbazg->data($data)->add();
           
            if ($res) 
                $this->success('操作成功');
            else
                $this->error('操作失败');
                 
            
        } else {
                $this->display();
        }
    }

   
}
