<?php if (!defined('THINK_PATH')) exit();?><!doctype html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo ($title); ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<meta name="format-detection" content="telephone=no"/>
<script>
var APP_PATH="";
var CONST_PUBLIC="/Public";
var CONST_CART="<?php echo U('Cart/URL');?>";
var EXTRA_PARAM="<?php echo get_fid();?>";
</script>
<script type="text/javascript" src="/Public/Home/member/js/jquery-2.0.3.min.js"></script><script type="text/javascript" src="/Public/Home/js/jquery.func.js"></script><script type="text/javascript" src="/Public/Admin/js/jquery.numeric.only.js"></script><script type="text/javascript" src="/Public/Shop/js/jquery.lazyload.js"></script>
<link rel="stylesheet" type="text/css" href="/Public/Home/member/css/pure-min.css" /><link rel="stylesheet" type="text/css" href="/Public/Home/member/css/pure-style.css" /><link rel="stylesheet" type="text/css" href="/Public/Home/css/main.css" /> 
 
<script src="/Public/Home/js/touchslider.dev.js"></script>
<script src="/Public/Home/js/banner.js"></script>
</head>
<body style=" background:#F8F8F8; ">

<!--顶部-->
<header><a id="list" href="<?php echo Uu('Category/index');?>"></a>  <?php echo ($shoptitle); ?>  </header>
<!--banner-->
<div id="wrapper">
  <div class="swipe">
    <ul id="slider">
      <?php echo lbl('wx_banner');?>
    </ul>
    <div id="pagenavi"></div>
  </div>
</div>

<!--图标-->
<div class="icoboxindex"> 
<?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><a href="<?php echo Uu('Product/lists','id='.$vo['id']);?>"><span><img src="/Public/Home/images/icons/<?php echo ($i); ?>.png"></span>
  <h1><?php echo ($vo["name"]); ?></h1>
  </a><?php endforeach; endif; else: echo "" ;endif; ?>
  </div>
<!--新品上市-->
<div class="imgbox"> 
<?php echo lbl("wx_ad");?>
</div>
<!--版权-->
<hr class="spliter" />
<div class="footindex"><?php echo C("config.WEB_SITE_ICP");?></div>
<!--底部-->

  <div class="footer-form hide" id="searchform">
    <form class="pure-form" id="formSearch" name="formSearch" method="get" action="/Home/Search/index.html">
      <input type="text" value="<?php echo I('get.keyword');?>" id="keyword1" name="keyword" placeholder="keyword" class="pure-input pure-input-1">
      <button class="pure-button btnSearch" type="submit"><i class="fa fa-search"></i></button>
    </form>
  </div>
<footer>
  <ul>
    <li><a href="<?php echo Uu('Search/index');?>" id="sou"></a></li>
    <li><a href="<?php echo Uu('Shop/cart');?>" id="gwc"><span id="CartNo">0</span></a></li>
    <li><a href="<?php echo Uu('Member/index');?>" id="hy"></a></li>
    <li onClick="$('.tcbox').toggle()">
      <div class="tcbox">
          <div class="smenu"><a href="javascript:void(0);" onClick="WeixinJSBridge.call('closeWindow');">Suggestion</a><a href="<?php echo Uu('Content/lists','id=1');?>">Help</a></div>
        <div class="jiao">
          <svg  height="5" >
            <polygon points="0,0 10,0 5,5 "
   style="fill:#90B830" />
          </svg>
        </div>
      </div>
      <a href="javascript:void(0);" id="help"></a></li>
  </ul>
</footer>
</body>
</html>