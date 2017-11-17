<?php
namespace Admin\Controller;

use Think\Page;

/**
 * 回收控制器
 * 
 */
class OrderController extends AdminController
{

    //提供帮助
    public function index()
    {

        // 搜索
        $keyword                                  = I('keyword', '', 'string');
        if($keyword!=''){
            $condition                                = array('like', '%' . $keyword . '%');
            $map['a_account|a_username|r_account|r_username'] = $condition;
        }

         //按日期搜索
        // $res=date_query('play_datetime');
        // if($res)
        //     $map=$res;

        
        
        $status=I('status');
        if($status){
           $map['status'] = $status;  
        }

        $type=I('type');
        if(empty($status)){
            if($type=='over'){
               $map['status'] = array('egt', 2);  
            }else{
               $map['status'] = array('lt', 2);  
            }
        }
        
        
        $table   = D('Order');
        //分页
        $p=getpage($table,$map,10);
        $page=$p->show();  

        $data_list     = $table
            ->where($map)
            ->order('id desc')
            ->select();
       // dump($table->_sql());die;
        $this->assign('list',$data_list);
        $this->assign('table_data_page',$page);
        $this->display();
    }

   

}
