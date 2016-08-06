<?php
/**
 * @name TestsAct
 * @description 测试增删改查Action
 * @author JBF
 */
class TestsAct extends CommonAct{
	private  $where;			//临时保存查询参数
	
    public function __construct(){
    	$this -> where = array(
    			'name' => isset($_REQUEST['name']) ? trim($_REQUEST['name']) : '',
    			//'is_delete'	=> 0
    	);
    	
        parent::__construct();
    }
    
    /**
     * 获取单挑数据信息
     * @param int $id
     * @return array|multitype:number string
     */
    public function act_getInfo($id) {
    	$id = intval($id);
    	if (!empty($id)) {
    		$mod = M($this -> act_getModel());
    		$data = $mod -> get($id);
    		unset($mod);
    		
    		if (!empty($data)) {
    			return $data;
    		}
    	}
    	
    	return array('id' => 0, 'name' => '', 'value' => '');
    }
    
    /**
     * 检查名称 Action
     * @param string $name
     * @param int $id
     * @return boolean
     */
    public function act_checkName($name, $id) {
    	if (empty($name)) return false;
    	else {
    		$name = strtoupper(trim($name));
    		return M($this -> act_getModel()) -> checkName($name, $id);
    	}
    	
    }
    
    /**
     * @description 保存数据Action
     * @author jbf
     */
    public function act_save() {
    	$data = array(
    			'id'		=> isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0,
    			'name'		=> isset($_REQUEST['name']) ? strtoupper(trim($_REQUEST['name'])) : '',
    			'value'		=> isset($_REQUEST['zhi']) ? trim($_REQUEST['zhi']) : ''
    	);
    	
    	
    	if (empty($data['id'])) $data['addtime'] = time();
    	return  M($this -> act_getModel()) -> save($data);
    }
    
    /**
     * 获取列表数据
     * @author jbf
     */
    public function act_getDataList(){
	
    	return M($this -> act_getModel()) -> getList($this -> where, "*", array('id' => 'DESC'), $this -> page, $this -> perpage);
    }
    
    /**
     * 获取总的记录个数
     * 
     * @author jbf
     */
    public function act_getDataCount(){
    	return M($this->act_getModel())->getListCount($this -> where);
    }
    
    /**
     * @description 删除数据
     * @return boolean
     * @author jbf
     */
    public function act_delete() {
    	$id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
    	
    	if (!empty($id)) {
    		return M($this->act_getModel()) -> deleteRow($id);
    	}
    	
    	return false;
    }
}

?>