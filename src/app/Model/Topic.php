<?php
namespace App\Model;

use PhalApi\Model\NotORMModel as NotORM;

class Topic extends NotORM {
	/**
	 * [getListItems 爱贴首页]
	 * @return [type] [description]
	 */
    public function getListItems($uid,$title,$pages,$length,$type) {
        $model_obj = $this->getORM()->select('*');

        if (!empty($uid)) {
        	$model_obj->where('uid', $uid);
        }

        if (!empty($title)) {
        	$model_obj->where('title', $title);
        }
        $iOffset = ($pages - 1) * $length;
        // limit(开始位置，数量)
        return $model_obj->order($type. ' DESC')->limit($iOffset,$length)->fetchAll();
    }

    public function mPublish($data){
    	$data['create_time'] = date('Y-m-d H:i:s');
    	return $this->insert($data);//increment_id
    }

    public function getRow(){
    	
    }
}