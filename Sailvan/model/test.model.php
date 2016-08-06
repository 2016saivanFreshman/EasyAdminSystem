<?php
/**
 * @name TestModel
 * @Description 测试模型
 * @author lzj
 */

class TestModel extends CommonModel {
    
    public function __construct() {
	parent::__construct();
    }
    
    
    public function getAll() {
    	echo "执行sql语句 , 需要获取table名字 , 运行getTableName() : ".$this->getTableName."来动态绑定sql要操作的表";
    	//这里的sql（）函数未定义，执行base.model里面的__call魔法函数（所有未定义的都执行__call()）
    	//这里的select是执行sql语句
	return $this->sql("SELECT * FROM ".$this->getTableName()." WHERE 1")->sort('ORDER BY id')->page('1')->perpage('1')->limit('100')->key('id')->select(array('mysql'));
    }
}
