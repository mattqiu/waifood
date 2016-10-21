<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="content-Type" content="text/html; charset=utf-8"/>
    <title>订单列表</title>
    <script>
var APP_PATH="";
var CONST_PUBLIC="/Public";
var CONST_UPLOAD="<?php echo U('Admin/File/upload');?>";
</script>
<script type="text/javascript" src="/Public/Admin/js/jquery-1.4.4.min.js"></script><script type="text/javascript" src="/Public/Admin/js/jquery.page.js"></script><script type="text/javascript" src="/Public/Admin/js/jquery.lhgcalendar.min.js"></script><script type="text/javascript" src="/Public/Admin/js/jquery.numeric.only.js"></script><script type="text/javascript" src="/Public/Admin/js/uploadify/jquery.uploadify.min.js"></script>
<link rel="stylesheet" type="text/css" href="/Public/Admin/images/style.css" />
</head>
<body>
<form action="" method="get" name="form1" id="form1">
    <input type="hidden" name="status" id="status" value="<?php echo I('status');?>"/>
    <table border="0" cellpadding="3" cellspacing="1" class="MainTbl">
        <tr>
            <td>关 键 词：
                <input type="text" class="inputText1" id="keyword"
                       name="keyword" value="<?php echo ($keyword); ?>"/>
                <select id="searchtype"
                        name="searchtype">
                    <option value="0">订单号</option>
                    <?php if(($searchtype == 1)): ?><option value="1" selected="selected">姓名</option>
                        <?php else: ?>
                        <option value="1">姓名</option><?php endif; ?>
                    <?php if(($searchtype == 2)): ?><option value="2" selected="selected">手机号</option>
                        <?php else: ?>
                        <option value="2">手机号</option><?php endif; ?>
                </select>
                <input type="submit" class="btn1" value="查询"/></td>
        </tr>
        <tr>
            <td>订单状态： <a href="<?php echo U('Cms/order');?>">全部</a>
                <?php $_result=parse_field_attr(C('config.CONFIG_STATUS_LIST'));if(is_array($_result)): $i = 0; $__LIST__ = $_result;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$type): $mod = ($i % 2 );++$i; if((strlen($status)) == "0"): ?><a href="<?php echo U('Cms/order','orderfrom='.$orderfrom.'&status='.$key.'&pay='.$pay);?>"><?php echo ($type); ?></a>
                        <?php else: ?>
                        <?php if(($key) == $status): ?><a href="<?php echo U('Cms/order','orderfrom='.$orderfrom.'&status='.$key.'&pay='.$pay);?>"
                               class="fc_red"><?php echo ($type); ?></a>
                            <?php else: ?>
                            <a href="<?php echo U('Cms/order','orderfrom='.$orderfrom.'&status='.$key.'&pay='.$pay);?>"><?php echo ($type); ?></a><?php endif; endif; endforeach; endif; else: echo "" ;endif; ?>
            </td>
        </tr>
        <tr>
            <td>付款状态： <a href="<?php echo U('Cms/order');?>">全部</a>
                <?php if(($pay == 1)): ?><a href="<?php echo U('Cms/order','orderfrom='.$orderfrom.'&pay=1&status='.$status);?>" class="fc_red">已付款</a>
                    <?php else: ?>
                    <a href="<?php echo U('Cms/order','orderfrom='.$orderfrom.'&pay=1&status='.$status);?>">已付款</a><?php endif; ?>
                <?php if(($pay == '0')): ?><a href="<?php echo U('Cms/order','orderfrom='.$orderfrom.'&pay=0&status='.$status);?>" class="fc_red">未付款</a>
                    <?php else: ?>
                    <a href="<?php echo U('Cms/order','orderfrom='.$orderfrom.'&pay=0&status='.$status);?>">未付款</a><?php endif; ?>
            </td>
        </tr>
        <tr>
            <td>订单来源： <a href="<?php echo U('Cms/order');?>">全部</a>
                <?php if(($orderfrom == 1)): ?><a href="<?php echo U('Cms/order','orderfrom=1&pay='.$pay.'&status='.$status);?>" class="fc_red">微信</a>
                    <?php else: ?>
                    <a href="<?php echo U('Cms/order','orderfrom=1&pay='.$pay.'&status='.$status);?>">微信</a><?php endif; ?>
                <?php if(($orderfrom == '0')): ?><a href="<?php echo U('Cms/order','orderfrom=0&pay='.$pay.'&status='.$status);?>" class="fc_red">网站</a>
                    <?php else: ?>
                    <a href="<?php echo U('Cms/order','orderfrom=0&pay='.$pay.'&status='.$status);?>">网站</a><?php endif; ?>
            </td>
        </tr>
    </table>
</form>
<div class="dot"></div>
<table>
    <tr class="toolbar">
        <td colspan="10" class="tc">【 管理订单 】</td>
    </tr>
    <tr class="row0">
        <td colspan="10">
            <a href="<?php echo U('Admin/Cms/addOrder');?>" class="btnAdd">添加</a>
        </td>
    </tr>
</table>
<table border="0" cellpadding="3" cellspacing="1" class="MainTbl">
    <tr class="header">
        <th width="50">ID</th>
        <th width="80">下单时间</th>
        <th width="80">送货时间</th>
        <th width="100">客户姓名</th>
        <th width="80">订单总额</th>
        <th width="50">发票</th>
        <th width="100">付款方式</th>
        <th>留言</th>
        <th width="100">状态</th>
        <th width="50">操作</th>
    </tr>
    <?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i; if($vo['memo']): ?><tr class="row<?php echo ($i % 2+1); ?> hasmemo" title="<?php echo ($vo['memo']); ?>" >
            <?php else: ?>
                <tr class="row<?php echo ($i % 2+1); ?>" ><?php endif; ?>
            <td>
                <input type="checkbox" name="SelectIDs" value="<?php echo ($vo["id"]); ?>"/>
                <?php echo ($vo["id"]); ?>
                <!--<?php if($vo['memo']): ?><img src="/Public/Admin/images/message.png" width="20" alt=""/><?php endif; ?>-->
            </td>
            <td><?php echo ($vo["addtime"]); ?><br/>
                <?php if(($vo["orderfrom"]) == "1"): ?><span class="fc_red" title="该订单来自微信">微</span><?php endif; ?>
            </td>
            <td><?php echo ($vo["delivertime"]); ?></td>
            <td>
                <?php if(($vo["usertype"]) == "2"): echo ($vo["username"]); ?>
                    <?php else: ?>
                    <?php echo get_username($vo['userid']); endif; ?>
                <br/><?php echo ($vo["telephone"]); ?>
            </td>
            <td> <?php echo ($vo["amount"]); ?><br/>
                <?php switch($vo["ordertype"]): case "0": ?><span class="fc_red">物</span><?php break;?>
                    <?php case "1": ?><span class="fc_red">洗</span><?php break;?>
                    <?php case "2": ?><span class="fc_red">物+洗</span><?php break; endswitch;?>
            </td>
            <td>
                <?php if(($vo["invoice"]) == "1"): ?>是
                    <?php else: ?>
                    否<?php endif; ?>
            </td>
            <td>
                <?php if(($vo["paymethod"]) == "4"): ?>货到付款
                    <?php else: ?>
                    在线支付<?php endif; ?>
                <br/>
                <?php if(($vo["pay"]) == "0"): ?><span class="fc_blue">未支付</span>
                    <?php else: ?>
                    <span class="fc_red">已支付</span><?php endif; ?>
            </td>
            <td><?php echo ($vo["info"]); ?></td>
            <td>
                <select onchange="setOrder(this,'<?php echo ($vo["id"]); ?>')">
                    <?php if(is_array($statuslist)): $i = 0; $__LIST__ = $statuslist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$type): $mod = ($i % 2 );++$i; if(($vo["status"] == $key)): ?><option value="<?php echo ($key); ?>" selected="selected"><?php echo ($type); ?></option>
                            <?php else: ?>
                            <option value="<?php echo ($key); ?>"><?php echo ($type); ?></option><?php endif; endforeach; endif; else: echo "" ;endif; ?>
                </select>


            </td>
            <td>
                <?php if(($vo['status'] == 3)): ?><a href="<?php echo U('Cms/editOrder','id='.$vo['id']);?>" class="btnEdit">查看</a>

                    <?php else: ?>
                    <a href="<?php echo U('Cms/editOrder','id='.$vo['id']);?>" class="btnEdit">编辑</a><?php endif; ?>
                <a href="<?php echo U('Cms/editOrder','act=print&id='.$vo['id']);?>" target="_blank" class="btnAdd">打印</a>
                <a href="<?php echo U('Cms/editOrder','act=print&act1=1&id='.$vo['id']);?>" target="_blank" class="btnAdd">内打</a>
            </td>
        </tr><?php endforeach; endif; else: echo "" ;endif; ?>

    <tr class="row0">
        <td colspan="12" class="tr"><input type="hidden" value="order"
                                           id="ConstTbl" name="ConstTbl"/> <input type="button" class="btn2"
                                                                                  value="批量删除" id="AllDel"/> <input
                type="button" class="btn1"
                value="全选" id="AllCheck"/> <input type="button" class="btn1"
                                                  value="反选" id="ReverseCheck"/></td>
    </tr>
    <tr class="footer">
        <td colspan="12">
            <div class="page"><?php echo ($page); ?></div>
        </td>
    </tr>
</table>

<script>
    $(function(){
        $('.hasmemo').css({'background':'orange','color':'#fff'});
        $('.hasmemo td').css({'background':'orange','color':'#fff'});
    })
</script>
</body>
</html>