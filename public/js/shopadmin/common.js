const base_url = 'http://zupu.com/index.php/admin/';
const codeArr = new Array();
codeArr['e200'] = 200;//请求成功
codeArr['e201'] = 201;//数据不存在
codeArr['e202'] = 202;//数据重复
codeArr['e300'] = 300;//缺少必要参数
codeArr['e400'] = 400;//操作失败
codeArr['e401'] = 401;//没有权限
codeArr['e500'] = 500;//用户未登录


function get_token()
{
	return sessionStorage.getItem('user_token');
}

function set_session(key,val)
{
	sessionStorage.setItem(key,val); 
}

function get_session(key)
{
	return sessionStorage.getItem(key);
}

//时间戳转时期
function getDate(str,hour=false) {  
    var oDate = new Date(str),  
    oYear = oDate.getFullYear(),  
    oMonth = oDate.getMonth()+1,  
    oDay = oDate.getDate(),  
    oHour = oDate.getHours(),  
    oMin = oDate.getMinutes(),  
    oSen = oDate.getSeconds(),  
    oTime = oYear +'/'+ getzf(oMonth) +'/'+ getzf(oDay);//最后拼接时间  
    if(hour)
    {
    	oTime += ' '+ getzf(oHour) +':'+ getzf(oMin) +':'+getzf(oSen);
    }
    return oTime;  
}

function getzf(num) {  
	if(parseInt(num) < 10){  
		num = '0'+num;  
	}  
	return num;  
}


//获取url参数
function getParam(name) {
     var reg = new RegExp("(^|&)"+ name +"=([^&]*)(&|$)");
     var r = window.location.search.substr(1).match(reg);
     if(r!=null)return  unescape(r[2]); return null;
}

