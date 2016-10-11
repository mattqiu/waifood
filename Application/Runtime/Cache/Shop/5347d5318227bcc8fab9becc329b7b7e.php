<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<title><?php echo ($title); echo lbl('subtitleshop');?></title>
<meta name="keywords" content="<?php echo ($keywords); ?>" />
<meta name="description" content="<?php echo ($description); ?>" />
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

<script type="text/javascript" src="/Public/Shop/js/jquery.idTabs.min.js"></script>
<script type="text/javascript" src="/Public/Shop/js/jquery.jcarousel.pack.js"></script><script type="text/javascript" src="/Public/Shop/js/jquery.jqzoom-core-pack.js"></script><script type="text/javascript" src="/Public/Shop/js/jquery.jproducts.js"></script>
<link rel="stylesheet" type="text/css" href="/Public/Shop/js/jquery.jcarousel.css" /><link rel="stylesheet" type="text/css" href="/Public/Shop/js/jquery.jqzoom.css" />
<script language="javascript">
$(function(){
	$(".btnDeduct").on("click",function() {
		var minbuy = parseInt($("#buyAmount").attr("min")); 
		var num = parseInt($("#buyAmount").val()); 
		if(minbuy!=0){
			if(num<minbuy){
				jTip('Sorry, this product requires a minimum quantity of '+minbuy+'.');	
				$("#buyAmount").val(minbuy);
				return false;
			}
		};
		return false;
 	});
});
</script>
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
  <div class="lbox">
      
    <div class="product_menu">
  <ul>
    <?php $_result=get_product_channel(2,20);if(is_array($_result)): $i = 0; $__LIST__ = $_result;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li><span>
        <?php if(empty($vo["linkurl"])): ?><a href="<?php echo U('Product/lists','id='.$vo['id']);?>" ><?php echo ($vo["name"]); ?></a>
          <?php else: ?>
          <a href="<?php echo ($vo["linkurl"]); ?>" ><?php echo ($vo["name"]); ?></a><?php endif; ?>
        </span>
        <div class="submenu">
          <?php $_result=get_product_channel($vo['id'],20);if(is_array($_result)): $i = 0; $__LIST__ = $_result;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo1): $mod = ($i % 2 );++$i; if(empty($vo1["linkurl"])): ?><a href="<?php echo U('Product/lists','id='.$vo1['id']);?>" ><?php echo ($vo1["name"]); ?></a>
              <?php else: ?>
              <a href="<?php echo ($vo1["linkurl"]); ?>" > <?php echo ($vo1["name"]); ?></a><?php endif; endforeach; endif; else: echo "" ;endif; ?>
        </div>
      </li><?php endforeach; endif; else: echo "" ;endif; ?>
  </ul>
</div>

      <div class="clear"></div>
      <div class="ladbox">
      <?php echo lbl('adleft');?>
      </div>
    </div>
    
  <div class="rbox">
    <div class="path"><?php echo get_product_location($db['pid']);?> </div>
   
          <div class="cpshow">
     <?php if((count($gallery)) != "0"): ?><div class="bigcp">
                <?php if(is_array($gallery)): $i = 0; $__LIST__ = $gallery;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i; if(($key) == "0"): ?><a href="<?php echo ($vo); ?>" class="jqzoom" rel="gal1" title="<?php echo ($db["title"]); ?>"> <img src="<?php echo ($vo); ?>" width="360" height="360"  alt="" /></a><?php endif; endforeach; endif; else: echo "" ;endif; ?>
                </div>
            <div class="smallcp"> <a href="javascript:void(null)" class="pro-prev" id="mycarousel-prev"></a>
              <div  class="mycarousel-box">
              <ul  id="mycarousel">
                <?php if(is_array($gallery)): $i = 0; $__LIST__ = $gallery;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li><a href="javascript:void(0);" <?php if(($key) == "0"): ?>class="zoomThumbActive"<?php endif; ?> rel="{ gallery: 'gal1',largeimage:'<?php echo ($vo); ?>',smallimage:'<?php echo ($vo); ?>' }" title=""><img src="<?php echo (get_thumb($vo,80,80)); ?>" width="60" height="60"  alt="" /></a></li><?php endforeach; endif; else: echo "" ;endif; ?>
              </ul>
              </div>
              <a href="javascript:void(null)" class="pro-next" id="mycarousel-next"></a> </div>
          <?php else: ?>
           <div class="bigcp"   ><img src="<?php echo ((isset($db["indexpic"]) && ($db["indexpic"] !== ""))?($db["indexpic"]):C('DEFAULT_AVATAR')); ?>" width="360" height="360"  alt="<?php echo ($db["title"]); ?>"  />
    </div><?php endif; ?>
          </div>
    <div class="cpcs">
      <h2><?php echo ($db["title"]); ?></h2>
      <p class="cs"><strong>Unit:</strong> <?php echo ($db["unit"]); ?><br>
        <strong>Preservation:</strong> <?php echo ($db["storage"]); ?><br>
        <strong>Origin:</strong> <?php echo ($db["origin"]); ?><br>
        <?php if(!empty($db["brand"])): ?><strong>Brand:</strong>
        <?php echo ($db["brand"]); ?><br /><?php endif; ?>
        <span class="jiage02">&yen;<?php echo ($db["price"]); ?></span>
        <?php if(($db["price1"]) > "0"): ?><span class="marketprice">&yen;<?php echo ($db["price1"]); ?></span><?php endif; ?>
        <?php if(!empty($db["notice"])): ?><br /><strong>Notice:</strong>
        <?php echo ($db["notice"]); endif; ?>
        </p>
       
      <p><strong>Quantity:</strong>&nbsp;<a href="javascript:void(null)" class="jian btnDeduct"></a>
      <?php if(($db["minbuy"]) > "0"): ?><input type="text"  class="num cartnum" id="buyAmount" name="buyAmount" value="<?php echo ($db["minbuy"]); ?>" min="<?php echo ($db["minbuy"]); ?>" max="999" data-stock="<?php echo ($db["stock"]); ?>">
      <?php else: ?>
        <input type="text" class="num cartnum" id="buyAmount" min="1" max="999" value="1"  data-stock="<?php echo ($db["stock"]); ?>"><?php endif; ?>
        <a href="javascript:void(null)" class="jia btnPlus"></a></p>
      <p>
        <input type="image" name="imageField" id="imageField" src="/Public/Shop/images/addbtn02.jpg" onclick="$.addCart('<?php echo ($db["id"]); ?>',$('#buyAmount').val())" />
      </p>
    </div>
    <div class="clear"></div>
    <?php if(!empty($db["content"])): ?><div class="tjcp">
      <h2>Description</h2>
    </div>
    <div class="cpcontent" style="padding:10px 0px;">
    	<?php echo ($db["content"]); ?>
    </div><?php endif; ?>
    <div class="tjcp">
      <h2>You might like</h2>
    </div>
    <div class="cptxt">
      <ul>
      <?php $_result=get_product_lists($db['pid'],4,2);if(is_array($_result)): $i = 0; $__LIST__ = $_result;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i; if(empty($vo["linkurl"])): ?><li><a href="<?php echo U('Product/view','id='.$vo['id']);?>" target="_blank"><img src="/Public/Shop/images/grey.gif" class="lazy"  data-original="<?php echo ($vo["indexpic"]); ?>"  alt="<?php echo ($vo["title"]); ?>"  width="187" height="187"  />
                <h2><?php echo ($vo["title"]); ?></h2>
                </a>
                <p>Unit: <?php echo ($vo["unit"]); ?><br> 
                  Origin: <?php echo ($vo["origin"]); ?><br>
                  <span class="jiage02">&yen;<?php echo ($vo["price"]); ?></span></p>
                <p class="jiarugwc"><a href="javascript:void(null)" class="addgwc" onclick="$.addCart('<?php echo ($vo["id"]); ?>',$(this).parent().find('input').val())"></a><a href="javascript:void(null)" class="jian btnDeduct"></a>
                  <input type="text" value="1" class="num" />
                  <a href="javascript:void(null)" class="jia btnPlus"></a></p>
              </li>
              <?php else: ?>
              <li><a href="<?php echo ($vo["linkurl"]); ?>" target="_blank"><img src="<?php echo ($vo["indexpic"]); ?>"  alt="<?php echo ($vo["title"]); ?>"  width="187" height="187"  />
                <h2><?php echo ($vo["title"]); ?></h2>
                </a>
                <p>Unit: <?php echo ($vo["unit"]); ?><br> 
                  Origin: <?php echo ($vo["origin"]); ?><br>
                  <span class="jiage02">&yen;<?php echo ($vo["price"]); ?></span></p>
                <p class="jiarugwc"><a href="javascript:void(null)" class="addgwc" onclick="$.addCart('<?php echo ($vo["id"]); ?>',$(this).parent().find('input').val())"></a><a href="javascript:void(null)" class="jian btnDeduct"></a>
                  <input type="text" value="1" class="num" />
                  <a href="javascript:void(null)" class="jia btnPlus"></a></p>
              </li><?php endif; endforeach; endif; else: echo "" ;endif; ?>
      </ul>
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