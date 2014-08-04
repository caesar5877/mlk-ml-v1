<!--
$(document).ready(function(){
	//checkSubmit
	$('#regUser').submit(function () {
		if($('#txtUsername').val()==""){
			$('#txtUsername').focus();
			alert("User name can not be empty!");
			return false;
		}
		if($('#txtPassword').val()=="")
		{
			$('#txtPassword').focus();
			alert("password name can not be empty");
			return false;
		}
		if($('#userpwdok').val()!=$('#txtPassword').val())
		{
			$('#userpwdok').focus();
			alert("The two passwords are not the same!");
			return false;
		}
		if($('#uname').val()=="")
		{
			$('#uname').focus();
			alert("Your name can not be empty! ");
			return false;
		}
		if($('#vdcode').val()=="")
		{
			$('#vdcode').focus();
			alert("Validate Code can not be empty!");
			return false;
		}
	})
	
	//AJAX changChickValue
	$("#txtUsername").change( function() {
		$.ajax({type: reMethod,url: "index_do.php",
		data: "dopost=checkuser&fmdo=user&lang=en&cktype=1&uid="+$("#txtUsername").val(),
		dataType: 'html',
		success: function(result){$("#_userid").html(result);}}); 
	});
	
	$("#uname").change( function() {
		$.ajax({type: reMethod,url: "index_do.php",
		data: "dopost=checkuser&fmdo=user&lang=en&cktype=0&uid="+$("#uname").val(),
		dataType: 'html',
		success: function(result){$("#_uname").html(result);}}); 
	});
	
	$("#email").change( function() {
		var sEmail = /\w+([-+.']\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*/;
		if(!sEmail.exec($("#email").val()))
		{
			$('#_email').html("<font color='red'><b>×Email format is incorrect!</b></font>");
			$('#email').focus();
		}else{
			$.ajax({type: reMethod,url: "index_do.php",
			data: "dopost=checkmail&fmdo=user&lang=en&email="+$("#email").val(),
			dataType: 'html',
			success: function(result){$("#_email").html(result);}}); 
		}
	});	
	
	$('#txtPassword').change( function(){
		if($('#txtPassword').val().length < pwdmin)
		{
			$('#_userpwdok').html("<font color='red'><b>×Password can not be less than "+pwdmin+" Letter!</b></font>");
		}
		else if($('#userpwdok').val()!=$('txtPassword').val())
		{
			$('#_userpwdok').html("<font color='red'><b>×The two passwords are not the same!</b></font>");
		}
		else if($('#userpwdok').val().length < pwdmin)
		{
			$('#_userpwdok').html("<font color='red'><b>×Password can not be less than "+pwdmin+" Letter!</b></font>");
		}
		else
		{
			$('#_userpwdok').html("<font color='#4E7504'><b>√OK</b></font>");
		}
	});
	
	$('#userpwdok').change( function(){
		if($('#txtPassword').val().length < pwdmin)
		{
			$('#_userpwdok').html("<font color='red'><b>×Password can not be less than"+pwdmin+" Letter!</b></font>");
		}
		else if($('#userpwdok').val()=='')
		{
			$('#_userpwdok').html("<b>Please fill in the Confirm Password!</b>");
		}
		else if($('#userpwdok').val()!=$('#txtPassword').val())
		{
			$('#_userpwdok').html("<font color='red'><b>×The two passwords are not the same!</b></font>");
		}
		else
		{
			$('#_userpwdok').html("<font color='#4E7504'><b>√OK</b></font>");
		}
	});
	
	$("a[href*='#vdcode'],#vdimgck").bind("click", function(){
		$("#vdimgck").attr("src","../include/vdimgck.php?tag="+Math.random());
		return false;
	});
});
-->