<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>节点授权</title>
<script>
var APP_PATH="";
var CONST_PUBLIC="/Public";
var CONST_UPLOAD="<?php echo U('Admin/File/upload');?>";
</script>
<script type="text/javascript" src="/Public/Admin/js/jquery-1.4.4.min.js"></script><script type="text/javascript" src="/Public/Admin/js/jquery.page.js"></script><script type="text/javascript" src="/Public/Admin/js/jquery.lhgcalendar.min.js"></script><script type="text/javascript" src="/Public/Admin/js/jquery.numeric.only.js"></script><script type="text/javascript" src="/Public/Admin/js/uploadify/jquery.uploadify.min.js"></script>
<link rel="stylesheet" type="text/css" href="/Public/Admin/images/style.css" />
<script language="javascript" type="text/javascript"
	src="/Public/Admin/js/ztree/js/jquery.ztree.all-3.5.min.js"></script>
<link href="/Public/Admin/js/ztree/css/zTreeStyle/zTreeStyle.css" rel="stylesheet" type="text/css" />
<script type="text/javascript">
<!--
	var setting = {
		check : {
			enable : true
		},
		data : {
			simpleData : {
				enable : true
			}
		}
	};

	var zNodes = [
	<?php if(is_array($nodelist)): $i = 0; $__LIST__ = $nodelist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>{
		id : <?php echo ($vo["id"]); ?>,pId : <?php echo ($vo["pid"]); ?>,level : "<?php echo ($vo["level"]); ?>",name : "<?php echo ($vo["title"]); ?>",checked : <?php if(($vo['access'] == 1)): ?>true<?php else: ?>false<?php endif; ?>,open :true
	},<?php endforeach; endif; else: echo "" ;endif; ?>
	 ];

	var zNodesChannel = [
	<?php if(is_array($channellist)): $i = 0; $__LIST__ = $channellist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>{
		id : <?php echo ($vo["id"]); ?>,pId : <?php echo ($vo["pid"]); ?>,level : "<?php echo ($vo["depth"]); ?>",name : "<?php echo ($vo["name"]); ?>",checked : <?php if(($vo['access'] == 1)): ?>true<?php else: ?>false<?php endif; ?>,open :true
	},<?php endforeach; endif; else: echo "" ;endif; ?>
	 ];
	 
	var zNodesShop = [
	<?php if(is_array($shoplist)): $i = 0; $__LIST__ = $shoplist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>{
		id : <?php echo ($vo["id"]); ?>,pId : <?php echo ($vo["pid"]); ?>,level : "<?php echo ($vo["depth"]); ?>",name : "<?php echo ($vo["name"]); ?>",checked : <?php if(($vo['access'] == 1)): ?>true<?php else: ?>false<?php endif; ?>,open :true
	},<?php endforeach; endif; else: echo "" ;endif; ?>
	 ];
	 
	 
	$(document).ready(function() {
		$.fn.zTree.init($("#nodeTree"), setting, zNodes);
		var zTree = $.fn.zTree.getZTreeObj("nodeTree");
		
		$.fn.zTree.init($("#channelTree"), setting, zNodesChannel);
		var zTreeChannel = $.fn.zTree.getZTreeObj("channelTree");
		
		$.fn.zTree.init($("#shopTree"), setting, zNodesShop);
		var zTreeShop = $.fn.zTree.getZTreeObj("shopTree");
		
		$("#btnAccess").click(function(){
			var nodes =(zTree.getCheckedNodes(true));
			var str="";
			for(var i=0;i<nodes.length;i++){
				str+=(nodes[i].id+"_"+(parseInt(nodes[i].level)+1)+",");
				
			}; 
			$("#access").val(str);
			
			
			
			var nodes =(zTreeChannel.getCheckedNodes(true));
			var str="";
			for(var i=0;i<nodes.length;i++){
				str+=(nodes[i].id+",");
				
			}; 
			$("#channel").val(str);
			
			/*var nodes =(zTreeShop.getCheckedNodes(true));
			var str="";
			for(var i=0;i<nodes.length;i++){
				str+=(nodes[i].id+",");
				
			}; 
			$("#shop").val(str);*/
			
		})
	});
//-->
</script> 
</head>
<body>
	<form action="/Admin/Rbac/access" method="post">
		<input type="hidden" id="id" name="id" value="<?php echo ($id); ?>" />
		<input type="hidden" name="access" id="access" />
		<input type="hidden" name="channel" id="channel" />
		<input type="hidden" name="shop" id="shop" />
		<table border="0" cellpadding="3" cellspacing="1" class="MainTbl">
			<tr class="toolbar">
				<td colspan="3" class="tc">【 管理授权】</td>
			</tr>
			<tr class="header">
				<td>模块授权</td>
				<td>分类授权</td> 
			</tr>
			<tr class="row0">
				<td colspan="3">* 勾选相应节点即可</td>
			</tr>
			<tr class="row0">
				<td><ul id="nodeTree" class="ztree"></ul></td>
				<td><ul id="channelTree" class="ztree"></ul></td> 
			</tr>
			<tr class="footer">
				<td colspan="3" class="tc"><input type="submit" class="btn1"
					value="保存" id="btnAccess"/> <input type="button" class="btn1" value="返回"
					onclick="history.back();" /></td>
			</tr>
		</table>
	</form>
	
</body>
</html>