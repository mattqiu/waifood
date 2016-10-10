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
      <div class="left"> <div class="Ltit01"><strong>Account</strong></div>
<div class="txt01 hymenu">
<a href="<?php echo U('Member/info');?>">Personal information</a>
<!--<a href="<?php echo U('Member/address');?>">Shipping address</a>  -->
<a href="<?php echo U('Member/order');?>">My order</a> 
<a href="<?php echo U('Member/coupon');?>">My coupon</a> 
<a href="<?php echo U('Member/cleaning');?>">My cleaning</a> 
<a href="<?php echo U('Member/friends');?>">My friends list</a> 
<a href="<?php echo U('Member/pwd');?>">Change password</a> </div>
<script>
$(function(){
	var url="/Member/order.html";
	$(".hymenu a").each(function(){
		var href=$(this).attr("href");	
		if(href==url){
			$(this).addClass("selected");	
		}
	});
});
</script>
 </div>
      <div class="right">
      <div class="hybox">
        <div class="hytit"><strong>My order</strong>
          <div class="order-status"><a href="<?php echo U('Member/order?status=0');?>" <?php if(($status) == "0"): ?>class="selected"<?php endif; ?> >Draft(<?php echo ($orderstat[0]); ?>)</a><a href="<?php echo U('Member/order?status=1');?>" <?php if(($status) == "1"): ?>class="selected"<?php endif; ?> >Confirmed(<?php echo ($orderstat[1]); ?>)</a><a href="<?php echo U('Member/order?status=2');?>" <?php if(($status) == "2"): ?>class="selected"<?php endif; ?> >Delivering(<?php echo ($orderstat[2]); ?>)</a><a href="<?php echo U('Member/order?status=3');?>" <?php if(($status) == "3"): ?>class="selected"<?php endif; ?> >Completed(<?php echo ($orderstat[3]); ?>)</a><a href="<?php echo U('Member/order?status=4');?>" <?php if(($status) == "4"): ?>class="selected"<?php endif; ?> >Cancelled(<?php echo ($orderstat[4]); ?>)</a></div>
          </div>
        <div class="ddlist">
           <table width="100%" border="0" cellspacing="0" cellpadding="0">
             <thead>
              <tr>
                <td>Order no</td>
                <td width="80">Order place time</td>
                <td width="80">Delivery time</td>
                <td>Total amount</td>
                <td>Payment method</td>
                <td>Order status</td>
                <td>Action</td>
              </tr>
             </thead>
             <tbody> 
             
            <?php if(($listcount) == "0"): ?><tr  >
                <td colspan="7">not found.</td>
               </tr>
            <?php else: ?>
            <?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr>
                <td>
               <?php echo ($vo["orderno"]); ?>
             </td>
                <td width="80"><?php echo ($vo["addtime"]); ?></td>
                <td width="80"><?php echo ($vo["delivertime"]); ?></td>
                <td><span class="jiage02">&yen;<?php echo to_price($vo['amount']);?></span> </td>
                <td><?php if(($vo["paymethod"]) == "4"): ?>Cash on delivery<?php else: ?>Payment online (Paypal)<?php endif; ?> </td>
                <td><strong><?php echo get_status($vo['status']);?></strong></td>
                <td>
                <?php if(($vo["pay"]) == "1"): switch($vo["status"]): case "3": ?><a href="<?php echo U('Member/orderView?orderno='.$vo['orderno']);?>">View</a><!-- | 
                        <a href="<?php echo U('Member/orderComment?orderno='.$vo['orderno']);?>">评价</a>--><?php break;?>
                    <?php case "2": ?><a href="<?php echo U('Member/orderView?orderno='.$vo['orderno']);?>">View</a><br><a href="javascript:void(0);" onclick="$.confirmOrder('<?php echo ($vo["orderno"]); ?>');" class="qrsh"></a><?php break;?>
                    <?php default: ?>
                  	  <a href="<?php echo U('Member/orderView?orderno='.$vo['orderno']);?>">View</a><?php endswitch;?>
                <?php else: ?>
                	<?php if(($vo["paymethod"]) == "4"): else: ?> 
            <?php if(in_array(($vo["status"]), explode(',',"0,1"))): ?><a href="<?php echo U('Settle/pay?orderno='.$vo['orderno']);?>" class="jixu" style="color:#fff" target="_blank">Pay now</a><br /><?php endif; endif; ?>
                 	<?php switch($vo["status"]): case "0": ?><a href="<?php echo U('Member/orderView?orderno='.$vo['orderno']);?>">View</a>  <a href="javascript:void(0);" onclick="$.cancelOrder('<?php echo ($vo["orderno"]); ?>');">Cancel</a><?php break;?>
                    <?php case "1": ?><a href="<?php echo U('Member/orderView?orderno='.$vo['orderno']);?>">View</a>  <a href="javascript:void(0);" onclick="$.cancelOrder('<?php echo ($vo["orderno"]); ?>');">Cancel</a><?php break;?>
                    <?php default: ?>
                  	  <a href="<?php echo U('Member/orderView?orderno='.$vo['orderno']);?>">View</a><?php endswitch; endif; ?>
                </td>
              </tr><?php endforeach; endif; else: echo "" ;endif; endif; ?>
              </tbody>
          </table>

        </div>
        <div class="pagelist ddpage"><?php echo ($page); ?></div>
      </div>
    </div>
       
      <div class="clear"></div>
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