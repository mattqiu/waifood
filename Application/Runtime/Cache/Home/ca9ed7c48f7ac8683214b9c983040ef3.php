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
          <form action="<?php echo U('Login/register');?>" method="post" onSubmit="return checkregform()" class="pure-form">
           <input type="hidden" name="usertype" id="usertype" value="3">
            <p><span class="fc_red">*</span> User name：</p>
            <p class="clearfix">
              <input type="text" name="username" id="username"  placeholder="enter your user name" class="pure-input-1"  required="required">
            </p>
            <p><span class="fc_red">*</span> Password：</p>
            <p class="clearfix">
              <input type="password" name="userpwd" id="userpwd"  placeholder="enter your password" class="pure-input-1" required>
            </p>
            <p><span class="fc_red">*</span> Confirm password：</p>
            <p class="clearfix">
              <input type="password" name="userpwd1" id="userpwd1"  placeholder="enter your password again" class="pure-input-1" required>
            </p> 
            <h3 style="width:100%; border-bottom:1px #ccc solid; margin-bottom:10px; line-height:30px;">Receiving information</h3>
           
            <p ><span class="fc_red">*</span> Name：</p>
            <p class="clearfix">
              <input type="text" name="userreal" id="userreal"  placeholder="enter your name" class="pure-input-1" required>
            </p>
            <p ><span class="fc_red">*</span> Gender：</p>
            <p class="clearfix">
               <label  ><input name="sex" type="radio" id="sex1" value="1" checked />
                  Male</label>
                  <label ><input type="radio" name="sex" id="sex2" value="2" />
                  Female</label>
            </p>
            <p><span class="fc_red">*</span> Email：</p>
            <p class="clearfix">
              <input type="text" name="email" id="email"  placeholder="enter your email" class="pure-input-1" required>
            </p>
            <p><span class="fc_red">*</span> Mobile：</p>
            <p class="clearfix">
              <input type="text" name="telephone" id="telephone"  placeholder="enter your mobile" class="pure-input-1" required>
            </p>
            <p><span class="fc_red">*</span> Address：</p>
            <p class="clearfix">
              <input type="text" name="address" id="address"  placeholder="enter your address" class="pure-input-1" required>
            </p>
            <p>Remark：</p>
            <p class="clearfix">
              <input type="text" name="info" id="info"  placeholder="enter your remark" class="pure-input-1" required>
            </p>
            <span id="erroInfonew" class="red"></span>
            <p class="t_cen m_t"> 
                <button class="pure-button pure-button-success" style="width:100%;" type="submit" >Register</button>
            </p>
          <p><a href="<?php echo U('Login/index');?>">Already have an account >></a></p>
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