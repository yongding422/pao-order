<?php
/**
 * Created by PhpStorm.
 * User: liangbaobin
 * Date: 2016/11/13
 * Time: 23:33
 */

namespace Vertical\Controller;
use Think\Controller;
use PayMethod\alipaydirect\lib\AlipaySubmit;
use PayMethod\alipaydirect\lib\AlipayNotify;

class AlipayDirectController extends Controller
{

    private $alipay_config;

    public function __construct(){
        Controller::__construct();

        $configModel = D('config');
        $condition['config_type'] = "alipay";
        $condition['restaurant_id'] = session("restaurant_id");
        $alipayConfig = $configModel->where($condition)->select();
        $alipayC = dealConfigKeyForValue($alipayConfig);
	
		$alipay_config = array();
		//$alipay_config['partner'] = '2088521274983214';
        $alipay_config['alipay_public_key'] = "MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQDDI6d306Q8fIfCOaTXyiUeJHkrIvYISRcc73s3vF1ZT7XN8RNPwJxo8pWaJMmvyTn9N4HQ632qJBVHf8sxHi/fEsraprwCtzvzQETrNRwVxLO5jVmRGi60j8Ue1efIlzPXV9je9mkjzOmdssymZkh2QhUrCmZYI/FCEa3/cNMW0QIDAQAB";
        $alipay_config['merchant_private_key'] = "MIIEpAIBAAKCAQEA1b4I/MrpwBPEgSKTXbGGBVgGTK4wYG0pC3BZxrsGvIJ1toNyAOYLoFDHXzIyjPBLSp51ME+dCQT+AQlrfjyeaAa/yZq2JHpyfYLhteDtbakE8BR5LlTBF/COZ8Wm0bmCY8DDNdfFeEiRhjwyGz8Pk4qzPBvMGYi05LjpECTq4jZAEkwEMUNDG2BDcgLBMaTnyOXIYWbkc8xZM6ZRPcNtCOebPRsyPE8COhTmqeze0VqrSP1FqmsWE/RPL1nMcR7u3tb4dIR9YdC+HneULkvu9NRwOUvDLZfM+Bdk7lpttT1ghpnii3foF9ko2iSflKJFoR+skK7eSH6immDQW1K/iQIDAQABAoIBADe5aaJuTG61OJ/qIb1lkOlHv6yyJQ6epvrCecVj8SeoY4QH1OsDDrjnFnNtJNy1sQttMGQ9/VTipk4i5JFuwGWijNHEsgPkoIC0ZKAEWiIi1JS64bUAh670GkxXrVw0HxheowYgkO0BDl+OxglwcWEn4f6jyJxHldGjm6+H0GzU+eS2tsk64uPXE2MBr0ib/LB26C0TcSD0+ndmyMpEHxx9Vsm1j0W70//yGzdqzNERtzaYbB57AiVOFnZFFxKdjz5NWUBopRipYypmbqSHOWhTE/JX02AYeVHl6brg/qY33K4kMM0aFPY5X1pSuzuNnWP259B9iA8DMWueSaHDnmECgYEA8uFMwT3cQPaTQ4sd7WNYNKLCMO0fuc2l6fMP3yFb6UJidSRufbFHxFj/HpEJ2A1NxVw0Vi3x+LybtaGdQ+YDqRO/sTXlyaxAf5Lbpnq4lJnck29dVpQHgRLwzg49wCoYCw0FYJIt0EMBOuNFj8x9XtZA9cmOEg0cZG+amhaUj3UCgYEA4UnMxrZdzpdcjHEFdL/G6XDSC1z4WlGsmesTZrzy5Wz5jDwmM/B6E1xdJLMP2iX4smDwVISquqDZbWiGfAWg1Vzd7hG7ehCff+eNDLteZ2W4AC/4G4NNF2OWya+duANzroEHUtFchXFSOjPZ2q1920qzYuly9NuX7355TORiIUUCgYEAw9UZOC6Squalsq43ATM66aXaAe0PyZahElNjFrwlCZz3GhC37izE+vHvMkwrrcJ8VIeVw27kTXR18CtRTG/nv6omw9H7Z3K1xqc457DHpzmv+kiu76IHsvD8rIgbDhhirnZfQZBb1DJSiZK6gcmVxKPoWMWdAb0EiY7bDybpvgkCgYEAgy9IMMXspen5mEYP2UX6++AYT0mZzG4WxaHfMEoCXpowTfqQfFohm7iC/GXQa0UFJknQaEbAa+BR+6WL+hgE6XkkOgbVVmTrm70noyxP8oGGvYUxQkQY1e/F1DeQ7aAE8q5rp0wGRiYQrZeX2gCAcvtyrbwmlnJwbKMG20F56skCgYBn2nzTrDH2bD07Bkh5eSQfjBhiofYbQSy5cSOf7rpbjyC42w4JwuH+ lsIfULSdnd2TEYAgKjSyL6y0RmI8CGqoAldPcSiBNMzaWP4RLtzbwJORgmtDZN+E7FGhebuVkCMLt0rZnvZ+kWOO2PuOYGo4eEKw6JCEhV+X8v9fbFKldA==";
        $alipay_config['charset'] = "UTF-8";
		$alipay_config['gatewayUrl'] = 'https://openapi.alipay.com/gateway.do';
        $alipay_config['app_id']	= "2016121604351905";
        $alipay_config['notify_url'] = 'http://shop.founya.com/index.php/home/AlipayDirect/notify';
		$alipay_config['MaxQueryRetry'] = '10';
		$alipay_config['QueryDuration'] = '3';	
		//$alipay_config['transport'] = 'https';
		$alipay_config['cacert'] = getcwd().'cacert.pem';
		$alipay_config['sign_type'] = 'RSA';		
		$this->alipay_config = $alipay_config;	
    }
    public function index(){
        //商户订单号，商户网站订单系统中唯一订单号，必填
        $out_trade_no = I('get.order_sn');

        $orderModel = D("order");
        $o_condition['order_sn'] = $out_trade_no;
        $rel = $orderModel->where($o_condition)->field("total_amount,order_sn")->find();

        //订单名称，必填
        $subject = "方雅点餐系统";

        //付款金额，必填
        $total_fee = $rel['total_amount'];
//        $total_fee = 0.01;

        //商品描述，可空
        $body = "方雅点餐系统";

        //公用回传参数
        $extra_common_param  = cookie("device_code");

        $alipay_config = $this->alipay_config;
//
        $parameter = array(
            "service"       => $alipay_config['service'],
            "partner"       => $alipay_config['partner'],
            "seller_id"  => $alipay_config['seller_id'],
            "payment_type"	=> $alipay_config['payment_type'],
            "notify_url"	=> $alipay_config['notify_url'],
            "return_url"	=> $alipay_config['return_url'],

            "anti_phishing_key"=>$alipay_config['anti_phishing_key'],
            "exter_invoke_ip"=>$alipay_config['exter_invoke_ip'],
            "qr_pay_mode"=>$alipay_config['qr_pay_mode'],
            "out_trade_no"	=> $out_trade_no,
            "qrcode_width" => "300",
            "subject"	=> $subject,
            "total_fee"	=> $total_fee,
            "body"	=> $body,
            "extra_common_param" => $extra_common_param,
            "_input_charset"	=> trim(strtolower($alipay_config['input_charset']))
            //其他业务参数根据在线开发文档，添加参数.文档地址:https://doc.open.alipay.com/doc2/detail.htm?spm=a219a.7629140.0.0.kiX33I&treeId=62&articleId=103740&docType=1
            //如"参数名"=>"参数值"

        );

        //建立请求
        $alipaySubmit = new AlipaySubmit($alipay_config);

        $html_text = $alipaySubmit->buildRequestForm($parameter,"get", "确认");
        exit($html_text);
    }

    public function notify(){
    	//Vendor('alipayf2f.lib.alipay_notify');
        $out_trade_no = $_POST['out_trade_no'];
        $orderModel = D('order');
        $ono_condition['order_sn'] = $out_trade_no;
        $orderInfo = $orderModel->where($ono_condition)->find();
        session('restaurant_id',$orderInfo['restaurant_id']);
        $this::__construct();
		
		import('Vendor.alipayf2f.lib.alipay_notify');
		$alipay_config = $this->alipay_config;
		$alipay_config['sign_type'] = $_POST['sign_type'];
		$alipay_config['notify_id'] = $_POST['notify_id'];
        $alipayNotify = new \AlipayNotify($alipay_config);
        $verify_result = $alipayNotify->verifyNotify();
		//file_put_contents(__DIR__."/log.text",implode("|",$_POST)."\r\n",FILE_APPEND);
        if($verify_result) {//验证成功
            //商户订单号
			file_put_contents(__DIR__."/log.text",'进来了',FILE_APPEND);
//            $out_trade_no = $_POST['out_trade_no'];

            //支付宝交易号

            $trade_no = $_POST['trade_no'];

            //交易状态
            $trade_status = $_POST['trade_status'];

//            $extra_common_param = $_POST['extra_common_param'];

            if($_POST['trade_status'] == 'TRADE_FINISHED') {
                //判断该笔订单是否在商户网站中已经做过处理
                //如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
                //请务必判断请求时的total_fee、seller_id与通知时获取的total_fee、seller_id为一致的
                //如果有做过处理，不执行商户的业务程序

                //注意：
                //退款日期超过可退款期限后（如三个月可退款），支付宝系统发送该交易状态通知

                //调试用，写文本函数记录程序运行情况是否正常
                //logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
            } else if ($_POST['trade_status'] == 'TRADE_SUCCESS') {
//                $device_code = $extra_common_param;


                //操作数据库处理订单信息；
                $order_sn = $out_trade_no;
//                session("num".$order_sn,1);
                $orderModel = D("order");
                $o_condition['order_sn'] = $order_sn;
                $data['order_status'] = 3;
                $data['pay_type'] = 1;
                $time = time();
                $data['pay_time'] = $time;
                $rel = $orderModel->where($o_condition)->save($data); //更改订单状态为支付状态


                if($rel !== false){

                    file_put_contents(__DIR__."/"."payLog.txt","订单号：".$order_sn."|"."时间：".date("Y-m-d H:i:s",time())."|支付方式：支付宝扫码支付"."\r\n",FILE_APPEND);

                    //获取订单信息，判断是否要推送到展示餐牌号展示页面
                    $orderInfo = $orderModel->where($o_condition)->field("table_num,desk_code,restaurant_id")->find();
                    $restaurantModel = D("Restaurant");
                    $rr_condition['restaurant_id'] = $orderInfo['restaurant_id'];
                    $show_device_code = $restaurantModel->where($rr_condition)->field("show_num_d")->find()['show_num_d'];
                    if($orderInfo['table_num'] == 0 && $orderInfo['desk_code'] == 0){
                        $content['tips'] = "下单成功推送showNum";
                        $contentJson = json_encode($content);
                        $post_data = array ("type" => "publish","to" => $show_device_code,"content" => $contentJson);
                        $rel2 = sendMsgToDevice($post_data);
                        $restaurant_id = $orderInfo['restaurant_id'];
                        pushAllDistrict($restaurant_id,$order_sn);
                    }
                }

                //推送信息到android设备，使其打印小票

                //判断该笔订单是否在商户网站中已经做过处理
                //如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
                //请务必判断请求时的total_fee、seller_id与通知时获取的total_fee、seller_id为一致的
                //如果有做过处理，不执行商户的业务程序

                //注意：
                //付款完成后，支付宝系统发送该交易状态通知

                //调试用，写文本函数记录程序运行情况是否正常
                //logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
            }

            //——请根据您的业务逻辑来编写程序（以上代码仅作参考）——

            echo "success";		//请不要修改或删除

            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        }
        else {
            //验证失败
            echo "fail";

            //调试用，写文本函数记录程序运行情况是否正常
            //logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
        }
    }

    function alipay_code(){
    	unlink("qrcode.png");
    	Vendor('alipayf2f.f2fpay.service.AlipayTradeService');
		Vendor('alipayf2f.f2fpay.model.builder.AlipayTradePrecreateContentBuilder');
		$out_trade_no = I('get.order_sn');
		if (!empty($out_trade_no)&& trim($out_trade_no)!=""){
			// (必填) 商户网站订单系统中唯一订单号，64个字符以内，只能包含字母、数字、下划线，
			// 需保证商户系统端不能重复，建议通过数据库sequence生成，
			//$outTradeNo = "qrpay".date('Ymdhis').mt_rand(100,1000);
			$orderModel = D("order");
	        $o_condition['order_sn'] = $out_trade_no;
	        $rel = $orderModel->where($o_condition)->field("total_amount,order_sn,order_status,restaurant_id,discount")->find();
	        if($rel['order_status'] == 3){
	            exit;
	        }
            $price = $rel['total_amount'];
            if($price < 0.01){
                $price = 0.01;
            }
			$outTradeNo = $out_trade_no;
		
			// (必填) 订单标题，粗略描述用户的支付目的。如“xxx品牌xxx门店当面付扫码消费”
			$subject = "方雅点餐系统";
		
			// (必填) 订单总金额，单位为元，不能超过1亿元
			// 如果同时传入了【打折金额】,【不可打折金额】,【订单总金额】三者,则必须满足如下条件:【订单总金额】=【打折金额】+【不可打折金额】
			$totalAmount = $price;
			
			// 业务扩展参数，目前可添加由支付宝分配的系统商编号(通过setSysServiceProviderId方法)，系统商开发使用,详情请咨询支付宝技术支持
			$providerId = "2088421780481061"; //系统商pid,作为系统商返佣数据提取的依据
			$extendParams = new \ExtendParams();
			$extendParams->setSysServiceProviderId($providerId);
			$extendParamsArr = $extendParams->getExtendParams();
					
			// 支付超时，线下扫码交易定义为5分钟
			$timeExpress = "5m";

            //第三方应用授权令牌,商户授权系统商开发模式下使用
            $restaurant_other_info = D("restaurant_other_info");
            $restaurant_id = $rel['restaurant_id'];
            $oti_data['restaurant_id'] = $restaurant_id;
            $aat_rel = $restaurant_other_info->where($oti_data)->find();
            $appAuthToken = $aat_rel['app_auth_token'];//根据真实值填写

			if(!$appAuthToken){
				exit;
			}
			
			// 创建请求builder，设置请求参数
			$qrPayRequestBuilder = new \AlipayTradePrecreateContentBuilder();
			$qrPayRequestBuilder->setOutTradeNo($outTradeNo);
			$qrPayRequestBuilder->setTotalAmount($totalAmount);
			$qrPayRequestBuilder->setTimeExpress($timeExpress);
			$qrPayRequestBuilder->setSubject($subject);
			$qrPayRequestBuilder->setBody($body);
			$qrPayRequestBuilder->setUndiscountableAmount($undiscountableAmount);
			$qrPayRequestBuilder->setExtendParams($extendParamsArr);
			$qrPayRequestBuilder->setGoodsDetailList($goodsDetailList);
//			$qrPayRequestBuilder->setStoreId("hz008");
//			$qrPayRequestBuilder->setOperatorId($operatorId);
//			$qrPayRequestBuilder->setAlipayStoreId($alipayStoreId);
			$qrPayRequestBuilder->setAppAuthToken($appAuthToken);
			
			//公用回传参数
       		$extra_common_param  = cookie("device_code");	
			 
			$alipay_config = $this->alipay_config;
			$config = array (	
				//支付宝公钥
				'alipay_public_key' => $alipay_config['alipay_public_key'],
				//商户私钥
				'merchant_private_key' => $alipay_config['merchant_private_key'],
				//编码格式
				'charset' => $alipay_config['charset'],
		
				//支付宝网关
				'gatewayUrl' =>  $alipay_config['gatewayUrl'],
		
				//应用ID
				'app_id' => $alipay_config['app_id'],
		
				//异步通知地址,只有扫码支付预下单可用
				'notify_url' => $alipay_config['notify_url'],
		
				//最大查询重试次数
				'MaxQueryRetry' => $alipay_config['MaxQueryRetry'],
		
				//查询间隔
				'QueryDuration' => $alipay_config['QueryDuration'],
				
			);

			// 调用qrPay方法获取当面付应答
			$qrPay = new \AlipayTradeService($config);
			$qrPayResult = $qrPay->qrPay($qrPayRequestBuilder);
			$response = $qrPayResult->getResponse();
			$code_addr = $response->qr_code;
			Vendor("phpqrcode.phpqrcode");
            $errorCorrectionLevel = 'M';//容错级别
            $matrixPointSize = 6;//生成图片大小
            \QRcode::png($code_addr,'qrcode.png', $errorCorrectionLevel, $matrixPointSize,2);
            $logo = 'alicode.png';//准备好的logo图片
            $QR = 'qrcode.png';//已经生成的原始二维码图

            if ($logo !== FALSE){
                $QR = imagecreatefromstring(file_get_contents($QR));
                $logo = imagecreatefromstring(file_get_contents($logo));
                $QR_width = imagesx($QR);//二维码图片宽度
                $QR_height = imagesy($QR);//二维码图片高度
                $logo_width = imagesx($logo);//logo图片宽度
                $logo_height = imagesy($logo);//logo图片高度
                $logo_qr_width = $QR_width / 5;
                $scale = $logo_width/$logo_qr_width;
                $logo_qr_height = $logo_height/$scale;
                $from_width = ($QR_width - $logo_qr_width) / 2;
                //重新组合图片并调整大小
                imagecopyresampled($QR, $logo, $from_width, $from_width, 0, 0, $logo_qr_width,
                    $logo_qr_height, $logo_width, $logo_height);
            }
            //输出图片
            ob_clean();
            Header("Content-type: image/png");
            ImagePng($QR);
            imagedestroy($QR);
    	}
	}
}