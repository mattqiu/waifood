<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>参数设置</title>
<script>
var APP_PATH="";
var CONST_PUBLIC="/Public";
var CONST_UPLOAD="<?php echo U('Admin/File/upload');?>";
</script>
<script type="text/javascript" src="/Public/Admin/js/jquery-1.4.4.min.js"></script><script type="text/javascript" src="/Public/Admin/js/jquery.page.js"></script><script type="text/javascript" src="/Public/Admin/js/jquery.lhgcalendar.min.js"></script><script type="text/javascript" src="/Public/Admin/js/jquery.numeric.only.js"></script><script type="text/javascript" src="/Public/Admin/js/uploadify/jquery.uploadify.min.js"></script>
<link rel="stylesheet" type="text/css" href="/Public/Admin/images/style.css" /> 
<script language="javascript" type="text/javascript"
	src="/Public/Admin/js/jquery.idTabs.min.js"></script>
<script>
$(function(){ 
	
});
</script>
</head>
<body>
<form action="/Admin/System/setting" method="post" name="form1"
		id="form1"> 
  <table border="0" cellspacing="1" cellpadding="3" class="MainTbl">
    <tr class="toolbar">
      <td colspan="2" class="tc">【 参数设置 】</td>
    </tr>
    <tr class="row0">
      <td colspan="2"><div class="tab">
          <ul class="idTabs">  
   
		<?php if(is_array($group)): $i = 0; $__LIST__ = $group;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li><a href="javascript:void(0);" rel="#autotab_<?php echo ($i); ?>"><?php echo ($vo); ?>配置</a></li><?php endforeach; endif; else: echo "" ;endif; ?>
		
          </ul>
        </div></td>
    </tr>
  </table>
   
  <div id="autotab_1">
     <table border="0" cellspacing="1" cellpadding="3" class="MainTbl"> 
	<?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$config): $mod = ($i % 2 );++$i; if((1 == $config['group'])): ?><tr class="row0">
        <td class="col1"><?php echo ($config["title"]); ?>：</td>
        <td><?php switch($config["type"]): case "0": ?><input type="text" class="inputText1" name="config[<?php echo ($config["name"]); ?>]" value="<?php echo ($config["value"]); ?>" /><?php break;?>
			<?php case "1": ?><input type="text" class="inputText1 w350" name="config[<?php echo ($config["name"]); ?>]" value="<?php echo ($config["value"]); ?>" /><?php break;?>
			<?php case "2": ?><textarea name="config[<?php echo ($config["name"]); ?>]"  class="inputText1 editor1"><?php echo ($config["value"]); ?></textarea><?php break;?>
			<?php case "3": ?><textarea name="config[<?php echo ($config["name"]); ?>]"  class="inputText1 editor1"><?php echo ($config["value"]); ?></textarea><?php break;?>
			<?php case "4": ?><select name="config[<?php echo ($config["name"]); ?>]">
				<?php $_result=parse_field_attr($config['extra']);if(is_array($_result)): $i = 0; $__LIST__ = $_result;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($key); ?>" <?php if(($config["value"]) == $key): ?>selected<?php endif; ?>><?php echo ($vo); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
			</select><?php break; endswitch;?>（<?php echo ($config["remark"]); ?>）</td>
      </tr><?php endif; endforeach; endif; else: echo "" ;endif; ?> 
      <tr class="footer">
        <td colspan="2" class="tc"><input type="submit" class="btn1"
						value="保存" />
          <input type="button" class="btn1" value="返回"
						onclick="history.back();" /></td>
      </tr>
		</table> 
  </div>
  <div class="clr"></div>

  <div id="autotab_2">
     <table border="0" cellspacing="1" cellpadding="3" class="MainTbl"> 
	<?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$config): $mod = ($i % 2 );++$i; if((2 == $config['group'])): ?><tr class="row0">
        <td class="col1"><?php echo ($config["title"]); ?>：</td>
        <td><?php switch($config["type"]): case "0": ?><input type="text" class="inputText1 numeric" name="config[<?php echo ($config["name"]); ?>]" value="<?php echo ($config["value"]); ?>" /><?php break;?>
			<?php case "1": ?><input type="text" class="inputText1 w350" name="config[<?php echo ($config["name"]); ?>]" value="<?php echo ($config["value"]); ?>" /><?php break;?>
			<?php case "2": ?><textarea name="config[<?php echo ($config["name"]); ?>]"  class="inputText1 editor1"><?php echo ($config["value"]); ?></textarea><?php break;?>
			<?php case "3": ?><textarea name="config[<?php echo ($config["name"]); ?>]"  class="inputText1 editor1"><?php echo ($config["value"]); ?></textarea><?php break;?>
			<?php case "4": ?><select name="config[<?php echo ($config["name"]); ?>]">
				<?php $_result=parse_field_attr($config['extra']);if(is_array($_result)): $i = 0; $__LIST__ = $_result;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($key); ?>" <?php if(($config["value"]) == $key): ?>selected<?php endif; ?>><?php echo ($vo); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
			</select><?php break; endswitch;?>（<?php echo ($config["remark"]); ?>）</td>
      </tr><?php endif; endforeach; endif; else: echo "" ;endif; ?> 
      <tr class="footer">
        <td colspan="2" class="tc"><input type="submit" class="btn1"
						value="保存" />
          <input type="button" class="btn1" value="返回"
						onclick="history.back();" /></td>
      </tr>
		</table> 
  </div>
  <div class="clr"></div>
  
  <div id="autotab_3" class="hide">
     <table border="0" cellspacing="1" cellpadding="3" class="MainTbl"> 
	<?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$config): $mod = ($i % 2 );++$i; if((3 == $config['group'])): if(($config["type"]) == "5"): ?><tr class="row0">
	 <td colspan="2"><?php echo ($config["title"]); ?></td>
	 </tr>
	<?php else: ?>
		 <tr class="row0">
        <td class="col1"><?php echo ($config["title"]); ?>：</td>
        <td><?php switch($config["type"]): case "0": ?><input type="text" class="inputText1 numeric" name="config[<?php echo ($config["name"]); ?>]" value="<?php echo ($config["value"]); ?>" /><?php break;?>
			<?php case "1": ?><input type="text" class="inputText1 w350" name="config[<?php echo ($config["name"]); ?>]" value="<?php echo ($config["value"]); ?>" /><?php break;?>
			<?php case "2": ?><textarea name="config[<?php echo ($config["name"]); ?>]"  class="inputText1 editor1"><?php echo ($config["value"]); ?></textarea><?php break;?>
			<?php case "3": ?><textarea name="config[<?php echo ($config["name"]); ?>]"  class="inputText1 editor1"><?php echo ($config["value"]); ?></textarea><?php break;?>
			<?php case "4": ?><select name="config[<?php echo ($config["name"]); ?>]">
				<?php $_result=parse_field_attr($config['extra']);if(is_array($_result)): $i = 0; $__LIST__ = $_result;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($key); ?>" <?php if(($config["value"]) == $key): ?>selected<?php endif; ?>><?php echo ($vo); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
			</select><?php break; endswitch;?>（<?php echo ($config["remark"]); ?>）</td>
      </tr><?php endif; endif; endforeach; endif; else: echo "" ;endif; ?> 
      <tr class="footer">
        <td colspan="2" class="tc"><input type="submit" class="btn1"
						value="保存" />
          <input type="button" class="btn1" value="返回"
						onclick="history.back();" /></td>
      </tr>
		</table> 
  </div>
  <div class="clr"></div>
  
  
  <div class="hide">
     <table border="0" cellspacing="1" cellpadding="3" class="MainTbl"> 
	<?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$config): $mod = ($i % 2 );++$i; if((0 == $config['group'])): ?><tr class="row0">
        <td class="col1"><?php echo ($config["title"]); ?>：</td>
        <td><?php switch($config["type"]): case "0": ?><input type="text" class="inputText1" name="config[<?php echo ($config["name"]); ?>]" value="<?php echo ($config["value"]); ?>" /><?php break;?>
			<?php case "1": ?><input type="text" class="inputText1 w350" name="config[<?php echo ($config["name"]); ?>]" value="<?php echo ($config["value"]); ?>" /><?php break;?>
			<?php case "2": ?><textarea name="config[<?php echo ($config["name"]); ?>]"  class="inputText1 editor1"><?php echo ($config["value"]); ?></textarea><?php break;?>
			<?php case "3": ?><textarea name="config[<?php echo ($config["name"]); ?>]"  class="inputText1 editor1"><?php echo ($config["value"]); ?></textarea><?php break;?>
			<?php case "4": ?><select name="config[<?php echo ($config["name"]); ?>]">
				<?php $_result=parse_field_attr($config['extra']);if(is_array($_result)): $i = 0; $__LIST__ = $_result;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($key); ?>" <?php if(($config["value"]) == $key): ?>selected<?php endif; ?>><?php echo ($vo); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
			</select><?php break; endswitch;?>（<?php echo ($config["remark"]); ?>）</td>
      </tr><?php endif; endforeach; endif; else: echo "" ;endif; ?> 
      <tr class="footer">
        <td colspan="2" class="tc"><input type="submit" class="btn1"
						value="保存" />
          <input type="button" class="btn1" value="返回"
						onclick="history.back();" /></td>
      </tr>
		</table> 
  </div>
  <div class="clr"></div>
  
  
  
</form>
<div class="clr"></div>

</body>
</html>