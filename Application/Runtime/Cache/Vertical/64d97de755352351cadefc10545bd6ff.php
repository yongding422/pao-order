<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">

		<!-- 引入样式 -->
		<link rel="stylesheet" href="/Public/element-ui/lib/theme-default/index.css">
		<link rel="stylesheet" href="/Public/css/vertical_template.css">
	</head>
	<body>
		<div id="ad">
		    <div v-if="show">
		    	<el-carousel :interval="advertise_time" arrow="never" indicator-position="none" :autoplay="true" :height="windowHeight">
		    		<el-carousel-item v-for="pic in adData">
		    	  		<a href="/index.php/Vertical/Template/serviceRoute/current_action/index">
		    	  			<img :src="pic.advertisement_image_url" :height="windowHeight">
		    	  		</a>
		    	  	</el-carousel-item>
		    	</el-carousel>
		    </div>
		</div>
	</body>
	<!-- 先引入 Vue -->
	<script src="/Public/js/vue.js"></script>
	<!-- 引入组件库 -->
	<script src="/Public/element-ui/lib/index.js"></script>
	<script src="/Public/js/jquery-3.1.0.min.js"></script>
	<script src="/Public/js/prevent.js"></script>
	<script>
		new Vue({
			data:{	
				show:false,
				adData:[],
				advertise_time:3000,
			},
			computed: {
			    windowHeight: function () {
			      return window.innerHeight+'px'
			    }
			},
			mounted:function(){
				$('iframe').hide();
				$('#ad').siblings('div').hide();
			},
			beforeCreate:function(){
				_self=this;
				$.ajax({
					type: "get",
					url: "/index.php/api/Advertisement/getAdvertisementList/advertisement_type/1",
					dataType: 'json',  
					success: function(data){
						_self.adData=data.data;
						_self.advertise_time = data.advertise_time*1000;
						_self.show=true;
					},
					error: function(data){
						console.log('error'+data);
					}
				});
			}

		}).$mount('#ad');
</script>
</html>