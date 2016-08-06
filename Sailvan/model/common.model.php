<?php
/*
*模板通用操作类
*@add by : linzhengxiang ,date : 20140525
*/
class CommonModel extends ValidateModel{
	
	public function __construct(){
		parent::__construct();
	}
	/**
	 * 插入信息
	 * @param array $data
	 * @author lzx
	 */
	public function inserData($data){
		$fdata = $this->formatInsertField($this->getTableName(), $data);
		
		if ($fdata===false){
			self::$errMsg = $this->validatemsg;
			return false;
		}
		if ($this->checkIsExists($fdata)){
			return false;
		}
		print_r(array2sql($fdata));
		return $this->sql("INSERT INTO ".$this->getTableName()." SET ".array2sql($fdata))->insert();
	}
	
	/**
	 * 根据id更新信息
	 * @param array $data
	 * @author lzx
	 */
	public function updateData($id, $data){
		$id = intval($id);
		if ($id==0){
		        self::$errMsg[10110] = get_promptmsg(10110,'更新');
			return false;
		}
		
		$fdata = $this->formatUpdateField($this->getTableName(), $data);
		if ($fdata===false){
			self::$errMsg = $this->validatemsg;
			return false;
		}

		return $this->sql("UPDATE ".$this->getTableName()." SET ".array2sql($fdata)." WHERE id={$id}")->update();
	}
	/**
	 * 获取信息
	 * @author wcx
	 */
	public function getData($fieldArr='*', $whereArr='1', $sort=' order by id desc ',$page=1, $perpage=20){
		
	    if(empty($fieldArr)){
	        $field  =   "*";
	    }else{
	        if(is_array($fieldArr)){//判断是否array
    	        $field =   implode(',', $fieldArr);//

	        }else{
	            $field =   $fieldArr;
	        }
	    }
	    if(empty($whereArr)){
	        $where  =   "1";
	    }else{
	        if(is_array($whereArr)){
	            $whereArr  =   $this->formatWhereField($this->getTableName(), $whereArr);

	            if(empty($whereArr)){
	                $this::$errMsg =   $this->validatemsg;
	            	return false;
	            }
	            $where = 1;
	            foreach($whereArr as $k=>$v){
	                $where .=  " AND `$k`='$v'";
	            }
	        }else{
	            $where =   $whereArr;
	        }
	    }

	    $sql = 'SELECT '.$field.' FROM `'.$this->getTableName().'` WHERE '.$where;
	    return $this->sql($sql)->sort($sort)->page($page)->perpage($perpage)->select(array('mysql'));
	}
	/**
	 *  获取单个信息
	 *  @author wcx
	*/
	public function getSingleData($fieldArr='*',$whereArr='1'){
	    $ret   =   $this->getData($fieldArr,$whereArr);
	    if(empty($ret)){
	    	return $ret;
	    }else{
	    	return $ret[0];
	    }
	}
	/**
	 * 更新多条信息
	 * @author wcx
	 */
	public function updateDataWhere($data,$whereArr='0'){
	    if(!is_array($whereArr)){
	        $where =   '';
	        $where .=  $whereArr;  
	    }else{
	        $where =   '1';
    	    foreach($whereArr as $k=>$v){
    	        $where .=  " AND $k='".mysql_real_escape_string($v)."'";
    	    }
	    }
	    $fdata = $this->formatUpdateField($this->getTableName(), $data);
	    
	    if ($fdata===false){
	        self::$errMsg = $this->validatemsg;
	        return false;
	    }
	    return $this->sql("UPDATE ".$this->getTableName()." SET ".array2sql($fdata)." WHERE $where")->update();
	}
	public function replaceData($id, $data, $column='id'){
		$id = intval($id);
		$column = addslashes($column);
		if ($id==0){
		        self::$errMsg[10110] = get_promptmsg(10110,'更新或插入');
			return false;
		}
		$fdata = $this->formatUpdateField($this->getTableName(), $data);
		if ($fdata===false){
			self::$errMsg = $this->validatemsg;
			return false;
		}
		if (!$this->checkIsExists($fdata)){
			return false;
		}
		$check = $this->sql("SELECT COUNT(*) AS count FROM {$this->getTableName()} WHERE {$column}={$id}")->count();
		if ($check==0) {
			$fdata[$column] = $id;
			return $this->insertData($fdata);
		}else{
			return $this->sql("UPDATE ".$this->getTableName()." SET ".array2sql($fdata)." WHERE {$column}={$id}")->update();
		}
	}
	
	/**
	 * 删除信息
	 * @param array $data
	 * @author lzx
	 */
	public function deleteData($id){
		$id = intval($id);
		if ($id==0){
		    	self::$errMsg[10110] = get_promptmsg(10110,'删除');
			return false;
		}
		return $this->sql("UPDATE ".$this->getTableName()." SET is_delete=1 WHERE id={$id}")->delete();
	}
	
	/*
	 * 获取记录条数
	 */
	public function getDataCount($whereArr='1'){
	    $num   =   $this->getSingleData("COUNT(*) as count",$whereArr);
	    if(empty($num)){
	    	return 0;
	    }else{
	    	return $num['count'];
	    }
	}
	/**
	 * sql记录条数统计
	 * @param array $data
	 * @author lzx
	 */
	public function replaceSql2Count($sql){
		if (preg_match("/(`[a-z]*`)\.\*/", $sql)>0){
			return preg_replace("/(`[a-z]*`)\.\*/", "COUNT(\$1.id) AS count", $sql);
		}else if(preg_match("/^SELECT\s*\*/i", $sql)>0){
			return preg_replace("/^SELECT\s*\*/i", "SELECT COUNT(*) AS count", $sql);
		}else{
		    	self::$errMsg[10111] = get_promptmsg(10111);
			return false;
		}
	}
	
	/**
	 * 数据保存方法（方法根据数据自动确定采取更新还是修改已有条目）
	 * @param array $data
	 * @return boolean
	 * @author jbf
	 */
	public function save($data) {
		$sql = $this -> buildSql($data);
		return $this ->dbConn -> query($sql);
	}
	
	/**
	 * 根据ID获取单个条目
	 * @param int id
	 * @param array field
	 * @return array
	 * @author jbf
	 */
	public function get($id, $field = array()) {
		if (!empty($id)) {
			$fieldStr = empty($field) ? '*' : implode(',', $field);
			$sql = "SELECT ".$fieldStr." FROM `".$this -> getTableName()."` WHERE `id` = '".$id."'";
			
			$data = $this -> dbConn -> fetch_first($sql);
			
			unset($fieldStr);
			unset($sql);
			if (!empty($data)) {
				return $data;
			} else {
				return null;
			}
		} else {
			return false;
		}
	}
	
	/**
	 * @description 构造更新语句
	 * @param unknown $data
	 * @return string|boolean
	 * @author jbf
	 */
	public function buildSql($data) {
		if (is_array($data)) {

			$id = $data['id'];
			
			unset($data['id']);
				
			if (!empty($id)) {
				return "UPDATE `".$this->getTableName()."` SET ".array2sql($data)." WHERE `id` = '".$id."'";
			} else {
				return "INSERT INTO ".$this->getTableName()." SET ".array2sql($data);
			}
		}
	
		return false;
	}
	
	/**
	 * 根据条件获取数据表中的List数据
	 * @param array|string $where
	 * @param array|string $fields
	 * @param array|string $sorts
	 * @param number $page
	 * @param number $pageSize
	 */
	public function getList($where = array(), $fields = array(), $sorts = array(), $page = 1, $pageSize = 20) {
		$field = self::buildField($fields);
		$where = buildWhereSql($where);
		$sort = self::buildSort($sorts);
		$limit = self::buildLimit($page, $pageSize);
		
		$sql = 'SELECT ' .$field. ' FROM `' .$this -> getTableName(). '` ' .(empty($where) ? '' : ('WHERE '.$where)). $sort.' '. $limit;
		return $this -> dbConn -> fetch_all($sql);
	}
	
	public function getListCount($where = array()) {
		$where = buildWhereSql($where);
		
		$sql = 'SELECT count(*) AS cnt FROM `' .$this -> getTableName(). '` ' . (empty($where) ? '' : ('WHERE '.$where));
		$res = $this -> dbConn -> fetch_first($sql);
		return empty($res) ? 0 : $res['cnt'];
	}
	
	private function buildLimit($page, $pageSize = 20) {
		return "LIMIT ".(($page - 1) * $pageSize).','.$pageSize;
	}
	
	private function buildField($fields) {
		$field = null;
		
		if (!empty($fields)) {
			$field = empty($field) ? ' * ' : '`'.implode('`,`', $fieldArr).'`' ;
		} else {
			$field = ' * ';
		}
		
		return $field;
	}
	
	private function buildSort($sortArr) {
		$sort = '';
		if (!empty($sortArr)) {
			if (is_array($sortArr)) {
				$sortStr .= '';
				foreach ($sortArr AS $key => $value) {
					$sortStr .= ' `'.$key.'` '.$value.',';
				}
				
				$sort = 'ORDER BY'. substr($sortStr, 0, -1);
			} else {
				$sort = $sortArr;
			}
		}
		
		return $sort;
	}
	

	public function checkIsExists($data){
		return false;
	}
	
	public function resetCache(){
		$this->recache = true;
	}
	
	public function getErrorMsg(){
		return self::$errMsg;
	}
}