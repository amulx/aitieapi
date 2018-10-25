<?php
namespace App\Model;

use PhalApi\Model\NotORMModel as NotORM;
/**
 * 消费
 */
class Consume extends NotORM {
   	/**
   	 * [addConsumeLog 增加消费]
   	 * @param [int] $uid     [用户ID]
   	 * @param [int] $topicid [爱贴ID]
   	 * @param [int] $gold    [金币数量]
   	 * @param [string] $type    [赚取 or 消费]
   	 */
    public function addConsumeLog($uid,$topicid,$gold,$type,$phoneOrWechat){
        $increment_id = $this->insert(
            [
                'uid' => $uid,
                'topicid' => $topicid,
                'gold' => $gold,
                'type' => $type,
                $phoneOrWechat=>1,
                'create_time' => date('Y-m-d H:i:s')
            ]
        );
        return $increment_id;
    }

    public function isExist($uid,$topicid){
        $sql = 'select consumeid from tbl_consume where uid = ? and topicid = ?';
        return $this->getORM()->queryRows($sql, [$uid,$topicid]);    	
    }

    public function forTopicDetail($uid,$topicid){
        $sql = 'select phone,wechat from tbl_consume where uid = ? and topicid = ?';
        return $this->getORM()->queryRows($sql, [$uid,$topicid]);      	
    }
}