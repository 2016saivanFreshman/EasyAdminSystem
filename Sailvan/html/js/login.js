$(document).ready(function(){
			$("button#login").click(function(){
				var username = $("#username").val();
				var password = $("#password").val();
				$.ajax({
					cache  	: false,
					type   	: "POST",
					url    	: "index.php?mod=user&act=login",
					data	: {"username":username, "password":password},
					async  	: false,
					dataType: 'json',
					error  	: function(request){
						alert("Error!");
					},
					success	: function(data){
						//console.log(data）这个用控制台输出调试信息，贼6
						if(data.message == "fail"){
							//失败的代码在这里插入
							$("i").append("失败");
						}else{
							alert(data.message);
							//跳转操作代码在此
							window.location.href = "index.php?mod=user&act=realLogin&page=1" ;
						}
					}
				});	
		});
});


$(document).ready(function(){
			$("button#register").click(function(){
				var username = $("#username").val();
				//邮箱格式不正确,
				if(!isEmail(username) || username.length > 16 ){
					alert("邮箱格式不对");
					return;
				}
				var password = $("#password").val();
				if(password.length > 16){
					alert("密码长度不能大于16");
					return ;
				}
				$.ajax({
					cache  	: false,
					type   	: "POST",
					url    	: "index.php?mod=user&act=register",
					data	: {'username':username, 'password':password},
					async  	: false,
					error  	: function(request){
						alert("Error!");
					},
					success	: function(data){
						if(data == "false"){
							//失败的代码在这里插入
							$("#errmsg").text("用户名或密码已存在");
						}else{
							//跳转操作代码在此
							alert(data);
							window.location.href = "index.php?mod=user&act=realLogin";
						}	
					}
				});	
			});
		});

function isEmail(username){
	if(username != ""){
		//这个需要看，暂时还不懂正则表达式
		//var reg = /^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/;  
		var reg = /^\w+\@[/A-Za-z0-9]+\.[A-Za-z0-9]+$/;
		var isok= reg.test(username);
		if(!isok){//不对，返回false
			return false;
		}
		return true;
	}
}