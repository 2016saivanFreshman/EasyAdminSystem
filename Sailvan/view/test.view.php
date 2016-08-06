<?php
/*
 * @name testView
 * @description 测试显示
 * @author lzj
 */
class TestView extends BaseView{


    public function __construct() {
		parent::__construct();
    }
    
    public function view_index(){
		//print_r($_REQUEST);//调试用，原来没有备注
		$request = isset($_REQUEST) ? $_REQUEST : '';
		
		
		//print_r(Http::getParam('aa'));//调试中输出，打印变量的相关信息，需要去掉备注
		//die();//die（）之后退出脚本
		
		$this->smarty->assign('request', $request);

		echo "用A方法来加载运行".$this->getAction()."<br/>";
		$temp = A($this->getAction())->act_getAll();
		
		//$this->view_listShow();
		$list	  = A($this->getAction())->act_getDataList();
		$showPage = $this->getPageformat(A($this->getAction())->act_getDataCount(), A($this->getAction())->act_getPerpage());
		$this->smarty->assign(array('list' => $list, 'show_page' => $showPage));
		
		$this->smarty->display('test/index.html');
    }
    
    public function view_save() {
		$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
		$res = 0;
		if($id > 0) {
		    $res = A($this->getAction())->act_update($id);
		}else{
		    $res = A($this->getAction())->act_save();
		}
		
		if (empty($res)) {
			$this -> error("保存失败！",'/index.php?mod=test&act=index',3);
		} else {
			$this -> success("保存成功！", '/index.php?mod=test&act=index');
		}
    }
    

    public function view_delDate(){
		$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
		$res = A($this->getAction())->act_delDate($id);
		echo json_encode($res);
    }
    
    public function view_getInfo() {
	$res = A($this->getAction())->act_getInfo();
	echo $this->ajaxReturn($res);
	//echo json_encode($res);
    }

    
}