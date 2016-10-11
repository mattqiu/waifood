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
    <div class="path"><?php echo get_product_location($id);?> </div>
      <div class="saixuan"> 
        <div class="saixuan_txt">
          <p><strong>Brand:</strong> <a href="<?php echo set_url('brand');?>" 
            <?php if(($brand) == ""): ?>class="selected"<?php endif; ?>
            >All</a>
            <?php if(is_array($brandlist)): $i = 0; $__LIST__ = $brandlist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><a href="<?php echo set_url('brand',$vo);?>" 
              <?php if((parse_param(I('get.brand'),true) == strtolower($vo))): ?>class="selected"<?php endif; ?>
              ><?php echo ($vo); ?></a><?php endforeach; endif; else: echo "" ;endif; ?>
          </p>
          <p><strong>Origin:</strong> <a href="<?php echo set_url('origin');?>" 
            <?php if(($origin) == ""): ?>class="selected"<?php endif; ?>
            >All</a>
            <?php if(is_array($originlist)): $i = 0; $__LIST__ = $originlist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><a href="<?php echo set_url('origin',$vo);?>" 
              <?php if((parse_param(I('get.origin'),true) == strtolower($vo))): ?>class="selected"<?php endif; ?>
              ><?php echo ($vo); ?></a><?php endforeach; endif; else: echo "" ;endif; ?>
          </p>
         

        </div>
      </div>
      <div class="rtitle01 px_tit"> <span id="fenye">
        <?php if(($pageprev) != ""): ?><a href="<?php echo set_url('p',$pageprev);?>"><span class="prev">Previous</span></a>
          <?php else: ?>
          <a href="javascript:void(0)" class="disabled"><span class="prev">Previous</span></a><?php endif; ?>
        <?php if(($pagenext) != ""): ?><a href="<?php echo set_url('p',$pagenext);?>"><span class="next">Next</span></a>
          <?php else: ?>
          <a href="javascript:void(0)" class="disabled"><span class="next">Next</span></a><?php endif; ?>
        </span><strong>Sort</strong><a href="<?php echo set_url('order',1);?>"  
      
        <?php if(($order) == "1"): ?>class="selected"<?php endif; ?>
        >Default</a><a href="<?php echo set_url('order',2);?>"  
      
        <?php if(($order) == "2"): ?>class="selected"<?php endif; ?>
        ><span  class="up">Price</span></a><a href="<?php echo set_url('order',3);?>"  
      
        <?php if(($order) == "3"): ?>class="selected"<?php endif; ?>
        ><span  class="down">Sales</span></a> </div>
      <div class="cptxt">
        <ul>
          <?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li><a  href="<?php echo U('Product/view?id='.$vo['id']);?>"><div class="hotico"><?php if(($vo["tag1"]) == "1"): ?><span class="ico01" title="Hot"></span><?php endif; if(($vo["tag2"]) == "1"): ?><span class="ico02" title="Discount"></span><?php endif; if(($vo["tag3"]) == "1"): ?><span class="ico03"></span><?php endif; if(($vo["tag4"]) == "1"): ?><span class="ico04"></span><?php endif; ?></div><img src="/Public/Shop/images/grey.gif" class="lazy" data-original="<?php echo ($vo["indexpic"]); ?>" alt="<?php echo ($vo["title"]); ?>" width="187" height="187"  alt="<?php echo ($vo["title"]); ?>" />
              <h2><?php echo ($vo["title"]); ?></h2>
              </a>
              <p>Unit: <?php echo ($vo["unit"]); ?><br>
                Origin: <?php echo ($vo["origin"]); ?><br>
                <span class="jiage02">&yen;<?php echo ($vo["price"]); ?></span>
                <?php if(($vo["price1"]) != "0"): ?><span class="marketprice">&yen;<?php echo ($vo["price1"]); ?></span><?php endif; ?>
                </p>
              <p class="jiarugwc"><a href="javascript:void(null)" class="addgwc" onclick="$.addCart('<?php echo ($vo["id"]); ?>',$(this).parent().find('input').val())"></a><a href="javascript:void(null)" class="jian btnDeduct"></a>
                <input type="text" value="1" class="num cartnum"  data-stock="<?php echo ($vo["stock"]); ?>" />
                <a href="javascript:void(null)" class="jia btnPlus"></a></p>
            </li><?php endforeach; endif; else: echo "" ;endif; ?>
        </ul>
      </div>
      <div class="pagelist"><?php echo ($page); ?></div>
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