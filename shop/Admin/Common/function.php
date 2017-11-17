<?php 

/**
 * 检测用户是否登录
 * @return integer 0-未登录，大于0-当前登录用户ID
 * @author jry <598821125@qq.com>
 */
function is_login()
{
    return D('Admin/Manage')->is_login();
}

/**
 * [status_name 表状态配置]
 * @param  [type] $moble [数据表]
 * @param  [type] $value [状态值]
 * @return [type]        [description]
 */
function status_name($moble,$value){
	$arr=array();
	switch ($moble) {
		case 'traing':
			$arr=array(0=>"<span style='color:#2699ed' >出售成功</span>",1=>"<span style='color:#3c763d' >购买者已确认</span>",2=>"<span style='color:#ff7826' >交易完成</span>",3=>"<span style='color:#ef2a2a' >交易取消</span>");
			break;
		
		default:
			# code...
			break;
	}

	return $arr[$value];
}

/**
 * 字节格式化
 * @access public
 * @param string $size 字节
 * @return string
 */
function byte_Format($size) {
    $kb = 1024;          // Kilobyte
    $mb = 1024 * $kb;    // Megabyte
    $gb = 1024 * $mb;    // Gigabyte
    $tb = 1024 * $gb;    // Terabyte

    if ($size < $kb)
        return $size . 'B';

    else if ($size < $mb)
        return round($size / $kb, 2) . 'KB';

    else if ($size < $gb)
        return round($size / $mb, 2) . 'MB';

    else if ($size < $tb)
        return round($size / $gb, 2) . 'GB';
    else
        return round($size / $tb, 2) . 'TB';
}


//按日期搜索
function date_query($field){

        $date_start=I('date_start');
        $date_end=I('date_end');
        if($date_start!='' && $date_end!=''){
            $map[$field]=array('between',array(strtotime($date_start),strtotime($date_end)));
        }
        if($date_start!='' && $date_end==''){
            $map[$field]=array('gt',$date_start);
        }
        if($date_start=='' && $date_end!=''){
            $map[$field]=array('lt',$date_end);
        }
        if($map)
            return $map;
}