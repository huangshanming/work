RongIMLib.RongIMClient.init('pwe86ga5pi0b6');
// 设置连接监听状态 （ status 标识当前连接状态 ）
 // 连接状态监听器
 RongIMClient.setConnectionStatusListener({
    onChanged: function (status) {
        switch (status) {
            case RongIMLib.ConnectionStatus.CONNECTED:
                console.log('链接成功');
                break;
            case RongIMLib.ConnectionStatus.CONNECTING:
                console.log('正在链接');
                break;
            case RongIMLib.ConnectionStatus.DISCONNECTED:
                console.log('断开连接');
                break;
            case RongIMLib.ConnectionStatus.KICKED_OFFLINE_BY_OTHER_CLIENT:
                console.log('其他设备登录');
                break;
              case RongIMLib.ConnectionStatus.DOMAIN_INCORRECT:
                console.log('域名不正确');
                break;
            case RongIMLib.ConnectionStatus.NETWORK_UNAVAILABLE:
              console.log('网络不可用');
              break;
            }
    }});



		// 消息监听器
RongIMClient.setOnReceiveMessageListener({
    // 接收到的消息
    onReceived: function (message) {
        // 判断消息类型
        switch(message.messageType){
            case RongIMClient.MessageType.TextMessage:
            // console.log(message);
                var senderUserId = message.senderUserId;
                if(message.conversationType == 3) {
                    senderUserId = 0 - message.targetId;
                }
                var obj = new Object;//创建一个空对象
                obj.type = 2;
                obj.content = RongIMLib.RongIMEmoji.symbolToEmoji(message.content.content);
                obj.headimg = '';
                $.each(alldata, function(ii,value) {
                    if(value.send_uid == message.senderUserId){
                        obj.headimg = alldata[ii]['headimg'];
                    }
                });
                if(message.conversationType == 3) {
                    $.each(allgroup, function(ii,value) {
                        if(value.send_uid == senderUserId){
                            allgroup.unshift(allgroup[ii]);
                            if(vc.send_uid != senderUserId) {
                                allgroup[0].news += 1;
                            }
                            allgroup.splice(ii+1,1);
                        }
                    });
                }else {
                    $.each(alldata, function(ii,value) {
                        if(value.send_uid == senderUserId){
                            vm.items.unshift(vm.items[ii]);
                            if(vc.send_uid != senderUserId) {
                                vm.items[0].news += 1;
                            }
                            vm.items.splice(ii+1,1);
                        }
                    });
                }
                obj.send_time = new Date();
                if(!vc.all_items[senderUserId]) {
                    vc.all_items[senderUserId] = [];
                }
                vc.all_items[senderUserId].push(obj);
                // vc.items = vc.all_items[senderUserId];
                
                // console.log(message.content.content);
                // message.content.content => 消息内容
                break;
            case RongIMClient.MessageType.VoiceMessage:
                // 对声音进行预加载                
                // message.content.content 格式为 AMR 格式的 base64 码
                break;
            case RongIMClient.MessageType.ImageMessage:
               // message.content.content => 图片缩略图 base64。
               // message.content.imageUri => 原图 URL。
               break;
            case RongIMClient.MessageType.DiscussionNotificationMessage:
               // message.content.extension => 讨论组中的人员。
               break;
            case RongIMClient.MessageType.LocationMessage:
               // message.content.latiude => 纬度。
               // message.content.longitude => 经度。
               // message.content.content => 位置图片 base64。
               break;
            case RongIMClient.MessageType.RichContentMessage:
               // message.content.content => 文本消息内容。
               // message.content.imageUri => 图片 base64。
               // message.content.url => 原图 URL。
               break;
            case RongIMClient.MessageType.InformationNotificationMessage:
                // do something...
               break;
            case RongIMClient.MessageType.ContactNotificationMessage:
                // do something...
               break;
            case RongIMClient.MessageType.ProfileNotificationMessage:
                // do something...
               break;
            case RongIMClient.MessageType.CommandNotificationMessage:
                // do something...
               break;
            case RongIMClient.MessageType.CommandMessage:
                // do something...
               break;
            case RongIMClient.MessageType.UnknownMessage:
                // do something...
               break;
            default:
                // do something...
        }
    }
});


var token = '';
var data = {};
data['user_token'] = get_token();
$.ajax({
    url: base_url+'Rongyun/get_token',
    type: 'post',
    data: data,
    dataType: 'json',
    async:true,
    timeout: 3000,
    success: function (data, status) {
         
        if(data.code==codeArr['e200'])
        {
            token = data.data.token;
            RongIMClient.connect(token, {
		    onSuccess: function(userId) {
		      console.log("Connect successfully." + userId);
		    },
		    onTokenIncorrect: function() {
		      console.log('token无效');
		    },
		    onError:function(errorCode){
		          var info = '';
		          switch (errorCode) {
		            case RongIMLib.ErrorCode.TIMEOUT:
		              info = '超时';
		              break;
		            case RongIMLib.ErrorCode.UNKNOWN_ERROR:
		              info = '未知错误';
		              break;
		            case RongIMLib.ErrorCode.UNACCEPTABLE_PaROTOCOL_VERSION:
		              info = '不可接受的协议版本';
		              break;
		            case RongIMLib.ErrorCode.IDENTIFIER_REJECTED:
		              info = 'appkey不正确';
		              break;
		            case RongIMLib.ErrorCode.SERVER_UNAVAILABLE:
		              info = '服务器不可用';
		              break;
		          }
		          console.log(errorCode);
		        }
		  });
        }else
        {
            weui.alert('请求异常',function(){
            	history.go(-1);
            });
        }
        RongIMClient.getInstance().hasRemoteUnreadMessages(token,{
		    onSuccess:function(hasMessage){
		        if(hasMessage){
		            console.log(hasMessage);
		        }else{
		            // 没有未读的消息
		        }
		    },onError:function(err){
		        // 错误处理...
		    }
		});
        
    },
    fail: function (err, status) {
        weui.alert('请求异常',function(){
        	history.go(-1);
        });
    }
})

var callback = {
    onSuccess: function(userId) {
        console.log("Reconnect successfully." + userId);
    },
    onTokenIncorrect: function() {
        console.log('token无效');
    },
    onError:function(errorCode){
        console.log(errorcode);
    }
};
var config = {
    // 默认 false, true 启用自动重连，启用则为必选参数
    auto: true,
    // 重试频率 [100, 1000, 3000, 6000, 10000, 18000] 单位为毫秒，可选
    url: 'cdn.ronghub.com/RongIMLib-2.2.6.min.js',
    // 网络嗅探地址 [http(s)://]cdn.ronghub.com/RongIMLib-2.2.6.min.js 可选
    rate: [100, 1000, 3000, 6000, 10000]
};
RongIMClient.reconnect(callback, config);


/**
* 发送消息
* @param  {integer} uid  用户id
* @param  {string}  word 发送的消息
*/
function send_msg(uid,word){
	// 定义消息类型,文字消息使用 RongIMLib.TextMessage
	var msg = RongIMLib.TextMessage.obtain(word.toString());
    if(uid < 0) {
        var conversationtype = RongIMLib.ConversationType.GROUP;  // 群聊
    }else {
        var conversationtype = RongIMLib.ConversationType.PRIVATE;  // 私聊
    }
	var targetId = Math.abs(uid).toString(); // 目标 Id
	RongIMClient.getInstance().sendMessage(conversationtype, targetId, msg, {
		// 发送消息成功
		onSuccess: function (message) {
		    //message 为发送的消息对象并且包含服务器返回的消息唯一Id和发送消息时间戳
		    console.log("Send successfully");
		},
		onError: function (errorCode,message) {
		    var info = '';
		    switch (errorCode) {
		        case RongIMLib.ErrorCode.TIMEOUT:
		            info = '超时';
		            break;
		        case RongIMLib.ErrorCode.UNKNOWN_ERROR:
		            info = '未知错误';
		            break;
		        case RongIMLib.ErrorCode.REJECTED_BY_BLACKLIST:
		            info = '在黑名单中，无法向对方发送消息';
		            break;
		        case RongIMLib.ErrorCode.NOT_IN_DISCUSSION:
		            info = '不在讨论组中';
		            break;
		        case RongIMLib.ErrorCode.NOT_IN_GROUP:
		            info = '不在群组中';
		            break;
		        case RongIMLib.ErrorCode.NOT_IN_CHATROOM:
		            info = '不在聊天室中';
		            break;
		        default :
		            info = x;
		            break;
		    }
		    console.log('发送失败:' + info);
		}
	});
   
}