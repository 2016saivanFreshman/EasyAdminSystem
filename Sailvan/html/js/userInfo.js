$(document).ready(function (){
	$("button#edit").click(function(){
		var id 			= $("input#id").val();
		var username 	= $("input#username").val();
		if(!isEmail(username)){
			alert("邮箱格式不对");
			return;
		}
		var password 	= $("input#password").val();
		var sex 		= $("input#sex").val();
		var name 		= $("input#name").val();
		var age 		= $("input#age").val();
		var introduction= $("input#introduction").val();
		var picture 	= $("input#picture").val();
		$.ajax({
			cache	: false,
			type	: "POST",
			url		: "index.php?mod=user&act=update",
			data 	: {'id':id, 'username':username, 'password':password, 'sex':sex, 'name':name, 'age':age, 'introduction':introduction, 'picture':picture},
			async	: false,
			error	: function(request){
				alert("Error!");
			},
			success : function(data){
				if(data == "SUCCESS"){
					alert(data);
					window.location.href = "index.php?mod=user&act=realLogin&page=1";
				}else{
					alert(data);
				}
			}
		});
	});
});

function isEmail(username){
	if(username != ""){
		//这个需要看，暂时还不懂正则表达式
		var reg = /^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/;  
		var isok= reg.test(username);
		if(!isok){//不对，返回false
			return false;
		}
		return true;
	}
}

$(document).ready(function(){
	$("button#getInfoThroughApi").click(function(){
		$.ajax({
			type: "GET",
			url : "index.php?mod=user&act=getInfoThroughApi",
			async: false,
			dataType: 'json',
			error	: function(request){
				alert("Error!");
			},
			complete: function(request){
				alert("complete");
			},
			success : function(data){
				alert(data);
			}
		});
	});
});