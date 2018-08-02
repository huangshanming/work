var chatBox 	= $('.chat-content');
var headBox 	= $('.user img');//右边用户的头像
var titleBox 	= $('.user .title');//右边用户的名字
var alldata 	= new Array();//所有人的数据
var allgroup 	= new Array();//所有群的数据
var contentdata = new Array();//聊天的数据
var headimg 	= "http://imgs.jiaoyou0.cn/uploads/photo/2017/06/594b4c68ca643.jpg";//后台服务器传过来的头像
var send_uid 	= null;//发送消息人的id
var msg 		= null;//发送的信息
var timer 		= null;
//模拟数据
// var item = [{
// 	"send_uid":10,
// 	"nick_name":"金韩彬",
// 	"headimg":"http://imgs.jiaoyou0.cn/uploads/photo/2017/06/594b4c68ca643.jpg",
// 	"send_time":1504167796,
// 	"content":"what are you doing?",
// 	"type":1//type:0是用户左边说的话，type:1是客服右边说的话
// },{
// 	"send_uid":20,
// 	"nick_name":"june",
// 	"headimg":"http://imgs.jiaoyou0.cn/uploads/photo/2017/06/594b4c68ca643.jpg",
// 	"send_time":1504167790,
// 	"content":"wo像二哈？",
// 	"type":1
// },{
// 	"send_uid":30,
// 	"nick_name":"美美",
// 	"headimg":"http://imgs.jiaoyou0.cn/uploads/photo/2017/06/594b4c68ca643.jpg",
// 	"send_time":1504167794,
// 	"content":"hello everyone！",
// 	"type":1
// },{
// 	"send_uid":40,
// 	"nick_name":"肉肉",
// 	"headimg":"http://imgs.jiaoyou0.cn/uploads/photo/2017/06/594b4c68ca643.jpg",
// 	"send_time":1504167798,
// 	"content":"人性化?",
// 	"type":1
// },{
// 	"send_uid":50,
// 	"nick_name":"dage",
// 	"headimg":"http://imgs.jiaoyou0.cn/uploads/photo/2017/06/594b4c68ca643.jpg",
// 	"send_time":1504167794,
// 	"content":"qifuwo！",
// 	"type":1
// },{
// 	"send_uid":60,
// 	"nick_name":"bobby",
// 	"headimg":"http://imgs.jiaoyou0.cn/uploads/photo/2017/06/594b4c68ca643.jpg",
// 	"send_time":1504167798,
// 	"content":"hanbinna?",
// 	"type":1
// },{
// 	"send_uid":70,
// 	"nick_name":"美美",
// 	"headimg":"http://imgs.jiaoyou0.cn/uploads/photo/2017/06/594b4c68ca643.jpg",
// 	"send_time":1504167794,
// 	"content":"hello everyone！",
// 	"type":1
// },{
// 	"send_uid":80,
// 	"nick_name":"肉肉",
// 	"headimg":"http://imgs.jiaoyou0.cn/uploads/photo/2017/06/594b4c68ca643.jpg",
// 	"send_time":1504167798,
// 	"content":"人性化?",
// 	"type":1
// },{
// 	"send_uid":90,
// 	"nick_name":"美美",
// 	"headimg":"http://imgs.jiaoyou0.cn/uploads/photo/2017/06/594b4c68ca643.jpg",
// 	"send_time":1504167794,
// 	"content":"hello everyone！",
// 	"type":1
// },{
// 	"send_uid":100,
// 	"nick_name":"肉肉",
// 	"headimg":"http://imgs.jiaoyou0.cn/uploads/photo/2017/06/594b4c68ca643.jpg",
// 	"send_time":1504167798,
// 	"content":"人性化?",
// 	"type":1
// }]

//读取网络服务器
function getmessagebox(){
	
}
each_item();


//聊天列表vue绑定

var vm = new Vue({
	el:'.chat-list',//操作范围
	data:{//数据
		items:alldata,
		groups:allgroup,
		head_name:'',
	},
	methods:{//方法
		doThis:function(sid,title,rheadimg,content,index){
			if(sid<0) {
				vm.groups[index].type = 0;//???????
				vm.groups[index].news = 0;//???????
			}else {
				vm.items[index].type = 0;//???????
				vm.items[index].news = 0;//???????
			}
			vc.head_name = title;
			vc.head_photo = rheadimg;
			vc.send_uid = sid;
			send_uid = sid;
			vc.items = vc.all_items[vc.send_uid];
			chatBox.css('display','block');
			headBox.attr('src','rheadimg');//右边用户的头像改变src
			titleBox.text(title);//显示右边用户的名字
			// chatCentent();
		},
		remove:function(index){//删除
			alldata.splice(index,1);
			vc.items.splice(0,vc.items.length);//????????
		}
	}
})

//聊天记录

var vc = new Vue({
	el:'#chat',
	data:{
		items:[],//存储信息
		all_items:[],//存储信息
		user:true,
		message:'',
		head_photo:'',
		head_name:'',
		send_uid:0,
		emoji_key:false,
	},
	methods:{
		inputing:function(e){
			if(e.keyCode === 13){
				if(!this.send_uid) {
					weui.alert('请选择聊天对象');
					return false;
				}
				var user_content 	= this.message;
				var user_type 		= 1;
				var user_headimg 	= get_session('head_photo');
				var obj 			= new Object;//创建一个空对象
				obj.type 			= user_type; 
				obj.content 		= user_content;
				obj.headimg 		= user_headimg;
				obj.send_time 		= new Date();
				if(!vc.all_items[this.send_uid]) {
					vc.all_items[this.send_uid] = [];
				}
				vc.all_items[this.send_uid].push(obj);
				send_msg(this.send_uid, user_content);
				this.message = '';
				vc.items = vc.all_items[this.send_uid];
			}
		},
		classli:function(type){
			switch(type){
				case 1:
					return{
						'self':true
					};
					break;
				default:	
					break;
			}
		},
		shop_show:function() {
			$('#shop_modal').modal();
			var data = {};
			data['p'] = 1;
			data['cate_id'] = '';
			data['user_token'] = get_token();
			$.ajax({
				url: base_url+'Goods/get_list',
				type: 'post',
				data: data,
				dataType: 'json',
				timeout: 1000,
				success: function (data, status) {
					
					if(data.code==codeArr['e200'])
					{
						vs.$data.goods_list = data.data.list;
						vs.$data.cate_list = data.data.cates;
						var pageNavObj = null;//用于储存分页对象
						pageNavObj = new PageNavCreate("PageNavId",{ pageCount:data.data.pageCount, currentPage:1, perPageNum:5, });
						pageNavObj.afterClick(function(page){
							vs.$options.methods.setPage(page);
						});
						vs.user_info     = data.data.user_info;
						vs.$data.address = data.data.user_info.address;
						vs.$data.name 	 = data.data.user_info.nickname;
						vs.$data.phone 	 = data.data.user_info.phone;
					}else
					{
						weui.alert(data.msg);
						window.location.href = './index.html';
					}
					
				},
				fail: function (err, status) {
					alert('数据请求异常');
				}
			})

			$.ajax({
				url: base_url+'Adv/get_model',
				type: 'post',
				data: data,
				dataType: 'json',
				timeout: 1000,
				success: function (data, status) {
					
					if(data.code==codeArr['e200'])
					{
						vs.$data.adv_list = data.data;
					}
					
				},
				fail: function (err, status) {
					alert('数据请求异常');
				}
			})
		},
	},
	directives:{//自定义指令
		//发送消息后滚动到底部
		'scroll-bottom'(){
			Vue.nextTick(() => {
				var scrollitem = document.getElementsByClassName('m-message')[0];
				scrollitem.scrollTop = scrollitem.scrollHeight - scrollitem.clientHeight;
			});
		}
	},
	filters:{//过滤器
		time(date){
			if(typeof date === 'number'){
				date = new Date(date);
			}
			return date.getHours() + ':' + date.getMinutes();
		}
	},
})

//商城vue绑定

var vs = new Vue({
	el:'#shop',//操作范围
	data:{//数据
		goods_list:[],
		cate_list:[],
		page:1,
		cate_id:'',
		order:'',
		search_text:'',
		min_price:'',
		max_price:'',
		shop_key:1,
		goods_detail:[],
		attrs:[],
		attr_select:[],
		attr_index:0,
		attr_select_color:'#ddd',
		buy_num:1,
		buy_price:'',
		user_info:[],
		rechage_price:5,
		pay_code:'',
		address:'',
		name:'',
		phone:'',
		remark:'',
	},
	watch:{
		rechage_price:function() {
			$('#pay_code').css('display', 'none');
			if(vs.$data.rechage_price.length==1) {
				vs.$data.rechage_price = vs.$data.rechage_price.toString().replace(/[^1-9]/g,'');
			} else{
				vs.$data.rechage_price = vs.$data.rechage_price.toString().replace(/\D/g,'');
			}  
			if(vs.$data.rechage_price.length==1) {
				vs.$data.rechage_price = vs.$data.rechage_price.toString().replace(/[^1-9]/g,'0');
			} else{
				vs.$data.rechage_price = vs.$data.rechage_price.toString().replace(/\D/g,'');
			}
			if(vs.$data.rechage_price >= 9999) {
				vs.$data.rechage_price = 10000;
			}
		},
	},
	methods:{//方法
		recharge_confirm:function() {
			$('#recharge_confirm_btn').attr('disabled', 'disabled');
			clearTimeout(timer);
			if(isNaN(vs.$data.rechage_price) || vs.$data.rechage_price === "" || vs.$data.rechage_price ==null) {
				$('#pay_code').css('display', 'none');
                return false;
            }
            var data = {};
            data['user_token'] = get_token();
            data['money'] = vs.$data.rechage_price;
            var url = base_url+'recharge/confirm';
            $.ajax({
                url: url,
                type: 'post',
                data: data,
                dataType: 'json',
                timeout: 3000,
                success: function (data, status) {
                     
                    if(data.code==codeArr['e200'])
                    {
                        var data = data.data;
                        // $('#pay_code').attr('src','data.url');
                        document.getElementById("pay_code").src = data.url;
                        // vs.$data.pay_code = data.url;
						$('#pay_code').css('display', 'block');
                        search(data.recharge_num);
						setTimeout(function(){
	                    	$('#recharge_confirm_btn').attr('disabled', false);
	                    },2000);
                        return false;
                    }else 
                    {
                        weui.alert(data.msg);
                    }
                    
                },
                fail: function (err, status) {

                }
            })
		},
		rechage_price_add:function() {
			if(vs.$data.rechage_price >= 9999)
			{
				vs.$data.rechage_price = 10000;return false;
			}
			vs.$data.rechage_price = parseInt(vs.$data.rechage_price) + 1;
		},
		rechage_price_reduce:function() {
			if(vs.$data.rechage_price <= 1)
			{
				vs.$data.rechage_price = 1; return false;
			}
			vs.$data.rechage_price = parseInt(vs.$data.rechage_price) - 1;
		},
		pay:function() {
			var balance = vs.$data.user_info.balance;
			var price   = vs.$data.buy_price;
			if(isNaN(balance) || balance === "" || balance ==null) {
				weui.alert('余额获取失败，请重新登录');
				return false;
			}
			if(isNaN(price) || price === "" || price ==null) {
				weui.alert('商品价格有误，请重试');
				return false;
			}
			if(balance < price) {
				$('#recharge_modal').modal();return false;
			}
			var address = vs.$data.address;
			var phone 	= vs.$data.phone;
			var name 	= vs.$data.name;
			if(address == "" || address ==null || phone == "" || phone ==null || name == "" || name ==null  ) {
				weui.alert('请填写完整收货信息');return false;
			}
			weui.confirm('确定使用亲链币付款？',function(){
				var attr_id 		= vs.$data.goods_detail.attr_id;
				var data 			= {};
				data['number'] 		= vs.$data.buy_num;
				data['attr_id'] 	= attr_id;
				data['address'] 	= address;
				data['phone'] 		= phone;
				data['name'] 		= name;
				data['remark']  	= vs.$data.remark;
				data['user_token'] 	= get_token();
				$.ajax({
					url: base_url+'Order/build',
					type: 'post',
					data: data,
					dataType: 'json',
					timeout: 1000,
					success: function (data, status) {
						
						if(data.code==codeArr['e200'])
						{
							weui.alert('下单成功，可在我的订单中查看');
							vs.$data.user_info.balance = vs.$data.user_info.balance - price;
							return false;
						}else
						{
							weui.alert(data.msg);
							return false;
						}
						
					},
					fail: function (err, status) {
						alert('数据请求异常');
					}
				})
			});

		},
		reduce:function() {
			if(vs.$data.buy_num > 1)
			{
				vs.$data.buy_num = parseInt(vs.$data.buy_num) - 1;
			}
			vs.$data.buy_price = (vs.$data.goods_detail.price * vs.$data.buy_num).toFixed(2);
		},
		add:function() {
			if(vs.$data.buy_num < vs.$data.goods_detail.count)
			{
				vs.$data.buy_num = parseInt(vs.$data.buy_num) + 1;
			}
			vs.$data.buy_price = (vs.$data.goods_detail.price * vs.$data.buy_num).toFixed(2);
		},
		getAttr:function(id,index) {
			vs.$data.attr_index 				= index;
			vs.$data.goods_detail.price 		= vs.$data.attrs[index].price;
			vs.$data.goods_detail.origin_price 	= vs.$data.attrs[index].origin_price;
			vs.$data.goods_detail.count 		= vs.$data.attrs[index].count;
			vs.$data.goods_detail.attr_id 		= id;
			vs.$data.buy_price					= vs.$data.attrs[index].price;
			vs.$data.buy_num 					= 1;
		},
		goods_select:function(list) {
			console.log(list);
			vs.$data.goods_detail = list;
			var data = {};
			var id = list.id;
			data['id'] = id;
			$.ajax({
				url: base_url+'Goods/detail',
				type: 'post',
				data: data,
				dataType: 'json',
				timeout: 1000,
				success: function (data, status) {
					if(data.code==codeArr['e200'])
					{
						var goods 							= data.data.goods;
						var attrs 							= data.data.attrs;
						vs.$data.attrs 						= attrs.slice(0,7);
						vs.$data.goods_detail.price 		= vs.$data.attrs[0].price;
						vs.$data.goods_detail.origin_price 	= vs.$data.attrs[0].origin_price;
						vs.$data.goods_detail.count 		= vs.$data.attrs[0].count;
						vs.$data.goods_detail.attr_id 		= vs.$data.id;
						vs.$data.attr_select.price 			= attrs[0].price;
						vs.$data.attr_select.attr_id 		= attrs[0].id;
						vs.$data.attr_select.origin_price 	= attrs[0].origin_price;
						vs.$data.buy_price					= attrs[0].price;
						vs.$data.buy_num 					= 1;
						vs.$data.shop_key 					= 2;
					}else
					{
						weui.alert(data.msg);
					}
					
				},
				fail: function (err, status) {
					weui.alert('数据请求异常');
				}
			})
		},
		cate_select:function(cate_id) {
			if(vs.$data.cate_id == cate_id) {
				vs.$data.cate_id = '';
			}else {
				vs.$data.cate_id = cate_id;
			}
			var loading;
			var data = {};
			var p = 1;
			data['p'] = p;
			data['user_token'] = get_token();
			data['cate_id'] = vs.$data.cate_id;
			data['order'] = this.order;
			data['search'] = this.search_text;
			data['min'] = this.min_price;
			data['max'] = this.max_price;
			$.ajax({
				url: base_url+'Goods/get_list',
				type: 'post',
				data: data,
				dataType: 'json',
				timeout: 1000,
				beforeSend:function() {
					loading = weui.loading();
				},
				success: function (data, status) {
					
					loading.hide();
					if(data.code==codeArr['e200'])
					{
						vs.$data.goods_list = data.data.list;
						vs.$data.cate_list = data.data.cates;
						var pageNavObj = null;//用于储存分页对象
						pageNavObj = new PageNavCreate("PageNavId",{ pageCount:data.data.pageCount, currentPage:p, perPageNum:5, });
						pageNavObj.afterClick(function(page){
							vs.$options.methods.setPage(page);
						});
					}

				},
				fail: function (err, status) {
					loading.hide();
					alert('数据请求异常');
				}
			})
		},
		setPage:function(page) {
			var data = {};
			this.page = page;
			var loading;
			data['p'] = page;
			data['cate_id'] = vs.$data.cate_id;
			data['order'] = vs.$data.order;
			data['user_token'] = get_token();
			data['search'] = this.search_text;
			data['min'] = this.min_price;
			data['max'] = this.max_price;
			$.ajax({
				url: base_url+'Goods/get_list',
				type: 'post',
				data: data,
				dataType: 'json',
				timeout: 1000,
				beforeSend:function() {
					loading = weui.loading();
				},
				success: function (data, status) {
					
					loading.hide();
					if(data.code==codeArr['e200'])
					{
						vs.$data.goods_list = data.data.list;
						vs.$data.cate_list = data.data.cates;
						var pageNavObj = null;//用于储存分页对象
						pageNavObj = new PageNavCreate("PageNavId",{ pageCount:data.data.pageCount, currentPage:page, perPageNum:5, });
						pageNavObj.afterClick(function(page){
							vs.$options.methods.setPage(page);
						});
					}
				},
				fail: function (err, status) {
					loading.hide();
					alert('数据请求异常');
				}
			})
		},
	},
	created:function() {
		
	},
})

$('.user .cha').on('click',function(){//点X号删除聊天内容
	vc.items.splice(0,vc.items.length);//从零开始删除  删除所有信息的长度
})

//从服务器上获取数据
function chatCentent(){
	if(send_uid == 10){
		msg = [{
			send_uid:10,
			nick_name:"金韩彬",
			headimg:"http://imgs.jiaoyou0.cn/uploads/photo/2017/06/594b4c68ca643.jpg",
			send_time:1504167796,
			content:"what are you doing?",
			type:0
		}]
	}else if(send_uid == 20){
		msg = [{
			send_uid:20,
			nick_name:"june",
			headimg:"http://imgs.jiaoyou0.cn/uploads/photo/2017/06/594b4c68ca643.jpg",
			send_time:1504167796,
			content:"Are you 二哈?",
			type:0
		}]
	}else if(send_uid == 30){
		msg = [{
			send_uid:30,
			nick_name:"美美",
			headimg:"http://imgs.jiaoyou0.cn/uploads/photo/2017/06/594b4c68ca643.jpg",
			send_time:1504167796,
			content:"妮维雅?",
			type:0
		}]
	}else{
		msg = [{
			send_uid:40,
			nick_name:"肉肉",
			headimg:"http://imgs.jiaoyou0.cn/uploads/photo/2017/06/594b4c68ca643.jpg",
			send_time:1504167796,
			content:"双胞胎?",
			type:0
		}]
	}
	// vc.items.splice(0,vc.items.length);
	// vc.items = vc.items.concat(convert(msg));//concat连接两个数组
};

//数据处理
function each_item(){
	var item = {};
	window.onload = function() {
		var data = {};
        data['user_token'] = get_token();
        $.ajax({
            url: base_url+'Jiapu/get_chat_list',
            type: 'post',
            data: data,
            dataType: 'json',
            async:true,
            timeout: 1000,
            success: function (data, status) {
                 
                if(data.code==codeArr['e200'])
                {
                    var str = '';
                    var list = data.data;
                    item = list;
                    $.each(item,function(index,data){
						var edata = new Object();
						edata.send_uid = data.id;
						edata.nick_name = data.nickname;
						edata.headimg = data.photo;
						edata.content = 11;
						edata.type = 1;
						edata.news = 0;
						editlist(edata);
					});
                }else
                {
                    alert('请先登录');
                    history.go(-1);
                }
                
            },
            fail: function (err, status) {
                alert('数据请求异常');
            }
        })
	}
	
}

function editlist(data){
	$.each(alldata, function(ii,value) {
		if(value.send_uid == data.send_uid){
			alldata.splice(ii,1);
		}
		
	});
	$.each(allgroup, function(ii,value) {
		if(value.send_uid == data.send_uid){
			allgroup.splice(ii,1);
		}
		
	});
	if(data.send_uid < 0) {
		allgroup.unshift(data);
	}else {
		alldata.unshift(data);
	}
}

function convert(items){
	var newItems = [];
	items.forEach(function(item){
		newItems.push({
			send_uid:item.send_uid,
			nick_name:item.nick_name,
			headimg:item.headimg,
			content:item.content,
			send_time:item.send_time,
			type:item.type
		});
	});
	return newItems;
}

function search(recharge_num) {
    var data = {};
    data['recharge_num'] = recharge_num;
    var url = base_url+'recharge/get_recharge_status';
    $.ajax({
        url: url,
        type: 'post',
        data: data,
        dataType: 'json',
        timeout: 3000,
        success: function (data, status) {
            if(data.data==1)
            {
                window.setTimeout(function() {
                    $('#recharge_modal').hide();
                    vs.user_info.balance = vs.user_info.balance + vs.rechage_price;
                },1500);
                weui.alert('充值成功');
            }else {
				clearTimeout(timer);
                timer = setTimeout(function(){
                    search(recharge_num);
                },1000);
            }
        },
        fail:function (err, status) {
            timer = setTimeout(function(){
                search(recharge_num)
            },1000);
        }
    })
}