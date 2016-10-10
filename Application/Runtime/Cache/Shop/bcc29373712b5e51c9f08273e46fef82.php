<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
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
    <div class="box980">
      <div class="regbox">
        <h1><span style="float:right">I have registered,<a href="<?php echo U('Login/index');?>" style="color:#0e87d8;">Login Now</a></span><strong>Member registration</strong></h1>
        <div class="reg">
          <form id="myform" method="post" onsubmit="return false;" >
            <table border="0" align="center" cellpadding="0" cellspacing="0">
                <tr>
                    <td align="right">User Name：</td>
                    <td><input type="text" class="reginput"  size="40" name="username" id="username"  placeholder="enter your user username" required="required" />
                        <span class="fc_red">*</span></td>
                </tr>
                <tr>
                    <td align="right">Email：</td>
                    <td><input  id="email" name="email" type="text" maxlength="50"  value="" required="required" placeholder="Email for login or receive key information" class="input03"  size="40"> <span class="fc_red">*</span></td>
                </tr>
                <tr>
                    <td align="right">Phone：</td>
                    <td><input type="text" class="reginput" maxlength="50"  size="40" name="telephone" id="telephone"  placeholder="Phone to contact you" required="required" />
                        <span class="fc_red">*</span></td>
                </tr>

                <tr>
                    <td align="right">Password：</td>
                    <td><input  class="reginput" maxlength="32" size="40" type="password" name="userpwd" id="userpwd"  placeholder="enter your password" required="required" />
                        <span class="fc_red">*</span></td>
                </tr>
                <tr>
                    <td align="right">Confirm Password：</td>
                    <td><input  class="reginput" maxlength="32" size="40" type="password" name="userpwd1" id="userpwd1"  placeholder="confirm your password" required="required" />
                        <span class="fc_red">*</span></td>
                </tr>
                <tr>
                    <td align="right"> City in China：</td>
                    <td>
                        <ul id="levecity" class="city">
                            <li class="hover"  data-city="chengdu">Chengdu</li>
                            <li data-city="chongqing">Chongqing</li>
                            <li data-city="xi'an">Xi'an</li>
                            <li data-city="kunming">Kunming</li>
                            <li data-city="other">Other</li>
                        </ul>
                    </td>
                </tr>
                <tr>
                    <td align="right">Your Nationality：</td>
                    <td>
                        <select name="nationality" style="height: 35px;padding: 0 45px;" >
                            <option value=""> Please select your nationality</option>
                            <option value="Andorra">Andorra</option>
                            <option value="Argentina">Argentina</option>
                            <option value="Australia">Australia</option>
                            <option value="Austria">Austria</option>
                            <option value="Belarus">Belarus</option>
                            <option value="Belgium">Belgium</option>
                            <option value="Bolivia">Bolivia</option>
                            <option value="Bosnia and Herzegovina">Bosnia and Herzegovina</option>
                            <option value="Brazil">Brazil</option>
                            <option value="Brunei">Brunei</option>
                            <option value="Bulgaria">Bulgaria</option>
                            <option value="Burma">Burma</option>
                            <option value="Canada">Canada</option>
                            <option value="Chile">Chile</option>
                            <option value="China">China</option>
                            <option value="China Taiwan">China Taiwan</option>
                            <option value="Colombia">Colombia</option>
                            <option value="Crotia">Crotia</option>
                            <option value="Cuba">Cuba</option>
                            <option value="Cyprus ">Cyprus </option>
                            <option value="Czech Republic">Czech Republic</option>
                            <option value="Denmark">Denmark</option>
                            <option value="Ecuador">Ecuador</option>
                            <option value="Egypt">Egypt</option>
                            <option value="Fiji Islands">Fiji Islands</option>
                            <option value="Finland">Finland</option>
                            <option value="France">France</option>
                            <option value="Germany">Germany</option>
                            <option value="Greece">Greece</option>
                            <option value="HK">HK</option>
                            <option value="Hungary">Hungary</option>
                            <option value="Iceland">Iceland</option>
                            <option value="Iceland">Iceland</option>
                            <option value="India">India</option>
                            <option value="Indonesia">Indonesia</option>
                            <option value="Indonesia">Indonesia</option>
                            <option value="Iran">Iran</option>
                            <option value="Ireland">Ireland</option>
                            <option value="Israel">Israel</option>
                            <option value="Italy">Italy</option>
                            <option value="Jamaica">Jamaica</option>
                            <option value="Japan">Japan</option>
                            <option value="Jordan">Jordan</option>
                            <option value="Kazakhstan">Kazakhstan</option>
                            <option value="Korea">Korea</option>
                            <option value="Kyrgyzstan">Kyrgyzstan</option>
                            <option value="Liechtenstein ">Liechtenstein </option>
                            <option value="Luxembourg">Luxembourg</option>
                            <option value="Madagascar">Madagascar</option>
                            <option value="Malaysia">Malaysia</option>
                            <option value="Maldives">Maldives</option>
                            <option value="Mexico">Mexico</option>
                            <option value="Monaco">Monaco</option>
                            <option value="Mongolia">Mongolia</option>
                            <option value="Morocco">Morocco</option>
                            <option value="Netherlands">Netherlands</option>
                            <option value="New Zealand">New Zealand</option>
                            <option value="North Korea">North Korea</option>
                            <option value="Norway">Norway</option>
                            <option value="Pacific Ocean">Pacific Ocean</option>
                            <option value="Pakistan">Pakistan</option>
                            <option value="Peru">Peru</option>
                            <option value="Philippines">Philippines</option>
                            <option value="Poland">Poland</option>
                            <option value="PORTUGAL">PORTUGAL</option>
                            <option value="Puerto Rico">Puerto Rico</option>
                            <option value="Qatar">Qatar</option>
                            <option value="Romania">Romania</option>
                            <option value="Russia">Russia</option>
                            <option value="Saudi Arab">Saudi Arab</option>
                            <option value="Scotland">Scotland</option>
                            <option value="Singapore">Singapore</option>
                            <option value="Slovakia">Slovakia</option>
                            <option value="Slovenia">Slovenia</option>
                            <option value="South Africa">South Africa</option>
                            <option value="Spain">Spain</option>
                            <option value="Sri Lanka">Sri Lanka</option>
                            <option value="Sweden">Sweden</option>
                            <option value="Switzerland">Switzerland</option>
                            <option value="Syria">Syria</option>
                            <option value="Thailand">Thailand</option>
                            <option value="The Republic of Mauritius">The Republic of Mauritius</option>
                            <option value="The State of Israel">The State of Israel</option>
                            <option value="Tunisia">Tunisia</option>
                            <option value="Turkey">Turkey</option>
                            <option value="Turkmenistan">Turkmenistan</option>
                            <option value="UAE">UAE</option>
                            <option value="Ukraine">Ukraine</option>
                            <option value="United Kingdom">United Kingdom</option>
                            <option value="United States">United States</option>
                            <option value="Uruguay">Uruguay</option>
                            <option value="Uzbekistan">Uzbekistan</option>
                            <option value="Vatican">Vatican</option>
                            <option value="Vietnam">Vietnam</option>
                            <option value="Yemen">Yemen</option>
                        </select>
                    </td>
                </tr>

                <tr>
                <td align="right">Number Of Family Members：</td>
                <td><input type="text" class="reginput"  size="40" name="familynum" id="familymun"  placeholder="enter your number of family members" /> </td>
              </tr>
              <tr>
                <td align="right">Occupation：</td>
                <td><input type="text" class="reginput"  size="40" name="occupation"  placeholder="enter your occupation" /> </td>
              </tr>

              <tr>
                <td align="right">Verification Code：</td>
                <td>
                    <input type="text" class="reginput fl" size="20" maxlength="5" name="verify" id="verify"  placeholder="enter verification code" required="required" />                       <img style="cursor:pointer;margin-left:5px; cursor: hand; width:100px; height:36px; float:left;" title="click to reload captcha" alt="" onclick="this.src='/index/verify.html?random='+Math.random()" src="/index/verify.html?v=<?php echo rand(0,999);?>" />
                </td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td>
                    <button onclick="checkregform()" id="imageField" class="lgbtn" >Register now</button>
              </tr>
            </table>
          </form>
        </div>
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