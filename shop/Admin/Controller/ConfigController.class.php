<?php
// +----------------------------------------------------------------------
// | 零云 [ 简单 高效 卓越 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016 http://www.lingyun.net All rights reserved.
// +----------------------------------------------------------------------
// | Author: jry <598821125@qq.com>
// +----------------------------------------------------------------------
namespace Admin\Controller;

use Think\Page;

/**
 * 系统配置控制器
 * @author jry <598821125@qq.com>
 */
class ConfigController extends AdminController
{
    /**
     * 获取某个分组的配置参数
     */
    public function group($group = 1)
    {
        //根据分组获取配置
        $map['group']  = array('eq', $group);
        $field         = 'name,value,tip,type';
        $data_list     = D('Config')->lists($map,$field);
        $display=array(1=>'base',2=>'system',3=>'siteclose',4=>'fee');
        $this->assign('info',$data_list)->display($display[$group]);
    }

    /**
     * 批量保存配置
     * @author jry <598821125@qq.com>
     */
    public function groupSave()
    {
        $config=I('post.');
        unset($config['file']);
        if ($config && is_array($config)) {
            $config_object = D('Config');
            foreach ($config as $name => $value) {
                $map = array('name' => $name);
                // 如果值是数组则转换成字符串，适用于复选框等类型
                if (is_array($value)) {
                    $value = implode(',', $value);
                }

                $config_object->where($map)->setField('value',$value);
            }
        }

        $this->success('保存成功！');
    }

    public function sitecloseSave()
    {
        $config=I('post.');
        if ($config && is_array($config)) {
            $map['name']='TOGGLE_WEB_SITE';
            $config_object = D('Config');
            $data['value']=$config['TOGGLE_WEB_SITE'];
            $data['tip']=$config['tip'];
            $config_object->where($map)->save($data);
        }

        $this->success('保存成功！');
    }

    public function turntable(){
        $info=F('turntable_data','','./Public/data/');
        $this->assign('info',$info);
        $this->display();
    }

    //保存转盘数据
    public function savezhuanpan(){
        $data = I('post.');
        F('turntable_data',$data,'./Public/data/');
        $this->success('保存成功！');
    }


    //奖金配置
    public function system(){
        if(IS_POST){
            $data = I('post.');
            F('ststemconfig',$data,'./Public/data/');
            $this->success('保存成功！');
        }else{

            $info=F('ststemconfig','','./Public/data/');
            $this->assign('info',$info);
            $this->display(); 
        }
       
    }

    public function onlineUp(){
        // 生成随机增长数
        $rand = mt_rand(10,50);
        // 替换配置文件
        $data=F('ststemconfig','','./Public/data/');
        $data['m_one_count'] = $data['m_one_count'] + $rand;
        F('ststemconfig',$data,'./Public/data/');
    }

    public function onlineDown(){
        // 生成随机增长数
        $rand = mt_rand(10,50);
        // 替换配置文件
        $data=F('ststemconfig','','./Public/data/');
        $data['m_one_count'] = $data['m_one_count'] - $rand;
        F('ststemconfig',$data,'./Public/data/');
    }

    //提现设置
    public function fee(){
        if(IS_POST){
            $data = I('post.');
            F('feeconfig',$data,'./Public/data/');
            $this->success('保存成功！');
        }else{

            $info=F('feeconfig','','./Public/data/');
            $this->assign('info',$info);
            $this->display(); 
        }
       
    }
}
