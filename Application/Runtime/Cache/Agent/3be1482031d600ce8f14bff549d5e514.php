<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head lang="en">
    <meta charset="UTF-8">
    <title></title>
</head>
<body>
    <div class="container-fluid">
    <form class="form-horizontal" method="post" onSubmit="return save_public_number_set(this)">
        <div class="form-group">
            <label for="appid" class="col-sm-3 control-label">appid:</label>
            <div class="col-sm-6 col-lg-6">
                <input type="text" name="appid" value="<?php echo ($public_number_set['appid']); ?>" id="appid" class="form-control" dataType="Require" placeholder="请输入公众号appid">
            </div>
        </div>
        <div class="form-group">
            <label for="appsecret" class="col-sm-3 control-label">appsecret:</label>
            <div class="col-sm-6 col-lg-6">
                <input type="text" name="appsecret" value="<?php echo ($public_number_set['appsecret']); ?>" id="appsecret" class="form-control" dataType="Require" placeholder="请输入公众号appsecret">
            </div>
        </div>
        <div class="form-group">
            <label for="public_number_url" class="col-sm-3 control-label">会员通过公众号注册的链接入口:</label>
            <div class="col-sm-6 col-lg-6">
                <input type="text" name="public_number_url" id="public_number_url" value="http://shop.founya.com/index.php/Mobile/weixin/getUserDetail?business_id=<?php echo ($business_id); ?>" class="form-control">
                <font color="#f00">（请勿填写或者更改链接入口地址，只需复制粘贴至微信公众号设置即可）</font>
                <input type="hidden" name="business_id" value="<?php echo ($business_id); ?>"/>
                <input type="hidden" name="id" value="<?php echo ($public_number_set['id']); ?>"/>
            </div>
        </div>
        <div class="col-sm-10 text-center">
            <button class="btn btn-black" type="submit">保存</button>
        </div>
    </form>
</div>
</body>
</html>