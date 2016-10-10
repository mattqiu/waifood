<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Order: <?php echo ($db["orderno"]); ?></title>
<style type="text/css">
.table1 td{height:25px;}
.bd{border-bottom:1px #000 solid; width:210px;}
.w710{width:710px;}
</style>
</head>
<body>
<table cellpadding="0" cellspacing="0" width="850" style="margin:0 auto; ">
  <tr>
    <td colspan="2"><img src="/Public/Shop/images/logo.jpg" width="210" height="100" /></td>
    <td colspan="2" align="right"  ><div style="text-align:center; float:right;"><img src="/Public/Shop/images/qr.jpg" align="Scan by WeChat" style="width:100px; height:100px;" /><br />  Shopping on Phone</div></td>
  </tr>
  <tr>
    <td colspan="4" style="font-size:26px; font-weight:bold; text-align:center; padding:30px 0px 30px 10px;">Order Information</td>
  </tr>
  <tr>
    <td width="127">Name</td>
    <td width="355"><div class="bd" > <?php if(($vo["usertype"]) == "2"): echo get_username($db['userid']);?>
            <?php else: ?>
            <?php echo ($db["username"]); endif; ?></div></td>
    <td width="145">Phone Number</td>
    <td width="221"><div class="bd" ><?php echo ($db["telephone"]); ?></div></td>
  </tr>
  <tr>
    <td>Payment Method</td>
    <td><div class="bd" >
        <?php if(($db["paymethod"]) == "4"): ?>cash on delivery
          <?php else: ?>online payment<?php endif; ?>
      </div></td>
    <td>Invoice Required</td>
    <td><div class="bd" >
        <?php if(($db["invoice"]) == "1"): ?>yes
          <?php else: ?>
          no<?php endif; ?></div></td>
  </tr>
  <tr>
    <td>Order Date</td>
    <td><div class="bd" ><?php echo ($db["addtime"]); ?></div></td>
    <td>Delivery Date</td>
    <td><div class="bd" ><?php echo ($db["delivertime"]); ?></div></td>
  </tr>
  <tr>
    <td>Delivery Address</td>
    <td colspan="3"><div class="bd w710" ><?php echo ($db["address"]); ?></div></td>
  </tr>
</table>
<table border="1" style="width:850px; margin:0 auto; margin-top:15px; text-align:center; " cellpadding="2" cellspacing="0" class="table1">
  
  <?php if(($isShop) == "true"): $subtotal=0; ?> 
     <?php if(($isService) == "true"): ?><tr class="toolbar">
      <td colspan="6" class="tc">product list</td>
    </tr><?php endif; ?>
  <tr>
    <td width="123">No.</td>
    <td width="223">Product Name</td>
    <td width="99">Unit</td>
    <td width="97">Price</td>
    <td width="93">Quantity</td>
    <td width="213">Subtotal</td>
  </tr>
  <?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i; if(strpos($vo['sortpath'],',2,')){ $sub=(float)$vo['price']*$vo['num']; $subtotal+=$sub; ?>
    <tr align="center">
      <td><?php echo ($i); ?></td>
      <td align="left"><?php echo ($vo["productname"]); ?></td>
      <td><?php echo ($vo["unit"]); ?></td>
      <td>&yen;<?php echo ($vo["price"]); ?></td>
      <td><?php echo ($vo["num"]); ?></td>
      <td>&yen;<?php echo to_price($sub);?></td>
    </tr>
     <?php
 } endforeach; endif; else: echo "" ;endif; endif; ?>
  
  
  <?php if(($isService) == "true"): $subtotal=0; ?> 
     <?php if(($isShop) == "true"): ?><tr class="toolbar">
      <td colspan="6" class="tc">laundry service list</td>
    </tr><?php endif; ?>
  <tr>
    <td width="123">No.</td>
    <td width="223">Product Name</td>
    <td width="99">Unit</td>
    <td width="97">Price</td>
    <td width="93">Quantity</td>
    <td width="213">Subtotal</td>
  </tr>
  <?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i; if(strpos($vo['sortpath'],',3,')){ $sub=(float)$vo['price']*$vo['num']; $subtotal+=$sub; ?>
    <tr align="center">
      <td><?php echo ($i); ?></td>
      <td align="left"><?php echo ($vo["productname"]); ?></td>
      <td><?php echo ($vo["unit"]); ?></td>
      <td>&yen;<?php echo ($vo["price"]); ?></td>
      <td><?php echo ($vo["num"]); ?></td>
      <td>&yen;<?php echo to_price($sub);?></td>
    </tr>
     <?php
 } endforeach; endif; else: echo "" ;endif; endif; ?>
  
  <tr> 	
    <td colspan="5" align="right">Shipping fee</td>
    <td><?php if(($db["shipfee"]) == "0"): ?>FREE
        <?php else: ?>
        &yen;<?php echo ($db["shipfee"]); endif; ?></td>
  </tr>
  <?php if(($db["discount"]) != "0"): ?><tr> 
    <td colspan="5" align="right">Discount</td>
    <td> 
       - &yen;<?php echo ($db["discount"]); ?> </td>
  </tr><?php endif; ?>
  <tr> 
    <td colspan="5" align="right">Coupon</td>
    <td> 
       - &yen;<?php echo ($db["couponamount"]); ?> </td>
  </tr>
  <tr>
    <td colspan="5">Total</td>
    <td>&yen;<?php echo ($db["amount"]); ?></td>
  </tr>
</table>
<table style="width:850px; margin:0 auto; margin-top:20px;">
  <tr>
    <td>Customer Message:<?php echo ($db["info"]); ?></td>
  </tr>
  <tr>
    <td  height="100" align="center"><?php echo lbl('printfooter');?><br /><input type="button" value="打印" onclick="this.style.display='none';window.print();" /></td>
  </tr>
</table>
</body>
</html>