<?php

/**
 * 验证手机号是否正确
 * @author 陶
 * @param $mobile
 */
function isMobile($mobile) {
    if (!is_numeric($mobile)) {
        return false;
    }
    return preg_match('#^1[34578]\d{9}$#', $mobile) ? true : false;
}

/**
 * 检测用户是否登录
 * @return integer 0-未登录，大于0-当前登录用户ID
 * @author jry <598821125@qq.com>
 */
function user_login()
{
    return D('Home/user')->user_login();
}

function get_userid(){
    $user = session('user_login');
    return $user['userid'];
}

//交易状态
function StatusName($value){
    $arr=array(2=>'交易成功',3=>'取消交易');
    return $arr[$value];
}

/**
 * [caimin_state 判断能否采矿]
 * @param  [type] $fid  [好友ID]
 * @param  [type] $type [description]
 * @return [type]       [description]
 */
function caimin_state($fid,$type=null){
    if(empty($fid)){
        return false;
    }

    $level=I('type');//二代
    if($level=='two'){
        $count=10;//D('User')->where(array('pid'=>get_userid()))->count(1);
        if($count<10){
            $type!=null? $str='<span>不可采矿</span>' : $str=false;
            return $str;
        }
    }

    $store=M('store');
    $where['uid']=$fid;
    $where['sign_time']=date('Ymd');
    $userid=get_userid();
    $where["caimi_fids"]=array('NOTLIKE','%,'.$userid.',%');
    $count=$store->where($where)->count(1);
    if($count==1){
        $type!=null? $str='<span class="red" data="'.$fid.'"  >采矿</span>' : $str=true;
    }else{
        $type!=null? $str='<span>不可采矿</span>' : $str=false;
    }
    return $str;
}

function user_level($level){
    $arr=array(
        '0'=>'一般股东',
        '30'=>'银1',
        '50'=>'银2',
        '80'=>'银3',
        '130'=>'金1',
        '200'=>'金2',
        '280'=>'金3',
        '400'=>'钻1',
        '600'=>'钻2',
        '800'=>'钻3',
    );
    foreach ($arr as $k => $val) {
        if($level>=$k){
            $level_name=$val;
        }
    }
    return $level_name;
}

//检查网站是否关闭
function is_close_site(){

    $where['name']='TOGGLE_WEB_SITE';
    $info=D('Config')->where($where)->field('value,tip')->find();
    return $info;
}

/**
 * [SearchDate 获取上周的还是时间和结束时间]
 */
function SearchDate(){
    $date=date('Y-m-d');  //当前日期
    $first=1; //$first =1 表示每周星期一为开始日期 0表示每周日为开始日期
    $w=date('w',strtotime($date));  //获取当前周的第几天 周日是 0 周一到周六是 1 - 6
    $now_start=date('Y-m-d',strtotime("$date -".($w ? $w - $first : 6).' days')); //获取本周开始日期，如果$w是0，则表示周日，减去 6 天
    $last_start=strtotime("$now_start - 7 days");  //上周开始时间
    $last_end=strtotime("$now_start - 1 days");  //上周结束时间
    //获取上周起始日期
    $arr['week_start'] = $last_start;
    $arr['week_end'] = strtotime($now_start);//本周开始时间,即上周最后时间
    return $arr;
}

function img_uploading($path_old=null)
{
    $images_path='./Uploads/';
    if (!is_dir($images_path)) {
        mkdir($images_path);
    }

    $upload             = new \Think\Upload();// 实例化上传类
    $upload->maxSize    =     10485760 ;// 设置附件上传大小
    $upload->exts       =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
    $upload->rootPath   =      $images_path; // 设置上传根目录    // 上传文件
    $upload->savePath   =      ''; // 设置上传子目录    // 上传文件
    $info               =   $upload->upload();

    if(!$info)
    {// 上传错误提示错误信息
        $res['status']=0;
        $res['res']=$upload->getError();
    }
    else
    {// 上传成功
        foreach($info as $file){

            $img_path = $file['savepath'].$file['savename'];
        }
        //上传成功后删除原来的图片
        if($path_old && $img_path)
        {

            unlink('.'.$path_old);

            // echo '删除成功';
        }
        $res['status']=1;
        $res['res']='/Uploads/'.$img_path;
    }
    return $res;
}



/**
 * [sendmail 发送邮件]
 * @return [type] [description]
 */
function sendmail($email){

    session('set_time',time());
    session('user_email',$email);
    session('sms_code',getCode(6,1));

    require APP_PATH.'Common/Mailer/Mailer.class.php';
    $mail = new \PHPMailer(true);
    $mail->IsSMTP();
    $mail->CharSet='UTF-8'; //设置邮件的字符编码，这很重要，不然中文乱码
    $mail->SMTPAuth   = true;                  //开启认证
    $mail->Port       = 25;
    $mail->Host       = "smtp.ym.163.com"; // // 发现件人邮箱 服务器
    $mail->Username   = "sb@qqlove.vip";  //发件人 注意：普通邮件认证不需要加 @域名
    $mail->Password   = '111111';  //密码
    //$mail->IsSendmail(); //如果没有sendmail组件就注释掉，否则出现“Could  not execute: /var/qmail/bin/sendmail ”的错误提示
    $mail->AddReplyTo("sb@qqlove.vip","test");//回复地址
    $mail->From       = "sb@qqlove.vip";//发件人邮箱
    $mail->FromName   = "test";//发件人
    $to = $email;//收件人邮箱
    $mail->AddAddress($to);
    $mail->Subject  = "验证码";
    $mail->Body = "【玉石矿山】您的验证码为".session('sms_code').",10分钟内有效";
    $mail->AltBody    = "To view the message, please use an HTML compatible email viewer!"; //当邮件不支持html时备用显示，可以省略
    $mail->WordWrap   = 80; // 设置每行字符串的长度
    //$mail->AddAttachment("f:/test.png");  //可以添加附件
    $mail->IsHTML(true);
    $res=$mail->Send();
    return $res;
}

function getCode($length = 6 , $numeric = 0) {
    if($numeric) {
        $hash = sprintf('%0'.$length.'d', mt_rand(0, pow(10, $length) - 1));
    } else {
        $hash = '';
        $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789abcdefghjkmnpqrstuvwxyz';
        $max = strlen($chars) - 1;
        for($i = 0; $i < $length; $i++) {
            $hash .= $chars[mt_rand(0, $max)];
        }
    }

    return $hash;
}

function check_code($value,$send_email){
    $time=session('set_time');
    $email=session('user_email');
    $code=session('sms_code');
    if(time() - $time > 600 ||  $code !=  $value  || $code == '' || $email != $send_email ){
        return false;
    }
    return true;
}

function isupdate($type,$id){
    $userid=get_userid();
    switch ($type) {
        case '1':
            $user=D('User');
            $where['pid']=$userid;
            $count=$user->where($where)->count(1);
            if($count>=10){
                $str='<a href="javascript:;" cartype="0" data="'.$id.'"  class="dian">升级</a>';
            }else{
                $str='<a class="noupdate" cartype="0" data="'.$id.'" href="javascript:;" >不可升级</a>';
            }
            break;

        case '2':
            $user=D('User');
            $where['pid']=$userid;
            $count=$user->where($where)->count(1);
            if($count>=30){
                $str='<a href="javascript:;" cartype="1" data="'.$id.'"  class="dian">升级</a>';
            }else{
                $str='<a class="noupdate" cartype="1" data="'.$id.'" href="javascript:;" >不可升级</a>';
            }
            break;

        case '3':
            $str='<a class="noupdate" href="javascript:;" >不可升级</a>';
            break;

    }
    return $str;
}




/**
 * 数组分页函数  核心函数  array_slice
 * 用此函数之前要先将数据库里面的所有数据按一定的顺序查询出来存入数组中
 * $count   每页多少条数据
 * $page   当前第几页
 * $array   查询出来的所有数组
 * order 0 - 不变     1- 反序
 */
function data_page($count,$page,$array,$order){
    global $countpage; #定全局变量
    $page=(empty($page))?'1':$page; #判断当前页面是否为空 如果为空就表示为第一页面
    $start=($page-1)*$count; #计算每次分页的开始位置
    if($order==1){
        $array=array_reverse($array);
    }
    $totals=count($array);
    $countpage=ceil($totals/$count); #计算总页面数
    $pagedata=array();
    $pagedata=array_slice($array,$start,$count);
    return $pagedata;  #返回查询数据
}


function set_verify($vid=1){
    ob_clean();
    $config =    array(
        'codeSet' =>  '0123456789',
        'fontSize'    =>    30,    // 验证码字体大小
        'length'      =>    4,     // 验证码位数
        'fontttf'     =>   '5.ttf',
        'useCurve'    => false,
        'bg'          => array(229, 237, 240),
    );
    $Verify =     new \Think\Verify($config);
    $Verify->entry($vid);
}

/**
 * 检测验证码
 * @param  integer $id 验证码ID
 * @return boolean 检测结果
 */
function check_verify($code, $vid = 1)
{
    $verify = new \Think\Verify();
    return $verify->check($code, $vid);
}

//获取广告图
function get_banner($type){
    $where['banner_type']=$type;
    $where['status']=1;
    $banner=D('banner')->where($where)->order('sort desc')->select();
    return $banner;
}


/**
 * 验证身份证
 * @return string
 */
function check_id_card($id_card){
    if(strlen($id_card)==18){
        return idcard_checksum18($id_card);
    }elseif((strlen($id_card)==15)){
        $id_card=idcard_15to18($id_card);
        return idcard_checksum18($id_card);
    }else{
        return false;
    }
}
// 计算身份证校验码，根据国家标准GB 11643-1999
function idcard_verify_number($idcard_base){
    if(strlen($idcard_base)!=17){
        return false;
    }
    //加权因子
    $factor=array(7,9,10,5,8,4,2,1,6,3,7,9,10,5,8,4,2);
    //校验码对应值
    $verify_number_list=array('1','0','X','9','8','7','6','5','4','3','2');
    $checksum=0;
    for($i=0;$i<strlen($idcard_base);$i++){
        $checksum += substr($idcard_base,$i,1) * $factor[$i];
    }
    $mod=$checksum % 11;
    $verify_number=$verify_number_list[$mod];
    return $verify_number;
}
// 将15位身份证升级到18位
function idcard_15to18($idcard){
    if(strlen($idcard)!=15){
        return false;
    }else{
        // 如果身份证顺序码是996 997 998 999，这些是为百岁以上老人的特殊编码
        if(array_search(substr($idcard,12,3),array('996','997','998','999')) !== false){
            $idcard=substr($idcard,0,6).'18'.substr($idcard,6,9);
        }else{
            $idcard=substr($idcard,0,6).'19'.substr($idcard,6,9);
        }
    }
    $idcard=$idcard.idcard_verify_number($idcard);
    return $idcard;
}
// 18位身份证校验码有效性检查
function idcard_checksum18($idcard){
    if(strlen($idcard)!=18){
        return false;
    }
    $idcard_base=substr($idcard,0,17);
    if(idcard_verify_number($idcard_base)!=strtoupper(substr($idcard,17,1))){
        return false;
    }else{
        return true;
    }
}


//验证短信验证码
function check_sms($code,$mobile){

    $md5_code=sha1(md5(trim($code).trim($mobile)));
    $set_time=session('set_time');
    $sms_code=session('sms_code');
    if(time() - $_SESSION['set_time'] <= 300  && $code!='' && $md5_code==$sms_code){
        $res=true;
        session('sms_code',null);
    }else{
        $res=false;
    }
    return $res;

}


// 发送短信验证
function sendMsg($mobile){

    $mobile=safe_replace($mobile);
    if(empty($mobile)){
        $mes['status']=0;
        $mes['message']='手机号码不能为空';
    }

    if(time() >  session('set_time')+60 || session('set_time') == '') {
        session('set_time',time());
        $user_mobile =  $mobile;
        session('user_mobile',$user_mobile);
        $code=getCode(6,1);
        $sms_code=sha1(md5(trim($code).trim($mobile)));
        session('sms_code',$sms_code);

        //发送短信
        if(isMobile($user_mobile)){//国内短信发送接口
//            $content="您的验证码为：".$code.",5分钟内有效";//要发送的短信内容
            $res=setmyCode($user_mobile,'1',$code);
            if($res){
                $mes['status']=1;
                $mes['message']='短信发送成功';
                return $mes;
            }
            else{
                $mes['status']=0;
                $mes['message']='短信发送失败';
                return $mes;
            }
        }else{
            $mes['status']=0;
            $mes['message']='手机号码不正确';
            return $mes;
        }

    }else{
        $msgtime=session('set_time')+60 - time();
        $data = $msgtime.'秒之后再试';
        $mes['status']=0;
        $mes['message']=$data;
        return $mes;
    }
}



function dhtmlspecialchars($string, $flags = null) {
    if (is_array($string)) {
        foreach ($string as $key => $val) {
            $string[$key] = dhtmlspecialchars($val, $flags);
        }
    } else {
        if ($flags === null) {
            $string = str_replace(array('&', '"', '<', '>'), array('&amp;', '&quot;', '&lt;', '&gt;'), $string);
            if (strpos($string, '&amp;#') !== false) {
                $string = preg_replace('/&amp;((#(\d{3,5}|x[a-fA-F0-9]{4}));)/', '&\\1', $string);
            }
        } else {
            if (PHP_VERSION < '5.4.0') {
                $string = htmlspecialchars($string, $flags);
            } else {
                if (strtolower(CHARSET) == 'utf-8') {
                    $charset = 'UTF-8';
                } else {
                    $charset = 'ISO-8859-1';
                }
                $string = htmlspecialchars($string, $flags, $charset);
            }
        }
    }
    return $string;
}

//获取奖金配置
function get_config($field=null){
    $data=F('ststemconfig','','./Public/data/');
    if(empty($field))
        return $data;
    else
        return $data[$field];
}

//获取排单时间 10:00-10:30,15:00-22:30'
function check_order_time(){

    $pin_time = get_config('m_w_time');
    if(empty($pin_time) || $pin_time==0){
        return false;
    }

    $time_arr = explode(',', $pin_time);

    $isok = false;
    foreach ($time_arr as $k => $v) {
        $arr = explode('-', $v);
        $start_time = strtotime(date('Y-m-d ' . $arr[0]));
        $end_time = strtotime(date('Y-m-d ' . $arr[1]));
        if (strtotime(date('Y-m-d H:i', time())) > $start_time && strtotime(date('Y-m-d H:i', time())) <= $end_time) {
            $isok = true;
            break;
        }
    }

    return $isok;

}


function check_money_num($money){
    //验证单数是否抢完
    if(empty($money)){
        return false;
    }

    $pin_money=get_config('m_pin_money');//金额
    $pin_money_num=get_config('m_pin_money_num');//金额数

    $pin_money_arr=explode(",",$pin_money);
    $pin_money_num_arr=explode(",",$pin_money_num);

    if(!in_array($money, $pin_money_arr)){
        return false;
    }

    $key=array_search($money,$pin_money_arr);//按金额取对应键值
    $num=$pin_money_num_arr[$key];//金对应的数量
    if($num<=0 || empty($num)){
        return false;
    }

    $where['money']=$money;
    $where['datestr']=date('Ymd');
    $buy_num = M('daywork')->where($where)->getField('num');//已抢数量

    if ($buy_num >= $num) {
        return false;
    }
    return true;
}


//获取提现配置
function get_feeconfig($field=null){
    $data=F('feeconfig','','./Public/data/');
    if(empty($field))
        return $data;
    else
        return $data[$field];
}

function check_tx_time(){
    $tx_time=get_feeconfig('m_tx_time');
    if(!empty($tx_time)){
        $time_arr = explode(',', $tx_time);
        $isok = false;
        foreach ($time_arr as $k => $v) {
            $arr = explode('-', $v);
            $start_time = strtotime(date('Y-m-d ' . $arr[0]));
            $end_time = strtotime(date('Y-m-d ' . $arr[1]));
            if (strtotime(date('Y-m-d H:i', time())) > $start_time && strtotime(date('Y-m-d H:i', time())) < $end_time) {
                $isok = true;
                break;
            }
        }
        return $isok;
    }
}


//推荐积分提现值
function get_tx($cx_val){

    $cx_val=(float) $cx_val;

    $arr=array(
        '0-30'=>'500',
        '30-50'=>'1000',
        '50-70'=>'2000',
        '70-100'=>'5000',
        '100-150'=>'10000',
        '150'=>'50000',
    );


    foreach($arr as $k=>$v){

        $kk=explode('-',$k);

        if(empty($kk[1])){

            if($cx_val>=$kk[0]){
                return  $v;

            }

        }else{

            if($cx_val>=$kk[0] && $cx_val<$kk[1]){

                return  $v;

            }
        }



    }

}

//诚信分组
function get_tx_name($cx_val){
    $cx_val=(float) $cx_val;
    $arr=array(
        '0-30'=>'普会员',
        '30-50'=>'银1',
        '50-80'=>'银2',
        '80-130'=>'银3',
        '130-200'=>'金1',
        '200-280'=>'金2',
        '280-400'=>'金3',
        '400-600'=>'钻1',
        '600-800'=>'钻2',
        '800'=>'钻3',
    );


    foreach($arr as $k=>$v){

        $kk=explode('-',$k);

        if(empty($kk[1])){

            if($cx_val>=$kk[0]){
                return  $v;

            }

        }else{

            if($cx_val>=$kk[0] && $cx_val<$kk[1]){
                return  $v;
            }
        }

    }

}

/**
 * [set_cx_val 设置诚信值]
 * @param [type] $k [description]
 */
function set_cx_val($k){
    $arr=array(
        '10000.00'=>'10',
        '5000.00'=>'5',
        '3000.00'=>'3',
        '2000.00'=>'2',
        '1000.00'=>'1',
    );

    if($arr[$k]){
        return $arr[$k];
    }else{
        return '0';
    }

}


//获取利息配置
function get_lixinconfig($field=null){
    $data=F('turntable_data','','./Public/data/');
    if(empty($field))
        return $data;
    else
        return $data[$field];
}