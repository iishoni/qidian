define(function (){
	var clickDan = function(){
		var index = layer.load(1, {
		  shade: [0.1,'#fff']
		});
		//判断
		$.post('/Panic/CheckTime',{'ac':'time'},function(data){
			 
			if(data.status=='0'){
				layer.close(index);
				msg_alert(data.message);
			}else{
			
				var money=yx_clickJinDanView();
				
				$.post('/Panic/CheckTime',{'ac':'money','money':money},function(data){
					layer.close(index);
					if(data.status=='0'){
						msg_alert(data.message)
					}else{
						clickJinDanView(money);
					}
					
				},'json');
				
				return false;
			}	
			
		},'json')
		
	}

	var jiangPinResult = function(money){
			
		var str = '';
		str += '<div class="bg-mask"></div>';
		str +='<div class="jiangPinResult">';
		str +='<img class="gongxi" src="/Public/home/images/gx.png" alt="恭喜你" title="恭喜">';
		str +='<p>'+money+'金额</p>';
		str +='<img class="imgJiangPin" src="/Public/home/images/jiangpin.png" alt="奖品" title="奖品">';
		// str +='<a href="#"><input class="btn_Get" value="领取奖品" type="button"></a>';
		str +='<img class="colseJiangPin" src="/Public/home/images/close.png" alt="关闭" title="关闭">';
		str +='</div>';
		
		str +='<div class="gboy_sub" style="position: absolute; z-index: 10003; top: 80%;  left: 0px; right: 0px; margin: 0px auto; font-size: 14px; text-align:center;">'

		str +='<a href="javascript:;" id="close_pin" class="gboy_button" style="top:0;background:#FCB03B;border:1px solid #FCB03B;color:#333;">再来一次</a>';
		str +='<a href="javascript:;" id="send_pin"  class="gboy_button" style="top:0;background:#FCB03B;border:1px solid #FCB03B;color:#333;" data-money="'+money+'">确认提交</a>';

		str +='</div>';

		$('body').css({'overflow':'hidden'});
		$('body').prepend(str);

		setTimeout(function(){
			$('.jiangPinResult').addClass('active');
		},200);
				
		
	
	}

	var alertLogin = function(){
		var str = '';
		str += '<div class="bg-mask"></div>';
		str += '<div class="inputInfo">';
		str += '<img class="colseLogin" src="/Public/home/images/close.png" alt="关闭">';
		str += '<p class="inputInfoTitle">输入用户信息</p>';
		str += '<div class="userNameDiv">';
		str += '<label for="nameInput">用户名:</label>';
		str += '<input class="nameInput" name="nameInput" id="nameInput" type="text" value="admin" placeholder="请输入用户名">';
		str += '</div>';
		str += '<div class="phoneDiv">';
		str += '<label for="phoneInput">手机号:</label>';
		str += '<input class="phoneInput" name="phoneInput" id="phoneInput" type="text" value="13868686868" placeholder="请输入手机号号码">';
		str += '</div>';
		str += '<input class="submitTijiao" type="button" value="提交">';
		str += '</div>';

		$('body').css({'overflow':'hidden'});
		$('body').prepend(str);

		setTimeout(function(){
			$('.inputInfo').addClass('active');
		},200);
		
		//点击切换另一个弹窗
		$('.submitTijiao').click(function(){
			var phoneNumber = $('.phoneInput').val();
			var userName = $('.nameInput').val();
			if(phoneNumber===''||userName===''){
				alert('信息不能为空！');
			}else{
				if(isRightPhoneNumber(phoneNumber)){
					$('.bg-mask').remove();
					$('.inputInfo').remove();
					$('body').css({overflow:'hidden'});
					$('.tishiChouJiang').html(userName+', 你好!点击金蛋抽奖<span class="loginOutSpan">退出<span>');
					$('body').css({'overflow':'visible'});
				}else{
					alert('电话号码格式不正确!');
				}
			}
		});
	}


	var isRightPhoneNumber = function(val){
		var re=/^(13[0-9]{9})|(15[0-9][0-9]{8})|(18[0-9][0-9]{8})|(17[0][0-9]{8})|(14[7][0-9]{8})$/;   
		if(!re.test(val)){      
			return 0;
		}else{
			return 1;
		}
	}

	var clickJinDanView = function(money){
		var str = '';
		str += '<div class="bg-mask"></div>';
		str +='<div class="chouJiang">';
		str +='<p class="chouJiangTishi">请稍等...</p>';
		str +='<img class="caiDai" src="/Public/home/images/caidai.png" alt="彩带">';
		str +='<img class="imgDan" src="/Public/home/images/egg.png" alt="砸蛋" title="砸蛋">';
		str +='<img class="imgChuiZi" src="/Public/home/images/chuizi.png" alt="锤子">';
		str +='</div>';


		$('body').css({'overflow':'hidden'});
		$('body').prepend(str);
		
		setTimeout(function(){
				$('.bg-mask').remove();
				$('.chouJiang').remove();
				//var arr=['1000','2000','5000','10000'];
				//var num = parseInt(Math.random() * 4);
				//jiangPinResult();
				//jiangPinResult(arr[num]);
				jiangPinResult(money);
		},2000);
			
			

	}	
	
	var yx_clickJinDanView = function(){
	
		var arr=['1000','2000','5000'];
		var num = parseInt(Math.random() * 3);
		//jiangPinResult();
		//jiangPinResult(arr[num]);
		
		return arr[num];

	}

	return{
		clickDan:clickDan,  //敲击蛋判断是否登录
		jiangPinResult:jiangPinResult, //显示奖品的弹窗
		//alertLogin:alertLogin,   //提示输入用户名
		//isRightPhoneNumber:isRightPhoneNumber,   //验证电话号码
		clickJinDanView:clickJinDanView,    //显示敲击金蛋效果
	}
});