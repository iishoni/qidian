<?php
namespace Admin\Controller;

use Think\Page;

/**
 * 回收控制器
 * 
 */
class MatchController extends AdminController
{


    //匹配
    public function match(){

        $OrderBuy   = D('OrderBuy');
        $OrderReceipt  = D('OrderReceipt');
        if(IS_POST){

            $post=I('post.');
            if (empty($post['id'])) {
                return false;
            }
            if (empty($post['ids'])) {
                $this->error('请选择要操作的数据');
            }
            $type=$post['type'];
            if($type=='play'){

                $result=$OrderBuy->match($post);
                if(!$result){
                    $this->error($OrderBuy->getError());
                }
                $this->success('匹配成功',U('Help/GiveHelp'));
                
            }
            else{
                
                $result=$OrderReceipt->match($post);
                if(!$result){
                    $this->error($OrderReceipt->getError());
                }
                $this->success('匹配成功',U('Help/ReceiptHelp'));
            }
        }else{
            
            $sqlmap=array();
             // 搜索
            $keyword                                  = I('keyword', '', 'string');
            if($keyword!=''){
                $condition                                = array('like', '%' . $keyword . '%');
                $sqlmap['username|account|money'] = $condition;
            }
             //按日期搜索
            $res=date_query('datetime');
            if($res)
                $sqlmap=$res;


            $id=I('id',0,'intval');
            $sqlmap['status']=0;
            //提供帮助
            $type=I('type');
            if($type=='play'){
                
                if(!$info=$OrderBuy->find($id)){
                     $this->error($OrderBuy->getError());
                }
                //分页
                $p=getpage($OrderReceipt,$sqlmap,10);
                $result=$OrderReceipt->where($sqlmap)->order('id asc')->select();
            }else{ //接受帮助
                if(!$info=$OrderReceipt->find($id)){
                     $this->error($OrderBuy->getError());
                }
            
                //分页
                $p=getpage($OrderBuy,$sqlmap,10);
                $result=$OrderBuy->where($sqlmap)->order('id asc')->select();
            }
                    
          
            $page=$p->show();  
            $this->assign('list',$result);
            $this->assign('info',$info);
            $this->assign('table_data_page',$page);
            $this->display('Help/match');
            
        }
        
    }
}
