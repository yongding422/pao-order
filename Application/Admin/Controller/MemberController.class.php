<?php
namespace Admin\Controller;
use Think\Controller;
class MemberController extends Controller {
    # 获取设置信息
    public function get_set(){
        $set = D("set");
        $condition['type'] = I("get.type");
        $condition['restaurant_id'] = session("restaurant_id");
        $if_open = $set->where($condition)->getField("if_open");
        echo $if_open;
    }

    # 增删改查后重新获取折扣信息
    public function get_discount(){
        // 获取会员组信息,用作下拉框选择
        // all_benefit里面已经有了的就不再查询出来
        // 先用店铺ID做条件查询出all_benefit里面的group_id，然后再查询出vip_group里面group_id不在all_benefit里面的group_id的分组信息
        // 问题：所属分组就不能处于默认选中状态了

        // 根据店铺ID获取代理ID
        $restaurant = D("restaurant");
        $condition['restaurant_id'] = session("restaurant_id");
        $where['business_id'] = $restaurant->where($condition)->getField("business_id");

        $vip_group = D("vip_group");
        $group_info = $vip_group->where($where)->select();

        $discount = D("discount");
        $discount_rules = $discount->where($condition)->select();
        $this->assign("discount_rules",$discount_rules);
        $this->assign("group_info",$group_info);
        $this->display("ajaxDiscount");
    }


    /*----------------------消费折扣开始（添加设置信息和折扣规则入库）----------------------*/
    # 从后台添加设置信息到数据库
    public function discount_set(){
        # 接收设置信息，存入设置表
        $receive = array();
        $receive['if_open'] = I("post.if_open");    // 是否开启  1开启，0关闭
        $receive['if_vip'] = I("post.if_vip");      // 全部还是会员，0全部，1会员*/
        // 为空的就去掉（因为同一时间，只能提交是否开启或者是否会员，其余一个肯定为空的），不让该空字段更新
        if($receive['if_open'] == ''){
            unset($receive['if_open']);
        }
        if($receive['if_vip'] == ''){
            unset($receive['if_vip']);
        }
        # 设置信息存入数据表
        // 判断数据表中是否已经有了此记录
        $set = D("set");
        $restaurant_id = session("restaurant_id");
        $receive['restaurant_id'] = $restaurant_id;
        $where = array("restaurant_id"=>$restaurant_id,"type"=>0);
        $data = $set->where($where)->find();
        if($data){
            // p($receive);
            // 已有记录，就更新
            if($set->where($where)->save($receive) !== false){
                $this->success("更新成功");
            }else{
                $this->error("更新失败，请重试");
            }
        }else{
            // 没有记录就添加
            // 添加的时候要指定类型为折扣 0
            $receive['type'] = 0;
            if($set->add($receive)){
                $this->success("添加成功");
            }else{
                $this->error("添加失败");
            }
        }
    }

    # 模态框添加折扣信息存入discount表
    public function add_discount(){
        // 店铺id，类型，直接用这五个数据去数据库查询是否有相同的记录，如果有，则不允许。是不行的。黄金：满100折20,店铺ID：100,类型：0；这样也是查询出没记录的喔。
        // 所以不行，要黄金跟店铺ID跟类型的组合做查询条件查询出来的记录是唯一一个才行
        $condition['restaurant_id'] = session("restaurant_id");
        $condition['group_id'] = I("post.group_id");
        $discount = D("discount");
        $record = $discount->where($condition)->find();
        if($record ){
            // 当前添加的会员组已经有了对应的折扣信息
            $this->error("当前添加的会员组已经有了对应的折扣信息，请勿重复添加");
        }else{
            $add['money'] = I("post.account");
            $add['discount'] = I("post.benefit");
            $add['group_id'] = I("post.group_id");
            $add['restaurant_id'] = session("restaurant_id");

            if($discount->create($add)){
                if($discount->add($add)){
                    // 添加成功后实时获取数据库数据
                    $this->get_discount();
                }else{
                    $this->error("添加失败，请重试");
                }
            }else{
                $this->error("添加失败，请重试");
            }
        }
    }

    // 每条折扣信息后面的保存处理
    public function save_discount(){
        // 使用id做条件来编辑即可
        /*<pre>Array
        (
            [money] => 108.00
            [discount] => 10.00
            [id] => 170
            [group_id] => 56
        )
        </pre>*/

        // 先要查询你提交过来的所属分组id是否存在于discount，存在则不允许编辑（问题：本身也是在all_benefit，如果本身没变的时候，那得允许编辑啊）
        // 解决：根据all_benefit里面的id查询出最原始的group_id，然后unset掉
        $discount = D("discount");
        $self_id = $discount->where(array("id"=>I("post.id")))->getField("group_id");

        $all_group_id = $discount->where(array("restaurant_id"=>session("restaurant_id")))->field("group_id")->select();
        $arr = array();
        foreach($all_group_id as $v){
            if($v['group_id'] == $self_id){
                // 去掉当前编辑的对象本身没改变时的id
                unset($v['group_id']);
            }else{
                $arr[] = $v['group_id'];
            }
        }
       if(in_array(I("post.group_id"),$arr)){
           // 说明该分组已经有折扣规则使用
           $this->error("已有折扣规则在使用该分组名，请另外选择");
       }else{
           // 允许修改
           if($discount->create(I("post."))){
               if($discount->save(I("post.")) !== false){
                   $this->success("保存成功");
               }else{
                   $this->error("保存失败，请重试");
               }
           }else{
               $this->error("保存失败，请重试");
           }
       }
    }

    # 每条折扣信息后面的删除
    public function del_discount(){
        // 只需根据ID去删除即可
        $discount = D("discount");
        $where["id"] =I("post.id");
        // 删除折扣信息
        if($discount->where($where)->delete()){
            // $this->success("删除成功");
            // 实时获取会员组信息
            $this->get_discount();
        }else{
            $this->error("删除失败，请重试");
        }
    }
    /*----------------------消费折扣结束（添加设置信息和折扣规则入库）----------------------*/

    # 公共的添加设置的封装方法(但折扣的设置方法是自己独立出来一个的，因为折扣那里多了一个是否会员的判断)
    // 参数为类型  0：折扣  1：预充值  2：积分设置  3：积分现金  4：积分物品
    public function set($type)
    {
        # 接收设置信息，存入设置表
        $receive = array();
        $receive['if_open'] = I("post.if_open");    // 是否开启  1开启，0关闭

        # 设置信息存入数据表
        // 有两种做法：一、只做更新的，因为在新增店铺的时候就同时给它一条设置记录  二、做更新和做添加，根据店铺id去查询是否有此记录，没有则添加，有则更新。
        // 判断数据表中是否已经有了此记录
        $set = D("set");
        $restaurant_id = session("restaurant_id");
        $receive['restaurant_id'] = $restaurant_id;
        $where = array("restaurant_id"=>$restaurant_id,"type"=>$type);  // 指定类型为$type
        $data = $set->where($where)->find();
        if($data){
            // 已有记录，就更新
            if($set->where($where)->save($receive) !== false){
                $this->success("更新成功");
            }else{
                $this->error("更新失败");
            }
        }else{
            // 没有记录就添加
            // 添加的时候要指定类型为$type
            $receive['type'] = $type;
            if($set->add($receive)){
                $this->success("添加成功");
            }else{
                $this->error("添加失败");
            }
        }
    }

    # 从后台添加积分设置信息到数据表set（消费多少送多少积分）
    public function score_set()
    {
        $this->set(2);
    }

    # 判断微信公众号的支付信息跟代理的是否相同
    public function if_same()
    {
        // 获取店铺的微信appip
        $condition['restaurant_id'] = session("restaurant_id");
        $condition['config_type'] = "wxpay";
        $condition['config_name'] = "wxpay_appid";
        $wxpay_appid = D("config")->where($condition)->getField("config_value");

        // 获取所属代理的微信appid
        $configModel = D("wx_prepaid_config");
        $business_id = D("restaurant")->where(array("restaurant_id"=>session("restaurant_id")))->getField("business_id");
        $data['business_id'] = $business_id;
        $data['config_name'] = "wxpay_appid";
        $bus_wxpay_appid = $configModel->where($data)->getField("config_value");

        if($bus_wxpay_appid == $wxpay_appid){
            echo 1;
        }else{
            // 关闭开关
            // 判断是否由此记录
            $off_condition['type'] = 2;
            $off_condition['restaurant_id'] = session("restaurant_id");
            $off_info = D("set")->where($off_condition)->find();
            if($off_info){
                // 如果有此记录，就将其更新为0，没有则不做处理
                D("set")->where($off_condition)->save(array("if_open"=>0));
            }
            echo 0;
        }
    }

    # 从后台添加积分现金设置信息到数据表set
    public function cash_set()
    {
        $this->set(3);
    }

    # 从后台添加积分物品设置信息到数据表set
    public function goods_set()
    {
        $this->set(4);
    }

    # 会员组设置
    public function vip_group(){
        $restaurant = D("restaurant");
        $condition['restaurant_id'] = session("restaurant_id");
        $where['business_id'] = $restaurant->where($condition)->getField("business_id");
        $vip_group = D("vip_group");
        $group_info = $vip_group->where($where)->select();
        $this->assign("group_info",$group_info);

        $this->display();
    }

    # 消费折扣
    public function discount(){
        // 会员组信息
        $restaurant = D("restaurant");
        $condition['restaurant_id'] = session("restaurant_id");
        $where['business_id'] = $restaurant->where($condition)->getField("business_id");

        $vip_group = D("vip_group");
        $group_info = $vip_group->where($where)->select();

        // 折扣规则信息
        $discount = D("discount");
        $discount_rules = $discount->where($condition)->select();

        $set = D("set");
        $set_condition['type'] = 0;
        $set_condition['restaurant_id'] = session("restaurant_id");
        $set_info = $set->where($set_condition)->find();
        $this->assign("if_vip",$set_info['if_vip']);
        $this->assign("if_open",$set_info['if_open']);

        $this->assign("discount_rules",$discount_rules);
        $this->assign("group_info",$group_info);

        $this->display();
    }

    # 预充值
    public function prepaid(){
        // 要根据business_id和类型去获取
        $where['restaurant_id'] = session("restaurant_id");
        $restaurant = D("restaurant");
        $business_id = $restaurant->where($where)->getField("business_id");
        $condition['business_id'] = $business_id;
        $condition['type'] = 1;
        $all_benefit = D("all_benefit");
        $prepaid_rules = $all_benefit->where($condition)->select();
        $this->assign("prepaid_rules",$prepaid_rules);

        $this->display();
    }

    # 积分设置
    public function point_set(){
        // 积分规则
        // 要根据business_id和类型去获取
        $where['restaurant_id'] = session("restaurant_id");
        $restaurant = D("restaurant");
        $business_id = $restaurant->where($where)->getField("business_id");
        $condition['business_id'] = $business_id;

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

        // 积分开关
        $set = D("set");
        $condition['type'] = 2;
        $condition['restaurant_id'] = session("restaurant_id");
        $if_open = $set->where($condition)->getField("if_open");
        $this->assign("if_open",$if_open);

        $this->display();
    }

    # 积分消费
    public function point_consumptio(){
        # 获取积分现金规则
        // 要根据business_id和类型去获取
        $where['restaurant_id'] = session("restaurant_id");
        $restaurant = D("restaurant");
        $business_id = $restaurant->where($where)->getField("business_id");
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

        # 获取积分现金开关
        // 积分现金开关
        $set = D("set");
        $cash_condition['type'] = 3;
        $cash_condition['restaurant_id'] = session("restaurant_id");
        $cash_open = $set->where($cash_condition)->getField("if_open");
        $this->assign("cash_open",$cash_open);



        # 积分物品
        // 要根据business_id和类型去获取
        $g_condition['business_id'] = $business_id;
        $score_goods = D("score_goods");
        $img_rules = $score_goods->where($g_condition)->select();
        $this->assign("img_rules",$img_rules);

        // 积分物品开关
        $goods_condition['type'] = 4;
        $goods_condition['restaurant_id'] = session("restaurant_id");
        $goods_open = $set->where($goods_condition)->getField("if_open");
        $this->assign("goods_open",$goods_open);
        $this->display();
    }
}