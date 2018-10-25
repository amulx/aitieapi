<?php
namespace App\Model;

use PhalApi\Model\NotORMModel as NotORM;
/**
 * 收藏
 */
class Collect extends NotORM {

	public function isCollect($uid,$topicid) {        
        $rs = $this->getORM()->select('uid')->where('uid',$uid)->where('topicid',$topicid)->fetchOne();
        if (empty($rs)) {	    	
	    	return $this->insert(['uid'=>$uid,'topicid'=>$topicid,'collect_time'=>date('Y-m-d H:i:s',time())]);//increment_id
        }
        return 0;
    }

	public function unCollect($uid,$topicid) {
        return $this->getORM()->where('uid',$uid)->where('topicid',$topicid)->delete();
    }

    public function forTopicDetail($uid,$topicid) {
        $rs = $this->getORM()->select('uid')->where('uid',$uid)->where('topicid',$topicid)->fetchOne();
        return $rs;
    }
}