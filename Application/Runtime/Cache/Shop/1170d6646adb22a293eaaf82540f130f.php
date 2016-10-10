<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
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

    <script>
        var CONST_UPLOAD = "<?php echo U('Admin/File/upload');?>";
    </script>
    <script type="text/javascript" src="/Public/Admin/js/uploadify/jquery.uploadify.min.js"></script>
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
            <div class="left">
                <div class="Ltit01"><strong>Account</strong></div>
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
	var url="/member/info";
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
                    <div class="hytit"><strong>Personal information</strong></div>
                    <div class="grzl">

                        <table border="0" cellspacing="0" cellpadding="0">
                            <tr>
                                <td align="right">User Name：</td>
                                <td><?php echo get_username(get_userid());?></td>
                            </tr>
                            <tr>
                                <td align="right">Consignee：</td>
                                <td><?php echo ($db["userreal"]); ?></td>
                            </tr>
                            <tr>
                                <td align="right">Gender：</td>
                                <td>
                                    <?php if(($db["sex"]) == "1"): ?>male
                                        <?php else: ?>
                                        female<?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <td align="right">Mobile：</td>
                                <td><?php echo ($db["telephone"]); ?></td>
                            </tr>
                            <tr>
                                <td align="right">Email：</td>
                                <td><?php echo ($db["email"]); ?></td>
                            </tr>

                            <tr>
                                <td align="right">Address：</td>
                                <td><?php echo ($db["address"]); ?></td>
                            </tr>
                            <tr>
                                <td align="right">Registration：</td>
                                <td><?php echo ($db["addtime"]); ?></td>
                            </tr>
                            <tr>
                                <td>&nbsp;</td>
                                <td><a href="<?php echo U('Member/info1');?>" class="jixu">Edit</a></td>
                            </tr>
                        </table>
                    </div>
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

<script language="javascript">
    $(function () {
        $("body").mousemove(function () {
            var v = $("#indeximg").attr("src");
            var v1 = $("#indexpic").val();
            if (v != v1) {
                $("#indeximg").attr("src", $("#indexpic").val());
            }
        });
    });
</script>
</body>
</html>