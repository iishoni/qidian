
function msg_alert(message,url){
	if(url){
		layer.msg(message,{time:1000},function(){
          window.location.href=url;
        });
	}else{
		layer.msg(message,{time:1500});
	}
	
}
