<?php
namespace Admin\Controller;

use Think\Page;

/**
 * 用户控制器
 * 
 */
class FruitsDetailController extends AdminController
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
        $admin_kucun=M('admin_kucun')->find();
        #+++++平台总数据+++++
        
        $this->assign('admin_kucun',$admin_kucun);   
        $this->assign('list',$data_list);
        $this->assign('table_data_page',$page);
        $this->display();
    }


   
}
