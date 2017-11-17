<?php
namespace Home\Controller;
use Think\Controller;
class NewsController extends CommonController {
    /**
     * 直推奖励 
     */
    public function index(){
        $news=M('news');
        $info=$news->where('status=1')->order('id desc')->limit(10)->select();
        // $this->readuser($news,$info);
        $this->assign('news',$info);
        $this->display();
    }

    //设置为已读
    private function readuser($news,$arr){
        $m=M();
        $uid=get_userid();
        $where['uid_str']=array('like','%,'.$uid.',%');
        $where['status']=1;
        foreach ($arr as $key => $val) {
            $where['id']=$val['id'];
            $count=$news->where($where)->count(1);
            if($count==0){
                $map['id']=$val['id'];
                $uid_str=$news->where($map)->getField('uid_str');
                $str=$uid_str.$uid.','; 
                $news->where($map)->setField('uid_str',$str);             
            }
        }

    }

    public function NewsDetail(){
        $id=I('id',0,'intval');
        if($id){
            $info=M('news')->find($id);
            $this->assign('info',$info);
        }
        $this->display();
    }
    
}
