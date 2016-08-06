function viewInformation(id){
	$.ajax({
			cache  	: false,
			type   	: "POST",
			url    	: "index.php?mod=user&act=register",
			data	: {'username':username, 'password':password},
			async  	: false,
			error  	: function(request){
				alert("Error!");
			},
			complete: function(){
				alert("complete!");
			},
			success	: function(data){
				if(data == "false"){
					//失败的代码在这里插入
					$("#errmsg").text("用户名或密码已存在");
				}else{
					//跳转操作代码在此
					window.open("index.php?mod=user&act=realLogin");
				}	
			}
		});	
}