<?php
namespace Home\Controller;
use Think\Controller;
class TraingRecordController extends CommonController {

    /**
     * 出售记录
     */
    public function SellRecord(){

        $sql="SELECT * FROM (SELECT * FROM ysk_traing UNION ALL SELECT * FROM ysk_traing_free ) a  WHERE sell_id=".get_userid()." AND status IN (2,3) ORDER BY create_time DESC limit 100";
        $info=M()->query($sql);
        $this->assign('info',$info)->display();
    }

    /**
     * 购买记录
     */
    public function BuyRecord(){

        $sql="SELECT * FROM (SELECT * FROM ysk_traing UNION ALL SELECT * FROM ysk_traing_free ) a  WHERE buy_id=".get_userid()." AND status IN (2,3) ORDER BY create_time DESC limit 100";
        $info=M()->query($sql);
        $this->assign('info',$info)->display();
    }

    
}