<?php
class mysql{

	var $version = '';
	var $querynum = 0;
	var $link = null;


	//数据库连接操作在此
	function connect($dbhost, $dbuser, $dbpw, $dbname = '', $pconnect = 0, $halt = TRUE, $dbcharset2 = '') {

		$func = empty($pconnect) ? 'mysql_connect' : 'mysql_pconnect';
		
		//@的作用是去掉警告。
		//先执行赋值，后判断是否为空
		//$func($dbhost, $dbuser, $dbpw, 1)输出的时候显示为Resource #id=xxx，资源句柄，用于打开资源
		
		if(!$this->link = @$func($dbhost, $dbuser, $dbpw, 1)) {//如果不能连接
			
			$halt && $this->halt('Can not connect to MySQL server');//halt是输出错误信息的
		} else {
			if($this->version() > '4.1') {//这个是设置mysql编码问题的
				global $charset, $dbcharset;
				$dbcharset = $dbcharset2 ? $dbcharset2 : $dbcharset;
				$dbcharset = !$dbcharset && in_array(strtolower($charset), array('gbk', 'big5', 'utf-8')) ? str_replace('-', '', $charset) : $dbcharset;
				$serverset = $dbcharset ? 'character_set_connection='.$dbcharset.', character_set_results='.$dbcharset.', character_set_client=binary' : '';
				$serverset .= $this->version() > '5.0.1' ? ((empty($serverset) ? '' : ',').'sql_mode=\'\'') : '';
				$serverset && mysql_query("SET $serverset", $this->link);
			}
			$dbname && @mysql_select_db($dbname, $this->link);//设置活动的 MySQL 数据库。如果成功，则该函数返回 true。如果失败，则返回 false。（前一个参数规定要选择的数据库。后面一个参数规定 MySQL 连接）

		}

	}

	function select_db($dbname) {
		return mysql_select_db($dbname, $this->link);
	}

	function fetch_array($query, $result_type = MYSQL_ASSOC) {
		return mysql_fetch_array($query, $result_type);
	}

	function fetch_array_all($query, $result_type = MYSQL_ASSOC){
		$arr	=	array();//从结果集中取得一行作为关联数组，或数字数组，同下
		while(1 && $ret	=	mysql_fetch_array($query, $result_type)){
			$arr[]	=	$ret;
		}
		return $arr;
	}

	//MYSQL_ASSOC
	function fetch_all($sql, $result_type = MYSQL_ASSOC) {
		$res = $this -> query($sql);
		if (!empty($res)) {
			$datas = array();
			//mysql_fetch_array函数从结果集中取得一行作为关联数组
			while ($row = mysql_fetch_array($res, $result_type)) {
				$datas[] = $row;
			}
			
			return $datas;
		}
		
		return false;
	}

	function fetch_first($sql) {
		return $this->fetch_array($this->query($sql));
	}

	function result_first($sql) {
		return $this->result($this->query($sql), 0);
	}

	function query($sql, $type = '') {

		global $debug, $sqldebug, $sqlspenttimes;

		//如果type是unbuffered没有缓存，且存在mysql_unbuffered_query方法
		$func = $type == 'UNBUFFERED' && @function_exists('mysql_unbuffered_query') ?
			'mysql_unbuffered_query' : 'mysql_query';

			//如果无法执行SQL语句
		if(!($query = $func($sql, $this->link))) {
			//in_array()在数组中搜索
			if(in_array($this->errno(), array(2006, 2013)) && substr($type, 0, 5) != 'RETRY') {
				//$this->close();
				require WEB_PATH."conf/common.php";
				//获取配置文件中的数据库信息名
				$db_config	=	C("DB_CONFIG");
				//$this->connect($dbhost, $dbuser, $dbpw, $dbname, $pconnect, true, $dbcharset);
				$this->connect($db_config["master1"][0], $db_config["master1"][1], $db_config["master1"][2], $db_config["master1"][4]);
				return $this->query($sql, 'RETRY'.$type);
			} elseif($type != 'SILENT' && substr($type, 5) != 'SILENT') {
				$this->halt('MySQL Query Error', $sql);
			}
		}

		$this->querynum++;
		return $query;
	}

	function affected_rows() {
		return mysql_affected_rows($this->link);
	}

	function error() {
		return (($this->link) ? mysql_error($this->link) : mysql_error());
	}

	function errno() {
		return intval(($this->link) ? mysql_errno($this->link) : mysql_errno());
	}

	function result($query, $row = 0) {
		$query = @mysql_result($query, $row);
		return $query;
	}

	function num_rows($query) {
		$query = mysql_num_rows($query);
		return $query;
	}

	function num_fields($query) {
		return mysql_num_fields($query);
	}

	function free_result($query) {
		return mysql_free_result($query);
	}

	function insert_id() {
		return ($id = mysql_insert_id($this->link)) >= 0 ? $id : $this->result($this->query("SELECT last_insert_id()"), 0);
	}

	function fetch_row($query) {
		$query = mysql_fetch_row($query);
		return $query;
	}

	function fetch_fields($query) {
		return mysql_fetch_field($query);
	}

	function version() {
		if(empty($this->version)) {
			$this->version = mysql_get_server_info($this->link);
		}
		return $this->version;
	}

	function close() {
		return mysql_close($this->link);
	}

	function halt($message = '', $sql = '') {
		if(!empty($sql)){
			$errorStr	=	"message : ".$message. ", sql: ".$sql."\r\n";
		}else{
			$errorStr	=	"message : ".$message."\r\n";
		}
		Log::write($errorStr,Log::ERR);
		throw new Exception($message);
	}

	/*************************
	 * 事务支持(必须是inodb或ndb引擎)
	 */
	function begin(){
		//$this->query("SET AUTOCOMMIT=0");
		$this->query("BEGIN");
	}

	function commit(){
		$this->query("COMMIT");
	}

	function rollback(){
		$this->query("ROLLBACK");
	}

	// 兼容旧系统db class 的方法 add by xiaojinhua
	function execute($sql, $type = ''){
		$query = $this->query($sql, $type = '');
		return $query;
	}

	function fetch_one($query){
		$data = $this->fetch_array($query);
		return $data;
	}

	function getResultArray($query){
		$arr = $this->fetch_array_all($query);
		return $arr;
	}
}
?>