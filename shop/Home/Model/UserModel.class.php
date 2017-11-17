<?php
namespace Home\Model;

use Common\Model\ModelModel;

/**
 * 用户模型
 *
 */
class UserModel extends ModelModel
{
    protected $tableName = 'user';

    /**
     * 自动验证规则
     *
     */
    protected $_validate = array(
        // self::EXISTS_VALIDATE 或者0 存在字段就验证（默认）
        // self::MUST_VALIDATE 或者1 必须验证
        // self::VALUE_VALIDATE或者2 值不为空的时候验证

        //验证手机号码
        array('mobile', 'require', '手机号码不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_INSERT),
        array('mobile', '/^1\d{10}$/', '手机号码格式不正确', self::MUST_VALIDATE, 'regex', self::MODEL_INSERT),
        array('mobile', '', '手机号被占用', self::MUST_VALIDATE, 'unique', self::MODEL_INSERT),
        //验证用户名
        // array('account', '', '账号已被使用', self::MUST_VALIDATE, 'unique', self::MODEL_BOTH),
        // array('account', '/^(?!_)(?!\d)(?!.*?_$)[\w]+$/', '用户名只可含有数字、字母、下划线且不以下划线开头结尾，不以数字开头！', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('username', 'require', '姓名不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_INSERT),


        //验证一级密码
        array('login_pwd', 'require', '密码不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_INSERT),
        array('login_pwdrep', 'require', '确认密码不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_INSERT),
        array('safety_pwd', '6,20', '二级密码长度为6-20位', self::MUST_VALIDATE, 'length', self::MODEL_INSERT),
        array('safety_pwdrep', 'safety_pwd', '两次输入的二级密码不一致', self::MUST_VALIDATE, 'confirm', self::MODEL_INSERT),
        // array('email', 'require', '邮箱不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_INSERT),
        // array('email', '/^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/', '邮箱格式不正确', self::EXISTS_VALIDATE, 'regex', self::MODEL_INSERT),
        // array('email', '', '该邮箱已被使用', self::MUST_VALIDATE, 'unique', self::MODEL_INSERT),
        array('pid', 'require', '推荐人手机号不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_INSERT),
        array('pid', 'checkParent', '推荐人不存在', self::MUST_VALIDATE, 'callback', self::MODEL_INSERT),

        array('agree', 'require', '选择同意用户注册协议', self::MUST_VALIDATE, 'regex', self::MODEL_INSERT),

    );

     /**
     * 自动完成规则
     *
     */
    protected $_auto = array(
        array('reg_ip', 'get_client_ip', self::MODEL_INSERT, 'function'),
        array('pid', 'getParent', self::MODEL_BOTH, 'callback'),
        array('order_time', 'time', self::MODEL_INSERT, 'function'),
        array('reg_date', 'time', self::MODEL_INSERT, 'function'),
        array('status', '1', self::MODEL_INSERT),
        array('activate', '0', self::MODEL_INSERT),
        array('paidang_time', 'time', self::MODEL_INSERT, 'function'),
    );

    /**
     * [getdeep 层级]
     * @return [type] [description]
     */
    public function getdeep($userid){
        $where['userid']=$userid;
        $deep=$this->where($where)->getField('deep');
        return $deep+1;
    }

    protected function checkParent($value){
        $where['mobile']=$value;
        $count=$this->where($where)->count(1);
        if($count>0)
            return true;
        else
            return false;
    }

    protected function getParent($value){
        $where['mobile']=$value;
        return $this->where($where)->getField('userid');
    }

    /**
     * [getpath 路径]
     * @return [type] [description]
     */
    public function getpath($userid){
        $where['userid']=$userid;
        $path=$this->where($where)->getField('path');
        if(empty($path))
            $path='-'.$userid.'-';
        else
            $path.=$userid.'-';
        return $path;
    }

    /**
     * 用户登录
     *
     */
    public function login($account, $password, $map = null)
    {
        //去除前后空格
        $account = trim($account);
        if(!isset($account) || empty($account)){
            $this->error='账号不能为空';
            return false;
        }
        if(!isset($password) || empty($password)){
            $this->error='密码不能为空';
            return false;
        }

        //匹配登录方式
        // if (preg_match("/^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/", $account)) {
        //     $map['email'] = array('eq', $account); // 邮箱登陆
        // } elseif (preg_match("/^1\d{10}$/", $account)) {
        //     $map['mobile'] = array('eq', $account); // 手机号登陆
        // } else {
            $map['account'] = array('eq', $account); // 用户名登陆
        // }
        $map['status'] = array('egt', 0);
        $user_info     = $this->where($map)->find(); //查找用户
        if (!$user_info) {
            $this->error = '账号或密码错误!';
            return false;
        }
        elseif($this->pwdMd5($password,$user_info['login_salt']) !== $user_info['login_pwd'])
        {
                $this->error = '账号或密码错误！';
                return false;
        }
        elseif ($user_info['status']==0) {
            $this->error = '您的账号已锁定，请联系管理员!';
            return false;
        }
        elseif ($user_info['activate']==0) {
            $this->error = 'activate';
            return false;
        }
        else
        {
            //操时封号操作
            R('GetMoney/not_login',array('userid'=>$user_info['userid']));

            $session_id=session_id();
            $this->where($map)->setField('session_id',$session_id);
            return $user_info;
        }

        return false;
    }



     /**
     * [pwdMd5 用户密码加密]
     *
     */
    public function pwdMd5($value,$salt){
       $pwd=user_md5($value);
       $user_pwd=md5($pwd.$salt);
       return  $user_pwd;
    }


    /**
     * 设置登录状态
     *
     */
    public function auto_login($user)
    {
        // 记录登录SESSION和COOKIES
        $auth = array(
            'userid'      => $user['userid'],
            'account' => $user['account'],
            'mobile' => $user['mobile'],
        );
        session('user_login', $auth,43200);
        session('user_login_sign', $this->data_auth_sign($auth),43200);
        return $this->user_login();
    }

    /**
     * 数据签名认证
     * @param  array  $data 被认证的数据
     * @return string       签名
     *
     */
    public function data_auth_sign($data)
    {
        // 数据类型检测
        if (!is_array($data)) {
            $data = (array) $data;
        }
        ksort($data); //排序
        $code = http_build_query($data); // url编码并生成query字符串
        $sign = sha1($code); // 生成签名
        return $sign;
    }

    /**
     * 检测用户是否登录
     * @return integer 0-未登录，大于0-当前登录用户ID
     *
     */
    public function user_login()
    {
        $user = session('user_login');
        if (empty($user)) {
            return 0;
        } else {
            if (session('user_login_sign') == $this->data_auth_sign($user)) {
                return $user['userid'];
            } else {
                return 0;
            }
        }
    }

    /**
     * [UserInfo 获取用户信息]
     * @param [type] $where [description]
     * @param [type] $field [description]
     */
    public function UserInfo($field,$where=null){
        if(empty($where))
            $where['userid']=$this->user_login();
        $info=$this->where($where)->field($field)->find();
        $pid=$info['pid'];
        if(!$field && $pid>0){
            //读取父级信息
            $p_info=$this->where('userid = '.$pid)->field('account as p_account,username as p_username,mobile as p_mobile')->find();
            if($p_info)
            $info=array_merge($info,$p_info);
        }

        return $info;
    }

    /**
     * [Password 修改密码]
     */
    public function Password($data){
        if(!empty($data['new_pwd']) || !empty($data['new_pwdt'])){

            //一级密码
            if($data['new_pwd']!='' && $data['old_pwd']==''){
                $this->error='旧密码不能为空';
                return false;
            }
            if($data['new_pwd']!='' && $data['rep_pwd']==''){
                $this->error='确认密码不能为空';
                return false;
            }
            if($data['new_pwd'] != $data['rep_pwd']){
                $this->error='两次输入的密码不一致';
                return false;
            }


            //二级密码
            if($data['new_pwdt']!='' && $data['old_pwdt']==''){
                $this->error='旧密码不能为空';
                return false;
            }
            if($data['new_pwdt']!='' && $data['rep_pwdt']==''){
                $this->error='确认密码不能为空';
                return false;
            }
            if($data['new_pwdt'] != $data['rep_pwdt']){
                $this->error='两次输入的密码不一致';
                return false;
            }

            if(!empty($data['new_pwd']) && !$this->is_password($data['new_pwd'])){
                return false;
            }
            if(!empty($data['new_pwdt']) && !$this->is_password($data['new_pwdt'])){
                return false;
            }

            //取用户信息
            $u_info=$this->UserInfo();
            //验证旧密码
            $pwd_one=$u_info['login_pwd'];
            $salt_one=$u_info['login_salt'];
            $pwd_two=$u_info['safety_pwd'];
            $salt_two=$u_info['safety_salt'];

            if(!empty($data['new_pwd']) && $pwd_one!=$this->pwdMd5($data['old_pwd'],$salt_one)){
                $this->error='旧密码输入错误';
                return false;
            }
            $userid=$this->user_login();
            $where['userid']=$userid;
            $res=false;
            if(!empty($data['new_pwd'])){
                $time=time();
                $salt=user_salt($time);
                $save['login_salt']=$salt;
                $save['login_pwd']=$this->pwdMd5($data['new_pwd'],$salt);
                $res=$this->where($where)->save($save);

            }

            if(!empty($data['new_pwdt']) && $pwd_two!=$this->pwdMd5($data['old_pwdt'],$salt_two)){
                $this->error='旧密码输入错误';
                return false;
            }
            if(!empty($data['new_pwdt'])){
                $time=time();
                $salt=user_salt($time);
                $save['safety_salt']=$salt;
                $save['safety_pwd']=$this->pwdMd5($data['new_pwdt'],$salt);
                $res=$this->where($where)->save($save);
            }
            if($res)
                return true;
            else
                $this->error='修改失败';

        }else{
            $this->error="请输入新密码";
        }
    }


    protected function is_password($value){
        $len=strlen($value);
        if($len<6 || $len>20){
            $this->error='密码长度为6-20位';
            return false;
        }
        // $meth='/(?!^(\d+|[a-zA-Z]+|[~!@#$%^&*()_+{}:"<>?\-=[\];\',.\/]+)$)^[\w~!@#$%^&*()_+{}:"<>?\-=[\];\',.\/]+$/';
        // if(!preg_match($match,$value)){
        //     $this->error='密码至少由数字、字符组成';
        //     return false;
        // }
       return true;
    }

    public function CangkuNum($field){
        $userid=$this->user_login();
        $where['uid']=$userid;
        return D('store')->field($field)->where($where)->find();
    }
    /**
     * [ChildrenNum 直推人数]
     */
    public function ChildrenNum(){
        $where['pid']=$this->user_login();
        return $this->where($where)->count(1);
    }

    /**
     * 验证二级密码是否正确
     */
    public function CheckPwd($value){
        $where['userid']=$this->user_login();
        $u_info=$this->where($where)->field('safety_pwd,safety_salt')->find();
        $salt=$u_info['safety_salt'];
        $pwd=$u_info['safety_pwd'];
        if($pwd == $this->pwdMd5($value,$salt)){
            return true;
        }else{
            return false;
        }
    }







    /**
     * [saveaccount 修改用户信息]
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public function SaveUserInfo($data){
        $activate=$this->where(array('userid'=>get_userid()))->getField('activate');
        if($activate==2){
            $this->error="个人资料不能修改";
            return false;
        }
        foreach ($data as $k => $val) {
                if(empty($val)){
                    $this->error = '请填写所有信息';
                    return false;
                }
        }

        if (!check_id_card($data['idcard'])) {
            $this->error="身份证输入错误";
            return false;
        }

        $data['activate']=2;//2-资料已完善
        $data['userid']=get_userid();
        if ($this->save($data)===false) {
            $this->error = '修改失败';
            return false;
        }
        return true;
    }



    //取用户上级，最多去8代
    public function top_tj($uid){

        $path = $tj =array();
        if($uid){
            $old_path=$this->where(array('userid'=>$uid))->getField('path');
            $str=trim($old_path,'-');//去除两端的-
            $path=explode('-',$str);
            rsort($path);//倒序
            $pid=$path[0];
            $gid=$path[1];
            //取两代
            if($pid){
                $row=$this->where(array('userid'=>$pid))->field('userid,status,activate')->find();
                if($row['status']=='1' && $row['activate']!='0'){
                    $tj[]=$row['userid'];
                }else{
                    $tj[]='';
                }
            }
            if($gid){

                $row=$this->where(array('userid'=>$gid))->field('userid,status,activate')->find();
                if($row['status']=='1' && $row['activate']!='0'){
                    $tj[]=$row['userid'];
                }else{
                    $tj[]='';
                }
            }

        }
        return $tj;

    }

    //取用户上级，
    public function AllParent($uid){

        $path = $data =array();
        if($uid){
            $old_path=$this->where(array('userid'=>$uid))->getField('path');
            $str=trim($old_path,'-');//去除两端的-
            $path=explode('-',$str);
            rsort($path);//倒序
            $count=count($path);
            if($count<6){
                foreach($path as $k=>$v){
                    if($v){
                        $row=$this->where(array('userid'=>$v))->field('userid,status,activate')->find();
                        $count=$this->where(array('pid'=>$v))->count(1);
                        if($row['status']=='1' && $row['activate']!='0' ){
                            $data[]=$v;
                        }else{
                            $data[]='';
                        }
                    }
                }
            }else{
                for($i=0;$i<6;$i++){
                    $row=$this->where(array('userid'=>$path[$i]))->field('userid,status,activate')->find();
                    $count=$this->where(array('pid'=>$v))->count(1);
                    if($row['status']=='1' && $row['activate']!='0' ){
                        $data[]=$row['userid'];
                    }else{
                        $data[]='';
                    }
                }
            }

        }
        return $data;

    }

}
