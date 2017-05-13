<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title></title>
</head>
<body>
    <div class="sale-table-content">
        <table class="sale-table table table-bordered">
            <tbody>
                <tr class="text-center">
                    <td>序号</td>
                    <td>店铺名称</td>
                    <td>就餐方式</td>
                    <td>支付方式</td>
                    <td>销售总额</td>
                </tr>
                <?php if(is_array($sales_datas)): $k = 0; $__LIST__ = $sales_datas;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($k % 2 );++$k;?><tr class="text-center">
                        <td>
                            <?php echo ($k); ?>
                        </td>
                        <td><?php echo ($vo["restaurant_name"]); ?></td>
                        <td><?php echo ($vo["order_str"]); ?></td>
                        <td><?php echo ($vo["pay_str"]); ?></td>
                        <td><?php echo ($vo["sales_data"]); ?></td>
                    </tr><?php endforeach; endif; else: echo "" ;endif; ?>
            </tbody>
        </table>
    </div>    
    <div class="text-center sale-table-page" >
        <ul class="pagination" id="detail-page">
            <?php echo ($page); ?>
        </ul>
    </div>
    <input type="hidden" id="total_amount" value="<?php echo ($total_amount); ?>">
    <input type="hidden" id="pay_str" value="<?php echo ($pay_str); ?>">
    <input type="hidden" id="order_str" value="<?php echo ($order_str); ?>">
    <input type="hidden" id="restaurant_n" value="<?php echo ($restaurant_name); ?>">
</body>
<script>
     $("#detail-page").children().children("a").click(function() {
        var page = parseInt($(this).data("page"));
        var form = $("#search_form")[0];
        var formDate = new FormData(form);
        $.ajax({
            url:"/index.php/agent/sale/orderInfo/page/"+page,
            data:formDate,
            type:"post",
            cache:false,
            contentType:false,
            processData:false,
            success:function(data){
                $("#orderInfo").html(data);

                //修改统计结果
                var startDate = $("#startDate").val();
                var endDate = $("#endtDate").val();
                $("#search_data").html(startDate+" - "+endDate);

                var food_name = $("#food_name").val();
                if(food_name){
                    $("#search_food").html("菜品:"+food_name);
                }else{
                    $("#search_food").val("所有");
                }

                var pay_type = $("#pay_str").val();
                if(pay_type == ""){
                    $("#search_pay_type").html("支付方式：所有");
                }else{
                    $("#search_pay_type").html("支付方式："+pay_type);
                }

                var order_type = $("#order_str").val();
                if(order_type == ""){
                    $("#search_order_type").html("就餐方式：所有");
                }else{
                    $("#search_order_type").html("就餐方式："+order_type);
                }

                var search_total_amount = $("#total_amount").val();
                $("#search_total_amount").html(search_total_amount+"元");
            },
            error:function(){
                alert("出错了");
            }
        });
    });    

    function open_close(i){
        var tid = "#order_food"+i;
        var value = $(tid).data("value");
        console.log(value);
        if(value == 1){
            $(tid).hide();
            $(tid).data("value",0);
        }else if(value == 0){
            $(tid).show();
            $(tid).data("value",1)
        }
    }
    
</script>
</html>