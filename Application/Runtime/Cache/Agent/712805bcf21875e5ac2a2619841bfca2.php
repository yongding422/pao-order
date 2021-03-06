<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head lang="en">
    <meta charset="UTF-8">
    <title></title>
</head>
<body>
    <div class="modal-head">积分兑换物品设置</div>
    <div class="modal-wrapper">
        <div class="show-pic">
            <span>图片：</span>
            <img src="/Public/Uploads/Goods/<?php echo ($data[goods_img]); ?>" class="img-thumbnail" id="GoodsImg1">
            <input type="file" name="goods_img" id="GoodsImg2" onchange="change(this,'GoodsImg1')">
            <input type="hidden" name="_rootpath" value="/Public/Uploads/Goods"><!-- 图片的统一存储路径 -->
            <input type="hidden" name="id" value="<?php echo ($data['id']); ?>"/>
        </div>
        <p>
            <span>名称：</span>
            <input type="text" name="goods_name" value="<?php echo ($data['goods_name']); ?>" id="goods_name1">
        </p>
        <p>
            <span>积分：</span>
            <input type="text" name="score" value="<?php echo ($data['score']); ?>" id="_score1">
        </p>
        <!--  <p>
              <span>另加金额：</span>
              <input type="text" name="money" id="GoodsMoney">
          </p>-->
        <div id="edit-content" class="editor-content">
            <p><?php echo ($data['goods_desc']); ?></p>
        </div>
        <input type="hidden" name="goods_desc" value="" id="goods_desc1"/>
        <div class="text-center">
            <button class="btn btn-black" onclick="save_goods()">保存</button>
            <button class="btn btn-default" data-dismiss="modal">关闭</button>
        </div>
    </div>
    <script type="text/javascript">
        var editEditor = new wangEditor('edit-content');
        // 上传图片
        editEditor.config.uploadImgUrl = '/index.php/agent/members/img_upload';
        // 隐藏掉插入网络图片功能。该配置，只有在你正确配置了图片上传功能之后才可用。
        editEditor.config.hideLinkImg = true;
        editEditor.create();

        // 模态框文件上传
        function save_goods() {
            var goods_name = $("#goods_name1").val();
            var score = $("#_score1").val();
            // 获取编辑器纯文本内容
            var text = editEditor.$txt.html();
            $("#goods_desc1").val(text);
            if(goods_name == "" || score == ""){
                alert("物品信息不能为空");
            }else{
                var formData = new FormData($( "#uploadForm1" )[0]);
                $.ajax({
                    url: "/index.php/agent/Members/save_goods" ,
                    type: 'POST',
                    data: formData,
                    async: false,
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function (returndata) {
                        $("#edit-goods").modal("hide");
                        // 清除富文本编辑器里面的内容
                        editEditor.$txt.html('<p><br></p>');

                        $.post('/index.php/Agent/Members/get_img',"",function(data){
                            // 成功添加了就实时获取
                            $("#photo").html(data);
                        });
                    },
                    error: function (returndata) {
                        $("#edit-goods").modal("hide");
                        // 清除富文本编辑器里面的内容
                        editEditor.$txt.html('<p><br></p>');
                        alert(returndata.info);
                    }
                });
            }
        }
        
    </script>
</body>
</html>