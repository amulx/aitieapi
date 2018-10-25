<?php
namespace App\Api;

use PhalApi\Api;
// use App\Model\User as model_user;
/**
 * 用户模块接口服务
 */
class User extends Api {
    public function getRules() {
        return array(
            'login' => array(
                'username' => array('name' => 'username', 'require' => true, 'min' => 1, 'max' => 50, 'desc' => '用户名'),
                'password' => array('name' => 'password', 'require' => true, 'min' => 4, 'max' => 12, 'desc' => '密码'),
            ),            
            'register' => array(
                'username' => array('name' => 'username', 'require' => true, 'min' => 1, 'max' => 20, 'desc' => '用户名'),
                'phonenumber' => array('name' => 'phonenumber', 'require' => true, 'min' => 1, 'max' => 11, 'desc' => '手机号码'),
                'password' => array('name' => 'password', 'require' => true, 'min' => 4, 'max' => 12, 'desc' => '密码'),
            ),
            'forgetPwd' => array(
                'username' => array('name' => 'username', 'require' => true, 'min' => 1, 'max' => 20, 'desc' => '用户名'),
                'phonenumber' => array('name' => 'phonenumber', 'require' => true, 'min' => 1, 'max' => 11, 'desc' => '手机号码'),
                'password' => array('name' => 'password', 'require' => true, 'min' => 4, 'max' => 12, 'desc' => '密码'),
            ),
            'userinfomodify' => array(
                'password' => array('name' => 'password', 'require' => true, 'min' => 4, 'max' => 12, 'desc' => '密码')
            ),
            'delPublish' => array(
                'topicids' => array('name' => 'topicids', 'require' => true, 'type' => 'array', 'format' => 'explode', 'separator' => ',','desc' => '需要删除的爱贴ids')
            ),
            'myCollection' => array(
                'page' => array('name' => 'page', 'type'=>'int','require' => true, 'min' => 1, 'desc' => '当前是第几页，初始为1'),
                'pagesize' => array('name' => 'pagesize', 'type'=>'int','require' => true, 'min' => 1, 'desc' => '每页显示多少条')
            ),
            'consumeHistory' => array(
                'page' => array('name' => 'page', 'type'=>'int','require' => true, 'min' => 1, 'desc' => '当前是第几页，初始为1'),
                'pagesize' => array('name' => 'pagesize', 'type'=>'int','require' => true, 'min' => 1, 'desc' => '每页显示多少条')
            ),
            'homePage' => array(
                'uid' => array('name' => 'uid', 'type'=>'int','require' => true, 'min' => 1, 'desc' => '发布者ID')
            ),
            'userPublish' => array(
                'uid' => array('name' => 'uid', 'type'=>'int','require' => true, 'min' => 1, 'desc' => '发布者ID'),
                'page' => array('name' => 'page', 'type'=>'int','require' => true, 'min' => 1, 'desc' => '当前是第几页，初始为1'),
                'pagesize' => array('name' => 'pagesize', 'type'=>'int','require' => true, 'min' => 1, 'desc' => '每页显示多少条')
            )
        );
    }
    /**
     * 登录接口
     * @desc 根据账号和密码进行登录操作
     * @return boolean is_login 是否登录成功
     * @return int user_id 用户ID
     */
    public function login() {
        // 1、模型实例化
        $model = new \App\Model\User();
        $userInfo = $model->M_Login($this->username,md5($this->password));       
        if (!empty($userInfo)) {
            $userInfo['token'] = \App\genToken('username='.$userInfo['username'].'&uid='.$userInfo['id'].'&sign='.time());
            return $userInfo;
        } else {
            throw new \PhalApi\Exception\InternalServerErrorException('用户名或者密码不存在', 10);
        }
    }

    /**
     * [register 用户注册接口]
     * @return [type] [description]
     */
    public function register(){
        // 1、模型实例化
        $model = new \App\Model\User();

        // 2、验证当前用户是否已存在
        $existArr = $model->judgeUsernameExist($this->username);

        // 3、实例入库
        if (empty($existArr)) {
            $return_id = $model->M_register($this->username,md5($this->password),$this->phonenumber);

            $return_data = [
                'id' => $return_id,
                'username' => $this->username,
                'phonenumber' => $this->phonenumber,
                'userimage' => '',
                'wechat' => '',
                'sex' => '',
                'gold' => 10
            ];
            if ($return_id) {
                return $return_data;
            }            
        } else {
            throw new \PhalApi\Exception\InternalServerErrorException('当前用户名已被使用', 10);
        }
    }

    public function userinfomodify(){
        $model = new \App\Model\User();

        // 1、接受参数
        $request = new \PhalApi\Request();
        $username = $request->get('username','');
        $phonenumber = $request->get('phonenumber','');
        $userimage = $request->get('userimage','');
        $wechat = $request->get('wechat','');
        $sex = $request->get('sex','');
        // 2、验证当前用户是否已存在（如果用户名不为空）
        if (!empty($username)) {
            $existArr = $model->judgeUsernameExist($username);
            if (!empty($existArr)) {
                throw new \PhalApi\Exception\InternalServerErrorException('当前用户名已被使用', 10);
            }
        }
        // 3、获取当前需要修改用户信息的用户ID
        $row = $model->hasUserByIdPass($GLOBALS['userInfo']['uid'],$this->password);

        if (empty($row)) {
            throw new \PhalApi\Exception\InternalServerErrorException('当前用户密码不正确', 10);
        }
        // 4、修改用户信息
        $rs = $model->modifyUserInfo($GLOBALS['userInfo']['uid'],$this->password,$username,$phonenumber,$userimage,$wechat,$sex);
        if ($rs >= 1) {
            // 修改成功
            return ['res'=>1];
        } else if ($rs === 0) {
            // 相同数据，无更新
            return ['res'=>0];
        } else if ($rs === false) {
            // 更新失败
            throw new \PhalApi\Exception\InternalServerErrorException('更新失败', 10);
        }          
    }

    public function forgetPwd(){
        $model = new \App\Model\User();
        // 判断当前用户是否存在数据库中
        if ($model->judgeUsernameExist($this->username)) {
            $rs = $model->modifyPwd($this->username,$this->phonenumber,$this->password);

            if ($rs >= 1) {
                // 修改成功
                return ['res'=>1];
            } else if ($rs === 0) {
                // 相同数据，无更新
                return ['res'=>0];
            } else if ($rs === false) {
                // 更新失败
                throw new \PhalApi\Exception\InternalServerErrorException('更新失败', 10);
            }  
        } else {
            throw new \PhalApi\Exception\InternalServerErrorException('当前用户名不存在', 10);
        }      
    }

    public function delPublish(){
        $TopicModel = new \App\Model\Topic();
        $rs = $TopicModel->delTopic($this->topicids,intval($GLOBALS['userInfo']['uid']));
        return ['rs'=>$rs];
    }

    /**
     * [myCollection 我的收藏]
     * @return [type] [description]
     */
    public function myCollection(){
        $TopicModel = new \App\Model\Topic();
        return $TopicModel->myCollections(intval($GLOBALS['userInfo']['uid']),$this->page,$this->pagesize);
    }

    /**
     * [consumeHistory 我的消费记录/购买历史]
     * @return [type] [description]
     */
    public function consumeHistory(){
        $TopicModel = new \App\Model\Topic();
        return $TopicModel->myConsume(intval($GLOBALS['userInfo']['uid']),$this->page,$this->pagesize);
    }

    /**
     * [myGold 我的金币]
     * @return [array] [description]
     */
    public function myGold(){
        $model = new \App\Model\User();
        $gold = $model->getGoldById(intval($GLOBALS['userInfo']['uid']));
        return ['gold'=>$gold];
    }

    /**
     * [homePage 个人主页]
     * @return [type] [description]
     */
    public function homePage(){
        $model = new \App\Model\User();
        return $model->forHomePage($this->uid);
    }

    /**
     * [userPublish 用户发布的贴吧]
     * @return [type] [description]
     */
    public function userPublish(){
        $TopicModel = new \App\Model\Topic();
        return $TopicModel->userPublish($this->uid,$this->page,$this->pagesize);
    }
}