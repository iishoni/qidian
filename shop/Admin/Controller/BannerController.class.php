<?php
namespace Admin\Controller;

use Util\Tree;

/**
 * 广告图控制器
 * 
 */
class BannerController extends AdminController
{
    /**
     * 广告图列表
     * 
     */
    public function index()
    {
        // 搜索
        $keyword         = I('keyword', '', 'string');
        if($keyword!=''){
            $condition       = array('like', '%' . $keyword . '%');
            $map['banner_name'] = $condition;
        }
        
        $data_list     = D('banner')
            ->where($map)
            ->order('sort desc, id asc')
            ->select();

        $this->assign('list',$data_list);
        $this->display();
    }

    /**
     * 新增广告图
     * 
     */
    public function add()
    {
        $this->display('edit');
        
    }

    /**
     * 编辑广告图
     * 
     */
    public function edit($id)
    {
       //获取角色信息
        $where['id']=(int)$id;
        $info=D('Banner')->find($id);
       
        $this->assign('info', $info);
        
        $this->display('edit');
    }

    public function Save(){
        if (IS_POST) {
            $group_object       = D('banner');
            $data               = I('post.');
            if(empty($data['banner_type'])){
                $this->error('广告位不能为空');
            }
            if(empty($data['banner_img'])){
                $this->error('广告图不能为空');
            }
            //修改
            if($data['id']>0){
                 $id = $group_object->save($data);
                if ($id) {
                    $this->success('操作成功', U('index'));
                } else {
                    $this->error('操作失败');
                }
                return;
            }
            //添加
            if ($data){
                $data['status']=1;
                $data['create_time']=time();
                $id = $group_object->add($data);
                if ($id) {
                    $this->success('操作成功', U('index'));
                } else {
                    $this->error('操作失败');
                }
            }
        }
    }

    /**
     * 设置一条或者多条数据的状态
     * 
     */
    public function setStatus($model = CONTROLLER_NAME, $script = false)
    {
        parent::setStatus($model);
    }
}
