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
 
</head>

<body>
<header><a id="list" href="<?php echo Uu('Category/index');?>"></a><a href="javascript:void(0);" onclick="history.back();" id="back"></a> <a href="<?php echo Uu('Index/index');?>" > <?php echo ($shoptitle); ?> </a> </header>   
<div class="container">
  <div class="content">
    <div class="main-list">
      <div class="main-item">
        <div class="item-title bdnone">
          <form  action="<?php echo U('Login/index');?>" method="post" id="frm_login" class="pure-form" > 
            <div class="settings p_b">
              <p>User name:</p>
              <p class="clearfix">
                <input type="text" value="" class="pure-input-1" placeholder="enter your user name" name="username" id="username"  >
              </p>
              <p>Password:</p>
              <p class="clearfix">
                <input type="password" placeholder="enter your password" class="pure-input-1" name="userpwd" id="userpwd"  >
              </p>
              <p class="t_cen">
                <button class="pure-button pure-button-success" style="width:100%;" type="submit" >Login now</button>
              </p>
              <p>
				<a style="float:right; width:auto;" href="<?php echo U('Login/findpwd');?>">Forget your password?</a>
				<a class="blue" style="display:inline; float:none;" href="<?php echo U('Login/register');?>">Register&gt;&gt;</a>
			  </p>
            </div>
          </form>
        </div>
        <div class="clr"></div>
      </div>
    </div>
  </div>
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