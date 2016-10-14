<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>订单编辑</title>
    <script>
var APP_PATH="";
var CONST_PUBLIC="/Public";
var CONST_UPLOAD="<?php echo U('Admin/File/upload');?>";
</script>
<script type="text/javascript" src="/Public/Admin/js/jquery-1.4.4.min.js"></script><script type="text/javascript" src="/Public/Admin/js/jquery.page.js"></script><script type="text/javascript" src="/Public/Admin/js/jquery.lhgcalendar.min.js"></script><script type="text/javascript" src="/Public/Admin/js/jquery.numeric.only.js"></script><script type="text/javascript" src="/Public/Admin/js/uploadify/jquery.uploadify.min.js"></script>
<link rel="stylesheet" type="text/css" href="/Public/Admin/images/style.css" />
    <script language="javascript">
        $(function () {
            function caclTotal() {
                var total = parseFloat("<?php echo ($db["amountall"]); ?>");
                var fee = parseFloat($("#shipfee").val());
                var v = parseFloat($("#needpay").val());
                $("#discount").val(total + fee - v);
            }

            $("#shipfee").change(function () {
                caclTotal();
            });
            $("#needpay").change(function () {
                caclTotal();
            });
        });
    </script>
</head>
<body>
<form action="/Admin/Cms/editOrder" method="post" name="form1"
      id="form1">
<input type="hidden" id="id" name="id" value="<?php echo ($db["id"]); ?>"/>
<table border="0" cellspacing="1" cellpadding="3" class="MainTbl">
<tr class="toolbar">
    <td colspan="4" class="tc">【编辑订单】</td>
</tr>
<tr class="row0">
    <td class="col1" width="140">订单号：</td>
    <td colspan="3"><input type="text" class="inputText1 readonly" readonly="readonly" name="orderno" id="orderno"
                           value="<?php echo ($db["orderno"]); ?>"/>
        <?php if(($db["orderfrom"]) == "1"): ?><span class="fc_red">微信订单</span>
            <?php else: ?>
            网站订单<?php endif; ?>
    </td>
</tr>
<tr class="row0">
    <td class="col1">用户名：</td>
    <td colspan="3"><input type="text" class="inputText1" readonly="readonly" maxlength="100"
                           value="<?php echo get_username($db['userid']);?>"/>
        <span class="fc_red">*</span>
        <select name="sex" id="sex">
            <option value="1">先生</option>
            <?php if(($db["sex"] == 0)): ?><option value="0" selected="selected">女士</option>
                <?php else: ?>
                <option value="0">女士</option><?php endif; ?>
        </select>
    </td>
</tr>
<tr class="row0">
    <td class="col1">收件人：</td>
    <td colspan="3"><input type="text" class="inputText1 " name="username"
                           id="username" maxlength="100" value="<?php echo ($db["username"]); ?>"/></td>
</tr>
<tr class="row0">
    <td class="col1">手机号：</td>
    <td colspan="3"><input type="text" class="inputText1 " name="telephone"
                           id="telephone" maxlength="100" value="<?php echo ($db["telephone"]); ?>"/>
        <span class="fc_red">*</span></td>
</tr>
<tr class="row0">
    <td class="col1">Email：</td>
    <td colspan="3"><input type="text" class="inputText1 " name="email"
                           id="email" maxlength="100" value="<?php echo ($db["email"]); ?>"/>
    </td>
</tr>
<tr class="row0">
    <td class="col1">用户ID：</td>
    <td colspan="3"><input type="text" class="inputText1 numeric w50" name="userid"
                           id="userid" maxlength="100" value="<?php echo ($db["userid"]); ?>"/></td>
</tr>
<tr class="row0">
    <td class="col1">状态：</td>
    <td colspan="3"><select name="status" id="status">
        <?php $_result=parse_field_attr(C('config.CONFIG_STATUS_LIST'));if(is_array($_result)): $i = 0; $__LIST__ = $_result;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$type): $mod = ($i % 2 );++$i; if(($db["status"] == $key)): ?><option value="<?php echo ($key); ?>" selected="selected"><?php echo ($type); ?></option>
                <?php else: ?>
                <option value="<?php echo ($key); ?>"><?php echo ($type); ?></option><?php endif; endforeach; endif; else: echo "" ;endif; ?>
    </select>
        <?php if(($db["status"] == 3) Or ($db["status"] == 4) ): else: ?>
            <input type="submit" class="btn1"
                   value="保存" id="btnSubmit"/><?php endif; ?>
    </td>
</tr>
<tr class="row0">
    <td class="col1">未完事项：</td>
    <td colspan="3">
        <input type="hidden" name="memo_mid" value="<?php echo ($memo["m_id"]); ?>"/>
        <input type="text" name="memo_content" value="<?php echo ($memo["content"]); ?>" size="57"/>
        <?php if($memo['content']): ?><input type="button" class="btn1" value="完成" onclick="finishMemo(<?php echo ($memo["m_id"]); ?>)"/><?php endif; ?>
    </td>
</tr>
<tr class="row0" id="infos">
    <td class="col1">说明：</td>
    <td colspan="3">
        <textarea class="inputText1 editor1" name="info0" id="info0"><?php echo ($db["info0"]); ?></textarea>
        <textarea class="inputText1 editor1 hide" name="info1" id="info1"><?php echo ($db["info1"]); ?></textarea>
        <textarea class="inputText1 editor1 hide" name="info2" id="info2"><?php echo ($db["info2"]); ?></textarea>
        <textarea class="inputText1 editor1 hide" name="info3" id="info3"><?php echo ($db["info3"]); ?></textarea>
        <textarea class="inputText1 editor1 hide" name="info4" id="info4"><?php echo ($db["info4"]); ?></textarea>
    </td>
</tr>
<tr class="row0">
    <td colspan="4"><strong>付款信息</strong> （一般情况勿改动）</td>
</tr>
<tr class="row0">
    <td class="col1">付款方式：</td>
    <td><select name="paymethod" id="paymethod">
        <option value="">--选择--</option>
        <?php $_result=parse_field_attr(C('config.CONFIG_PAYMETHOD_LIST'));if(is_array($_result)): $i = 0; $__LIST__ = $_result;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$type): $mod = ($i % 2 );++$i; if(($db["paymethod"] == $key)): ?><option value="<?php echo ($key); ?>" selected="selected"><?php echo ($type); ?></option>
                <?php else: ?>
                <option value="<?php echo ($key); ?>"><?php echo ($type); ?></option><?php endif; endforeach; endif; else: echo "" ;endif; ?>
    </select>
        <select name="pay" id="pay">
            <option value="0">未付款</option>
            <?php if(($db["pay"]) == "1"): ?><option value="1" selected="selected">已付款</option>
                <?php else: ?>
                <option value="1">已付款</option><?php endif; ?>
        </select>
    </td>
    <td class="col1">付款时间：</td>
    <td><input type="text" class="inputText1 calendar1" readonly="readonly" name="paytime" id="paytime" maxlength="20"
               value="<?php echo ($db["paytime"]); ?>"/></td>

</tr>
<tr class="row0">
    <td class="col1">交易流水号：</td>
    <td><input type="text" class="inputText1" readonly="readonly" name="trade_no" id="trade_no" maxlength="50"
               value="<?php echo ($db["trade_no"]); ?>"/></td>

    <td class="col1">使用积分：</td>
    <td><input type="text" class="inputText1 w50" name="credit" id="credit" maxlength="50" value="<?php echo ($db["credit"]); ?>"
               readonly="readonly"/>
        <?php if(($db["creditamount"]) > "0"): ?>抵扣 <strong style="color:#f00;"><?php echo ($db["creditamount"]); ?></strong> 元<?php endif; ?>
    </td>

</tr>
<tr class="row0">
    <td class="col1">(+) 运费：</td>
    <td><input type="text" class="inputText1 numeric w50" name="shipfee" id="shipfee" maxlength="50"
               value="<?php echo ($db["shipfee"]); ?>"/></td>

    <td class="col1">(-) 折扣：</td>
    <td><input type="text" class="inputText1 numeric w50" name="discount" id="discount" maxlength="50"
               value="<?php echo ($db["discount"]); ?>"/></td>

</tr>
<tr class="row0">
    <td class="col1">(-) 优惠券：</td>
    <td colspan="3"><input type="text" class="inputText1 numeric w50" name="couponamount" id="couponamount"
                           maxlength="50" value="<?php echo ($db["couponamount"]); ?>" readonly="readonly"/></td>

</tr>
<tr class="row0">
    <td class="col1 "><span class="fc_red">应支付金额：</span></td>
    <td colspan="3"><input type="text numeric" class="inputText1 w50" id="needpay" name="needpay" value="<?php echo ($db["amount"]); ?>"/>
    </td>

</tr>
<tr class="row0">
    <td colspan="4"><strong>订单信息</strong></td>
</tr>
<tr class="row0">
    <td class="col1">需要发票：</td>
    <td colspan="3">
        <?php if(($db["invoice"]) == "1"): ?><input name="invoice" type="radio" id="invoice1" value="1" checked/>
            <label for="invoice1">是</label>
            <input type="radio" name="invoice" id="invoice2" value="0"/>
            <label for="invoice2">否</label>
            <?php else: ?>
            <input name="invoice" type="radio" id="invoice1" value="1"/>
            <label for="invoice1">是</label>
            <input type="radio" name="invoice" id="invoice2" value="0" checked/>
            <label for="invoice2">否</label><?php endif; ?>
    </td>
</tr>
<tr class="row0">
    <td class="col1">送货时间：</td>
    <td colspan="3"><input type="text" class="inputText1 w350" name="delivertime" id="delivertime"
                           value="<?php echo ($db["delivertime"]); ?>"/></td>
</tr>
<tr class="row0">
    <td class="col1">收货地址：</td>
    <td colspan="3"><input type="text" class="inputText1 w350" name="address" id="address"
                           value="<?php echo ($db["proname"]); echo ($db["cityname"]); echo ($db["disname"]); echo ($db["address"]); ?>"/>
        <?php if(!empty($db["remark"])): ?>Remarks: <?php echo ($db["remark"]); endif; ?>
    </td>
</tr>
<tr class="row0">
    <td class="col1">备注说明：</td>
    <td colspan="3"><textarea class="inputText1 editor1" name="info" id="info"><?php echo ($db["info"]); ?></textarea></td>
</tr>
<tr class="row0">
    <td colspan="4"><span class="fl"><strong>订单详情</strong> <span
            class="">（数量：<?php echo ($db["num"]); ?>，总金额：<?php echo ($db["amountall"]); ?>）</span> </span> <span class="fr"><span id="spanAdd" class="hide">请输入产品ID： <input
            type="text" class="inputText1 numeric w50" name="AddProductID" id="AddProductID"/> </span><input
            type="button" class="btn1" value="添加"
            onclick="$.EditOrder(0,'<?php echo ($db["orderno"]); ?>',$('#AddProductID').val(),'1')"/></span></td>
</tr>
<tr class="row0">
    <td colspan="4">
        <?php if(($isShop) == "true"): $subtotal=0; ?>
            <table border="0" cellspacing="1" cellpadding="3" class="MainTbl">
                <?php if(($isService) == "true"): ?><tr class="toolbar">
                        <td colspan="8" class="tc">商品列表</td>
                    </tr><?php endif; ?>
                <tr class="header">
                    <th width="10%"> 商品编号</th>
                    <th> 商品名称</th>
                    <th>供应商</th>
                    <th width="10%"> 规格</th>
                    <th width="10%"> 价格</th>
                    <th width="10%"> 商品数量</th>
                    <th width="10%"> 金额</th>
                    <th width="10%"> 操作</th>
                </tr>
                <?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i; if(strpos($vo['sortpath'],',2,')){ $sub=(float)$vo['price']*$vo['num']; $subtotal+=$sub; ?>
                    <tr align="center">
                        <td><?php echo ($vo["productid"]); ?></td>
                        <td align="left"><?php echo ($vo["productname"]); ?></td>
                        <td align="left"><?php echo ($vo["supplyname"]); ?></td>
                        <td><?php echo ($vo["unit"]); ?></td>
                        <td><?php echo ($vo["price"]); ?></td>
                        <td><a href="javascript:void(0);" class="dplus dplus1 fl">-</a>
                            <input type="text" class="inputText1 numeric w50 fl" value="<?php echo ($vo["num"]); ?>"
                                   onchange="$.EditOrder(1,'<?php echo ($vo["orderno"]); ?>','<?php echo ($vo["id"]); ?>',$(this).val())" stock="9999"/>
                            <a href="javascript:void(0);" class="dplus dplus2 fl">+</a></td>
                        <td><?php echo to_price($sub);?></td>
                        <td><a href="javascript:void(0);" onclick="$.EditOrder(2,'<?php echo ($vo["orderno"]); ?>','<?php echo ($vo["id"]); ?>','1')">删除</a>
                        </td>
                    </tr>
                    <?php
 } endforeach; endif; else: echo "" ;endif; ?>
            </table><?php endif; ?>
        <?php if(($isService) == "true"): $subtotal=0; ?>
            <table border="0" cellspacing="1" cellpadding="3" class="MainTbl">
                <?php if(($isShop) == "true"): ?><tr class="toolbar">
                        <td colspan="8" class="tc">洗衣列表</td>
                    </tr><?php endif; ?>
                <tr class="header">
                    <th width="10%"> 商品编号</th>
                    <th> 商品名称</th>
                    <th>供应商</th>
                    <th width="10%"> 规格</th>
                    <th width="10%"> 价格</th>
                    <th width="10%"> 商品数量</th>
                    <th width="10%"> 金额</th>
                    <th width="10%"> 操作</th>
                </tr>
                <?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i; if(strpos($vo['sortpath'],',3,')){ $sub=(float)$vo['price']*$vo['num']; $subtotal+=$sub; ?>
                    <tr align="center">
                        <td><?php echo ($vo["productid"]); ?></td>
                        <td align="left"><?php echo ($vo["productname"]); ?></td>
                        <td align="left"><?php echo ($vo["supplyname"]); ?></td>
                        <td><?php echo ($vo["unit"]); ?></td>
                        <td><?php echo ($vo["price"]); ?></td>
                        <td><a href="javascript:void(0);" class="dplus dplus1 fl">-</a>
                            <input type="text" class="inputText1 numeric w50 fl" value="<?php echo ($vo["num"]); ?>"
                                   onchange="$.EditOrder(1,'<?php echo ($vo["orderno"]); ?>','<?php echo ($vo["id"]); ?>',$(this).val())" stock="9999"/>
                            <a href="javascript:void(0);" class="dplus dplus2 fl">+</a></td>
                        <td><?php echo to_price($sub);?></td>
                        <td><a href="javascript:void(0);" onclick="$.EditOrder(2,'<?php echo ($vo["orderno"]); ?>','<?php echo ($vo["id"]); ?>','1')">删除</a>
                        </td>
                    </tr>
                    <?php
 } endforeach; endif; else: echo "" ;endif; ?>
            </table><?php endif; ?>
    </td>
</tr>
<tr class="footer">
    <td colspan="4" class="tc">

        <?php if(($db["status"] == 3) ): ?><input type="button" disabled="disabled" class="btn2"
                   value="禁止修改"/>
            <input type="button" class="btn1" value="返回"
                   onclick="history.back();"/>
            <?php else: ?>
            <input type="submit" class="btn1"
                   value="保存" id="btnSubmit"/>
            <input type="button" class="btn1" value="返回"
                   onclick="history.back();"/><?php endif; ?>

    </td>
</tr>
</table>
</form>

<script type="text/javascript" src="/Public/Admin/js/alerts/jquery.alerts.js"></script>
<link rel="stylesheet" type="text/css" href="/Public/Admin/js/alerts/jquery.alerts.css" />
<script language="javascript">
    $(function () {
        $("#status").change(function () {
            var v = $(this).find("option:selected").val();
            $("#infos textarea").hide();
            $("#info" + v).show();
        }).change();

        $("#btnSubmit").click(function () {
            var v = $("#status").val();
            var msg = "";
            if (v == "3") {
                msg = "已完成";
            }
            if (v == "4") {
                msg = "已取消";
            }
            ;
            if (v == "3" || v == "4") {
                if (confirm("您确定要将本订单状态设置为【" + msg + "】吗？设置后该订单不可再次修改！")) {
                    return true;
                } else {
                    return false;
                }
            }
        });
    });

    function finishMemo(mid) {
        if (mid > 0) {
            if (confirm("您确定要完成该事项")) {
                $.post('/Admin/Cms/finishMemo', {'m_id': mid}, function (data) {
                    jAlert(data.message)
                    if(data.code==200){
                        window.location.reload();
                    }
                })
            }
        }
    }

</script>
</body>
</html>