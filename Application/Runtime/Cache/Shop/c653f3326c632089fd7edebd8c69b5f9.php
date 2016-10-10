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
	var url="/member/info1.html?goto=cashier&addressid=3";
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
                        <table border="0" cellspacing="0" cellpadding="0" style="margin:0px auto">
                            <input type="hidden" value="<?php echo ($db["id"]); ?>" name="id"/>
                            <tr>
                                <td align="right">User Name：</td>
                                <td><?php echo get_username(get_userid());?></td>
                            </tr>
                            <tr>
                                <td align="right">Consingee：</td>
                                <td><input id="userreal" name="userreal" type="text" value="<?php echo ($db["username"]); ?>" placeholder="enter your name" class="input03" size="22"></td>
                            </tr>
                            <tr>
                                <td align="right">Phone：</td>
                                <td><input id="telephone" name="telephone" type="text" maxlength="20" value="<?php echo ($db["telephone"]); ?>" placeholder="enter your mobile" class="input03" size="22"></td>
                            </tr>
                            <tr>
                                <td align="right">Alternative phone：</td>
                                <td><input id="telephone_con" name="telephone_con" type="text" maxlength="20" value="<?php echo ($db["telephone2"]); ?>"  placeholder="enter your alternative phone" class="input03" size="32"></td>
                            </tr>
                            <tr>
                                <td align="right">Gender：</td>
                                <td>
                                    <input name="sex" type="radio" id="radio" value="1" <?php if($db['sex'] == 1): ?>checked<?php endif; ?> />
                                    <label for="radio">Male</label>
                                    <input type="radio" name="sex" id="radio2" value="0" <?php if($db['sex'] == 0): ?>checked<?php endif; ?>/>
                                    <label for="radio2">Female</label>
                                </td>
                            </tr>
                            <tr>
                                <td align="right">City：</td>
                                <td>
                                    <ul class="city">
                                        <li <?php if($db['cityname'] == 'chengdu'): ?>class="hover"<?php endif; ?>  data-city="chengdu">Chengdu</li>
                                        <li <?php if($db['cityname'] == 'chongqing'): ?>class="hover"<?php endif; ?>   data-city="chongqing">Chongqing</li>
                                        <li <?php if($db['cityname'] == 'shenzhen'): ?>class="hover"<?php endif; ?> data-city="shenzhen">Shenzhen</li>
                                        <li <?php if($db['cityname'] == 'Shanghai'): ?>class="hover"<?php endif; ?> data-city="Shanghai">Shanghai</li>
                                        <li <?php if($db['cityname'] == 'other'): ?>class="hover"<?php endif; ?>  data-city="other">Other</li>
                                    </ul>
                                </td>
                            </tr>
                            <tr>
                                <td align="right">Address：</td>
                                <td><input id="address" name="address" type="text" value="<?php echo ($db["address"]); ?>"
                                           placeholder="enter your detailed address" class="input03" size="100"></td>
                            </tr>
                            <tr>
                                <td align="right">Language：</td>
                                <td>
                                    <ul id="language" class="language">
                                        <li data-id="1"  <?php if($db['language'] == 1): ?>class="hover"<?php endif; ?> >Chinese general</li>
                                        <li  <?php if($db['language'] == 2): ?>class="hover"<?php endif; ?> data-id="2">Fluent in Chinese</li>
                                        <li  <?php if($db['language'] == 3): ?>class="hover"<?php endif; ?> data-id="3">English</li>
                                    </ul>
                                </td>
                            </tr>

                            <tr class="hide">
                                <td align="right">Remark：</td>
                                <td><textarea id="info" name="info" type="text" placeholder="enter your remark"
                                              maxlength="50" class="input03" style="width:576px; height:100px;"><?php echo ($db["info"]); ?></textarea>
                                </td>
                            </tr>
                            <tr>
                                <td>&nbsp;</td>
                                <td><input type="submit" onclick="subAddress()" class="jixu"/>
                                    <input type="button" onclick="history.back()" class="jixu" value="Back"/>
                                    <input type="hidden" name="url" id="url" value="<?php echo ($_GET['url']); ?>"/></td>
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

    function subAddress() {
        var consingee = $('#userreal').val();
        var telephone = $('#telephone').val();
        var address = $('#address').val();
        if (!consingee) {
            jAlert("The recipient can't be empty");
            return;
        }
        if (!telephone) {
            jAlert("The recipient phone can not be empty");
            return;
        }
        if (!address) {
            jAlert("Receives an address can't be empty");
            return;
        }
        var d = {
            id: $('input[name=id]').val(),
            username: consingee,
            address: address,
            telephone: telephone,
            telephone2: $('#telephone_con').val(),
            sex: $('#sex').val(),
            cityname: $('.city .hover').data('city'),
            language: $('#language .hover').data('id'),
            info: $('#info').val()
        }
        $.post('/member/modifyShoppingAddr', d, function (data) {
            if (data.code == 200) {
                if (data.data) {
                    window.location.href = data.data;
                }
            } else {
                jAlert(data.message);
            }
        });
    }
</script>
</body>
</html>