<?php
namespace Admin\Controller;

use Think\Page;
/**
 * 用户控制器
 * 陶
 */
class TreeController extends AdminController
{


    // /**
    //  * 用户列表
    //  * @author jry <598821125@qq.com>
    //  */
    // public function index()
    // {   
    //      // 搜索
    //     $pid        =   I('keyword', '0', 'string');

    //     $user           =   M('user');
    //     if($pid!='0')
    //     {
    //         $k_where['userid|username|account'] = array(
    //             $pid,
    //             $pid,
    //             $pid,
    //             '_multi' => true,
    //         );
    //         $query=$user->where($k_where)->Field('userid,pid')->find();
    //         $pid=$query['pid'];
    //         $userid=$query['userid'];
            
    //         if($pid>0){
    //             $condition      =   array('LIKE', '%-' . $userid . '-%');
    //             $map['path']    =   $condition;
    //             $map['userid']  =   $userid;
    //             $map['_logic']  = 'OR';
    //         }
    //         $info = $user->where($map)->field('userid,account,pid,deep,mobile,username')->order('userid')->select();
    //     }else{
    //        $info = $user->where($map)->field('userid,account,pid,deep,mobile,username')->order('userid')->limit(1000)->select(); 
    //     }

       
    
    //     $tree           =   $this->getTree($info,$pid,$user);
    //     $this->assign('tree',$tree);

    //     $this->display();
    // }

   
   


    // /**
    //  * [getTree  会员树形结构 陶]
    //  * @param  [type] $data [description]
    //  * @param  [type] $pid  [description]
    //  * @param  [type] $user [description]
    //  * @return [type]       [description]
    //  */
    // public  function getTree($data, $pid, $user)
    // {
    //     $html = '';
    //     foreach($data as $k => $v)
    //     {
    //         $map['pid']=$v['userid'];
    //         $count=$user->where($map)->count(1);
    //         $class=$count==0 ? 'fa-user':'fa-sitemap';

    //        if($v['pid'] == $pid)
    //        {         //父亲找到儿子
    //         $html .= '<li style="display:none" >';
    //         $html .= '<span><i class="icon-plus-sign '.$class.' blue "></i>';
    //         $html .= $v['username'];
    //         $html .= '</span> <a href="'.U('User/edit',array('id'=>$v['userid'])).'">';
    //         $html .= $v['account'];
    //         $html .= '</a>';
    //         $html .= $this->getTree($data, $v['userid'],$user);
    //         $html = $html."</li>";
    //        }
    //     }
    //     return $html ? '<ul>'.$html.'</ul>' : $html ;
    // }
    

     /**
     * 用户列表
     * @author jry <598821125@qq.com>
     */
    public function index()
    {   
         // 搜索
        $pid        =   I('keyword', '0', 'string');
        $user           =   M('user');
        if($pid!='0')
        {
            $k_where['userid|username|account'] = array(
                $pid,
                $pid,
                $pid,
                '_multi' => true,
            );
            $query=$user->where($k_where)->Field('userid,pid')->find();
            $pid=$query['pid'];
        }
       
        $tree           =   $this->getTree($pid);
        $this->assign('tree',$tree);

        $this->display();
    }


    public  function getTree($pid='0')
    {
         $t=M('user');
        
        $list=$t->where(array('pid'=>$pid))->order('userid asc')->select();

        if(is_array($list)){
            $html = '';
            foreach($list as $k => $v)
            {
                $map['pid']=$v['userid'];
                $count=$t->where($map)->count(1);
                $class=$count==0 ? 'fa-user':'fa-sitemap';

               if($v['pid'] == $pid)
               {   
                    //父亲找到儿子
                    $html .= '<li style="display:none" >';
                    $html .= '<span><i class="icon-plus-sign '.$class.' blue "></i>';
                    $html .= $v['username'];
                    $html .= '</span> <a href="'.U('User/edit',array('id'=>$v['userid'])).'">';
                    $html .= $v['account'];
                    $html .= '</a>';
                    $html .= $this->getTree($v['userid']);
                    $html = $html."</li>";
               }
            }
            return $html ? '<ul>'.$html.'</ul>' : $html ;
        }
    }
    
}
