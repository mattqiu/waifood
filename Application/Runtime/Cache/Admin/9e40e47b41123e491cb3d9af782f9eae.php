<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo ($title); ?></title>
<script>
var APP_PATH="";
var CONST_PUBLIC="/Public";
var CONST_UPLOAD="<?php echo U('Admin/File/upload');?>";
</script>
<script type="text/javascript" src="/Public/Admin/js/jquery-1.4.4.min.js"></script><script type="text/javascript" src="/Public/Admin/js/jquery.page.js"></script><script type="text/javascript" src="/Public/Admin/js/jquery.lhgcalendar.min.js"></script><script type="text/javascript" src="/Public/Admin/js/jquery.numeric.only.js"></script><script type="text/javascript" src="/Public/Admin/js/uploadify/jquery.uploadify.min.js"></script>
<link rel="stylesheet" type="text/css" href="/Public/Admin/images/style.css" />
</head>
<body > 
<table width="100%" border="0" cellspacing="1" cellpadding="3" class="MainTbl">
  <tr class="row0">
    <td ><strong>登录信息</strong></td>
  </tr>
  <tr class="row0">
    <td>你好，<span class="fc_red"><?php echo Session('adminname');?></span>，欢迎使用本系统！ </td>
  </tr>
  <tr class="row0">
    <td><table width="100%" border="0" cellspacing="0" cellpadding="0" class="MainTbl">
        <tr>
          <td align="right" width="100">登录次数：</td>
          <td><?php echo ($db["logtimes"]); ?></td>
        </tr>
        <tr>
          <td align="right" width="100">上次登录时间：</td>
          <td><?php echo ($db["lastlogtime"]); ?></td>
        </tr>
        <tr>
          <td align="right" width="100">上次登录IP：</td>
          <td><?php echo ($db["lastlogip"]); ?></td>
        </tr>
      </table></td>
  </tr>
  <tr class="row0">
    <td ><strong>系统信息</strong></td>
  </tr>
  <tr class="row0">
    <td><table width="100%" border="0" cellspacing="0" cellpadding="0" class="MainTbl">
        <tr>
          <td align="right" width="100">操作系统：</td>
          <td><?php echo PHP_OS;?></td>
        </tr>
        <tr>
          <td align="right" width="100">服务器软件：</td>
          <td><?php echo ($_SERVER['SERVER_SOFTWARE']); ?></td>
        </tr>
        <tr>
          <td align="right" width="100">PHP版本：</td>
          <td><?php echo phpversion();?></td>
        </tr>
      </table></td>
  </tr>
</table>
</body>
</html>