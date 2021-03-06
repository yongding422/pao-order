<?php
namespace Agent\Controller;
use Think\Controller;

class StoreController extends Controller{
	public function store(){
		$restaurant_name_key = I('get.restaurant_name_key');
		$restaurant = D('Restaurant');
		$condition['business_id'] = session("business_id");
		$condition['status'] = 1;
		$p = I("param.page");
		$pageNum = 13;
		if($restaurant_name_key == ""){		
			$count = $restaurant->where($condition)->count();
			$Page = new \Think\Page($count,$pageNum);
			$show = $Page->show();
			$this->assign('page',$show);
			$Arrlist2 = $restaurant->where($condition)->field('restaurant_id,restaurant_name,address,telephone1,telephone2,city3')->page($p,$pageNum)->select();
		}else{
			$condition['restaurant_name'] = array('like',"%".I('get.restaurant_name_key')."%");
			$count = $restaurant->where($condition)->count();
			$Page = new \Think\Page($count,$pageNum);
			$show = $Page->show();
			$this->assign('page',$show);
			$Arrlist2 = $restaurant->where($condition)->field('restaurant_id,restaurant_name,address,telephone1,telephone2,city3')->page($p,$pageNum)->select();
		}
		$region = D('region');
		$order = D('order');
		foreach($Arrlist2 as $key=>$value){
			$condition1['id'] = $value['city3'];
			$Arrlist2[$key]['city3_name'] = $region->where($condition1)->field('name')->find()['name'];
			$restaurant_manager = D('restaurant_manager');
			$condition2['restaurant_id'] = $value['restaurant_id'];
			$condition2['business_id'] = session('business_id');
			$Arrlist2[$key]['login_account'] = $restaurant_manager->where($condition2)->field('login_account')->find()['login_account'];
		}
		$this->assign("Arrlist2",$Arrlist2);
		$this->display();
	}

	

	//新增表单的省填充
	public function show_province(){
		$region = D('region');
		$Arrlist = $region->where("level=1")->select();
		exit(json_encode($Arrlist));
	}
	
	//新增表单的市填充
	public function show_city(){
		$region = D('region');
		$condition['parent_id'] = I('get.id');
		$Arrlist = $region->where($condition)->select();
		exit(json_encode($Arrlist));
	}
	
	//新增表单的区填充
	public function show_area(){
		$region = D('region');
		$condition['parent_id'] = I('get.id');
		$Arrlist = $region->where($condition)->select();
		exit(json_encode($Arrlist));
	}
	
	//新增餐馆
	public function add_store(){
		$restaurant = D('Restaurant');
		$restaurant->startTrans();
		$data['restaurant_name'] = I('post.restaurant_name');
		$data['address'] = I('post.address');
		$data['business_id'] = I('post.session_id');
		$data['city1'] = I('post.province_id');
		$data['city2'] = I('post.city_id');
		$data['city3'] = I('post.area_id');
		$data['telephone1'] = I('post.telephone1');
		$data['telephone2'] = I('post.telephone2');
		$rel = $restaurant->add($data);
		if($rel){
			//添加店铺管理员所属店铺
			$restaurant_manager = D('restaurant_manager');
			$data1['login_account'] = I('post.login_account');
			$data1['password'] = I('post.password');
			$data1['business_id'] = session("business_id");
			$data1['restaurant_id'] = $rel;
			$rel3 = $restaurant_manager->add($data1);
			if($rel3 === false){
				$restaurant->rollback();
			}
			//为店铺添加支付
			$pay_select = D('pay_select');
			$data['name'] = "微信支付";
			$data['value'] = 1;
			$data['img'] = "/Public/images/pay_01.png";
			$data['restaurant_id'] = $rel;
			$data['config_name'] = "wechat-code";
			$data['s_num'] = 1;
			$pay_select->add($data);
			$data1['name'] = "支付宝支付";
			$data1['value'] = 1;
			$data1['img'] = "/Public/images/pay_03.png";
			$data1['restaurant_id'] = $rel;
			$data1['config_name'] = "ali-code";
			$data1['s_num'] = 4;
			$pay_select->add($data1);
			$data2['name'] = "微信刷卡支付";
			$data2['value'] = 1;
			$data2['img'] = "/Public/images/mricopay.png";
			$data2['restaurant_id'] = $rel;
			$data2['config_name'] = "wechat";
			$data2['s_num'] = 3;
			$pay_select->add($data2);
			$data3['name'] = "银联或现金";
			$data3['value'] = 0;
			$data3['img'] = "/Public/images/pay_02.png";
			$data3['restaurant_id'] = $rel;
			$data3['config_name'] = "cash";
			$data3['s_num'] = 2;
			$pay_select->add($data3);
			
			//为店铺添加流程
            $rst_process_model = D("restaurant_process");
            $rp_data['restaurant_id'] = $rel;    
            for($i=1;$i<=5;$i++){
            	if($i<=2){
            		$rp_data['process_status'] = 0;
            	}else{
            		$rp_data['process_status'] = 1;
            	}	
                $rp_data['process_id'] = $i;
                $rel4 = $rst_process_model->add($rp_data);
                if($rel4 === false){
                    $restaurant->rollback();
                }
            }

            //为店铺添加打印小票控制记录
            $rst_bill_model = D('restaurant_bill');
            $rb_data['restaurant_name'] = 1;
            $rb_data['qrcode'] = 1;
            $rb_data['address'] = 1;
            $rb_data['restaurant_phone'] = 1;
            $rb_data['take_out_phone'] = 0;
            $rb_data['subscription'] = 0;   		//公众号点餐，默认0
            $rb_data['restaurant_id'] = $rel;
            $rel5 = $rst_bill_model->add($rb_data);
            if($rel5 === false){
                $restaurant->rollback();
            }

            //为店铺添加默认的横屏、
            $advertisementModel = D('advertisement');
            $ad_data['advertisement_type'] = 0;
            $ad_data['advertisement_image_url'] = "./Application/Admin/Uploads/default/default_hengadv.jpg";
            $ad_data['restaurant_id'] = $rel;
            $rel6 = $advertisementModel->add($ad_data);
            if($rel6 === false){
                $restaurant->rollback();
            }

            //默认竖屏广告
            $ad_data['advertisement_type'] = 1;
            $ad_data['advertisement_image_url'] = "./Application/Admin/Uploads/default/default_shuadv.jpg";
            $ad_data['restaurant_id'] = $rel;
            $rel7 = $advertisementModel->add($ad_data);
            if($rel7 === false){
                $restaurant->rollback();
            }
			
			//默认叫号屏广告
			$ad_data['advertisement_type'] = 2;
            $ad_data['advertisement_image_url'] = "./Application/Admin/Uploads/default/default_hxadv.jpg";
            $ad_data['restaurant_id'] = $rel;
            $rel11 = $advertisementModel->add($ad_data);
            if($rel11 === false){
                $restaurant->rollback();
            }
			
            //添加默认横屏模板
            $rst_page_model = D('restaurant_page_group');
            $rsp_data['status'] = 1;
            $rsp_data['page_screen'] = 1;
			$rsp_data['group_id'] = 1;
            $rsp_data['restaurant_id'] = $rel;
            $rel8 = $rst_page_model->add($rsp_data);
            if($rel8 === false){
                $restaurant->rollback();
            }

            //添加默认竖屏模板
            $rsp_data['page_screen'] = 2;
			$rsp_data['group_id'] = 2;
            $rel9 = $rst_page_model->add($rsp_data);
            if($rel9 === false){
                $restaurant->rollback();
            }

            //添加手机端模板
            $rsp_data['page_screen'] = 3;
			$rsp_data['group_id'] = 3;
            $rel10 = $rst_page_model->add($rsp_data);
            if($rel10 === false){
                $restaurant->rollback();
            }
            
			//添加默认的两条打印机记录
			$printer = D('printer');
			$printer_data['printer_name'] = '面类打印机';
			$printer_data['printer_ip'] = '192.168.1.100';
			$printer_data['printer_brand'] = '芯烨';
			$printer_data['printer_version'] = 'Q200';
			$printer_data['printer_port'] = '9100';
			$printer_data['print_type'] = 0;
			$printer_data['restaurant_id'] = $rel;
			$printer_insert_id = $printer->add($printer_data);
			
			$printer_data1['printer_name'] = '粉类打印机';
			$printer_data1['printer_ip'] = '192.168.1.101';
			$printer_data1['printer_brand'] = '芯烨';
			$printer_data1['printer_version'] = 'Q201';
			$printer_data1['printer_port'] = '9100';
			$printer_data1['print_type'] = 0;
			$printer_data1['restaurant_id'] = $rel;
			$printer_insert_id1 = $printer->add($printer_data1);
			
			if($printer_insert_id === false && $printer_insert_id1 === false){
				$restaurant->rollback();
			}
			
			//为每个店铺新增默认菜品分类
			$food_category = D('food_category');
			$restaurant_id = $rel;
			$category_num = $food_category->where("restaurant_id=$restaurant_id")->max('sort');
			$food_category_data['food_category_name'] = '默认菜品分类';
			$food_category_data['is_timing'] = 0;
			$food_category_data['sort'] = str_pad($category_num+1,3,"0",STR_PAD_LEFT);   //排序号
	        $food_category_data['restaurant_id'] = $restaurant_id;
	        $rel11 = $food_category->add($food_category_data);
			if($rel11 === false){
                $restaurant->rollback();
            }
			
			//为每个店铺新增默认菜品并关联默认菜品分类
			$food = D('Food');
			$food_data['restaurant_id'] = $rel;
			$food_data['food_name'] = '默认菜品';
            $food_data['food_img'] = './Application/Admin/Uploads/default/default_foodimg.jpg';
            $food_data['food_price'] = 0.01;
			$food_data['star_level'] = 5;
           	$food_data['hot_level'] = 3;
            $food_data['foods_num_day'] = 10000;
            $food_data['food_desc'] = '默认菜品介绍..';
			$food_data['is_prom'] = 0;
            $food_data['print_id'] = $printer_insert_id; 
			$num = $food->where("restaurant_id=$rel")->max('sort');
			$food_data['sort'] = str_pad($num+1,3,"0",STR_PAD_LEFT);   //排序号
            $rel12 = $food->add($food_data);
            if($rel12 != false){
                $relative = D('food_category_relative');
	            $relative_data['food_id'] = $rel12;
	            $relative_data['food_category_id'] = $rel11;
				$rel13 = $relative->add($relative_data);
				if($rel13 === false){
					 $restaurant->rollback();
				}
			}else{
				$restaurant->rollback();
			}
			
			//为每个店铺新增两个菜品类别(单选和双选)
			$attr_type_model = D("attribute_type");
	        $attr_type_data['restaurant_id'] = $rel;
			$attr_type_data['type_name'] = '单选类别(默认)';
			$attr_type_data['print_id'] = $printer_insert_id;
			$attr_type_data['select_type'] = 0;
			$attr_type_data['food_id'] = $rel12;
	        $rel14 = $attr_type_model->add($attr_type_data);
			
			$attr_type_data1['restaurant_id'] = $rel;
			$attr_type_data1['type_name'] = '双选类别(默认)';
			$attr_type_data1['print_id'] = $printer_insert_id;
			$attr_type_data1['select_type'] = 1;
			$attr_type_data1['food_id'] = $rel12;
	        $rel15 = $attr_type_model->add($attr_type_data1);
	        if($rel14 === false && $rel15 === false){
	            $restaurant->rollback();
	        }
			
			//为每个类别增加默认3个属性
			$food_attribute = D('food_attribute');
			for($i=1;$i<=3;$i++){				//单选
				$food_attribute_data['attribute_name'] = '默认单选属性'.$i;
				$food_attribute_data['attribute_price'] = $i;
				$food_attribute_data['attribute_type_id'] = $rel14;
				$rel16 = $food_attribute->add($food_attribute_data);
				if($rel16 === false){
					$restaurant->rollback();
				}
			}
			
			for($i=1;$i<=3;$i++){				//双选
				$food_attribute_data1['attribute_name'] = '默认双选属性'.$i;
				$food_attribute_data1['attribute_price'] = $i;
				$food_attribute_data1['attribute_type_id'] = $rel15;
				$rel17 = $food_attribute->add($food_attribute_data1);
				if($rel17 === false){
					$restaurant->rollback();
				}
			}
			
			$restaurant->commit();	
		}
		$map['business_id'] = session('business_id');
		$map['status'] = array("neq",0);
		$count = $restaurant->where($map)->count();
		$pageNum = 13;
		$page = ceil($count/$pageNum);
		$msg['msg'] = "新增店铺成功！"; 
		$msg['code'] = 1;
		$msg['page'] = $page;
		exit(json_encode($msg));
	}

	//删除餐馆
	public function del_store(){
		$restaurant = D('Restaurant');
        $where['restaurant_id'] = I('get.restaurant_id');  
        $where['status'] = 0;
        $rel = $restaurant->save($where);
		$restaurant_manager = D('restaurant_manager');
		$where1['business_id'] = session('business_id');
		$where1['restaurant_id'] = I('get.restaurant_id');
		$id = $restaurant_manager->where($where1)->field('id')->find()['id'];
		$rel1 = $restaurant_manager->where("id=$id")->delete();
		if($rel && $rel1){
			$map['business_id'] = session('business_id');
			$map['status'] = array("neq",0);
			$count = $restaurant->where($map)->count();
			$pageNum = 13;
			$page = ceil($count/$pageNum);
			$msg['msg'] = "删除店铺成功！";
			$msg['code'] = 1;
			$msg['page'] = $page;
		}else{
			$msg['msg'] = "删除店铺失败！";
			$msg['code'] = 0;
		}
		exit(json_encode($msg));
	}

	//编辑店铺前的填充
	public function modify_store(){
		$restaurant = D('Restaurant');
		$condition['restaurant_id'] = I('get.restaurant_id');
		$object = $restaurant->where($condition)->find();
		$restaurant_manager = D('restaurant_manager');
		$condition2['business_id'] = session("business_id");
		$condition2['restaurant_id'] = I('get.restaurant_id');
		$object['login_account'] = $restaurant_manager->where($condition2)->field('login_account')->find()['login_account'];
		$object['password'] = $restaurant_manager->where($condition2)->field('password')->find()['password'];
		$object['password'] = $restaurant_manager->where($condition2)->field('password')->find()['password'];
		//dump($object);
		exit(json_encode($object));
	}
	
	//编辑餐馆
	public function edit_store(){
		$restaurant = D('Restaurant');
		$data['restaurant_name'] = I('post.restaurant_name');
		$data['address'] = I('post.address');
		$data['business_id'] = I('post.session_id');
		$data['city1'] = I('post.province_id');
		$data['city2'] = I('post.city_id');
		$data['city3'] = I('post.area_id');
		$data['telephone1'] = I('post.telephone1');
		$data['telephone2'] = I('post.telephone2');
		$data['restaurant_id'] = I('post.restaurant_id');
		$r = $restaurant->save($data);
		$restaurant_manager = D('restaurant_manager');
		$where1['restaurant_id'] = I('post.restaurant_id');
		$where1['business_id'] = I('post.session_id');
		$id = $restaurant_manager->where($where1)->field('id')->find()['id'];
		$data1['id'] = $id;
		$data1['login_account'] = I('post.login_account');
		$data1['password'] = I('post.password');
		$r1 = $restaurant_manager->save($data1);
		if($r || $r1){
			$msg['msg'] = "编辑店铺成功";
			$msg['code'] = 1;
		}else{
			$msg['msg'] = "编辑店铺失败";
			$msg['code'] = 1;
		}
		exit(json_encode($msg));
	}
	
	/*//修改餐馆信息
	public function editRestaurantinfo(){
		$restaurant = D('Restaurant');
		$data['restaurant_id'] = I('post.restaurant_id');
		$data['restaurant_name'] = I('post.restaurant_name');
		$data['telephone1'] = I('post.telephone1');
		$data['telephone2'] = I('post.telephone2');
		$data['restaurant_url'] = I('post.restaurant_url');
		$data['address'] = I('post.address');
		$result = $restaurant->save($data);
		if($result){
			$condition['restaurant_id'] = I('post.restaurant_id');
			$object = $restaurant->where($condition)->find();
			$this->assign("object",$object);
			$this->display('storeAjax');
		}
	}*/
}
