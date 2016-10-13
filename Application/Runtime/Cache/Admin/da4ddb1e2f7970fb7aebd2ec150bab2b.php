<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="content-Type" content="text/html; charset=utf-8" />
<title>清洁订单列表</title>
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
    <tr>
      <td>
      	<form action="<?php echo U('OrderCleaning/index', u_pam($p, null, array('keyword')));?>" method="get" name="form1" id="form1">
      	关 键 词：
        <input type="text" class="inputText1" id="keyword" name="keyword" value="<?php echo ($p['keyword']); ?>" />
        <input type="submit" class="btn1" value="查询" />
		订单状态： 
        <select onchange="location.href=this.value">
        	<option value="<?php echo U('OrderCleaning/index', u_pam($p, null, array('status')));?>" <?php echo ec($p['status']===null?'selected':'');?>>全部</option>
      		<option value="<?php echo U('OrderCleaning/index', u_pam($p, array('status'=>-1)));?>" <?php echo ec($p['status']===-1?'selected':'');?>>取消</option>
      		<option value="<?php echo U('OrderCleaning/index', u_pam($p, array('status'=>0)));?>" <?php echo ec($p['status']===0?'selected':'');?>>未确认</option>
      		<option value="<?php echo U('OrderCleaning/index', u_pam($p, array('status'=>1)));?>" <?php echo ec($p['status']===1?'selected':'');?>>已确认</option>
      		<option value="<?php echo U('OrderCleaning/index', u_pam($p, array('status'=>2)));?>" <?php echo ec($p['status']===2?'selected':'');?>>已完成</option>
        </select>
		付款状态：
        <select onchange="location.href=this.value">
        	<option value="<?php echo U('OrderCleaning/index', u_pam($p, null, array('paid')));?>" <?php echo ec($p['paid']===null?'selected':'');?>>全部</option>
        	<option value="<?php echo U('OrderCleaning/index', u_pam($p, array('paid'=>1)));?>" <?php echo ec($p['paid']===1?'selected':'');?>>已付款</option>
        	<option value="<?php echo U('OrderCleaning/index', u_pam($p, array('paid'=>0)));?>" <?php echo ec($p['paid']===0?'selected':'');?>>未付款</option>
        </select>
		支付方式：
        <select onchange="location.href=this.value">
        	<option value="<?php echo U('OrderCleaning/index', u_pam($p, null, array('payment')));?>" <?php echo ec($p['payment']===null?'selected':'');?>>全部</option>
        	<option value="<?php echo U('OrderCleaning/index', u_pam($p, array('payment'=>1)));?>" <?php echo ec($p['payment']===1?'selected':'');?>>余额支付</option>
        	<option value="<?php echo U('OrderCleaning/index', u_pam($p, array('payment'=>2)));?>" <?php echo ec($p['payment']===2?'selected':'');?>>在线支付</option>
        	<option value="<?php echo U('OrderCleaning/index', u_pam($p, array('payment'=>3)));?>" <?php echo ec($p['payment']===3?'selected':'');?>>服务时支付</option>
        </select>
        </form>
      </td>
    </tr>
  </table>

	<div class="dot"></div>
	<table border="0" cellpadding="3" cellspacing="1" class="MainTbl">
		<tr class="toolbar">
			<td colspan="10" class="tc">【 清洁订单 】</td>
		</tr>
		<tr class="header">
			<td width="50">ID</td>
			<td width="80">订单号</td>
			<td width="80">下单时间</td>
			<td width="80">清洁时间</td>
			<td width="100" >客户信息</td>
			<td width="80">订单总额</td>
			<td width="100">付款方式</td>
			<td >备注</td>
			<td width="100">状态</td>
			<td width="50">操作</td>
		</tr>
		<?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr class="row<?php echo ($i % 2+1); ?>">
			<td><input type="checkbox" name="SelectIDs" value="<?php echo ($vo["order_id"]); ?>" /> <?php echo ($vo["order_id"]); ?></td>
			<td><?php echo ($vo["orderno"]); ?></td>
			<td><?php echo ($vo["create_time"]); ?><br /><?php if(($vo["orderfrom"]) == "1"): ?><span class="fc_red" title="该订单来自微信">微</span><?php endif; ?></td>
			<td><?php echo ($vo["start_time"]); ?></td>
			<td>
	            <?php echo ($vo["phone"]); ?> <?php echo ($vo["address"]); ?>
	        </td>
			<td>
				<?php echo ($vo["amount"]); ?><br />
                <?php switch($vo["ordertype"]): case "0": ?><span class="fc_red">物</span><?php break;?>
                <?php case "1": ?><span class="fc_red">洗</span><?php break;?>
                <?php case "2": ?><span class="fc_red">物+洗</span><?php break; endswitch;?>
            </td>
			<td>
				<?php if(($vo["payment"]) == "3"): ?>货到付款<?php else: ?>在线支付<?php endif; ?>
				<br />
				<?php if(($vo["paid"]) == "0"): ?><span class="fc_blue">未支付</span>
                    <?php else: ?>  
                    <span class="fc_red">已支付</span><?php endif; ?>
            </td>
			<td><?php echo ($vo["remark"]); ?></td>
			<td>
            	<select onchange="setOrdernew(this,'<?php echo ($vo["order_id"]); ?>')" >
            		<option value="-1" <?php echo ec($vo['status']==-1?'selected':'');?>>已取消</option>
            		<option value="0" <?php echo ec($vo['status']==0?'selected':'');?>>未确认</option>
            		<option value="1" <?php echo ec($vo['status']==1?'selected':'');?>>已确认</option>
            		<option value="2" <?php echo ec($vo['status']==2?'selected':'');?>>已完成</option>
				</select>
			 </td>
			<td>
            <?php if(($vo['status'] == 3)): ?><a href="<?php echo U('OrderCleaning/editOrder','orderno='.$vo['orderno']);?>" class="btnEdit">查看</a>
            <?php else: ?>
            <a href="<?php echo U('OrderCleaning/editOrder','orderno='.$vo['orderno']);?>" class="btnEdit">编辑</a><?php endif; ?>  
            <a href="<?php echo U('Cms/editOrderCleaning','act=print&orderno='.$vo['orderno']);?>"  target="_blank" class="btnAdd">打印</a>
            <a href="<?php echo U('Cms/editOrderCleaning','act=print&act1=1&orderno='.$vo['orderno']);?>"  target="_blank" class="btnAdd">内打</a>
            </td>
		</tr><?php endforeach; endif; else: echo "" ;endif; ?>
		 
		<tr class="row0">
			<td colspan="12" class="tr">
				<input type="hidden" value="ordernew" id="ConstTbl" name="ConstTbl" />
				<input type="button" class="btn2" value="批量删除" id="AllDel" />
				<input type="button" class="btn1" value="全选" id="AllCheck" />
				<input type="button" class="btn1" value="反选" id="ReverseCheck" />
			</td>
		</tr>
		<tr class="footer">
			<td colspan="12"><div class="page"><?php echo ($page); ?></div></td>
		</tr>
	</table>
	
</body>
</html>