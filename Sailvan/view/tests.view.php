<?php
/**
 * @name TestsView
 * @description 测试CURD示例 View层
 * @author JBF
 */

class TestsView extends BaseView{
    /**
     * 构造函数
     */
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * @description 列表页&查询结果页
     * @author jbf
     */
    public function view_index() {
    	$this->view_listShow();
    	$this -> smarty -> display('tests/index.html');
    }
    
    /**
     * @description 新增页&编辑页
     * @author jbf
     */
    public function view_edit() {
    	$id = isset($_REQUEST['id']) ? $_REQUEST['id'] : 0;
    	
    	$info = A($this -> getAction()) -> act_getInfo($id);
    	$this -> smarty -> assign('tests', $info);
    	
    	$this -> smarty -> display("tests/edit.html");
    }
    
    /**
     * @description 检查名称是否重复
     * @author jbf
     */
    public function view_checkName() {
    	$name = isset($_REQUEST['name']) ? trim($_REQUEST['name']) : '';
    	$id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
    	if (!empty($name)) {
    		$act = A($this -> getAction());
    		
    		if ($act -> act_checkName($name, $id)) {
    			echo 'ok';
    		} else {
    			echo 'exist';
    		}
    		
    		unset($act);
    	}
    }
    
    /**
     * @description 保存数据（新增、修改）
     * @author jbf
     */
    public function view_save() {
    	$sts = A($this -> getAction()) -> act_save();
    	
    	if (empty($sts)) {
    		$this -> error("保存失败！",'', 3);
    	} else {
    		$this -> success("保存成功！", '/index.php?mod=tests&act=index');
    	}
    }
    
    /**
     * @description 删除数据
     * @author jbf
     */
    public function view_delete() {
    	$sts = A($this -> getAction()) -> act_delete();
    	
    	if (empty($sts)) {
    		$this -> error("删除失败！",'', 1);
    	} else {
    		$this -> success("删除成功！", '/index.php?mod=tests&act=index');
    	}
    }
}
?>