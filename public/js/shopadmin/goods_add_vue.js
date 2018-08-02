window.onload = function() {
var app = new Vue({
	el:'#app',
	data:{
		photo_selected:1,
		attr_list:[],
		av_list:[],
		cate_list:[],
	},
	methods:{
		photo_update:function(t) {
			this.photo_selected = t;
			$(".tailoring-container").toggle();
		},
		confim_photo:function() {
			if ($("#tailoringImg").attr("src") == null ){
	            return false;
	        }else{
	            var cas = $('#tailoringImg').cropper('getCroppedCanvas');//获取被裁剪后的canvas
	            var base64url = cas.toDataURL('image/png'); //转换为base64地址形式
	            if(this.photo_selected==1) {
	            	$("#bp").prop("src",base64url);//显示为图片的形式
	            }else if(this.photo_selected==2) {
	            	$("#sp1").prop("src",base64url);//显示为图片的形式
	            }else if(this.photo_selected==3) {
	            	$("#sp2").prop("src",base64url);//显示为图片的形式
	            }else if(this.photo_selected==4) {
	            	$("#sp3").prop("src",base64url);//显示为图片的形式
	            }else if(this.photo_selected==5) {
	            	$("#sp4").prop("src",base64url);//显示为图片的形式
	            }

	            //关闭裁剪框
	            closeTailor();
	        }
		}
	},
	created:function(){
		var data = {};
		data['user_token'] = get_token();
		$.ajax({
			url: base_url+'admin/Goods/get_add_info',
			type: 'post',
			data: data,
			dataType: 'json',
			timeout: 1000,
			success: function (data, status) {
            	var data = eval("("+data+")"); 
				if(data.code==codeArr['e200'])
				{
					var cates,attrs = {};
                    cates = data.data.cates;
                    attrs = data.data.attrs;
                    console.log(attrs);
					app.$data.attr_list = attrs;
					app.$data.cate_list = cates;
                    
                    
				}else
				{
					alert(data.msg);
					window.location.href = './login.html';
				}
				
			},
			fail: function (err, status) {
				alert('数据请求异常');
			}
		})
	}
})
	
}