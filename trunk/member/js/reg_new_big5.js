<!--
$(document).ready(function()
{
	//checkSubmit
	$('#regUser').submit(function ()
	{
		if($('#txtUsername').val()=='')
		{
			$('#txtUsername').focus();
			alert("用戶名不能為空！");
			return false;
		}
		if($('#txtPassword').val()=='')
		{
			$('#txtPassword').focus();
			alert("登陸密碼不能為空！");
			return false;
		}
		if($('#userpwdok').val()!=$('#txtPassword').val())
		{
			$('#userpwdok').focus();
			alert("兩次密碼不一致！");
			return false;
		}
		if($('#uname').val()=="")
		{
			$('#uname').focus();
			alert("用戶暱稱不能為空！");
			return false;
		}
		if($('#vdcode').val()=="")
		{
			$('#vdcode').focus();
			alert("驗證碼不能為空！");
			return false;
		}
	})

	//AJAX changChickValue
	$("#txtUsername").change( function()
	{
		$.ajax({type: reMethod,url: "index_do.php",
						data: "dopost=checkuser&fmdo=user&lang=big5&cktype=1&uid="+$("#txtUsername").val(),
						dataType: 'html',
						success: function(result){$("#_userid").html(result);}
				});
	});

	$("#uname").change( function()
	{
		$.ajax({type: reMethod,url: "index_do.php",
					data: "dopost=checkuser&fmdo=user&lang=big5&cktype=0&uid="+$("#uname").val(),
					dataType: 'html',
					success: function(result){$("#_uname").html(result);}
			});
	});

	$("#email").change( function()
	{
		var sEmail = /\w+([-+.']\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*/;
		if(!sEmail.exec($("#email").val()))
		{
				$('#_email').html(" <b><font color='red'>×Email格式不正確</font></b> ");
				$('#email').focus();
		}else{
				$.ajax({type: reMethod,url: "index_do.php",
				data: "dopost=checkmail&fmdo=user&lang=big5&email="+$("#email").val(),
				dataType: 'html',
				success: function(result){$("#_email").html(result);}});
		}
	});

	$('#txtPassword').change( function()
	{
		if($('#txtPassword').val().length < pwdmin)
		{
			$('#_userpwdok').html(" <b><font color='red'>×密碼不能小於"+pwdmin+"位</font></b> ");
		}
		else if($('#userpwdok').val()!=$('txtPassword').val())
		{
			$('#_userpwdok').html(" <b><font color='red'>×兩次輸入密碼不一致</font></b> ");
		}
		else if($('#userpwdok').val().length < pwdmin)
		{
			$('#_userpwdok').html(" <b><font color='red'>×密碼不能小於"+pwdmin+"位</font></b> ");
		}
		else
		{
			$('#_userpwdok').html(" <b><font color='#4E7504'>√填寫正確</font></b> ");
		}
	});

	$('#userpwdok').change( function()
	{
		if($('#txtPassword').val().length < pwdmin)
		{
		$('#_userpwdok').html(" <b><font color='red'>×密碼不能小於"+pwdmin+"位</font></b> ");
		}
		else if($('#userpwdok').val()=='')
		{
			$('#_userpwdok').html(" <b>請填寫確認密碼</b> ");
		}
		else if($('#userpwdok').val()!=$('#txtPassword').val())
		{
			$('#_userpwdok').html(" <b><font color='red'>×兩次輸入密碼不一致</font></b> ");
		}
		else
		{
				$('#_userpwdok').html(" <b><font color='#4E7504'>√填寫正確</font></b> ");
		}
	});

	$("a[href*='#vdcode'],#vdimgck").bind("click", function()
	{
		$("#vdimgck").attr("src","../include/vdimgck.php?tag="+Math.random());
			return false;
	});
	
});
-->