var chatBox = $('.chat-content'); //聊天框
var headBox = $('.user img'); //头像
var titleBox = $('.user .title'); //头像 
var alldata = new Array(); //头像
var Contentdata = new Array(); //头像
var headimg = "http://imgs.jiaoyou0.cn/uploads/photo/2017/06/594b4c68ca643.jpg"; //头像
var Send_uid = null //头像
var Msg = null;
//模拟数据
var item = [{
	"send_uid": 10,
	"nick_name": "午后",
	"headimg": "http://imgs.jiaoyou0.cn/uploads/photo/2017/06/594b4c68ca643.jpg",
	"send_time": 1504167796,
	"content": "你在哪里呀？",
	"type": 1
}, {
	"send_uid": 20,
	"nick_name": "午后imgimg",
	"headimg": "http://imgs.jiaoyou0.cn/uploads/photo/2017/06/594b4c68ca643.jpg",
	"send_time": 1504167796,
	"content": "你在as哪里呀？",
	"type": 1
}, {
	"send_uid": 31,
	"nick_name": "的旅的旅的旅后旅程",
	"headimg": "http://imgs.jiaoyou0.cn/uploads/photo/2017/06/594b4c68ca643.jpg",
	"send_time": 1504167796,
	"content": "你asdsads里呀？",
	"type": 1
}, {
	"send_uid": 40,
	"nick_name": "午后的旅的旅程",
	"headimg": "http://imgs.jiaoyou0.cn/uploads/photo/2017/06/594b4c68ca643.jpg",
	"send_time": 1504167796,
	"content": "你在哪里呀哪里呀哪里呀？",
	"type": 1
}]

//读取网络服务器
function getmessagebox() {

}
each_item();

//聊天列表vue绑定

var vm = new Vue({
	el: '.chat-list',
	data: {
		items: alldata
	},
	methods: {
		doThis: function(sid, title, rheadimg, content,index) {
			vm.items[index].type = 0;
			Send_uid = sid;
			chatBox.css('display', 'block');
			headBox.attr('src', rheadimg);
			titleBox.text(title);
			chatCentent();
		},
		remove: function(index) {
			alldata.splice(index, 1); //do this
			vc.items.splice(0, vc.items.length);
		}
	},
})

//聊天记录

var vc = new Vue({
	el: '#chat',
	data: {
		items: [],
		User: true,
		message: ''
	},
	methods: {
		inputing: function(e) {
			if(e.ctrlKey && e.keyCode === 13) {
				var user_content = this.message;
				var user_type = 1;
				var user_headimg = "http://imgs.jiaoyou0.cn/uploads/photo/2017/06/594b4c68ca643.jpg";
				var obj = new Object;
				obj.type = user_type;
				obj.content = user_content;
				obj.headimg = user_headimg;
				obj.send_time = new Date();
				vc.items.push(obj)
				this.message = ''
			}
		},
		classli: function(type) {
			switch(type) {
				case 1:
					return {
						'self': true
					};
					break;
				default:
					break;
			}
		}
	},
	directives: {
		// 发送消息后滚动到底部
		'scroll-bottom' () {
			Vue.nextTick(() => {
				var scroitem = document.getElementsByClassName('m-message')[0];
				scroitem.scrollTop = scroitem.scrollHeight - scroitem.clientHeight;

			});
		}
	},
	filters: {
		time(date) {
			console.log(date)
			if(typeof date === 'number') {

				date = new Date(date);
			}
			return date.getHours() + ':' + date.getMinutes();
		}
	},
})

$('.user .cha').on('click', function() {
	vc.items.splice(0, vc.items.length);
});

//从服务器上获取
function chatCentent() {
	if(Send_uid == 40) {
		Msg = [{
			send_uid: 10,
			nick_name: "asdsadsad午后",
			headimg: "http://imgs.jiaoyou0.cn/uploads/photo/2017/06/594b4c68ca643.jpg",
			send_time: 1504167796,
			content: "asdsadsa？",
			type: 1
		}]
	} else {
		Msg = [{
			send_uid: 10,
			nick_name: "asdsadsad午后",
			headimg: "http://imgs.jiaoyou0.cn/uploads/photo/2017/06/594b4c68ca643.jpg",
			send_time: 1504167796,
			content: "asdsadsa？",
			type: 0
		}, {
			send_uid: 10,
			nick_name: "asdsadsad午后",
			headimg: "http://imgs.jiaoyou0.cn/uploads/photo/2017/06/594b4c68ca643.jpg",
			send_time: 1504167796,
			content: "asdsadsa？",
			type: 1
		}, {
			send_uid: 10,
			nick_name: "asdsadsad午后",
			headimg: "http://imgs.jiaoyou0.cn/uploads/photo/2017/06/594b4c68ca643.jpg",
			send_time: 1504167796,
			content: "asdsadsa？",
			type: 0
		}, {
			send_uid: 10,
			nick_name: "asdsadsad午后",
			headimg: "http://imgs.jiaoyou0.cn/uploads/photo/2017/06/594b4c68ca643.jpg",
			send_time: 1504167796,
			content: "asdsadsa？",
			type: 0
		}, {
			send_uid: 10,
			nick_name: "asdsadsad午后",
			headimg: "http://imgs.jiaoyou0.cn/uploads/photo/2017/06/594b4c68ca643.jpg",
			send_time: 1504167796,
			content: "asdsadsa？",
			type: 0
		}, {
			send_uid: 10,
			nick_name: "asdsadsad午后",
			headimg: "http://imgs.jiaoyou0.cn/uploads/photo/2017/06/594b4c68ca643.jpg",
			send_time: 1504167796,
			content: "asdsadsa？",
			type: 0
		}, {
			send_uid: 10,
			nick_name: "asdsadsad午后",
			headimg: "http://imgs.jiaoyou0.cn/uploads/photo/2017/06/594b4c68ca643.jpg",
			send_time: 1504167796,
			content: "asdsadsa？",
			type: 1
		}]
	}
	vc.items.splice(0, vc.items.length)
	vc.items = vc.items.concat(convert(Msg));
};
//数据处理
function each_item() {
	$.each(item, function(index, data) {
		var edata = new Object();
		edata.send_uid = data.send_uid;
		edata.nick_name = data.nick_name;
		edata.headimg = data.headimg;
		edata.send_time = data.send_time;
		edata.content = data.content;
		edata.type = data.type;
		editlist(edata);
	});
}

function editlist(data) {
	$.each(alldata, function(ii, vaule) {
		if(vaule.send_uid == data.send_uid) {
			alldata.splice(ii, 1);
		}
	});
	alldata.unshift(data);
}

function convert(items) {
	var newItems = [];
	items.forEach(function(item) {
		newItems.push({
			send_uid: item.send_uid,
			nick_name: item.nick_name,
			headimg: item.headimg,
			content: item.content,
			send_time: item.send_time,
			type: item.type
		});
	});
	return newItems;
}