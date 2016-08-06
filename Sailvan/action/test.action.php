<?php
/**
 * @name TestAct
 * @description TestAct测试例子
 * @author lzj
 */
class TestAct extends CommonAct {
    
    public function __construct() {
	parent::__construct();
	$this->perpage = 3;
    }
    
    /**
     * @description 添加操作
     * @author lzj
     */
    public function act_save() {
	$date = array(
		'test_name'	 => isset($_POST['name']) ? $_POST['name'] : '',
		'test_value'  => isset($_POST['value']) ? $_POST['value'] : '',
		'test_status' => isset($_POST['status']) ? $_POST['status'] : 0,
	);
	
	return M($this->act_getModel())->insertData($date);
    }
    
    /**
     * @description 修改操作
     * @author lzj
     */
    public function act_update($id){
	$date = array(
		'test_name'	 => isset($_POST['name']) ? $_POST['name'] : '',
		'test_value'  => isset($_POST['value']) ? $_POST['value'] : '',
		'test_status' => isset($_POST['status']) ? $_POST['status'] : 0,
	);
	
	return M($this->act_getModel())->updateData($id, $date);
    }
    
    /**
     * @description 删除操作
     * @author lzj
     */
    public function act_delDate($id){
	return M($this->act_getModel())->deleteData($id);
    }


    /**
     * @description 显示列表
     * @author lzj
     * @return array 列表行数
     */
    public function act_getDataList() {
	return M($this->act_getModel())->getList('', '*', array('id' => 'DESC'), $this->page, $this->perpage);
    }
    
    /**
     * @description 获取行数
     * @author lzj
     * @return array 行总数
     */
    public function act_getDataCount(){
    	return M($this->act_getModel())->getListCount('');
    }
    
    /**
     * @description 设置页数
     * @author lzj
     * @return int 行数
     */
    public function act_getPerpage(){
	return $this->perpage;
    }
    
    /**
     * @description 获取单行
     * @author lzj
     * @return array 
     */
    public function act_getInfo() {
	$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
	return M($this->act_getModel())->get($id);
    }
    
    public function act_getAll() {
        
	return M($this->act_getModel())->getAll();
    }
}
