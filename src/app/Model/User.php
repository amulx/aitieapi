<?php
namespace App\Model;

use PhalApi\Model\NotORMModel as NotORM;

class User extends NotORM {
    public function getListItems() {
        return  $this->getORM()
            ->select('*')
            ->where('age', 18)->where('name','phalapi')
            ->order('create_date DESC')
            ->fetchAll();
    }
    public function getNameById($id) {
        $row = $this->getORM($id)->select('name')->fetchRow();
        return !empty($row) ? $row['name'] : '';
    }

    public function querybysql($sql){
		$rows = $this->getORM()->queryAll($sql, array());
		return $rows;    	
    }

    /**
     * [judgeUsernameExist 判断用户名是否已存在]
     * @param  [string] $username [用户名]
     * @return [array]           [用户记录行]
     */
    public function judgeUsernameExist($username){
        return $this->getORM()->where('username = ?', $username)->fetchRow();
    }

    /**
     * [hasUserByIdPass 根据用户的id和用户密码判断是否有匹配的用户存在]
     * @param  [int]  $id       [用户id]
     * @param  [string]  $password [用户密码]
     * @return boolean           [description]
     */
    public function hasUserByIdPass($id,$password){
        return $this->getORM()->where('id = ?', $id)->where('password = ?', md5($password))->fetchRow();
    }    

    public function M_Login($username,$password){
        return $this->getORM()->where('username = ?', $username)->where('password= ?',$password)->fetchRow();
    }

    /**
     * [M_register 用户注册接口]
     * @param [type] $username    [description]
     * @param [type] $password    [description]
     * @param [type] $phonenumber [description]
     */
    public function M_register($username,$password,$phonenumber){
        $increment_id = $this->insert(
            [
                'username' => $username,
                'password' => $password,
                'phonenumber' => $phonenumber,
                'gold' => 10,
                'create_time' => date('Y-m-d H:i:s')
            ]
        );
        return $increment_id;
    }

    /**
     * [modifyPwd 找回密码]
     * @param  [string] $username    [用户名]
     * @param  [string] $phonenumber [手机号码]
     * @param  [string] $password    [密码]
     * @return [bool]              [更新结果]
     */
    public function modifyPwd($username,$phonenumber,$password){
        $rs = $this->getORM()->where('username', $username)->where('phonenumber', $phonenumber)->update(['password'=>md5($password)]);
        return $rs;     
    }

    /**
     * [modifyUserInfo 修改用户信息]
     * @param  [type] $uid         [用户id]
     * @param  [type] $password    [用户密码]
     * @param  [type] $username    [用户名]
     * @param  [type] $phonenumber [用户手机号码]
     * @param  [type] $userimage   [用户头像]
     * @param  [type] $wechat      [用户微信号]
     * @param  [type] $sex         [性别]
     * @return [bool]              [修改成功与否]
     */
    public function modifyUserInfo($uid,$password,$username,$phonenumber,$userimage,$wechat,$sex){
        $orm = $this->getORM()->where('id', $uid)->where('password', md5($password));

        $update_arr = [];
        if (!empty($username)) {
            $update_arr['username'] = $username;
        }

        if (!empty($phonenumber)) {
            $update_arr['phonenumber'] = $phonenumber;
        }

        if (!empty($userimage)) {
            $update_arr['userimage'] = $userimage;
        } 

        if (!empty($wechat)) {
            $update_arr['wechat'] = $wechat;
        }  

        if (!empty($sex)) {
            $update_arr['sex'] = $sex;
        }

        $rs = $orm->update($update_arr);
        return $rs;     
    }    
}