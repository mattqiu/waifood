<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>内容添加</title>
<script>
var APP_PATH="";
var CONST_PUBLIC="/Public";
var CONST_UPLOAD="<?php echo U('Admin/File/upload');?>";
</script>
<script type="text/javascript" src="/Public/Admin/js/jquery-1.4.4.min.js"></script><script type="text/javascript" src="/Public/Admin/js/jquery.page.js"></script><script type="text/javascript" src="/Public/Admin/js/jquery.lhgcalendar.min.js"></script><script type="text/javascript" src="/Public/Admin/js/jquery.numeric.only.js"></script><script type="text/javascript" src="/Public/Admin/js/uploadify/jquery.uploadify.min.js"></script>
<link rel="stylesheet" type="text/css" href="/Public/Admin/images/style.css" />
<script type="text/javascript"
	src="/Public/Admin/js/xheditor/xheditor-1.2.1.min.js"></script>
<script type="text/javascript"
	src="/Public/Admin/js/xheditor/xheditor_lang/zh-cn.js"></script>
<script language="javascript" type="text/javascript"
	src="/Public/Admin/js/jquery.idTabs.min.js"></script> 
<script language="javascript" type="text/javascript"
	src="/Public/Admin/js/jquery.price.js"></script>
<script>
$(function(){ 

	<?php  if(I('rootid') == '2'){ $showhide=""; echo('$(".proext").show()'); }else{ $showhide="hide"; echo('$(".proext").hide()'); } ?>
});
</script>
</head>
<body>  
<form action="/Admin/Cms/addContent" method="post" name="form1" id="form1">
  <table border="0" cellspacing="1" cellpadding="3" class="MainTbl" >
    <tr class="toolbar">
      <td colspan="2" class="tc">【 添加内容 】</td>
    </tr>
    <tr class="row0">
      <td colspan="2"><div class="tab">
          <ul class="idTabs">
            <li><a href="javascript:void(0);" class="selected" rel="#autotab_1">基本信息</a></li>
            <li><a href="javascript:void(0);" rel="#autotab_2">高级属性</a></li>
            <li class="hide" id="ext_field"><a href="javascript:void(0);" rel="#autotab_3">扩展属性</a></li>
          </ul>
        </div></td>
    </tr>
  </table>
  <div id="autotab_1"><input type="hidden" name="shop_id" id="shop_id" value="1" />
    <table border="0" cellspacing="1" cellpadding="3" class="MainTbl"> 
      <tr class="row0">
        <td class="col1" width="140">所属分类：</td>
        <td><select name="pid" id="pid">
            <option value="0">--选择类别--</option>
           <?php echo R('Cms/treeselect', array($list));?>
          </select></td>
      </tr> 
      <tr class="row0 proext <?php echo ($showhide); ?>">
        <td class="col1" width="140">供应商：</td>
        <td><select name="supplyid" id="supplyid">
            <option value="0">--选择供应商--</option>
            <?php if(is_array($supplylist)): $i = 0; $__LIST__ = $supplylist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($vo["id"]); ?>"><?php echo ($vo["name"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
          </select></td>
      </tr>
      <tr class="row0">
        <td class="col1" >标题：</td>
        <td><input type="text" class="inputText1 w350" id="title" name="title"
				 value="" />
          <span class="fc_red">*</span></td>
      </tr>
      <tr class="row0 proext <?php echo ($showhide); ?>">
        <td class="col1" >（中文名）：</td>
        <td><input type="text" class="inputText1 w350" id="namecn" name="namecn"
					 value="" /> </td>
      </tr>
      <tr class="row0">
        <td class="col1">形象图：</td>
        <td><input type="text" class="inputText1 w350" name="indexpic"
					id="indexpic" maxlength="100" value="" /> 
					<input type="button" class="btn1 btnUpload" id="btnUpload" value="上传" /></td>
      </tr>
      <tr class="row0 proext <?php echo ($showhide); ?>" >
        <td class="col1">价格：</td>
        <td><input type="text" class="inputText1 w50 numeric" name="price"
					id="price" maxlength="100" value="" />
					市场价：<input type="text" class="inputText1 w50 numeric" name="price1"
					id="price1" maxlength="100" value="" />
					批发价：<input type="text" class="inputText1 w50 numeric" name="price2"
					id="price2" maxlength="100" value="" />
					 </td>
      </tr>
       <tr class="row0 proext <?php echo ($showhide); ?>">
        <td class="col1">库存：</td>
        <td><input type="text" class="inputText1 w50 numeric" name="stock"
					id="stock" maxlength="100" value="<?php echo ($db["stock"]); ?>" />
                    单位：<input type="text" class="inputText1" name="unit"
					id="unit" maxlength="100" value="<?php echo ($db["unit"]); ?>" /> 品牌：
          <input type="text" class="inputText1" name="brand"
					id="brand" maxlength="100" value="<?php echo ($db["brand"]); ?>" /> 产地：
          <input type="text" class="inputText1" name="origin"
					id="origin" maxlength="100" value="<?php echo ($db["origin"]); ?>" />
					保存：
          <input type="text" class="inputText1" name="storage"
					id="storage" maxlength="100" value="<?php echo ($db["storage"]); ?>" />
					</td>
      </tr>
       <tr class="row0 proext <?php echo ($showhide); ?>">
        <td class="col1" >Notice：</td>
        <td><textarea class="inputText1 editor1" id="notice"
							name="notice"><?php echo ($db["notice"]); ?></textarea> </td>
      </tr>
      <tr class="row0">
        <td class="col1">内容：</td>
        <td><textarea type="text" class="inputText1 editor" name="content" id="content" style="width:100%;" ></textarea></td>
      </tr>
      <tr class="row0">
        <td class="col1" >唯一标识：</td>
        <td><input type="text" class="inputText1" id="name" name="name"
					maxlength="100" value="<?php echo ($sort); ?>" />
          <span class="fc_red">* 同分类下不允许重复</span></td>
      </tr>
      <tr class="row0">
        <td class="col1">排序：</td>
        <td><input type="text" class="inputText1 numeric w50"
					name="sort" id="sort" maxlength="10" value="<?php echo ($sort); ?>" /></td>
      </tr>
      <tr class="row0">
        <td class="col1">状态：</td>
        <td><select name="status" id="status">
            <option value="1">启用</option>
            <option value="0">禁用</option>
          </select></td>
      </tr>
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
      <tr class="row0">
        <td class="col1" width="140">关键词：</td>
        <td ><input type="text" class="inputText1 w350" id="keywords"  name="keywords"  value=""  maxlength="100" />
          一般不超过100个字符</td>
      </tr>
      <tr class="row0">
        <td class="col1">描述：</td>
        <td ><textarea class="inputText1 editor1" id="description"  name="description" ></textarea>
          一般不超过200个字符</td>
      </tr>
      <tr class="row0">
        <td class="col1">外部链接：</td>
        <td ><input type="text" class="inputText1 w350" id="linkurl"  name="linkurl"  value=""  maxlength="100" /></td>
      </tr>
      <tr class="row0">
        <td class="col1">来源：</td>
        <td ><input type="text" class="inputText1" id="source"  name="source"  value=""  maxlength="100" /></td>
      </tr>
      <tr class="row0">
        <td class="col1">作者：</td>
        <td ><input type="text" class="inputText1" id="author"  name="author"  value=""  maxlength="100" /></td>
      </tr>
      <tr class="row0">
        <td class="col1">点击数：</td>
        <td ><input type="text" class="inputText1 numeric w50" id="hits"  name="hits"  value=""  maxlength="5" /></td>
      </tr>
      <tr class="footer">
        <td colspan="2" class="tc"><input type="submit" class="btn1"
					value="保存" />
          <input type="button" class="btn1" value="返回"
					onclick="history.back();" /></td>
      </tr>
    </table>
  </div>
  <div class="clr"></div>
  <div id="autotab_3">
     
  </div>
  <div class="clr"></div>
</form>
<div class="clr"></div>

     
</body>
</html>