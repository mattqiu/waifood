<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<title><?php echo ($title); echo lbl('subtitleshop');?></title>
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
  <div class="box980">
    <div class="gmlc">
      <div class="gmjd01"></div>
      <div class="spqd">
        <form action="<?php echo U('Order/submitOrder');?>" method="post" name="form1" id="form1">
          <input type="hidden" name="shop_id" id="shop_id" value="1" />
          <?php if(($cartnum) == "0"): ?><div class="nocp">
              <div class="tishi">Your shopping cart is empty!<br />
                <br />
              </div>
              <a href="/Shop/" id="jxgw">Continue shopping &gt;&gt;</a></div>
            <?php else: ?>
            <!-- <?php if((get_userid() == 0)): ?><div class="nocp">
                <div class="tishi">You must be logged in to submit orders!  <a href="<?php echo U('Login/index');?>" class="alogin"  >click here to login.</a>  </div>
              </div><?php endif; ?>-->
            <?php if(($isShop) == "true"): $subtotal=0; ?>
              <?php if(($isService) == "true"): ?><div class="shoptitle">Product list</div><?php endif; ?>
              <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <thead>
                  <tr>
                    <td align="left">ID</td>
                    <td>Product Name</td>
                    <td>Unit Price</td>
                    <td>Quantity</td>
                    <td>Subtotal</td>
                    <td>Action</td>
                  </tr>
                </thead>
                <tbody>
                  <?php if(is_array($cart["cart_items"])): $i = 0; $__LIST__ = $cart["cart_items"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i; if(strpos($vo['sortpath'],',2,')){ $sub=(float)get_price($vo['id'],$vo['price'],$vo['ext'])*$vo['num']; $subtotal+=$sub; ?>
                    <tr>
                      <td align="left"><?php echo ($vo["id"]); ?></td>
                      <td align="left"><a href="<?php echo U('Product/view?id='.$vo['id']);?>"><img alt="<?php echo ($vo["name"]); ?>" src="<?php echo ($vo["indexpic"]); ?>" width="60" height="60"  /><?php echo ($vo["name"]); ?></a></td>
                      <td><span class="jiage">&yen;<?php echo get_price($vo['id'],$vo['price'],$vo['ext']);?></span></td>
                      <td><a href="javascript:void(null)" class="jian btnDeduct" ></a>
                        <input type="text" class="num"  onchange="$.editCart(<?php echo ($vo["id"]); ?>,(parseInt($(this).val())),'<?php echo ($vo["ext"]); ?>')" value="<?php echo ($vo["num"]); ?>" data="<?php echo ($sub); ?>" maxlength="3" />
                        <a href="javascript:void(null)" class="jia btnPlus" ></a></td>
                      <td><?php echo to_price($sub);?></td>
                      <td><a href="javascript:void(0);" onclick="$.delCart(<?php echo ($vo["id"]); ?>,'<?php echo ($vo["ext"]); ?>')">delete</a></td>
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
                    <td>Product Name</td>
                    <td>Unit Price</td>
                    <td>Quantity</td>
                    <td>Subtotal</td>
                    <td>Action</td>
                  </tr>
                </thead>
                <tbody>
                  <?php if(is_array($cart["cart_items"])): $i = 0; $__LIST__ = $cart["cart_items"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i; if(strpos($vo['sortpath'],',3,')){ $sub=(float)get_price($vo['id'],$vo['price'],$vo['ext'])*$vo['num']; $subtotal+=$sub; ?>
                    <tr>
                      <td align="left"><?php echo ($vo["id"]); ?></td>
                      <td align="left"><a href="<?php echo U('Service/view?id='.$vo['id']);?>"><img alt="<?php echo ($vo["name"]); ?>" src="<?php echo ($vo["indexpic"]); ?>" width="60" height="60"  /><?php echo ($vo["name"]); ?></a></td>
                      <td><span class="jiage">&yen;<?php echo get_price($vo['id'],$vo['price'],$vo['ext']);?></span></td>
                      <td><a href="javascript:void(null)" class="jian btnDeduct" ></a>
                        <input type="text" class="num"  onchange="$.editCart(<?php echo ($vo["id"]); ?>,(parseInt($(this).val())),'<?php echo ($vo["ext"]); ?>')" value="<?php echo ($vo["num"]); ?>" data="<?php echo ($sub); ?>" maxlength="3" />
                        <a href="javascript:void(null)" class="jia btnPlus" ></a></td>
                      <td><?php echo to_price($sub);?></td>
                      <td><a href="javascript:void(0);" onclick="$.delCart(<?php echo ($vo["id"]); ?>,'<?php echo ($vo["ext"]); ?>')">delete</a></td>
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
                <tr>
                  <td colspan="2" align="left"><a href="javascript:void(0);" onclick="$.clearCart();">× Empty cart</a></td>
                  <td colspan="4" align="right">Amount: <span class="">&yen;<?php echo ($cart["cart_amount"]); ?></span> <br>
                    Delivery Fee: <span class="">&yen;<?php echo ($cart["cart_shipfee"]); ?></span> <br />
                    Total Amount：<span class="jiage02">&yen;<font id=""><?php echo to_price($cart['cart_amount']+$cart['cart_shipfee']);?></font></span></td>
                </tr>
                <tr>
                  <td colspan="2" align="left"><a href="/" id="jxgw">Continue shopping</a></td>
                  <td colspan="4" align="right"><?php if(($cart["cart_num"]) == "0"): ?><span class="jixu">proceed to checkout1</span>
                      <?php else: ?>
                      <?php if((get_userid() == 0)): ?><a href="/login/index.html?returnurl=/settle/cashier" class="jixu" style="color:#fff" id="b1tnCheckout111">proceed to checkout2</a>
                        <?php else: ?>
                        <a href="<?php echo U('Settle/cashier');?>" class="jixu" style="color:#fff" >proceed to checkout3</a><?php endif; endif; ?></td>
                </tr>
              </tfoot>
            </table><?php endif; ?>
        </form>
      </div>
    </div>
  </div>
  <div class="clr"></div>
</div>


<?php if((get_userid() == 0)): ?><div class="zhezhao ld hide"></div>
  <div class="lg ld hide">
    <div class="lgts_title">
      <h1>Login to checkout</h1>
      <span class="close" onclick="$('.ld').hide()">×close</span></div>
    <div class="lg_txt">
      <table border="0" align="center" cellpadding="0" cellspacing="0">
        <tr>
          <td width="270"><br />
            User name<br />
            <input type="text" name="username" id="username" class="user" /></td>
        </tr>
        <tr>
          <td>Password<br />
            <input type="password" name="password" id="password" class="mima" /></td>
        </tr>
        <tr>
          <td><a href="<?php echo U('Login/findpwd');?>" style="color:#0e87d8" target="_blank">Forget your password?</a></td>
        </tr>
        <tr>
          <td><a href="javascript:void(0);" onclick="$.login($('#username').val(),$('#password').val(),'<?php echo U('Settle/cashier');?>')" class="lgbtn">Login Now</a></td>
        </tr>
      </table>
    </div>
    <div class="lg_txt02"><a href="<?php echo U('Login/create');?>" class="create" id="confirmcreate">Continue without<br />
      login</a><br />
      <br />
      <a href="<?php echo U('Login/register');?>" target="_blank" class="fc_red" > Register a New Account</a> </div>
  </div><?php endif; ?>


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
<script>
$(function(){
	$("#confirmcreate").click(function(){
				var url=$(this).attr("href"); 
		jConfirm('We strongly recommend that you <a href="<?php echo U('Login/register');?>" target="_blank" class="fc_blue" >register a new account.</a><br /><br />Are you sure you want to remain anonymous to buy?',SYSTITLE,function(ret){
			if (ret){
				location=url;	
			};
		});
		return false;	
	});	
});
</script>
</body>
</html>