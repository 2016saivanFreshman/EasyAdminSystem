<?php
/**
 * @name TestsModel
 * @Description 增删改查测试Model
 * @author JBF
 */
class TestsModel extends CommonModel{
	public function __construct(){
		parent::__construct();
	}
	
	public function checkName($name, $id) {
		if (empty($name)) return false;
		else {
			$sql = "SELECT `id` FROM `".$this -> getTableName()."` WHERE `name` = '". $name ."'";
			if (!empty($id)) {
				$sql .= " AND id='". $id ."'";
			}
			$res = $this -> dbConn -> fetch_first($sql);
			
			unset($sql);
			
			if (empty($res) || (!empty($id) && !empty($res))) return true;
			else {
				unset($res);
				return false;
			}
		}
	}

	public function deleteRow($id) {
		if (empty($id)) return false;
		else {
			$data = array('id' => $id, 'is_delete' => 0);
			return $this -> save($data);
		}
	}
}
?>