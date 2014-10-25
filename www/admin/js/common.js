
function showModel(msg){
	//e.preventDefault();
	var refresh = arguments[1] ? arguments[1] : false; 
	var title = arguments[2] ? arguments[2] : '提示';
	var closeTime = arguments[3] ? arguments[3] : false;
	$("#xModalTitle").html(title);
	if(!refresh){
		$("#msg").html(msg);
		$('#xModal').modal('show');
	}else{
		$("#refresh-msg").html(msg);
		$('#xModalRefresh').modal('show');
	}

	//定时关闭功能
        if( closeTime ){
                setTimeout(function(){
                        $('#xModal').modal('hide');
                        $('#xModalRefresh').modal('hide');
                }, closeTime);
        }
}