<?php



/**
 * 用于生成插入datetime类型字段用的字符串
 * @param string $str 支持偏移字符串
 */
function datetime($str = 'now')
{
    return @date("Y-m-d H:i:s", strtotime($str));
}

/**
 * 时间戳格式化
 * @param int $time
 * @return string 完整的时间显示
 * @author jry <598821125@qq.com>
 */
function time_format($time = null, $format = 'Y-m-d H:i')
{
    $time = $time === null ? time() : intval($time);
    return date($format, $time);
}

/**
 * 系统非常规MD5加密方法
 * @param  string $str 要加密的字符串
 * @return string
 * @author jry <598821125@qq.com>
 */
function user_md5($str, $auth_key)
{
    if (!$auth_key) {
        $auth_key = C('AUTH_KEY') ?: '0755web';
    }
    return '' === $str ? '' : md5(sha1($str) . $auth_key);
}

/**
 * [user_salt 用户密码加密链接串]
 * @return [type] [description]
 */
function user_salt($time=null){
    if(isset($time) || empty($time)){
        $time=time();
    }
   return substr(md5($time),0,3);
}

/**
 * 获取上传文件路径
 * @param  int $id 文件ID
 * @return string
 * @author jry <598821125@qq.com>
 */
function get_cover($id = null, $type = null)
{
    return D('Admin/Upload')->getCover($id, $type);
}



/**
 * 检测是否使用手机访问
 * @access public
 * @return bool
 */
function is_wap()
{
    if (isset($_SERVER['HTTP_VIA']) && stristr($_SERVER['HTTP_VIA'], "wap")) {
        return true;
    } elseif (isset($_SERVER['HTTP_ACCEPT']) && strpos(strtoupper($_SERVER['HTTP_ACCEPT']), "VND.WAP.WML")) {
        return true;
    } elseif (isset($_SERVER['HTTP_X_WAP_PROFILE']) || isset($_SERVER['HTTP_PROFILE'])) {
        return true;
    } elseif (isset($_SERVER['HTTP_USER_AGENT']) && preg_match('/(blackberry|configuration\/cldc|hp |hp-|htc |htc_|htc-|iemobile|kindle|midp|mmp|motorola|mobile|nokia|opera mini|opera |Googlebot-Mobile|YahooSeeker\/M1A1-R2D2|android|iphone|ipod|mobi|palm|palmos|pocket|portalmmm|ppc;|smartphone|sonyericsson|sqh|spv|symbian|treo|up.browser|up.link|vodafone|windows ce|xda |xda_)/i', $_SERVER['HTTP_USER_AGENT'])) {
        return true;
    } else {
        return false;
    }
}

/**
 * 是否微信访问
 * @return bool
 * @author jry <598821125@qq.com>
 */
function is_weixin()
{
    if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
        return true;
    } else {
        return false;
    }
}

/**
 * TODO 基础分页的相同代码封装，使前台的代码更少
 * @param $m 模型，引用传递
 * @param $where 查询条件
 * @param int $pagesize 每页查询条数
 * @return \Think\Page
 */
function getpage(&$m,$where,$pagesize=10){
    $m1=clone $m;//浅复制一个模型
    $count = $m->where($where)->count();//连惯操作后会对join等操作进行重置
    $m=$m1;//为保持在为定的连惯操作，浅复制一个模型
    $p=new Think\Page($count,$pagesize);
    $p->lastSuffix=false;
    $p->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
    $p->setConfig('prev','上一页');
    $p->setConfig('next','下一页');
    $p->setConfig('last','末页');
    $p->setConfig('first','首页');
    //手机端访问
    if(is_wap()){
       $p->rollPage        = 3;
       $p->setConfig('theme','%UP_PAGE%%LINK_PAGE%%DOWN_PAGE%'); 
    }
    
    $p->parameter=I('get.');

    $m->limit($p->firstRow,$p->listRows);

    return $p;
}

/**
 * [get_verify 生成验证码]
 * @return [type] [description]
 */
function get_verify(){
    ob_clean();
    $config =    array(
    'codeSet' =>  '0123456789',   
    'fontSize'    =>    50,    // 验证码字体大小   
    'length'      =>    4,     // 验证码位数    
    'fontttf'     =>   '5.ttf',
    'useCurve'    => false,
    'bg'          => array(229, 237, 240),
    );
    $Verify =     new \Think\Verify($config);
    $Verify->entry();
}


/**
 * [ajaxReturn ajax提示款]
 * @param  [type]  $message [提示文字]
 * @param  integer $status  [1=成功 0=失败]
 * @param  string  $url     [跳转地址]
 * @param  string  $extra   [回传数据]
 * @return [type]           [json数据]
 */
function ajaxReturn($message,$status=0, $url ='',$extra='') {
    // 返回JSON数据格式到客户端 包含状态信息
    header('Content-Type:application/json; charset=utf-8');
    $result = array(
        'message' => $message,
        'status'  =>  $status,
        'url' => $url,
        'result'  =>  $extra
    );
    
    exit(json_encode($result));
}

// =陶==js消息提示框===
function error_alert($mes){
    echo "<meta charset=\"utf-8\"/><script>alert('".$mes."');javascript:history.back(-1);</script>";
    exit;
}
function success_alert($mes,$url=''){
    if($url!=''){
        echo "<meta charset=\"utf-8\"/><script>alert('".$mes."');location.href='" .$url. "';</script>";
    }else{
       echo "<meta charset=\"utf-8\"/><script>alert('".$mes."');location.href='" .$jumpUrl. "';</script>"; 
    }
    exit;
}
// =陶==js消息提示框===



//防注入，字符串处理，禁止构造数组提交
//字符过滤
//陶
function safe_replace($string) {
    if(is_array($string)){ 
       $string=implode('，',$string);
       $string=htmlspecialchars(str_shuffle($string));
    } else{
        $string=htmlspecialchars($string);
    }
    $string = str_replace('%20','',$string);
    $string = str_replace('%27','',$string);
    $string = str_replace('%2527','',$string);
    $string = str_replace('*','',$string);
    $string=str_replace("select","",$string);
    $string=str_replace("join","",$string);
    $string=str_replace("union","",$string);
    $string=str_replace("where","",$string);
    $string=str_replace("insert","",$string);
    $string=str_replace("delete","",$string);
    $string=str_replace("update","",$string);
    $string=str_replace("like","",$string);
    $string=str_replace("drop","",$string);
    $string=str_replace("create","",$string);
    $string=str_replace("modify","",$string);
    $string=str_replace("rename","",$string);
    $string=str_replace("alter","",$string);
    $string=str_replace("cas","",$string);
    $string=str_replace("or","",$string);
    $string=str_replace("=","",$string);
    $string = str_replace('"','&quot;',$string);
    $string = str_replace("'",'',$string);
    $string = str_replace('"','',$string);
    $string = str_replace(';','',$string);
    $string = str_replace('<','&lt;',$string);
    $string = str_replace('>','&gt;',$string);
    $string = str_replace("{",'',$string);
    $string = str_replace('}','',$string);
    $string = str_replace('--','',$string);
    $string = str_replace('(','',$string);
    $string = str_replace(')','',$string);

    return $string;
}

function payway($value){
    $arr=array('支付宝','微信');
    return $arr[$value];
}

/**
 * 获取父级账号
 */
function get_parent_account($pid){
    $account=D('User')->where('userid ='.$pid)->getField('account');
    if($account)
        return $account;
    else
        return '无';
}


function get_user_name($uid){
    $where['userid']=$uid;
    $info=M('user')->where($where)->field('account,username')->find();
    return $info['username']."(".$info['account'].")";
}


/*
 * 函数说明：截取指定长度的字符串
 * (UTF-8专用 汉字和大写字母长度算1，其它字符长度算0.5)
 *
 * @param  string  $sourcestr  原字符串
 * @param  int     $cutlength  截取长度
 * @param  string  $etc        省略字符...
 * @return string              截取后的字符串
 */

function restrlen($sourcestr, $cutlength = 10, $etc = '...') {
    $returnstr = '';
    $i = 0;
    $n = 0.0;
    $str_length = strlen($sourcestr); //字符串的字节数
    while (($n < $cutlength) and ( $i < $str_length)) {
        $temp_str = substr($sourcestr, $i, 1);
        $ascnum = ord($temp_str); //得到字符串中第$i位字符的ASCII码
        if ($ascnum >= 252) { //如果ASCII位高与252
            $returnstr = $returnstr . substr($sourcestr, $i, 6); //根据UTF-8编码规范，将6个连续的字符计为单个字符
            $i = $i + 6; //实际Byte计为6
            $n++; //字串长度计1
        } elseif ($ascnum >= 248) { //如果ASCII位高与248
            $returnstr = $returnstr . substr($sourcestr, $i, 5); //根据UTF-8编码规范，将5个连续的字符计为单个字符
            $i = $i + 5; //实际Byte计为5
            $n++; //字串长度计1
        } elseif ($ascnum >= 240) { //如果ASCII位高与240
            $returnstr = $returnstr . substr($sourcestr, $i, 4); //根据UTF-8编码规范，将4个连续的字符计为单个字符
            $i = $i + 4; //实际Byte计为4
            $n++; //字串长度计1
        } elseif ($ascnum >= 224) { //如果ASCII位高与224
            $returnstr = $returnstr . substr($sourcestr, $i, 3); //根据UTF-8编码规范，将3个连续的字符计为单个字符
            $i = $i + 3; //实际Byte计为3
            $n++; //字串长度计1
        } elseif ($ascnum >= 192) { //如果ASCII位高与192
            $returnstr = $returnstr . substr($sourcestr, $i, 2); //根据UTF-8编码规范，将2个连续的字符计为单个字符
            $i = $i + 2; //实际Byte计为2
            $n++; //字串长度计1
        } elseif ($ascnum >= 65 and $ascnum <= 90 and $ascnum != 73) { //如果是大写字母 I除外
            $returnstr = $returnstr . substr($sourcestr, $i, 1);
            $i = $i + 1; //实际的Byte数仍计1个
            $n++; //但考虑整体美观，大写字母计成一个高位字符
        } elseif (!(array_search($ascnum, array(37, 38, 64, 109, 119)) === FALSE)) { //%,&,@,m,w 字符按1个字符宽
            $returnstr = $returnstr . substr($sourcestr, $i, 1);
            $i = $i + 1; //实际的Byte数仍计1个
            $n++; //但考虑整体美观，这些字条计成一个高位字符
        } else { //其他情况下，包括小写字母和半角标点符号
            $returnstr = $returnstr . substr($sourcestr, $i, 1);
            $i = $i + 1; //实际的Byte数计1个
            $n = $n + 0.5; //其余的小写字母和半角标点等与半个高位字符宽...
        }
    }
    if ($i < $str_length) {
        $returnstr = $returnstr . $etc; //超过长度时在尾处加上省略号
    }
    return $returnstr;
}

/**
 *  完全过虑PHP，JS，css
 *
 * @access    public
 * @param     string  $str  需要过滤的字符串
 * @return    string
 */
function clearhtml($str) {

    $str = strip_tags($str);

    //首先去掉头尾空格
    $str = trim($str);

    //接着去掉两个空格以上的
    $str = preg_replace('/\s(?=\s)/', '', $str);

    //最后将非空格替换为一个空格
    $str = preg_replace('/[\n\r\t]/', ' ', $str);

    $str = str_replace(array('&nbsp;', '　'), '', $str);

    return trim($str);
}

/**
 * xss过滤函数
 *
 * @param $string
 * @return string
 */
function remove_xss($string) {
    $string = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]+/S', '', $string);
    $parm1 = Array('javascript', 'vbscript', 'expression', 'applet', 'meta', 'xml', 'blink', 'link', 'script', 'embed', 'object', 'iframe', 'frame', 'frameset', 'ilayer', 'layer', 'bgsound', 'title', 'base');
    $parm2 = Array('onabort', 'onactivate', 'onafterprint', 'onafterupdate', 'onbeforeactivate', 'onbeforecopy', 'onbeforecut', 'onbeforedeactivate', 'onbeforeeditfocus', 'onbeforepaste', 'onbeforeprint', 'onbeforeunload', 'onbeforeupdate', 'onblur', 'onbounce', 'oncellchange', 'onchange', 'onclick', 'oncontextmenu', 'oncontrolselect', 'oncopy', 'oncut', 'ondataavailable', 'ondatasetchanged', 'ondatasetcomplete', 'ondblclick', 'ondeactivate', 'ondrag', 'ondragend', 'ondragenter', 'ondragleave', 'ondragover', 'ondragstart', 'ondrop', 'onerror', 'onerrorupdate', 'onfilterchange', 'onfinish', 'onfocus', 'onfocusin', 'onfocusout', 'onhelp', 'onkeydown', 'onkeypress', 'onkeyup', 'onlayoutcomplete', 'onload', 'onlosecapture', 'onmousedown', 'onmouseenter', 'onmouseleave', 'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup', 'onmousewheel', 'onmove', 'onmoveend', 'onmovestart', 'onpaste', 'onpropertychange', 'onreadystatechange', 'onreset', 'onresize', 'onresizeend', 'onresizestart', 'onrowenter', 'onrowexit', 'onrowsdelete', 'onrowsinserted', 'onscroll', 'onselect', 'onselectionchange', 'onselectstart', 'onstart', 'onstop', 'onsubmit', 'onunload');
    $parm = array_merge($parm1, $parm2);

    for ($i = 0; $i < sizeof($parm); $i++) {
        $pattern = '/';
        for ($j = 0; $j < strlen($parm[$i]); $j++) {
            if ($j > 0) {
                $pattern .= '(';
                $pattern .= '(&#[x|X]0([9][a][b]);?)?';
                $pattern .= '|(&#0([9][10][13]);?)?';
                $pattern .= ')?';
            }
            $pattern .= $parm[$i][$j];
        }
        $pattern .= '/i';
        $string = preg_replace($pattern, '', $string);
    }
    return $string;
}


/**
 * 手机号码星星隐藏
 * @return string
 */
function mobile_hide($mobile)
{
    $mobile_asterisk = substr($mobile,0,3)."****".substr($mobile,7,4);
    return $mobile_asterisk;
} 



function pay_type($type)
{
  switch($type){
    
    case 1:
      $type='认筹股金';
    break;    
    
    case 2:
      $type='股金收益';
    break;    
    
    case 3:
      $type='业绩红利';
    break;    
  
    
    
  }
  
  return $type;
}




/**
 * 系统加密方法
 * @param string $data 要加密的字符串
 * @param string $key  加密密钥
 * @param int $expire  过期时间 单位 秒
 * @return string
 */
function think_encrypt($data, $key = '', $expire = 0) {
    $key  = md5(empty($key) ? C('DATA_AUTH_KEY') : $key);
    $data = base64_encode($data);
    $x    = 0;
    $len  = strlen($data);
    $l    = strlen($key);
    $char = '';

    for ($i = 0; $i < $len; $i++) {
        if ($x == $l) $x = 0;
        $char .= substr($key, $x, 1);
        $x++;
    }

    $str = sprintf('%010d', $expire ? $expire + time():0);

    for ($i = 0; $i < $len; $i++) {
        $str .= chr(ord(substr($data, $i, 1)) + (ord(substr($char, $i, 1)))%256);
    }
    return str_replace(array('+','/','='),array('-','_',''),base64_encode($str));
}
 


 //在线人数
function get_online_num(){
    
    $filename='data/online.txt';//数据文件 
    $cookiename='VGOTCN_OnLineCount';//cookie名称 
    $onlinetime=600;//在线有效时间，单位：秒 (即600等于10分钟) 
      
    $online=file($filename); 
    //PHP file() 函数把整个文件读入一个数组中。与 file_get_contents() 类似，不同的是 file() 将文件作为一个数组返回。数组中的每个单元都是文件中相应的一行，包括换行符在内。如果失败，则返回 false 
    $nowtime=$_SERVER['REQUEST_TIME']; 
    $nowonline=array(); 
    //得到仍然有效的数据 
    foreach($online as $line){ 
      $row=explode('|',$line); 
      $sesstime=trim($row[1]); 
      if(($nowtime - $sesstime)<=$onlinetime){//如果仍在有效时间内，则数据继续保存，否则被放弃不再统计 
        $nowonline[$row[0]]=$sesstime;//获取在线列表到数组，会话ID为键名，最后通信时间为键值 
      } 
    } 
    /* 
    @创建访问者通信状态 
    使用cookie通信 
    COOKIE 将在关闭浏览器时失效，但如果不关闭浏览器，此 COOKIE 将一直有效，直到程序设置的在线时间超时 
    */
    if(isset($_COOKIE[$cookiename])){//如果有COOKIE即并非初次访问则不添加人数并更新通信时间 
      $uid=$_COOKIE[$cookiename]; 
    }else{//如果没有COOKIE即是初次访问 
      $vid=0;//初始化访问者ID 
      do{//给用户一个新ID 
        $vid++; 
        $uid='U'.$vid; 
      }while(array_key_exists($uid,$nowonline)); 
      setcookie($cookiename,$uid); 
    } 
    $nowonline[$uid]=$nowtime;//更新现在的时间状态 
    //统计现在在线人数 
    $total_online=count($nowonline); 
    //写入数据 
    if($fp=@fopen($filename,'w')){ 
      if(flock($fp,LOCK_EX)){ 
        rewind($fp); 
        foreach($nowonline as $fuid=>$ftime){ 
          $fline=$fuid.'|'.$ftime."\n"; 
          @fputs($fp,$fline); 
        } 
        flock($fp,LOCK_UN); 
        fclose($fp); 
      } 
    }   
    
    return $total_online;
}


function setmyCode($mobile,$msg){

      $url="http://service.winic.org:8009/sys_port/gateway/index.asp?";
      $data = "id=%s&pwd=%s&to=%s&content=%s&time=";
      $id = 'yuzhu';
      $pwd = '123456web';
      $to = $mobile; 
      $content = iconv("UTF-8","GB2312",$msg);
      $rdata = sprintf($data, $id, $pwd, $to, $content);
      
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_POST,1);
      curl_setopt($ch, CURLOPT_POSTFIELDS,$rdata);
      curl_setopt($ch, CURLOPT_URL,$url);
      curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
      $result = curl_exec($ch);
      curl_close($ch);
      $result = substr($result,0,3);
      if($result=='000')
        return true;
      else
        return false;

}