<?php
namespace App\Model;

use PhalApi\Model\NotORMModel as NotORM;
/**
 * 点赞
 */
class Praise extends NotORM {

	public function inPraise($uid,$topicid) {        
        $rs = $this->getORM()->select('uid')->where('uid',$uid)->where('topicid',$topicid)->fetchOne();
        if (empty($rs)) {	    	
	    	return $this->insert(['uid'=>$uid,'topicid'=>$topicid,'praise_time'=>date('Y-m-d H:i:s',time())]);//increment_id
        }
        return 0;
    }

    public function forTopicDetail($uid,$topicid){
    	$rs = $this->getORM()->select('uid')->where('uid',$uid)->where('topicid',$topicid)->fetchOne();
    	return $rs;
    }
}