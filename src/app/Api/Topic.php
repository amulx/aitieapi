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
            	'tiebaId' => array('name' => 'tiebaId', 'type'=>'int','require' => true, 'min' => 1, 'desc' => '贴吧ID'),
            )
        );
    }	

	public function index(){
		// print_r($GLOBALS['userInfo']);
		$model = new \App\Model\Topic();

		$request = new \PhalApi\Request();
		$title = $request->get('title','');
		$type = $request->get('type','create_time'); // 排序方式
		$page = $request->get('page',1); //  当前页数
		$pagesize = $request->get('pagesize',1); // 每页显示多少条
		return $model->getListItems('',$title,$page,$pagesize,$type);
	}

	/**
	 * [myPublish 我的发布]
	 * @return [type] [description]
	 */
	public function myPublish(){
		$model = new \App\Model\Topic();

		$request = new \PhalApi\Request();
		$title = $request->get('title','');

		return $model->getListItems($GLOBALS['userInfo']['uid'],$title);
	}	

	/**
	 * [detail 爱贴详情]
	 * @return [type] [description]
	 */
	public function detail(){
		$model = new \App\Model\Topic();
		return $model->get($this->tiebaId);
	}

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
}