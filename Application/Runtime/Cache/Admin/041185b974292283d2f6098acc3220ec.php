<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>系统登录入口-<?php echo ($title); ?></title>
<script>
var APP_PATH="";
var CONST_PUBLIC="/Public";
var CONST_UPLOAD="<?php echo U('Admin/File/upload');?>";
</script>
<script type="text/javascript" src="/Public/Admin/js/jquery-1.4.4.min.js"></script><script type="text/javascript" src="/Public/Admin/js/jquery.page.js"></script><script type="text/javascript" src="/Public/Admin/js/jquery.lhgcalendar.min.js"></script><script type="text/javascript" src="/Public/Admin/js/jquery.numeric.only.js"></script><script type="text/javascript" src="/Public/Admin/js/uploadify/jquery.uploadify.min.js"></script>
<link rel="stylesheet" type="text/css" href="/Public/Admin/images/style.css" />
<script language="javascript">
$(function(){
	$("#username").focus();
	if(self.frameElement != null && (self.frameElement.tagName == "IFRAME" || self.frameElement.tagName == "iframe")){
		window.parent.location=APP_PATH+'/Admin/Login';
	}
	
	$("#form2").submit(function(){ 
		var u=$.trim($("#username").val());
		var p=$.trim($("#userpwd").val());
		var v=$.trim($("#verify").val());
		if(u==""){
			$("#username").focus();
			return false;
		}
		if(p==""){
			$("#userpwd").focus();
			return false;
		}
		if(v==""){
			$("#verify").focus();
			return false;
		}
		$.ajax({
			"type":"POST",
			"url":"Login/login",
			"data":"username="+u+"&userpwd="+p+"&verify="+v+"",
			"success":function(msg){
				if(msg.status==0){
					if(msg.info.indexOf('验证码')!=-1){
						$("#verify").val("").focus();
						$("#vimg").click();
					}
					alert(msg.info);
					return false;
				}else{
					location="/admin/";	
				}
			}
		});	
		
		return false;
	});
});
</script>
<style>
body{background:#E1E1E1;}
</style>
</head>
<body>
<div class="loginBox">
  <div class="loginForm">
	<form action="/Admin/Login/Login/login" method="post" name="form2"
		id="form2">  
      <table width="100%" border="0" cellspacing="0" cellpadding="0" class="loginTbl">
        <tr>
          <td colspan="4" class="tc">&nbsp;</td>
        </tr>
        <tr >
          <td class="tr">用户名：</td>
          <td><input type="text" class="inputText1" id="username" name="username" /></td>
          <td>&nbsp;</td>
        </tr>
        <tr >
          <td class="tr">密&nbsp;&nbsp;码：</td>
          <td><input type="password" class="inputText1" id="userpwd" name="userpwd" /></td>
          <td>&nbsp;</td>
        </tr>
        <tr >
          <td class="tr">验证码：</td>
          <td><input type="text" class="inputText1" id="verify" name="verify" style="width:50px; float:left;" maxlength="5" />
            <img id="vimg" style="cursor:pointer;margin-left:5px; cursor: hand; width:70px; height:24px;" title="看不清楚?换一张!" alt="" onclick="this.src='/shop/index/verify.html?random='+Math.random()" src="/shop/index/verify.html" /></td>
          <td>&nbsp;</td>
        </tr>
        <tr >
          <td>&nbsp;</td>
          <td colspan="3"><input type="submit" class="btnLogin" id="btnLogin" name="btnLogin" value=" " /></td>
        </tr>
      </table>
    </form>
  </div>
  <div class="copyright">Copyright &copy; 2014 成都中联无限科技有限公司 <br />
    技术支持：<a href="http://www.cc01.com.cn" title="点击获取技术支持">www.cc01.com.cn</a></div>
  <div class="clr"></div>
</div>
</body>
</html>