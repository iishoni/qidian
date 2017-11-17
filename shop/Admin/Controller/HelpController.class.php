<?php
namespace Admin\Controller;

use Think\Page;

/**
 * 回收控制器
 * 
 */
class HelpController extends AdminController
{

    //提供帮助
    public function GiveHelp()
    {

        // 搜索
        $keyword                                  = I('keyword', '', 'string');
        if($keyword!=''){
            $condition                                = array('like', '%' . $keyword . '%');
            $map['username|account'] = $condition;
        }

        //按日期搜索
        $res=date_query('datetime');
        if($res)
            $map=$res;
        // 获取所有用户
        $map['status'] = array('eq', '0'); 
        $table   = D('OrderBuy');
        //分页
        $p=getpage($table,$map,10);
        $page=$p->show();  

        $data_list     = $table
            ->where($map)
            ->order('id asc')
            ->select();

        $this->assign('list',$data_list);
        $this->assign('table_data_page',$page);
        $this->display();
    }

    //接受帮助
    public function ReceiptHelp(){
       // 搜索
        $keyword                                  = I('keyword', '', 'string');
        if($keyword!=''){
            $condition                                = array('like', '%' . $keyword . '%');
            $map['username|account'] = $condition;
        }
        
         //按日期搜索
        $res=date_query('datetime');
        if($res)
            $map=$res;

        // 获取所有用户
        $map['status'] = array('eq', '0'); 
        $table   = D('OrderReceipt');
        //分页
        $p=getpage($table,$map,10);
        $page=$p->show();  

        $data_list     = $table
            ->where($map)
            ->order('id asc')
            ->select();
       
        $this->assign('list',$data_list);
        $this->assign('table_data_page',$page);
        $this->display();
    }

      /**
     * 设置一条或者多条数据的状态
     * 
     */
    public function setStatus()
    {
        $model=I('request.model');
        $ids = I('request.ids');
        if(empty($ids)){
            $this->error('请选择要操作的数据');
        }
        if(is_array($ids)){
            $where['id']=array('in',$ids);
        }
        else{
            $where['id']=$ids;
        }

        $res=D($model)->where($where)->delete();

        if($res)
            $this->success('删除成功');
        else
            $this->error('删除失败');

    }

}
