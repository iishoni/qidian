<?php
namespace Admin\Controller;

use Think\Page;

/**
 * 用户控制器
 * 
 */
class TraingController extends AdminController
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
            $map['account|username'] = array(
                $condition,
                $condition,
                '_multi' => true,
            );
        }

        $type=I('type');
        if($type!=''){
           $map['type'] = $type;  
        }
        
        #++++转账明细++++++
        $table=M('money_detail');
         //分页
        $p=getpage($table,$map,10);
        $page=$p->show();  

        $data_list     = $table
            ->where($map)
            ->order('id desc')
            ->select();

        $this->assign('list',$data_list);
        $this->assign('table_data_page',$page);
        $this->display();
    }

    /**
     * 用户列表money_detail
     * 
     */
    public function freezedetail()
    {
         // 搜索
        $keyword                                  = I('keyword', '', 'string');
        if($keyword){
            $condition                                = array('like', '%' . $keyword . '%');
            $map['account|username'] = array(
                $condition,
                $condition,
                '_multi' => true,
            );
        }
        $isok=I('isok');
        if($isok!=''){
           $map['isok'] = $isok;  
        }

        $type=I('type');
        if($type!=''){
           $map['type'] = $type;  
        }
        
        #++++转账明细++++++
        $table=M('money_freeze');
         //分页
        $p=getpage($table,$map,10);
        $page=$p->show();  

        $data_list     = $table
            ->where($map)
            ->order('id desc')
            ->select();

        $this->assign('list',$data_list);
        $this->assign('table_data_page',$page);
        $this->display();
    }



    public function turntable(){
        // 搜索
        $keyword                                  = I('keyword', '', 'string');
        if($keyword){
            $condition                                = array('like', '%' . $keyword . '%');
            $map['bill_name|bill_username|bill_account'] = array(
                $condition,
                $condition,
                $condition,
                $condition,
                '_multi' => true,
            );
        }
        
        $map['bill_type']=1;
        #++++转账明细++++++
        $table=M('nzbill');
         //分页
        $p=getpage($table,$map,10);
        $page=$p->show();  

        $data_list     = $table
            ->where($map)
            ->order('bill_id desc')
            ->select();

        $this->assign('list',$data_list);
        $this->assign('table_data_page',$page);
        $this->display();
    }

   
}
