<?php
namespace Agent\Controller;
use Think\Controller;
class MembersController extends Controller {
    # 加载会员管理视图
    public function index(){
        $this->display("index");
    }

    /*----------------------页面加载完自动加载的内容开始----------------------*/
    # 加载会员组信息
    public function get_group()
    {
        $where['business_id'] = session("business_id");
        $vip_group = D("vip_group");
        $group_info = $vip_group->where($where)->select();
        $this->assign("group_info",$group_info);
        $this->display("ajaxGroup");
    }

    # 获取预充值信息
    public function get_prepaid(){
        $condition['business_id'] = session("business_id");
        $condition['type'] = 1;
        $all_benefit = D("all_benefit");
        $prepaid_rules = $all_benefit->where($condition)->select();
        $this->assign("prepaid_rules",$prepaid_rules);
        $this->display("ajaxPrepaid");
    }

    # 获取积分设置信息
    public function get_point_set(){
        $condition['business_id'] = session("business_id");
        $condition['type'] = 2;
        $all_benefit = D("all_benefit");
        $prepaid_rules = $all_benefit->where($condition)->find();

        $this->assign("prepaid_rules",$prepaid_rules);
        // 去掉小数点
        $score = intval($prepaid_rules['benefit']);
        if($score == 0){
            unset($score);
        }
        $this->assign("score",$score);
        $this->display("ajaxPointSet");
    }

    # 获取积分兑换现金设置信息
    public function get_point_cash(){
        $business_id = session("business_id");
        $condition['business_id'] = $business_id;
        $condition['type'] = 3;
        $all_benefit = D("all_benefit");
        $point_cash_rules = $all_benefit->where($condition)->find();

        $this->assign("point_cash_rules",$point_cash_rules);
        // 去掉小数点
        $score = intval($point_cash_rules['benefit']);
        if($score == 0){
            unset($score);
        }
        $this->assign("score",$score);
        $this->display("ajaxPointCash");
    }

    # 加载折扣消费的图片
    public function get_img()
    {
        $business_id = session("business_id");
        $condition['business_id'] = $business_id;
        $score_goods = D("score_goods");
        $img_rules = $score_goods->where($condition)->select();
        $this->assign("img_rules",$img_rules);
        $this->display("ajaxPointGoods");
    }

    # 页面加载完自动加载公众号设置信息
    public function get_public_number_set()
    {
        $business_id = session("business_id");
        $condition['business_id'] = $business_id;
        $public_number_set_model = D("public_number_set");
        $public_number_set = $public_number_set_model->where($condition)->find();
        $this->assign("public_number_set",$public_number_set);
        $this->assign("business_id",$business_id);
        $this->display("ajaxPublicNumberSet");
    }

    # 页面加载完自动加载短信对接信息
    public function get_sms_docking()
    {
        $business_id = session("business_id");
        $condition['business_id'] = $business_id;
        $sms_vip = D("sms_vip");
        $sms_vip_info = $sms_vip->where($condition)->find();
        $this->assign("sms_vip_info",$sms_vip_info);
        $this->display("ajaxSmsDocking");
    }
    /*----------------------页面加载完自动加载的内容结束----------------------*/

    /*----------------------会员组设置开始（添加会员组信息入库）----------------------*/
    # 模态框的会员组添加
    public function add_group(){
        $where['business_id'] = session("business_id");
        $where['group_name'] = I("post.group_name");
        $vip_group = D("vip_group");
        // 用分组名和代理ID去判断该代理是否存在一模一样的分组，如果存在，那就提醒该分组已经存在，不能重复添加，如果不存在，才允许添加
        $record = $vip_group->where($where)->find();
        if(!$record){
            //　不存在才添加
            if($vip_group->create($where)){
                if($vip_group->add($where)){
                    // $this->success("添加成功");
                    // 添加成功后，重新加载页面
                    $this->get_group();
                }else{
                    $this->error("添加失败，请重试");
                }
            }else{
                $this->error("添加失败,请重试");
            }
        }else{
            // 存在则不允许添加
            $this->error("此分组信息已经存在，请勿重复添加");
        }
    }

    # 每组会员组数据后面的保存按钮
    public function save_group(){
        $save['group_name'] = I("post.group_name");
        $save['group_id'] = I("post.group_id");
        // 编辑当前ID的数据，条件只需要id即可
        $vip_group = D("vip_group");

        // 先查询当前代理数据库是否已经有了同样的分组名，如果已经存在则不允许修改
        $condition['business_id'] = session("business_id");
        $condition['group_name'] = I("post.group_name");

        // 先查询出所有的分组名，然后用传递过来的分组id去获取对应的分组名，如果跟它同的就unset掉，然后在里面的就是已经存在的，不允许修改
        $self_name = $vip_group->where(array("group_id"=>I("post.group_id")))->getField("group_name");
        $group_names = $vip_group->where(array("business_id"=>session("business_id")))->field("group_name")->select();
        $arr = array();
        foreach($group_names as $val){
            if($val['group_name'] == $self_name){
                unset($val['group_name']);
            }else{
                $arr[] = $val['group_name'];
            }
        }

        if(in_array(I("post.group_name"),$arr)){
            $this->error("此分组名已经存在，请重新输入");
        }else{
            if($vip_group->create($save)){
                if($vip_group->save($save) !== false){
                    $this->success("保存成功");
                }else{
                    $this->error("保存失败,请重试");
                }
            }else{
                $this->error("保存失败,请重试");
            }
        }
    }

    # 删除会员组信息
    public function del_group(){
        // 只需根据ID去删除即可
        $vip_group = D("vip_group");
        $discount = D("discount");
        $where["group_id"] =I("post.group_id");
        // 判断该会员组下是否有折扣规则或者有会员，有则不让删除
        $rule = $discount->where($where)->find();
        $vip = D("vip");
        $people = $vip->where($where)->find();
        if($rule || $people){
            $this->error("当前会员组有对应的折扣规则或会员，不能删除");
        }else{
            // 删除会员组信息
            if($vip_group->where($where)->delete()){
                // 实时获取会员组信息
                $this->get_group();
            }else{
                $this->error("删除失败，请重试");
            }
        }
    }
    /*----------------------会员组设置结束（添加会员组信息入库）----------------------*/

    /*----------------------添加预充值规则开始（添加设置信息和预充值规则入库）----------------------*/
    # 从后台添加设置信息到数据表set
    public function prepaid_set()
    {
        # 接收设置信息，存入设置表
        $receive['if_open'] = I("post.if_open");    // 是否开启  1开启，0关闭

        # 设置信息存入数据表
        // 判断数据表中是否已经有了此记录
        $business_set = D("business_set");
        $business_id = session("business_id");
        $receive['business_id'] = $business_id;
        $where = array("business_id"=>$business_id,"type"=>0);  // 指定类型为$type
        $data = $business_set->where($where)->find();
        if($data){
            // 已有记录，就更新
            if($business_set->where($where)->save($receive) !== false){
                $this->success("更新成功");
            }else{
                $this->error("更新失败");
            }
        }else{
            // 没有记录就添加
            // 添加的时候要指定类型为$type
            $receive['type'] = 0;
            if($business_set->add($receive)){
                $this->success("添加成功");
            }else{
                $this->error("添加失败");
            }
        }
    }

    # 模态框添加预充值信息存入all_benefit表
    public function add_prepaid(){
        /* <pre>Array
         (
             [account] => 100
             [benefit] => 20
         )
         </pre>*/
        // 店铺id，类型，直接用这四个数据去数据库查询是否有相同的记录，如果有，则不允许。
        // 还要单独判断充值额是否有相同的，不止充值额和赠送的金额一起判断

        $condition['business_id'] = session("business_id");
        $condition['account'] = I("post.account");
        $condition['benefit'] = I("post.benefit");
        $condition['type'] = 1;
        $all_benefit = D("all_benefit");
        $record = $all_benefit->where($condition)->find();

        $account['business_id'] = session("business_id");
        $account['account'] = I("post.account");
        $account['type'] = 1;

        $benefit['business_id'] = session("business_id");
        $benefit['benefit'] = I("post.benefit");
        $benefit['type'] = 1;
        $record1 = $all_benefit->where($account)->find();
        $record2 = $all_benefit->where($benefit)->find();
        if($record || $record1 || $record2){
            $this->error("已存在相同的预充值信息，请勿重复添加");
        }else{
            if($all_benefit->create($condition)){
                if($all_benefit->add($condition)){
                    // 添加成功后实时获取数据库数据
                    $this->get_prepaid();
                }else{
                    $this->error("添加失败，请重试");
                }
            }else{
                $this->error("添加失败，请重试");
            }
        }
    }

    // 每条预充值信息后面的保存处理
    public function save_prepaid(){
        // 查询数据库除了自身外有没有其他数据是跟当前要编辑成的数据是一样的，有则不允许编辑
        // 查询提交过来account和benefit是否有对应的ID存在于all_benefit中  （得到id）
        // 如果没有或者跟当前主键ID是一样的，则允许编辑

        $condition['business_id'] = session("business_id");
        $condition['account'] = I("post.account");
        $condition['benefit'] = I("post.benefit");
        $condition['type'] = 1;
        $all_benefit = D("all_benefit");
        $id = $all_benefit->where($condition)->getField("id");
        $self_id = I("post.id");

        // 根据传递过来的id去查出对应的折扣信息，如果当前提交过来的预充值信息（分开金额和赠送）

        // 如果查出的id为空或者等于当前id，则允许编辑
        if(!$id || $id==$self_id){
            $save['business_id'] = session("business_id");
            $save['account'] = I("post.account");
            $save['benefit'] = I("post.benefit");
            $save['id'] = I("post.id");
            $save['type'] = 1;
            if($all_benefit->create($save)){
                if($all_benefit->save($save) !== false){
                    $this->success("编辑成功");
                }else{
                    $this->error("编辑失败");
                }
            }else{
                $this->error("编辑失败");
            }
        }else{
            $this->error("已存在相同的预充值信息，请勿重复编辑");
        }
    }

    # 删除预充值规则
    public function del_prepaid(){
        // 只需根据ID去删除即可
        $all_benefit = D("all_benefit");
        $where["id"] =I("post.id");
        // 删除预充值信息
        if($all_benefit->where($where)->delete()){
            // $this->success("删除成功");
            // 实时获取会员组信息
            $this->get_prepaid();
        }else{
            $this->error("删除失败，请重试");
        }
    }
    /*----------------------添加预充值规则结束（添加设置信息和预充值规则入库）----------------------*/

    /*----------------------积分设置开始（添加设置信息和积分规则入库）----------------------*/
    # 积分设置规则后面的保存
    public function save_point_set()
    {
        /* Array
         (
             [account] => 13
             [benefit] => 14
             [id] =>
         )*/
        $all_benefit = D("all_benefit");
        $add['account'] = I("post.account");
        $add['benefit'] = I("post.benefit");
        $add['business_id'] = session("business_id");
        $add['type'] = 2;

        // 判断id有没有值，有则是编辑，没有则是添加
        $id = I("post.id");
        if($id){
            $save['account'] = I("post.account");
            $save['benefit'] = I("post.benefit");
            $save['id'] = $id;

            if($all_benefit->create($save)){
                if($all_benefit->save($save) !== false){
                    $this->success("编辑成功");
                }else{
                    $this->error("编辑失败，请重试");
                }
            }else{
                $this->error("编辑失败，请重试");
            }
        }else{
            if($all_benefit->create($add)){
                if($all_benefit->add($add)){
                    $this->success("添加成功");
                }else{
                    $this->error("添加失败，请重试");
                }
            }else{
                $this->error("添加失败，请重试");
            }
        }
    }

    # 积分设置规则的删除
    public function del_point_set(){
        // 只需根据ID去删除即可
        $all_benefit = D("all_benefit");
        $where["id"] =I("post.id");
        // 如果传递过来的ID为空，则说明还没有积分设置数据
        if(I("post.id") == ''){
            $this->error("还没有录入任何积分设置数据，无需删除");
        }

        // 删除积分设置规则信息
        if($all_benefit->where($where)->delete()){
            // $this->success("删除成功");
            // 实时获取积分设置规则信息
            $this->get_point_set();
        }else{
            $this->error("删除失败，请重试");
        }
    }
    /*----------------------积分设置结束（添加设置信息和积分规则入库）----------------------*/

    /*----------------------（积分消费）积分现金开始（添加设置信息和积分现金规则入库）----------------------*/
    # 积分设置规则后面的保存
    public function save_point_cash()
    {
        /*Array
        (
            [account] => 15
            [benefit] => 1
            [id] =>
        )*/

        $all_benefit = D("all_benefit");
        $add['account'] = I("post.account");
        $add['benefit'] = I("post.benefit");
        $add['business_id'] = session("business_id");
        $add['type'] = 3;

        // 判断id有没有值，有则是编辑，没有则是添加
        $id = I("post.id");
        if($id){
            $save['account'] = I("post.account");
            $save['benefit'] = I("post.benefit");
            $save['id'] = $id;

            if($all_benefit->create($save)){
                if($all_benefit->save($save) !== false){
                    $this->success("编辑成功");
                }else{
                    $this->error("编辑失败，请重试");
                }
            }else{
                $this->error("编辑失败，请重试");
            }
        }else{
            if($all_benefit->create($add)){
                if($all_benefit->add($add)){
                    $this->success("添加成功");
                }else{
                    $this->error("添加失败，请重试");
                }
            }else{
                $this->error("添加失败，请重试");
            }
        }
    }

    # 积分现金规则的删除
    public function del_point_cash(){
        // 只需根据ID去删除即可
        $all_benefit = D("all_benefit");
        $where["id"] =I("post.id");
        // 如果传递过来的ID为空，则说明还没有积分设置数据
        if(I("post.id") == ''){
            $this->error("还没有录入任何积分设置数据，无需删除");
        }

        // 删除积分现金规则信息
        if($all_benefit->where($where)->delete()){
            // $this->success("删除成功");
            // 实时获取积分设置规则信息
            $this->get_point_cash();
        }else{
            $this->error("删除失败，请重试");
        }
    }

    /*----------------------(积分消费)积分现金结束（添加设置信息和积分规则入库）----------------------*/

    /*----------------------（积分消费）积分物品开始（添加设置信息和积分物品规则入库）----------------------*/
    # 从模态框添加积分兑换物品到数据库
    public function add_goods()
    {
        // 实例化模型
        $score_goods = D('score_goods');

        $res = upload();
        // 上传不成功
        if(is_string($res))
        {
            $this->error('文件上传失败，原因为' . $res);
        }

        // 绑定代理id
        $_POST['business_id'] = session("business_id");

        // 创建数据并入库
        if($score_goods->create()) {
            if($score_goods->add()) {
                $this->success('添加成功');
            }else {
                $this->error("添加失败，请重试");
            }
        }else {
            $this->error("添加失败，请重试");
        }
    }

    # 富文本框里面的图片存放
    public function img_upload(){
        $res = upload();
        $url = "http://".$_SERVER["HTTP_HOST"]."/".$res['wangEditorH5File']['savepath'].$res['wangEditorH5File']['savename'];
        echo $url;
    }

    # 删除积分兑换物品
    public function del_point_img(){
        // 只需根据ID去删除即可
        $score_goods = D("score_goods");
        $where["id"] =I("post.id");

        $goods_info = $score_goods->where(array("id"=>I("post.id")))->field("goods_img,goods_desc")->find();
        // 删除积分兑换物品
        if($score_goods->where($where)->delete()){
            // 删除掉原来的主图片
            @ unlink("./Public/Uploads/Goods/$goods_info[goods_img]");

            // 删除掉原来的商品描述中的图片  用正则从描述中匹配出src地址，然后删除掉
            $preg =  '/<img.*?src=[\"|\']?(.*?)[\"|\']?\s.*?>/i';
            $imgArr = array();
            preg_match_all($preg,htmlspecialchars_decode($goods_info['goods_desc']), $imgArr);
            foreach($imgArr[1] as $v){
                unlink("./".substr($v,strpos($v,"Public")));
            }

            // 实时获取积分物品信息
            $this->get_img();
        }else{
            $this->error("删除失败，请重试");
        }
    }
    
    # 编辑积分物品
    public function getGoodsInfos(){
        $goods_id = I("post.goods_id");
        $data = D("score_goods")->where(array("id"=>$goods_id))->find();
        $data['goods_desc'] = htmlspecialchars_decode($data['goods_desc']);
        $this->assign("data",$data);
        $this->display("ajaxGoodsEdit");
    }

    # 编辑积分物品的处理
    public function save_goods(){
        // 实例化模型
        $score_goods = D('score_goods');

        $res = upload();
        // 上传不成功
        if(is_string($res))
        {
            $this->error('文件上传失败，原因为' . $res);
        }

        $goods_info = $score_goods->where(array("id"=>I("post.id")))->field("goods_img,goods_desc")->find();

        // 先获取到传递过来的图片，然后再遍历原来的图片是否在传递过来的图片中，在则不删除
        $preg =  '/<img.*?src=[\"|\']?(.*?)[\"|\']?\s.*?>/i';
        $imgArr = array();
        $arr_origin = array();
        preg_match_all($preg,htmlspecialchars_decode($_POST['goods_desc']), $imgArr);
        foreach($imgArr[1] as $v){
            $arr_origin[] = $v;
        }

        // 创建数据并入库
        if($score_goods->create()) {
            if($score_goods->save()) {
                if(I("post.goods_img") != ""){
                    // 删除掉原来的主图片
                    @ unlink("./Public/Uploads/Goods/$goods_info[goods_img]");
                }
                // 删除掉原来的商品描述中的图片  用正则从描述中匹配出src地址，然后删除掉
                $preg =  '/<img.*?src=[\"|\']?(.*?)[\"|\']?\s.*?>/i';
                $imgArr = array();
                preg_match_all($preg,htmlspecialchars_decode($goods_info['goods_desc']), $imgArr);
                foreach($imgArr[1] as $val){
                    if(!in_array($val,$arr_origin)){
                        unlink("./".substr($val,strpos($v,"Public")));
                    }
                }
                $this->success('编辑成功');
            }else {
                $this->error("编辑失败，请重试");
            }
        }else {
            $this->error("编辑失败，请重试");
        }
    }
    /*----------------------（积分消费）积分物品结束（添加设置信息和积分物品规则入库）----------------------*/


    /*----------------------公众号设置开始----------------------*/
    public function add_public_number_set(){
        $public_number_set = D("public_number_set");
        // 根据ID来判断是添加还是编辑
        if(I("post.id")){
            // 编辑
            if($public_number_set->create()){
                if($public_number_set->save() !== false){
                    $this->success("编辑成功");
                    // $this->get_public_number_set();
                }else{
                    $this->error("编辑失败");
                }
            }else{
                $this->error("编辑失败");
            }
        }else{
            // 添加
            if($public_number_set->create()){
                if($public_number_set->add()){
                    $this->success("添加成功");
                    // $this->get_public_number_set();
                }else{
                    $this->error("添加失败");
                }
            }else{
                $this->error("添加失败");
            }
        }

    }
    /*----------------------公众号设置结束----------------------*/

    /*----------------------短信接口设置开始----------------------*/
    public function add_sms_docking(){
        $sms_vip = D("sms_vip");
        $_POST['business_id'] = session("business_id");
        // 根据ID来判断是添加还是编辑
        if(I("post.id")){
            // 编辑
            if($sms_vip->create()){
                if($sms_vip->save() !== false){
                    $this->success("编辑成功");
                }else{
                    $this->error("编辑失败");
                }
            }else{
                $this->error("编辑失败");
            }
        }else{
            // 添加
            if($sms_vip->create()){
                if($sms_vip->add()){
                    $this->success("添加成功");
                }else{
                    $this->error("添加失败");
                }
            }else{
                $this->error("添加失败");
            }
        }

    }
    /*----------------------短信接口设置结束----------------------*/

    # 会员组设置
    public function vip_group(){
        $where['business_id'] = session("business_id");
        $vip_group = D("vip_group");
        $group_info = $vip_group->where($where)->select();
        $this->assign("group_info",$group_info);
        $this->display();
    }

    # 消费折扣
    public function discount(){
        // 获取会员组信息,用作下拉框选择
        $condition['restaurant_id'] = session("restaurant_id");
        $vip_group = D("vip_group");
        $group_info = $vip_group->where($condition)->select();
        $this->assign("group_info",$group_info);
        $this->display();
    }

    # 预充值
    public function prepaid(){
        // 预充值规则
        $condition['business_id'] = session("business_id");
        $condition['type'] = 1;
        $all_benefit = D("all_benefit");
        $prepaid_rules = $all_benefit->where($condition)->select();
        $this->assign("prepaid_rules",$prepaid_rules);

        // 预充值开关
        $business_set = D("business_set");
        $condition['type'] = 0;
        $condition['business_id'] = session("business_id");
        $if_open = $business_set->where($condition)->getField("if_open");
        $this->assign("if_open",$if_open);

        $this->display();
    }

    # 积分设置
    public function point_set(){
        $condition['business_id'] = session("business_id");
        $condition['type'] = 2;
        $all_benefit = D("all_benefit");
        $prepaid_rules = $all_benefit->where($condition)->find();

        $this->assign("prepaid_rules",$prepaid_rules);
        // 去掉小数点
        $score = intval($prepaid_rules['benefit']);
        if($score == 0){
            unset($score);
        }
        $this->assign("score",$score);

        $this->display();
    }

    # 积分消费
    public function point_consumptio(){
        // 积分现金规则
        $business_id = session("business_id");
        $condition['business_id'] = $business_id;
        $condition['type'] = 3;
        $all_benefit = D("all_benefit");
        $point_cash_rules = $all_benefit->where($condition)->find();

        $this->assign("point_cash_rules",$point_cash_rules);
        // 去掉小数点
        $score = intval($point_cash_rules['benefit']);
        if($score == 0){
            unset($score);
        }
        $this->assign("score",$score);

        // 积分商品
        $goods_condition['business_id'] = $business_id;
        $score_goods = D("score_goods");
        $img_rules = $score_goods->where($goods_condition)->select();
        $this->assign("img_rules",$img_rules);

        $this->display();
    }

    # 短信对接
    public function sms_docking(){
        $business_id = session("business_id");
        $condition['business_id'] = $business_id;
        $sms_vip = D("sms_vip");
        $sms_vip_info = $sms_vip->where($condition)->find();
        $this->assign("sms_vip_info",$sms_vip_info);

        $this->display();
    }

    # 公众号设置
    public function official_accounts(){
        $business_id = session("business_id");
        $condition['business_id'] = $business_id;
        $public_number_set_model = D("public_number_set");
        $public_number_set = $public_number_set_model->where($condition)->find();
        $this->assign("public_number_set",$public_number_set);
        $this->assign("business_id",$business_id);

        $this->display();
    }

    # 点击左侧会员信息，获取代理下的所有会员信息
    public function members(){
        // 编辑完后跳转回来这里，然后再带关键字查询查询
        // 为什么此时的带关键字查询会有页码数的
        // 有可能是location.href的时候生成了，然后一直有
        $keyword = I("get.keyword");
        if($keyword !='')
        {
            $condition['phone'] = array('like', "%$keyword%");
        }

        $vip = D('vip');
        $business_id = session("business_id");
        $condition['business_id'] = $business_id;

        $count = $vip->where($condition)->count();

        $p = I('get.page') ? I('get.page'): 1;
        $pageNum = 8;
        $Page  = new \Think\PageAjax($count,$pageNum);// 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show();// 分页显示输出

        $this->assign('page',$show);// 赋值分页输出*/

        $vip_group= D("vip_group")->where($condition)->select();
        $this->assign("vip_group",$vip_group);
//        dump($p);
        $vips = D("vip")->where($condition)->page($p,$pageNum)->select();
//        dump($vips);
        $this->assign("vips",$vips);
        $this->assign("now_page",$p);
        $this->display();
    }

    # 点击分页按钮，会员分页
    public function vipPage(){
        $keyword = I("get.keyword");
        if($keyword !='')
        {
            $condition['phone'] = array('like', "%$keyword%");
        }

        $vip = D('vip');
        $condition['restaurant_id'] = session('restaurant_id');
        $pp = I("get.page");
        $p = I("get.page") ? I("get.page") : 1;
        $count = $vip->where($condition)->count();
        $page_num = 8;
        $page = new \Think\PageAjax($count,$page_num);
        $show = $page->show();
        $this->assign('page',$show);

        $vip_group1= D("vip_group")->where($condition)->select();
        $this->assign("vip_group1",$vip_group1);

        $vips1 = D("vip")->where($condition)->page($p,$page_num)->select();
        $this->assign('vips1',$vips1);
        if($pp == ""){
            $this->display('members');
        }else{
            $this->assign("now_page",$pp);
            $this->display('vipPage');
        }
    }

    # 点击每条数据后面的编辑按钮，获取编辑单个会员的信息，用于回显
    public function getVipInfos(){
        $id = I("post.id");
        $page = I("post.page");
        $business_id = session("business_id");
        $condition = array("business_id"=>$business_id);
        $vip_group1 = D("vip_group")->where($condition)->select();
        $this->assign("vip_group1",$vip_group1);
        $vipinfo = D("vip")->where(array("id"=>$id))->find();
        $this->assign("vipinfo",$vipinfo);
        $this->assign("page",$page);
        $this->display("ajaxEditVip");
    }

    # 编辑会员信息
    public function vip_info()
    {
        /*Array
        (
            username] => 唇铭
            [id] => 16
            [sex] => 1
            [phone] => 18318189263
            [birthday] => 2016/2/2
            [group_id] => 0
        )*/

        $vip = D("vip");
        // 判断提交过来的生日跟之前数据库的生日是否一样，不一样则进行更新年龄
        $now_birthday = I("post.birthday");
        $before_birthday = $vip->where(array("id"=>I("post.id")))->getField("birthday");
        if($before_birthday != $now_birthday){
            $year = explode("/",$now_birthday)[0];
            // 当前年份减去出生年份
            $_POST['age'] =  date("Y")-$year;
        }

        if($vip->create()){
            if($vip->save() !== false){
                $this->success("保存成功");
            }else{
                $this->error("保存失败");
            }
        }else{
            $this->error("保存失败");
        }
    }

    
    # 微信支付对接
    public function pay(){
        $configModel = D("wx_prepaid_config");
        $condition['business_id'] = session('business_id');
        $wx_config = $configModel->where($condition)->select();
        $wx_config_list = dealConfigKeyForValue($wx_config);
        $this->assign("wx_config",$wx_config_list);
        $this->display("pay");
    }

    # 添加修改支付对接信息
    public function editAddPayInfo(){
        $configModel = D("wx_prepaid_config");
        $configModel->startTrans();
        $pay_data = I('post.');
        $data['business_id'] = session("business_id");

        foreach($pay_data as $key => $val){
            $data['config_name'] = $key;
            $data['config_value'] = $val;
            $condition['config_name'] = $key;
            $condition['business_id'] = $data['business_id'];
            // 判断是否已有此记录
            $tempRel = $configModel->field("config_id")->where($condition)->find();

            if($tempRel){
                $data2['config_id'] = $tempRel['config_id'];
                $rel = $configModel->where($data2)->save($data);
            }else{
                $rel = $configModel->add($data);
            }
            if($rel === false){
                $configModel->rollback();
            }
        }
        $configModel->commit();
    }
}