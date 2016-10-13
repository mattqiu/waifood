<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" magic="text/html; charset=utf-8" />
<title>产品分组列表</title>
<script>
var APP_PATH="";
var CONST_PUBLIC="/Public";
var CONST_UPLOAD="<?php echo U('Admin/File/upload');?>";
</script>
<script type="text/javascript" src="/Public/Admin/js/jquery-1.4.4.min.js"></script><script type="text/javascript" src="/Public/Admin/js/jquery.page.js"></script><script type="text/javascript" src="/Public/Admin/js/jquery.lhgcalendar.min.js"></script><script type="text/javascript" src="/Public/Admin/js/jquery.numeric.only.js"></script><script type="text/javascript" src="/Public/Admin/js/uploadify/jquery.uploadify.min.js"></script>
<link rel="stylesheet" type="text/css" href="/Public/Admin/images/style.css" />
</head>
<body>
	<table border="0" cellpadding="3" cellspacing="1" class="MainTbl">
		<tr class="toolbar">
			<td colspan="6" class="tc">【 管理产品分组 】</td>
		</tr>
		<tr class="row0">
			<td colspan="6"><a href="<?php echo U('Admin/Cms/addMagictype');?>"
				class="btnAdd">添加</a></td>
		</tr>
		<tr class="header">
			<td width="50">ID</td>
			<td>产品分组名称</td>
			<td>产品分组描述</td>
			<td width="70">排序</td>
			<td width="50">状态</td>
			<td width="150">操作</td>
		</tr>
		<?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr class="row<?php echo ($i % 2+1); ?>">
			<td><?php echo ($vo["id"]); ?></td>
			<td> <a href="<?php echo U('Cms/magic','searchtype=2&keyword='.$vo['id']);?>"><?php echo ($vo["name"]); ?></a></td>
			<td><?php echo ($vo["remark"]); ?>&nbsp;</td>
			<td><input name="Item_1" id="Item_1"
				onchange="setVal('magictype','sort',<?php echo ($vo["id"]); ?>,$(this).val())"
				class="inputText1 numeric w50" value="<?php echo ($vo["sort"]); ?>" /></td>
			<td><?php if(($vo["status"] == 1)): ?><a
					href="javascript:void(0);"
					onclick="setVal('magictype','status',<?php echo ($vo["id"]); ?>,0,this,'禁用')">启用</a> <?php else: ?>
				<a href="javascript:void(0);"
					onclick="setVal('magictype','status',<?php echo ($vo["id"]); ?>,1,this,'启用')">禁用</a><?php endif; ?></td>
			<td> <a
				href="<?php echo U('Cms/editMagictype','id='.$vo['id']);?>" class="btnEdit">修改</a>
				<a class="btnDel" href="javascript:void(0);"
				onclick="var url='<?php echo U("Cms/deleteMagictype","id=".$vo['id']);?>';if(confirm('您确定删除该记录吗？')){location=url;}">删除</a></td>
		</tr><?php endforeach; endif; else: echo "" ;endif; ?>

	</table>
	
</body>
</html>