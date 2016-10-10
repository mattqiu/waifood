<?php if (!defined('THINK_PATH')) exit();?><!doctype html>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
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

</head>

<body>
<header><a id="list" href="<?php echo Uu('Category/index');?>"></a><a href="javascript:void(0);" onclick="history.back();" id="back"></a> <a href="<?php echo Uu('Index/index');?>" > <?php echo ($shoptitle); ?> </a> </header>  
<?php
$so=($sort=='desc'?'asc':'desc'); ?>
<div class="container pdt5">
  <div class="saixuan">
    <ul>
      <li><a href="<?php echo Uu('Product/lists','id='.$id.'&order=1');?>"  
        <?php if(($order) == "1"): ?>class="selected"<?php endif; ?>
        >Default</a></li>
      <li>
       <?php if(($order) == "2"): ?><a href="<?php echo Uu('Product/lists','id='.$id.'&order=2&sort='.$so);?>"   
        <?php if(($order) == "2"): ?>class="selected"<?php endif; ?>
        ><?php if(($sort) == "desc"): ?><span class="down">Price</span>
        <?php else: ?><span class="up">Price</span><?php endif; ?></a>
      <?php else: ?>
      <a href="<?php echo Uu('Product/lists','id='.$id.'&order=2&sort='.$so);?>"  ><span class="">Price</span></a><?php endif; ?>
    </li>
      <li>
      <?php if(($order) == "3"): ?><a href="<?php echo Uu('Product/lists','id='.$id.'&order=3&sort='.$so);?>"   
        <?php if(($order) == "3"): ?>class="selected"<?php endif; ?>
        ><?php if(($sort) == "desc"): ?><span class="down">Sold</span>
        <?php else: ?><span class="up">Sold</span><?php endif; ?></a>
      <?php else: ?>
      <a href="<?php echo Uu('Product/lists','id='.$id.'&order=3&sort='.$so);?>"  ><span class="">Sold</span></a><?php endif; ?>
      
      </li> 
    </ul>
  </div>
  <div class="clr"></div>
  <div class="splist">
    <ul>
      <?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li>
          <div class="box"> <a href="<?php echo Uu('Product/view','id='.$vo['id']);?>"> <img  src="/Public/Shop/images/grey.gif" class="lazy"  data-original="<?php echo ($vo["indexpic"]); ?>" alt="<?php echo ($vo["title"]); ?>"   width="100%" /> <strong  ><?php echo ($vo["title"]); ?></strong><br> 
            <div>Unit:<?php echo ($vo["unit"]); ?></div>
            <div>Preservation:<?php echo ($vo["storage"]); ?></div>
            <div>Origin:<?php echo ($vo["origin"]); ?></div>
            <?php if(!empty($vo["brand"])): ?><div>Brand:<?php echo ($vo["brand"]); ?></div><?php endif; ?></a>
            <div class="spjg">
              <div class="fl">
              <i class="price">&yen;<?php echo ($vo["price"]); ?></i>
              <?php if(($vo["price1"]) > "0"): ?><i class="price-delete">&yen;<?php echo ($vo["price1"]); ?></i>
                <?php else: ?>
                &nbsp;<?php endif; ?> </div>
              <a href="javascript:void(0);" onClick="$.addCart(<?php echo ($vo["id"]); ?>);" class="pure-button pure-button-success fr" >+ Cart</a>
              </div>
             </div>
        </li><?php endforeach; endif; else: echo "" ;endif; ?>
    </ul>
  </div>
  <div class="fybox"><?php echo ($page); ?></div>
  <div class="clr"></div>
</div>

<div class="container">
  <div class="main-list">
    <div class="main-item">
      <div class="bottom"><a href="#top" class="totop"></a>
        <?php  if(get_userid()=="0"){ ?>
        <a href="<?php echo Uu('Login/index');?>">Login</a><span style="margin:0 10px;">|</span><a href="<?php echo Uu('Login/register');?>">Register</a>
        <?php
 }else{ ?>
        <a href="<?php echo Uu('Member/info');?>">Account:<?php echo get_displayname();?></a> <a href="<?php echo Uu('Login/logout');?>"  class="hide">Logout</a>
        <?php
 } ?>
      </div>
    </div>
  </div>
  <div class="fnav"><?php echo lbl('navbot');?> </div>
  <div class="footer"> <?php echo C("config.WEB_SITE_ICP");?></div>
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
  <div class="clr"></div>
</div>

</body>
</html>