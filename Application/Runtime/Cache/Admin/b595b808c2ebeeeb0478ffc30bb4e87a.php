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
<script type="text/javascript" src="/Public/Admin/js/jquery.setui.js"></script> 
<style>
html {
	overflow: hidden;
}
</style>
</head>
<body class="MainBody">

	<div id="Header">
		<div class="topBar">
			<span class="fl"><a href="./"><img
					src="/Public/Admin/images/logo.jpg" alt="" /></a></span><span class="fr"
				style="padding:20px 20px 0px 40px; background:url(/Public/Admin/images/home.gif) no-repeat left 20px;">    <a href="../" target="_blank">网站首页</a><br />   <a href="/home/" target="_blank"  >微信首页</a></span>
		</div>
		<div class="location">
			<span class="fl" id="Location"></span> <span class="fr"> <a
				href="javascript:void(0);" id="ClrCache">更新缓存</a> 当前用户：<?php echo Session('adminname');?> <a
				href="javascript:void(0);" onclick="var url='<?php echo U('Admin/Login/logout');?>';if(confirm('您确定要退出登录吗？')){location=url;};" class="fc_red">安全退出</a>&nbsp;</span>
		</div>
	</div>
	<div id="Lefter">
		<div id="LeftMenu">
			<ul>
				<?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i; if(($supper == true) Or ($vo["access"] == 1)): ?><li><span><?php echo ($vo["title"]); ?></span>
					<ul>
						<?php if(is_array($vo["child"])): $i = 0; $__LIST__ = $vo["child"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$son): $mod = ($i % 2 );++$i; if(($supper == true) Or ($son["access"] == 1)): ?><li><a href="<?php echo isN($son['url'])?U($vo['name'].'/'.$son['name']):U($son['url']);?>"><?php echo ($son["title"]); ?></a></li><?php endif; endforeach; endif; else: echo "" ;endif; ?>
					</ul></li><?php endif; endforeach; endif; else: echo "" ;endif; ?>
			</ul>
			<div class="copyright">
				版权所有：<a href="http://www.cc01.com.cn" target="_blank">中联无限科技</a>
			</div>
		</div>
	</div>
	<div id="Spliter" class="spliterLeft"></div>
	<div id="Righter">
		<div class="container">
			<iframe src="<?php echo U('Index/index?sys=1');?>" width="100%" height="100%" frameborder="0"
				class="MainFrame" id="MainFrame" name="MainFrame"></iframe>
		</div>
	</div>
</body>
</html>