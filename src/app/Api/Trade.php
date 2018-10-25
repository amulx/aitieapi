<?php
namespace App\Api;

use PhalApi\Api;
// use App\Model\User as model_user;
/**
 * 用户交易接口服务
 */
class Trade extends Api {
	public function getRules() {
		return array(
			'consume' => array(
				'type' => array('name' => 'type','type'=>'enum','range'=>array('wechat','phone'),'require' => true,'desc' => '类型'),
				'topicid' => array('name' => 'topicid', 'type'=>'int','require' => true, 'min' => 1, 'desc' => '贴吧ID'),
				'gold' => array('name' => 'gold', 'type'=>'int','require' => true, 'min' => 1, 'desc' => '消费金币'),
				'uid' => array('name' => 'uid', 'type'=>'int','require' => true, 'min' => 1, 'desc' => '贴吧所有者ID')			
			)
		);
	}

	public function consume(){//如果是自己的贴子怎么处理
		// 0、判断是否是自己的贴子
		$Topicmodel = new \App\Model\Topic();
		$own_topic_uid = $Topicmodel->getUidByTopicId($this->topicid);
		if ($own_topic_uid == $GLOBALS['userInfo']['uid']) {
			throw new \PhalApi\Exception\InternalServerErrorException('不支持购买自己的爱贴', 10);
		}
		// 判断是否已经购买过了
		$consumeModel = new \App\Model\Consume();
		if ($consumeModel->isExist(intval($GLOBALS['userInfo']['uid']),$this->topicid)) {
			throw new \PhalApi\Exception\InternalServerErrorException('您已购买了此类商品了', 10);
		}
		// 1、判断当前购买者是否有足够的金币
		$usrModel = new \App\Model\User();
		$owngold = $usrModel->getGoldById($GLOBALS['userInfo']['uid']);
		if ($owngold < $this->gold) {
			throw new \PhalApi\Exception\InternalServerErrorException('当前金币不足'.$this->gold, 10);
		}

		// Step 1: 开启事务
		\PhalApi\DI()->notorm->beginTransaction('db_master');
		try {
			// Step 2: 数据库操作
			// 2、减去购买者当前所消费的金币
			$usrModel->modifGoldById(intval($GLOBALS['userInfo']['uid']),false,$this->gold);
			// 3、增加相应的金币到爱贴所有者的账号中去
			$usrModel->modifGoldById($this->uid,true,$this->gold);
			// 4、追加消费记录到对应的消费表中去
	    	
	    	$consumeModel->addConsumeLog(intval($GLOBALS['userInfo']['uid']),$this->topicid,$this->gold,'-',$this->type);
	    	$consumeModel->addConsumeLog($this->uid,$this->topicid,$this->gold,'+',$this->type);

		    // Step 3: 提交事务/回滚
		    \PhalApi\DI()->notorm->commit('db_master');
		    return ['consume_id'=>intval($GLOBALS['userInfo']['uid']),'uid'=>$this->uid,'gold'=>$this->gold];
	    } catch (Exception $e) {
		    \PhalApi\DI()->notorm->rollback('db_master');
		    throw new \PhalApi\Exception\InternalServerErrorException('当前金币不足', 10);
		}
	}
}