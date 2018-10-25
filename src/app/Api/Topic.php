<?php
namespace App\Api;

use PhalApi\Api;
// use App\Model\User as model_user;
/**
 * 用户模块接口服务
 */
class Topic extends Api {

    public function getRules() {
        return array(
            'publish' => array(
                'title' => array('name' => 'title', 'require' => true, 'min' => 1, 'desc' => '标题'),
                'location' => array('name' => 'location', 'require' => true, 'min' => 1, 'max' => 60, 'desc' => '地址'),
                'wechat' => array('name' => 'wechat', 'require' => true, 'min' => 1, 'max' => 60, 'desc' => '微信'),
                'wechatgold' => array('name' => 'wechatgold', 'type'=>'int','require' => true, 'min' => 1, 'desc' => '微信金币'),
                'phone' => array('name' => 'phone', 'require' => true, 'min' => 1, 'max' => 12, 'desc' => '联系电话'),
                'phonegold' => array('name' => 'phonegold', 'type'=>'int','require' => true, 'min' => 1, 'desc' => '密码'),
                'content' => array('name' => 'content', 'require' => true, 'min' => 1, 'max' => 900, 'desc' => '详情'),
                'imagelist' => array('name' => 'imagelist', 'require' => false, 'desc' => '图片集'),
            ),
            'detail' => array(
            	'topicid' => array('name' => 'topicid', 'type'=>'int','require' => true, 'min' => 1, 'desc' => '贴吧ID')
            ),
            'collect' => array(
            	'topicid' => array('name' => 'topicid', 'type'=>'int','require' => true, 'min' => 1, 'desc' => '贴吧ID'),
            	'isCollection' => array('name' => 'isCollection','require' => true,'desc' => '是否收藏')
            ),
            'reportError' => array(
            	'type' => array('name' => 'type','type'=>'enum','range'=>array('wechat','phone'),'require' => true,'desc' => '类型'),
            	'topicid' => array('name' => 'topicid', 'type'=>'int','require' => true, 'min' => 1, 'desc' => '贴吧ID')
            ),
            'index' => array(
            	'page' => array('name' => 'page', 'type'=>'int','require' => true, 'min' => 1, 'desc' => '当前是第几页，初始为1'),
            	'pagesize' => array('name' => 'pagesize', 'type'=>'int','require' => true, 'min' => 1, 'desc' => '每页显示多少条'),
            	'type' => array('name' => 'type', 'type'=>'enum','range'=>array('create_time','praise'),'require' => true, 'desc' => '排序方式'),
            ),
            'priase' => array(
            	'topicid' => array('name' => 'topicid', 'type'=>'int','require' => true, 'min' => 1, 'desc' => '贴吧ID')
            ),
            'myPublish' => array(
            	'page' => array('name' => 'page', 'type'=>'int','require' => true, 'min' => 1, 'desc' => '当前是第几页，初始为1'),
            	'pagesize' => array('name' => 'pagesize', 'type'=>'int','require' => true, 'min' => 1, 'desc' => '每页显示多少条')
            )
        );
    }	

	public function index(){
		// print_r($GLOBALS['userInfo']);
		$model = new \App\Model\Topic();

		$request = new \PhalApi\Request();
		$title = $request->get('title','');
		/*
		$type = $request->get('type','create_time'); // 排序方式
		$page = $request->get('page',1); //  当前页数
		$pagesize = $request->get('pagesize',1); // 每页显示多少条
		*/
		return $model->getListItems('',$title,$this->page,intval($this->pagesize),$this->type);
	}

	/**
	 * [myPublish 我的发布]
	 * @return [type] [description]
	 */
	public function myPublish(){
		$model = new \App\Model\Topic();

		$request = new \PhalApi\Request();
		$title = $request->get('title','');

		return $model->getListItems($GLOBALS['userInfo']['uid'],$title,$this->page,intval($this->pagesize),'create_time');
	}	

	/**
	 * [detail 爱贴详情]
	 * @return [type] [description]
	 */
	public function detail(){
		// 1、获取当前贴吧的详细信息
		$model = new \App\Model\Topic();
		$topic_row = $model->getRow($this->topicid)[0];

		// 获取token
		$request = new \PhalApi\Request();
		$token = $request->get('token',''); 
		// 2、获取收藏点赞信息	
		if (empty($token)) {
			$topic_row['isBuyWechat'] = false;
			$topic_row['isBuyPhone'] = false;
			$topic_row['isPraise'] = false;
			$topic_row['isCollection'] = false;			
		} else {
			\App\tokenCheck($token);
			if (empty($GLOBALS['userInfo'])) {
				$topic_row['isBuyWechat'] = false;
				$topic_row['isBuyPhone'] = false;
				$topic_row['isPraise'] = false;
				$topic_row['isCollection'] = false;	
			} else {
				//  到消费表中查找是否有购买记录
				$consumeModel = new \App\Model\Consume();
				$consume_arr = $consumeModel->forTopicDetail(intval($GLOBALS['userInfo']['uid']),$this->topicid)[0];
				if (empty($consume_arr)) {
					$topic_row['isBuyWechat'] = false;
					$topic_row['isBuyPhone'] = false;
				} else {
					$topic_row['isBuyWechat'] = boolval($consume_arr['wechat']);
					$topic_row['isBuyPhone'] = boolval($consume_arr['phone']);
				}

				//  到点赞表中查看是否有点赞记录
				$praiseModel = new \App\Model\Praise();
				$praise_res = $praiseModel->forTopicDetail(intval($GLOBALS['userInfo']['uid']),$this->topicid);
				if (empty($praise_res)) {
					$topic_row['isPraise'] = false;
				} else {
					$topic_row['isPraise'] = true;
				}				
				//  到收藏表中查看是否有收藏记录
				$collectModel = new \App\Model\Collect();
				$collect_res = $collectModel->forTopicDetail(intval($GLOBALS['userInfo']['uid']),$this->topicid);
				if (empty($collect_res)) {
					$topic_row['isCollection'] = false;	
				} else {
					$topic_row['isCollection'] = true;	
				}				
			}			
		}	
		return $topic_row;
	}

	/**
	 * [publish 发布贴子]
	 * @return [type] [description]
	 */
	public function publish(){
		$data = [
			'uid' => $GLOBALS['userInfo']['uid'],
			'title' => $this->title,
			'location' => $this->location,
			'wechat' => $this->wechat,
			'wechatgold' => $this->wechatgold,
			'phone' => $this->phone,
			'phonegold' => $this->phonegold,
			'content' => $this->content,
			'imagelist' => $this->imagelist
		];
		$model = new \App\Model\Topic();
		$resId = $model->mPublish($data);
		if ($resId) {
			return ['content'=>$this->title.'发布成功'];
		} else {
			throw new \PhalApi\Exception\InternalServerErrorException($this->title.'发布失败', 10);
		}
	}

	/**
	 * [collect 收藏与取消收藏]
	 * @return [type] [description]
	 */
	public function collect(){
		if( !in_array($this->isCollection, ['Y','N']) ){
			throw new \PhalApi\Exception\InternalServerErrorException('参数错误', 10);
		} 

		$model = new \App\Model\Collect();
		$TopicModel = new \App\Model\Topic();
		if ( $this->isCollection == 'Y' ) {
			// 1、在 收藏表 中增加 用户收藏记录
			$res = $model->isCollect(intval($GLOBALS['userInfo']['uid']),intval($this->topicid));
		} else {
			// 1、在 收藏表 中删除 用户收藏记录
			$res = $model->unCollect(intval($GLOBALS['userInfo']['uid']),intval($this->topicid));

		}
		// 2、在 贴吧表 中对该贴收藏数对应操作
		$TopicModel->doCollect($this->isCollection,intval($this->topicid));
		return ['rs'=>$res];
	}

	/**
	 * [priase 爱贴点赞]
	 * @return [type] [description]
	 */
	public function priase(){
		$TopicModel = new \App\Model\Topic();
		$doPriase_rs = $TopicModel->doPriase($this->topicid);

		$praiseModel = new \App\Model\Praise();
		$praiseModel->inPraise(intval($GLOBALS['userInfo']['uid']),intval($this->topicid));

		return ['rs'=>$doPriase_rs];
	}

	/**
	 * [reportError 贴吧报错]
	 * @return [type] [description]
	 */
	public function reportError(){
		$model = new \App\Model\Topic();

		return $model->publishError($this->type,intval($this->topicid));
	}
}