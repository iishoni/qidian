<?php
namespace Admin\Controller;

use Think\Page;

/**
 * 用户控制器
 * 
 */
class UserController extends AdminController
{


    /**
     * 用户列表
     * 
     */
    public function index()
    {
        // 搜索
        $keyword                                  = I('keyword', '', 'string');
        $condition                                = array('like', '%' . $keyword . '%');
        $map['a.userid|a.username|a.account'] = array(
            $condition,
            $condition,
            $condition,
            '_multi' => true,
        );

        // 获取所有用户
        $map['a.status'] = array('egt', '0'); // 禁用和正常状态
        $user   = M('user a');
        //分页
        $p=getpage($user,$map,10);
        $page=$p->show();  

        $data_list     = $user
            ->join('ysk_store b on a.userid=b.uid')
            ->field('a.userid,a.username,a.account,a.mobile,a.reg_date,a.status,a.activate,a.pid,b.common_num,b.income_num,b.recommen_num')
            ->where($map)
            ->order('a.userid desc')
            ->select();
       

        $this->assign('list',$data_list);
        $this->assign('table_data_page',$page);
        $this->display();
    }

    /**
     * 新增用户
     * 
     */
    // public function add()
    // {
    //     if (IS_POST) {

    //         $user_object = D('Manage');
    //         $data        = $user_object->create();
    //         if ($data) {
    //             $id = $user_object->add($data);
    //             if ($id) {
    //                 $this->success('新增成功', U('index'));
    //             } else {
    //                 $this->error('新增失败');
    //             }
    //         } else {
    //             $this->error($user_object->getError());
    //         }
    //     } else {
    //             $role=D('Group')->field('id,title')->order('id')->select();
    //             $this->assign('role',$role);
    //             $this->display('edit');
    //     }
    // }

    /**
     * 编辑用户
     * 
     */
    public function edit($id=null)
    {
        if (IS_POST) {
            if(empty($_POST['login_pwd'])){
                unset($_POST['relogin_pwd']);
            }
            if(empty($_POST['safety_pwd'])){
                unset($_POST['resafety_pwd']);
            }
            // 提交数据
            $user_object = D('User');
            $data        = $user_object->create();
            //如果没有密码，去掉密码字段
            if(empty($data['login_pwd']) || trim($data['login_pwd'])==''){
                unset($data['login_pwd']);
            }
            else{
               $salt=user_salt();
               $data['login_pwd']=$user_object->pwdMd5($data['login_pwd'],$salt);
               $data['login_salt']=$salt;
            }
            if(empty($data['safety_pwd']) || trim($data['safety_pwd'])==''){
                unset($data['safety_pwd']);
            }
            else{
               $salt=user_salt();
               $data['safety_pwd']=$user_object->pwdMd5($data['safety_pwd'],$salt);
               $data['safety_salt']=$salt;
            }
            if ($data) {
                $result = $user_object
                    ->save($data);
                if ($result) {
                    $this->success('更新成功', U('index'));
                } else {
                    $this->error('更新失败', $user_object->getError());
                }
            } else {
                $this->error($user_object->getError());
            }
        } else {

            // 获取账号信息
            $info = D('User')->find($id);
            unset($info['password']);
            $parent=D('User')->where(array('userid'=>$info['pid']))->getField('account');
            $info['parent']=$parent ? $parent :'无';


            $this->assign('info',$info);
            $this->display();
        }
    }

    /**
     * 设置一条或者多条数据的状态
     * 
     */
    public function setStatus($model = CONTROLLER_NAME)
    {
        $ids = I('request.ids');
        $status = I('request.status');
        switch ($status) {
            case 'forbid': // 禁用条目
                parent::setStatus($model);
                break;
            case 'resume': // 启用条目
                $user=M('user');
                $data = array('status' => 1);
                if (is_array($ids)) {
                    //修改时间
                    $where['userid']=array('in',$ids);
                }else{
                    $where['userid']=$ids;
                }

                $u_info=$user->where($where)->field('userid,activate')->select();
                $time=time();
                foreach ($u_info as $k => $val) {
                    if($val['activate']==0)
                        $data['reg_date']=$time;
                    $data['order_time']=$time;
                    $data['paidang_time']=$time;
                    $data['userid']=$val['userid'];
                    $user->save($data);
                }
                
                $this->success('解锁成功');
                break;
        }
    }




     /**
     * 编辑用户
     * 
     */
    public function AddFruits($id=null)
    {
        if (IS_POST) {
              
           $dbst=M('store');
           $data=I('post.');
           $res=$dbst->save($data);
           if ($res) 
                $this->success('操作成功');
           else 
                $this->error('操作失败');
                


        } else {

            // 获取账号信息
            $info = D('User')->field('userid,username,account')->find($id);
            $store=D('store')->field('uid,common_num,income_num,recommen_num')->where(array('uid'=>$info['userid']))->find();
            $u_info=array_merge($info,$store);

            $this->assign('info',$u_info);
            $this->display();
        }
    }


    //用户登录
    public function userlogin(){
        $userid=I('userid',0,'intval');
        $user=D('Home/User');
        $info=$user->find($userid);
        if(empty($info)){
            return false;
        }

        $login_id=$user->auto_login($info);
        if($login_id){
            session('in_time',time());
            session('login_from_admin','admin',10800);
            $this->redirect('Home/UserCenter/index');
        }
    }
}
