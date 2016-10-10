<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<title><?php echo ($title); ?></title>
<script>
var APP_PATH="";
var CONST_PUBLIC="/Public";
var CONST_CART="<?php echo U('Cart/URL');?>";
</script>
<script type="text/javascript" src="/Public/Shop/js/jquery-1.8.0.min.js"></script><script type="text/javascript" src="/Public/Shop/js/jquery.func.js"></script><script type="text/javascript" src="/Public/Shop/js/jquery.lazyload.js"></script><script type="text/javascript" src="/Public/Admin/js/jquery.numeric.only.js"></script>
<!--[if IE 6]>
<script type="text/javascript" src="/Public/Shop/js/pngfix.js"></script>
<![endif]-->
<link rel="stylesheet" type="text/css" href="/Public/Shop/css/style.css?04241" /> 

</head>

<body>

<div class="topbar">
	<div class="wrap">
		<div class="links">
			 <?php if((get_userid() != 0)): ?><a href="/">Home</a>/<a href="javascript:void(0);" onclick="$.logout()" class="fc_red">Log out</a>
  <?php else: ?>
   <a href="/">Home</a>|<a href="<?php echo U('Login/index');?>">Login</a>/<a href="<?php echo U('Login/register');?>">Register</a><?php endif; ?>|<a href="<?php echo U('Member/order');?>">My Order</a>|<a href="<?php echo U('Form/view','id=1');?>">Wish list</a>|<a href="<?php echo U('Content/lists','id=15');?>">About us</a>
		</div>
		<div class="server-text"><a href="<?php echo U('Member/index');?>"><?php echo get_displayname();?></a> Customer Service:<?php echo lbl('telephone');?></div>
	</div>
</div>

 <div class="container">
  <div class="top">
    <div class="logo"><a href="/"><img src="/Public/Shop/images/logo.jpg" width="210" height="100"  alt=""/></a></div>
	<div class="search">
     <form action="<?php echo U('Search/index');?>" method="get" >
	    <input type="text" name="keyword" id="keyword" placeholder="keywords..." 
        <?php if(!empty($keyword)): ?>value="<?php echo ($keyword); ?>"<?php endif; ?> 
        />
      <button type="submit"   value="" ></button>
	    </form> 
	</div>
	<div class="txt">
		<?php echo lbl('adtop');?>
	</div>
    
  </div> 
  <div class="clr"></div>
</div>

<div class="container"> 
  <div class="nav">
  <div class="nav_center"> 
	  <ul class="navigation"> 
	    <li ><a href="/" class="selected">Shopping</a></li>
          <!--<li style="background-color:#FFAE00;"><a href="<?php echo U('Service/lists','id=3');?>"><span class="icon-new"></span>Laundry Service</a></li>
		  <li><a href="<?php echo U('Cleaning/index');?>">House Cleaning</a></li>
		  <li><a href="<?php echo U('Special/index','id=2');?>">Countryside Farm</a>
		  <li><a href="<?php echo U('Special/index','id=3');?>">Chinese Training</a>
          <li class="min-font"><a href="javascript:void(0);" >Planning more...</a></li>-->
	  </ul>
	  
	  <div class="shopping"> <span class="gwcname"><a href="<?php echo U('Settle/Cart');?>" style="color:#FFF;">Shopping Cart</a></span><span class="shuliang" id="CartNo">0</span>
	    <div class="shop">
	      
	    </div>
	  </div>
	  <div class="username"></div>
	  </div>
	</div>  
  <div class="clr"></div>
</div>
<div class="container">
  <div class="content">
    <div class="box980">
      <div class="gmlc regbox">
        <div class="ddtit"><span style="float:right"><a href="<?php echo U('Member/order');?>" style="color:#1376bb">Back to my order</a></span><strong>Order No.：<?php echo ($db["orderno"]); ?>    Status：<span style="color:#FF6600;"><?php echo get_status($db['status']);?> </span></strong></div>
        <h2>Receiving information</h2>
        <div class="txt02">Name：<?php echo ($db["username"]); ?><br />
          Mobile：<?php echo ($db["telephone"]); ?> <br />
          <!--Email：<?php echo ($db["email"]); ?><br />-->
          Address：<?php echo ($db["proname"]); echo ($db["cityname"]); echo ($db["disname"]); ?> <?php echo ($db["address"]); ?></div>
        <h2>Payment</h2>
        <div class="txt02">
          <?php if(($db["paymethod"]) == "4"): ?>Cash on delivery
            <?php else: ?>
            Online payment<?php endif; ?>
          <?php if(($db["pay"]) == "1"): ?><span class="fc_red">( Paid )</span><?php endif; ?>
        </div>
        <h2>Invoice</h2>
        <div class="txt02">
          <?php if(($db["inovice"]) == "1"): ?>Yes
            <?php else: ?>
            No<?php endif; ?>
        </div>
        <h2>Delivery date</h2>
        <div class="txt02"><?php echo ($db["delivertime"]); ?></div>
        <h2>Remarks</h2>
        <div class="txt02"><?php echo ($db["info"]); ?></div>
        <h2>Product list</h2>
        <div class="spqd">
          <?php if(($isShop) == "true"): $subtotal=0; ?>
            <?php if(($isService) == "true"): ?><div class="shoptitle">Product list</div><?php endif; ?>
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
              <thead>
                <tr>
                  <td align="left">ID</td>
                  <td align="left">Product name</td>
                  <td>Price</td>
                  <td>Quantity</td>
                  <td>Amount</td>
                </tr>
              </thead>
              <tbody>
                <?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i; if(strpos($vo['sortpath'],',2,')){ $sub=(float)get_price($vo['id'],$vo['price'],$vo['ext'])*$vo['num']; $subtotal+=$sub; ?>
                  <tr>
                    <td align="left"><?php echo ($vo["productid"]); ?></td>
                    <td align="left"><a href="<?php echo U('Product/view','id='.$vo['productid']);?>" target="_blank"><img src="<?php echo ($vo["indexpic"]); ?>" alt="<?php echo ($vo["productname"]); ?>" width="60" height="60" /><?php echo ($vo["productname"]); ?></a></td>
                    <td><span class="jiage">&yen;<?php echo ($sub); ?></span></td>
                    <td><?php echo ($vo["num"]); ?></td>
                    <td><span class="jiage">&yen;<?php echo to_price($sub);?></span></td>
                  </tr>
                  <?php
 } endforeach; endif; else: echo "" ;endif; ?>
              </tbody>
              <?php if(($isService) == "true"): ?><tfoot>
                  <tr>
                    <td colspan="2" align="left">&nbsp;</td>
                    <td colspan="4" align="right"> Sub Total：<span class="jiage02">&yen;<font id=""><?php echo to_price($subtotal);?></font></span></td>
                  </tr>
                </tfoot><?php endif; ?>
            </table><?php endif; ?>
          <?php if(($isService) == "true"): $subtotal=0; ?>
            <?php if(($isShop) == "true"): ?><div class="shoptitle">Laundry service list</div><?php endif; ?>
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
              <thead>
                <tr>
                  <td align="left">ID</td>
                  <td align="left">Product name</td>
                  <td>Price</td>
                  <td>Quantity</td>
                  <td>Amount</td>
                </tr>
              </thead>
              <tbody>
                <?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i; if(strpos($vo['sortpath'],',3,')){ $sub=(float)get_price($vo['id'],$vo['price'],$vo['ext'])*$vo['num']; $subtotal+=$sub; ?>
                  <tr>
                    <td align="left"><?php echo ($vo["productid"]); ?></td>
                    <td align="left"><a href="<?php echo U('Product/view','id='.$vo['productid']);?>" target="_blank"><img src="<?php echo ($vo["indexpic"]); ?>" alt="<?php echo ($vo["productname"]); ?>" width="60" height="60" /><?php echo ($vo["productname"]); ?></a></td>
                    <td><span class="jiage">&yen;<?php echo ($sub); ?></span></td>
                    <td><?php echo ($vo["num"]); ?></td>
                    <td><span class="jiage">&yen;<?php echo to_price($sub);?></span></td>
                  </tr>
                  <?php
 } endforeach; endif; else: echo "" ;endif; ?>
              </tbody>
              <?php if(($isShop) == "true"): ?><tfoot>
                  <tr>
                    <td colspan="2" align="left">&nbsp;</td>
                    <td colspan="4" align="right"> Sub Total：<span class="jiage02">&yen;<font id=""><?php echo to_price($subtotal);?></font></span></td>
                  </tr>
                </tfoot><?php endif; ?>
            </table><?php endif; ?>
          <table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tfoot>
              <tr align="right">
                <td colspan="5"><!--<?php echo ($db["num"]); ?> product(s),--> total：&yen;<?php echo ($db["amountall"]); ?><br />
                  Delivery Fee： &yen;<?php echo ($db["shipfee"]); ?><br />
                  <?php if(($db["discount"]) > "0"): ?>Discount： -&yen;<?php echo ($db["discount"]); ?> <br /><?php endif; ?>
                  <?php if(($db["couponamount"]) > "0"): ?>Coupon： -&yen;<?php echo ($db["couponamount"]); ?> <br /><?php endif; ?>
                  
                  <!--Deductible： -&yen;<?php echo ($db["creditamount"]); ?>--></td>
              </tr>
              <tr align="right">
                <td colspan="5">Total Amount：<span class="jiage02">&yen;<?php echo ($db["amount"]); ?></span></td>
              </tr>
              <?php if(($db["pay"]) == "0"): if(in_array(($db["status"]), explode(',',"0,1"))): if(($db["paymethod"]) != "4"): ?><tr align="center">
                      <td colspan="5"><a href="<?php echo U('Settle/pay','orderno='.$db['orderno']);?>" target="_blank" class="jixu" style="color:#fff;">Pay now</a></td>
                    </tr><?php endif; endif; endif; ?>
            </tfoot>
          </table>
        </div>
      </div>
    </div>
  </div>
  <div class="clr"></div>
</div>
<div class="container">
<div class="fuwu">
  <ul>
    <li>
      <h2>Getting Started</h2>
       <?php $_result=get_lists(11,5,2);if(is_array($_result)): $i = 0; $__LIST__ = $_result;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i; if(empty($vo["linkurl"])): ?><p><a href="<?php echo U('Content/view','id='.$vo['id']);?>" ><?php echo ($vo["title"]); ?></a></p>
          <?php else: ?>
          
          <p><a href="<?php echo ($vo["linkurl"]); ?>" ><?php echo ($vo["title"]); ?></a></p><?php endif; endforeach; endif; else: echo "" ;endif; ?>
    </li>
    <li>
      <h2>Delivery Service</h2> 
       <?php $_result=get_lists(12,5,2);if(is_array($_result)): $i = 0; $__LIST__ = $_result;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i; if(empty($vo["linkurl"])): ?><p><a href="<?php echo U('Content/view','id='.$vo['id']);?>" ><?php echo ($vo["title"]); ?></a></p>
          <?php else: ?>
          
          <p><a href="<?php echo ($vo["linkurl"]); ?>" ><?php echo ($vo["title"]); ?></a></p><?php endif; endforeach; endif; else: echo "" ;endif; ?>
    </li>
    <li>
      <h2>Payment</h2>
       <?php $_result=get_lists(13,5,2);if(is_array($_result)): $i = 0; $__LIST__ = $_result;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i; if(empty($vo["linkurl"])): ?><p><a href="<?php echo U('Content/view','id='.$vo['id']);?>" ><?php echo ($vo["title"]); ?></a></p>
          <?php else: ?>
          
          <p><a href="<?php echo ($vo["linkurl"]); ?>" ><?php echo ($vo["title"]); ?></a></p><?php endif; endforeach; endif; else: echo "" ;endif; ?>
    </li>
    <li>
      <h2>After-Sale Service</h2>
       <?php $_result=get_lists(14,5,2);if(is_array($_result)): $i = 0; $__LIST__ = $_result;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i; if(empty($vo["linkurl"])): ?><p><a href="<?php echo U('Content/view','id='.$vo['id']);?>" ><?php echo ($vo["title"]); ?></a></p>
          <?php else: ?>
          
          <p><a href="<?php echo ($vo["linkurl"]); ?>" ><?php echo ($vo["title"]); ?></a></p><?php endif; endforeach; endif; else: echo "" ;endif; ?>
    </li>
  </ul>
</div>
<div class="foot"><?php echo lbl('copyright');?></div>
 
	<div class="clr"></div>
</div>
<script>
    $(function(){
        $('.city li').click(function () {
            $('.city li').removeClass('hover');
            $(this).addClass('hover');
        })

        $('.language li').click(function () {
            $('.language li').removeClass('hover');
            $(this).addClass('hover');
        })
    })
</script>
</body>
</html>