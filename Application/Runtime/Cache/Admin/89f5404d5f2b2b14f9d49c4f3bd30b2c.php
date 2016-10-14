<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="content-Type" content="text/html; charset=utf-8" />
<title>会员列表</title>
<script>
var APP_PATH="";
var CONST_PUBLIC="/Public";
var CONST_UPLOAD="<?php echo U('Admin/File/upload');?>";
</script>
<script type="text/javascript" src="/Public/Admin/js/jquery-1.4.4.min.js"></script><script type="text/javascript" src="/Public/Admin/js/jquery.page.js"></script><script type="text/javascript" src="/Public/Admin/js/jquery.lhgcalendar.min.js"></script><script type="text/javascript" src="/Public/Admin/js/jquery.numeric.only.js"></script><script type="text/javascript" src="/Public/Admin/js/uploadify/jquery.uploadify.min.js"></script>
<link rel="stylesheet" type="text/css" href="/Public/Admin/images/style.css" />
</head>
<body>
	<form action="" method="get" name="form1" id="form1">
		<input type="hidden" name="status" id="status" value="<?php echo I('status');?>" />
		<table border="0" cellpadding="3" cellspacing="1" class="MainTbl">
			<tr>
				<td>关 键 词： <input type="text" class="inputText1" id="keyword"
					name="keyword" value="<?php echo ($keyword); ?>" /> <select id="searchtype"
					name="searchtype">
						<option value="0">用户名</option>
						<?php if(($searchtype == 1)): ?><option value="1" selected="selected">收件人</option>
						<?php else: ?>
						<option value="1">收件人</option><?php endif; ?>
						<?php if(($searchtype == 2)): ?><option value="2" selected="selected">联系电话</option>
						<?php else: ?>
						<option value="2">联系电话</option><?php endif; ?>
						<?php if(($searchtype == 3)): ?><option value="3" selected="selected">Email</option>
						<?php else: ?>
						<option value="3">Email</option><?php endif; ?>
						<?php if(($searchtype == 4)): ?><option value="4" selected="selected">等级ID</option>
						<?php else: ?>
						<option value="4">等级ID</option><?php endif; ?>
						<?php if(($searchtype == 5)): ?><option value="5" selected="selected">微信名</option>
						<?php else: ?>
						<option value="5">微信名</option><?php endif; ?>
				</select> <input type="submit" class="btn1" value="查询" /></td>
			</tr>

			<tr>
				<td>会员类型： <a href="<?php echo U('Member/Member');?>" >全部</a>
					<?php if(is_array($levels)): $i = 0; $__LIST__ = $levels;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><a href="<?php echo U('Member/member','searchtype=4&keyword='.$vo['id']);?>" <?php if(($level) == $vo['id']): ?>class="fc_red"<?php endif; ?>><?php echo ($vo["name"]); ?></a>&nbsp;<?php endforeach; endif; else: echo "" ;endif; ?>

				</td>
			</tr>
		</table>
	</form>
	<div class="dot"></div>
	<table border="0" cellpadding="3" cellspacing="1" class="MainTbl">
		<tr class="toolbar">
			<td colspan="8" class="tc">【 管理会员 】</td>
		</tr>
		<tr class="row0">
			<td colspan="8"><a href="<?php echo U('Admin/Member/addMember');?>"
				class="btnAdd">添加</a></td>
		</tr>
		<tr class="header">
			<td width="60">ID</td>
			<td width="100">用户名/微信名</td>
			<td width="100">收件人</td>
			<td width="120">联系电话</td>
			<td  >Email地址</td>
			<td>用户地址</td>
			<td width="120">注册时间</td>
			<td width="110">操作</td>
		</tr>
		<?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr class="row<?php echo ($i % 2+1); ?>">
			<td><input type="checkbox" name="SelectIDs" value="<?php echo ($vo["id"]); ?>" />
				<?php echo ($vo["id"]); ?></td>
			<td><?php echo ($vo["username"]); if(!empty($vo["weixin"])): ?>/<?php echo ($vo["weixin"]); endif; ?></td>
			<td><?php echo ($vo["userreal"]); ?> </td>
			<td><?php echo ($vo["telephone"]); ?></td>
			<td><?php echo ($vo["email"]); ?></td>
			<td><?php echo ($vo["address"]); ?></td>
			<td><?php echo ($vo["addtime"]); ?></td>
			<td><a href="<?php echo U('Member/editMember','id='.$vo['id']);?>"
				class="btnEdit">修改</a> <a class="btnDel" href="javascript:void(0);"
				onclick="var url='<?php echo U("Member/deleteMember","id=".$vo['id']);?>';if(confirm('您确定删除该记录吗？')){location=url;}">删除</a></td>
		</tr><?php endforeach; endif; else: echo "" ;endif; ?>
		 
		<tr class="row0">
			<td colspan="10" class="tr"><input type="hidden" value="member"
				id="ConstTbl" name="ConstTbl" /> <input type="button" class="btn2"
				value="批量删除" id="AllDel" /> <input type="button" class="btn1"
				value="全选" id="AllCheck" /> <input type="button" class="btn1"
				value="反选" id="ReverseCheck" /></td>
		</tr>
		<tr class="footer">
			<td colspan="10"><div class="page"><?php echo ($page); ?></div></td>
		</tr>
	</table>
	
</body>
</html>