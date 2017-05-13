<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="zh-CN">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">

	<!-- Bootstrap 核心 CSS 文件 -->
	<link rel="stylesheet" href="/Public/bootstrap/css/bootstrap.min.css">

	<!-- admin CSS 文件 -->
	<link rel="stylesheet" href="/Public/css/agent.css">
	<!-- HTML5 Shim 和 Respond.js 用于让 IE8 支持 HTML5元素和媒体查询 -->	
	<!--[if lt IE 9]>	
		<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
		<script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
	<![endif]-->

	<!-- jQuery文件。务必在bootstrap.min.js 之前引入 -->
	<script src="/Public/js/jquery-3.1.0.min.js"></script>
	<!-- 最新的 Bootstrap 核心 JavaScript 文件 -->
	<script src="/Public/bootstrap/js/bootstrap.min.js"></script>

	<!-- layer CSS 文件 -->
	<link rel="stylesheet" href="/Public/css/layer.css">
	<script src="/Public/js/layer.js"></script>
	<title>方雅点餐系统代理后台</title>
</head>

<link rel="stylesheet" type="text/css" href="/Public/wangEditor/css/wangEditor.min.css">
<body class="members">
	<div class="container-fluid">
        <form>
            <div id="memberList">
                <div class="member-tab-item2" id="delCash">
                    积分：
                    <input type="text" name="account" value="<?php echo ($point_cash_rules[account]); ?>" id="man">分=现金：
                    <input type="text" name="benefit" value="<?php echo ($score); ?>" id="zhe">元

                    <input type="hidden" name="id" value="<?php echo ($point_cash_rules[id]); ?>"/>
                    <div class="tab-item-right">
                        <button class="btn btn-primary" onclick="return save_point_cash(this)">保存</button>
                        <button class="btn btn-danger" onclick="return shanchu_point_cash(this,'/index.php/Agent/Members/del_point_cash')">删除</button>
                    </div>
                </div>
            </div>
        </form>
        <div class="point-exchange">
            <div class="clearfix">
                <!--<div class="pull-left point-exchange-item">
                    <div class="pic-thumbnail">
                        <img src="/Public/images/dishes01.png">
                    </div>
                    <p>积分：100</p>
                    <div class="text-center">
                        <button class="btn btn-primary" data-toggle="modal" data-target="#edit-goods">编辑</button>
                        <button class="btn btn-danger">删除</button>
                    </div>
                </div>-->

                <div id="photo">
                    <?php if(is_array($img_rules)): foreach($img_rules as $key=>$v): ?><div class="pull-left point-exchange-item">
                            <div class="pic-thumbnail">
                                <img src="/Public/Uploads/Goods/<?php echo ($v[goods_img]); ?>">
                                <input type="hidden" name="id" value="<?php echo ($v['id']); ?>"/>
                            </div>
                            <p>积分：<?php echo ($v[score]); ?></p>
                            <input type="hidden"/>
                            <div class="text-center">
                                <button class="btn btn-primary" data-toggle="modal" data-target="#edit-goods" data-goods_id = "<?php echo ($v['id']); ?>" onclick="editInfo(this)">编辑</button>
                                <button class="btn btn-danger" onclick="return del_img(this,'/index.php/Agent/Members/del_point_img')">删除</button>
                            </div>
                        </div><?php endforeach; endif; ?>
                </div>

                <div class="pull-left point-exchange-item">
                    <button class="pic-thumbnail" data-toggle="modal" data-target="#add-goods">
                        <img src="/Public/images/add.png">
                    </button>
                </div>
            </div>
        </div>

        <div>
            <p>积分模板：（微信公众号积分公示）</p>
            <div>
                <img src="/Public/images/screenshot.png">
                <span>请将下面的链接地址加入到公众号菜单中</span>
                <span>URL:http://shop.fouya.com/id...</span>
            </div>
        </div>
	</div>
    <!-- 新增积分兑换物品 -->
    <div class="modal fade" id="add-goods">
        <form id= "uploadForm" action="javascript:void(0)">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-head">积分兑换物品设置</div>
                    <div class="modal-wrapper">
                        <div class="show-pic">
                            <span>图片：</span>
                            <img src="/Public/images/dishes01.png" class="img-thumbnail" id="add-goods-img">
                            <input type="file" name="goods_img" id="_goods_img"  onchange="change(this,'add-goods-img')">
                            <input type="hidden" name="_rootpath" value="/Public/Uploads/Goods"><!-- 图片的统一存储路径 -->
                        </div>
                        <p>
                            <span>名称：</span>
                            <input type="text" name="goods_name" id="_goods_name">
                        </p>
                        <p>
                            <span>积分：</span>
                            <input type="text" name="score" id="_score">
                        </p>
                       <!-- <p>
                            <span>另加金额：</span>
                            <input type="text" name="money" id="_money">
                        </p>-->
                        <div id="add-content" class="editor-content">
                            <p>请输入内容...</p>
                        </div>
                        <input type="hidden" name="goods_desc" value="" id="goods_desc"/>
                        <div class="text-center">
                            <button class="btn btn-black" onclick="doUpload()">保存</button>
                            <button class="btn btn-default" data-dismiss="modal">关闭</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <!-- 编辑积分兑换物品 -->
    <div class="modal fade" id="edit-goods">
        <form action="javascript:void(0)" id= "uploadForm1">
            <div class="modal-dialog">
                <div class="modal-content" id="goods_edit">

                </div>
            </div>
        </form>
    </div>

   <!-- <script type="text/javascript" src="/Public/wangEditor/js/lib/jquery-1.10.2.min.js"></script>-->
    <script type="text/javascript" src="/Public/wangEditor/js/wangEditor.min.js"></script>
    <script src="/Public/js/vip.js"></script>
    <script src="/Public/js/placeImg.js"></script>
    <script>
        //模态框消失后清空表单
        $('#add-goods').on('hidden.bs.modal', function (){
            // 执行一些动作...
            $('#add-goods-img').attr('src', '');
            $("#_goods_img").val("");
            $("#_goods_name").val("");
            $("#_score").val("");
            $("#_money").val("");
            // 不能传入空字符串，而必须传入如下参数
            addEditor.$txt.html('<p><br></p>');
        })


        var addEditor = new wangEditor('add-content');
        // 上传图片
        addEditor.config.uploadImgUrl = '/index.php/agent/members/img_upload';
        // 隐藏掉插入网络图片功能。该配置，只有在你正确配置了图片上传功能之后才可用。
        addEditor.config.hideLinkImg = true;
        addEditor.create();
        addEditor.$editorContainer.css('z-index', 1001)


        // 积分现金后面的保存
        function save_point_cash(obj){
            var account = $(obj).parent().siblings('[name="account"]').val();
            var benefit = $(obj).parent().siblings('[name="benefit"]').val();
            var id = $(obj).parent().siblings('[name="id"]').val();
            if(account == "" || benefit == ""){
                alert("积分现金信息不能为空");
            } else{
                // ajax提交
                $.post('/index.php/Agent/Members/save_point_cash',{"account": account, "benefit": benefit, "id":id},function(data){
                    alert(data.info);
                    $.post('/index.php/Agent/Members/get_point_cash',"",function(data){
                        // 成功添加了就实时获取
                        $("#memberList").html(data);
                    });
                });
            }
            return false;
        }

        // 积分现金后面的删除
        function shanchu_point_cash(obj,url){
            layer.confirm('您确定要删除吗？', {icon:3}, function(index){
                // 获取到它的ID，然后删除掉即可
                var hid = $(obj).parent().siblings("[name='id']").val();
                // ajax提交
                $.post(url,{"id":hid},function(data){
                    // console.log(data);
                    if(data.status == 0){
                        // 不成功
                        alert(data.info);
                    }else{
                        // 成功添加了就实时获取
                        $("#memberList").html(data);
                    }
                });
                layer.close(index);
            });
            return false;
        }

        // 模态框文件上传
        function doUpload() {
            var img = $("#_goods_img").val();
            var goods_name = $("#_goods_name").val();
            var score = $("#_score").val();
            // 获取编辑器纯文本内容
            var text = addEditor.$txt.html();
            $("#goods_desc").val(text);
            if(img == "" || goods_name == "" || score == ""){
                alert("物品信息不能为空");
            }else{
                var formData = new FormData($( "#uploadForm" )[0]);
                $.ajax({
                    url: "/index.php/agent/Members/add_goods" ,
                    type: 'POST',
                    data: formData,
                    async: false,
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function (returndata) {
                        $("#add-goods").modal("hide");
                        // 清除富文本编辑器里面的内容
                        addEditor.$txt.html('<p><br></p>');

                        $.post('/index.php/Agent/Members/get_img',"",function(data){
                            // 成功添加了就实时获取
                            $("#photo").html(data);
                        });
                    },
                    error: function (returndata) {
                        $("#add-goods").modal("hide");
                        // 清除富文本编辑器里面的内容
                        addEditor.$txt.html('<p><br></p>');
                        alert(returndata.info);
                    }
                });
            }
        }


        // 删除积分兑换物品
        function del_img(obj,url){
            layer.confirm('您确定要删除吗？', {icon:3}, function(index){
                // 获取到它的ID，然后删除掉即可
                var hid = $(obj).parent().siblings().find('[name="id"]').val();
                // ajax提交
                $.post(url,{"id":hid},function(data){
                    if(data.status == 0){
                        // 不成功
                        alert(data.info);
                    }else{
                        // 成功添加了就实时获取
                        $("#photo").html(data);
                    }
                });
                layer.close(index);
            });
            return false;
        }



        function editInfo(obj){
            var goods_id = $(obj).data('goods_id');
            $.ajax({
                url:"/index.php/agent/members/getGoodsInfos",
                type:"post",
                data:{"goods_id":goods_id},
//            dataType:"json",
                success:function(data){
                    $("#goods_edit").html(data);
                },
                error:function(){
                    console.log("访问出错");
                }
            });
        }

    </script>


</body>
</html>