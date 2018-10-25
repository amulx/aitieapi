<?php
namespace App\Model;

use PhalApi\Model\NotORMModel as NotORM;

class Topic extends NotORM {
	/**
	 * [getListItems 爱贴首页]
	 * @return [type] [description]
	 */
    public function getListItems($uid,$title,$pages,$length,$type) {
        $sql = 'select tbl_topic.*,tbl_user.username,tbl_user.userimage from tbl_topic LEFT JOIN tbl_user ON tbl_topic.uid = tbl_user.id WHERE 1=1';
        $where = '';
        $where_arr = [];

        if (!empty($uid)) {
        	$where .= ' uid = ? ';
        	$where_arr[] = $uid;
        }

        if (!empty($title)) {
        	$where .= ' title like ? ';
        	$where_arr[] = '%' . $title . '%';
        }

        if (empty($where)) {
        	$sql .= ' order by '. $type . ' DESC limit ?,?';
        } else {
        	$sql .= ' and '. $where . ' order by '.$type. ' DESC limit ?,?';
        }
        
        // echo $sql;
      
        
        $where_arr[] = ($pages - 1) * $length;
        $where_arr[] = intval($length);

        // $iOffset = ($pages - 1) * $length;
        // limit(开始位置，数量)
        // return $model_obj->order($type. ' DESC')->limit($iOffset,$length)->fetchAll();
		return $this->getORM()->queryAll($sql, $where_arr);
    }
    /**
     * [getListItems 爱贴首页]
     * @return [type] [description]
     */
    public function getListItemsPdo($uid,$title,$pages,$length,$type) {

        $sql = 'select tbl_topic.*,UNIX_TIMESTAMP(tbl_topic.create_time) as orderTime,tbl_user.username,tbl_user.userimage from tbl_topic LEFT JOIN tbl_user ON tbl_topic.uid = tbl_user.id WHERE 1=1 order by :orderby DESC limit :offset,:len';

        $db = new \PDO('mysql:host=127.0.0.1;port=3306;dbname=aitie', 'root', '');
        $db->query('set names utf8');
        $stmt = $db->prepare($sql);

        if ($type == 'create_time') {
            $type = '`orderTime`';
        } 
        $iOffset = ($pages - 1) * $length;

        $stmt->bindParam(':orderby', $type);
        $stmt->bindParam(':offset', $iOffset,\PDO::PARAM_INT);
        $stmt->bindParam(':len', $length,\PDO::PARAM_INT);

        $stmt->execute();
        $row = $stmt->fetchAll(\PDO::FETCH_ASSOC); 
        return $row;
    }
    /**
     * [mPublish 发布贴吧]
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public function mPublish($data){
    	$data['create_time'] = date('Y-m-d H:i:s');
    	return $this->insert($data);//increment_id
    }

    /**
     * [getRow 贴吧详情]
     * @param  [int] $topicid [贴吧id]
     * @return [array]          [当前记录行]
     */
    public function getRow($topicid){
    	$sql = 'select tbl_topic.*,tbl_user.username,tbl_user.userimage from tbl_topic LEFT JOIN tbl_user ON tbl_topic.uid = tbl_user.id WHERE tbl_topic.topicid =? limit 1';
    	return $this->getORM()->queryRows($sql, [intval($topicid)]);
    }

    public function getUidByTopicId($topicid){
        $sql = 'select uid from tbl_topic where topicid = ? limit 1';
        return $this->getORM()->queryRows($sql, [intval($topicid)])[0]['uid'];
    }

    /**
     * [publishError 贴吧报错]
     * @param  [string] $type    [报错类型]
     * @param  [int] $topicid [贴吧id]
     * @return [array]          [返回微信报错数量和手机号报错数量]
     */
    public function publishError($type,$topicid){
        if ( $type == 'wechat' ) {
            $column = 'wechatError';
        } else {
            $column = 'phoneError';
        }

        $rs = $this->getORM()->where('topicid', $topicid)->update(array($column => new \NotORM_Literal($column . "+ 1")));
        $row = $this->getORM()->select('wechatError, phoneError')->where('topicid',$topicid)->fetchOne();
        return $row;
    }

    /**
     * [doCollect 贴吧收藏]
     * @param  [string] $type    [收藏 or 取消收藏]
     * @param  [int] $topicid [贴吧ID]
     * @return [int]          [返回影响记录行]
     */
    public function doCollect($type,$topicid){
        if ( $type == 'Y' ) {
            $rs = $this->getORM()->where('topicid', $topicid)->update(array('collectnum' => new \NotORM_Literal("collectnum+1")));
        } else {
            $rs = $this->getORM()->where('topicid', $topicid)->update(array('collectnum' => new \NotORM_Literal("collectnum-1")));
        }
        return $rs;
    }

    /**
     * [doPriase 爱贴点赞]
     * @param  [int] $topicid [爱贴ID]
     * @return [返回记录行]          [description]
     */
    public function doPriase($topicid){
        $rs = $this->getORM()->where('topicid', $topicid)->update(array('praise' => new \NotORM_Literal("praise+1")));
        return $rs;
    }

    public function delTopic($topic_id_arr,$uid){
        $rs = $this->getORM()->where('topicid', $topic_id_arr)->where('uid',$uid)->delete();
        return $rs;        
    }

    /**
     * [myCollections 我的收藏]
     * @param  [int] $uid      [用户ID]
     * @param  [int] $page     [页数]
     * @param  [int] $pagesize [每页显示多少条]
     * @return [type]           [description]
     */
    public function myCollections($uid,$page,$pagesize){
        $sql = 'select tbl_topic.*,tbl_user.username from tbl_collect left join tbl_topic on tbl_topic.topicid = tbl_collect.topicid left join tbl_user on tbl_user.id=tbl_collect.uid where tbl_collect.uid=? order by tbl_collect.collect_time limit ?,?';      
        $offset = ($page - 1) * $pagesize;

        return $this->getORM()->queryAll($sql, [$uid,$offset,intval($pagesize)]);
    }

    /**
     * [myConsume 我的消费历史]
     * @param  [int] $uid      [用户ID]
     * @param  [int] $page     [页数]
     * @param  [int] $pagesize [每页显示多少条]
     * @return [type]           [description]
     */
    public function myConsume($uid,$page,$pagesize){
        $sql = 'select tbl_topic.*,tbl_user.username from tbl_consume left join tbl_topic on tbl_topic.topicid=tbl_consume.topicid left join tbl_user on tbl_user.id=tbl_consume.uid where tbl_consume.uid=? limit ?,?';      
        
        $offset = ($page - 1) * $pagesize;

        return $this->getORM()->queryAll($sql, [$uid,$offset,intval($pagesize)]);
    }

    public function userPublish($uid,$pages,$length){
        $sql = 'select tbl_topic.*,tbl_user.username,tbl_user.userimage from tbl_topic LEFT JOIN tbl_user ON tbl_topic.uid = tbl_user.id WHERE uid = ? order by create_time DESC limit ?,?';      
        $where_arr[] = $uid;
        $where_arr[] = ($pages - 1) * $length;
        $where_arr[] = intval($length);
        return $this->getORM()->queryAll($sql, $where_arr);
    }
}