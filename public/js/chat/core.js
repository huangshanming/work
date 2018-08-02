(function (win, $,angular ,undefined) {
    var myUtil = {
        msgType: function (message) {
            switch (message.getMessageType()) {
                case RongIMClient.MessageType.TextMessage:
                    return String.stringFormat('<div class="msgBody">{0}</div>', this.initEmotion(this.symbolReplace(message.getContent())));
                case RongIMClient.MessageType.ImageMessage:
                    return String.stringFormat('<div class="msgBody">{0}</div>', "<img class='imgThumbnail' src='data:image/jpg;base64," + message.getContent() + "' bigUrl='" + message.getImageUri() + "'/>");
                case RongIMClient.MessageType.VoiceMessage:
                    return String.stringFormat('<div class="msgBody voice">{0}</div><input type="hidden" value="' + message.getContent() + '">', "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" + message.getDuration());
                case RongIMClient.MessageType.LocationMessage:
                    return String.stringFormat('<div class="msgBody">{0}</div>{1}', "[位置消息]" + message.getPoi(), "<img src='data:image/png;base64," + message.getContent() + "'/>");
                case RongIMClient.MessageType.RichContentMessage:
                    return '<div class="msgBody">'+message.getContent()+'</div>'
                default :
                    return '<div class="msgBody">' + message.getMessageType().toString() + ':此消息类型Demo未解析</div>'
            }
        },
        initEmotion: function (str) {
            var a = document.createElement("span");
            return RongIMClient.Expression.retrievalEmoji(str, function (img) {
                a.appendChild(img.img);
                var str = '<span class="RongIMexpression_' + img.englishName + '">' + a.innerHTML + '</span>';
                a.innerHTML = "";
                return str;
            });
        },
        symbolReplace: function (str) {
            if (!str) return '';
            str = str.replace(/&/g, '&amp;');
            str = str.replace(/</g, '&lt;');
            str = str.replace(/>/g, '&gt;');
            str = str.replace(/"/g, '&quot;');
            str = str.replace(/'/g, '&#039;');
            return str;
        },
        replaceSymbol: function (str) {
            if (!str) return '';
            str = str.replace(/&amp;/g, '&');
            str = str.replace(/&lt;/g, '<');
            str = str.replace(/&gt;/g, '>');
            str = str.replace(/&quot;/g, '"');
            str = str.replace(/&#039;/g, "'");
            str = str.replace(/&nbsp;/g, " ");
            return str;
        }
    },historyStr = '<div class="xiaoxiti {0} user">' +
                        '<div class="user_img"><img onerror="this.src=\'images/personPhoto.png\'" src="{1}"/></div><span>{2}</span>' +
                   '<div class="msg">' +
                        '<div class="msgArrow"><img src="images/{3}"></div><span></span>{4}</div><p>少时诵诗书</p>' +
                   '<div messageId="{5}" class="status"></div>' +
                   '</div>' +
                   '<div class="slice"></div>',base64Str="";

    //Messages variable
    var historeMessage={};
    RongIMClient.init("cpj2xarlj5cdn");
    RongIMClient.setConnectionStatusListener({
        onChanged: function (status) {
            switch (status) {
                //链接成功
                case RongIMClient.ConnectionStatus.CONNECTED:
                    console.log('链接成功');
                    break;
                //正在链接
                case RongIMClient.ConnectionStatus.CONNECTING:
                    console.log('正在链接');
                    break;
                //重新链接
                case RongIMClient.ConnectionStatus.RECONNECT:
                    console.log('重新链接');
                    break;
                //其他设备登陆
                case RongIMClient.ConnectionStatus.OTHER_DEVICE_LOGIN:
                //连接关闭
                case RongIMClient.ConnectionStatus.CLOSURE:
                //未知错误
                case RongIMClient.ConnectionStatus.UNKNOWN_ERROR:
                //登出
                case RongIMClient.ConnectionStatus.LOGOUT:
                //用户已被封禁
                case RongIMClient.ConnectionStatus.BLOCK:
                    break;
            }
        }
    });
    // 消息监听器
    RongIMClient.getInstance().setOnReceiveMessageListener({
        // 接收到的消息
        onReceived: function (message) {
            if(owner.id){
                addHistoryMessages(message);
            }else{
                var count = RongIMClient.getInstance().getUnreadCount(message.getConversationType(),message.getTargetId())
                $('#friendlistId').find('li').each(function(index,item){
                    if(item.innerHTML.indexOf(message.getTargetId())>-1){
                       $(item).find('span')[3].innerHTML= count>4?'5+':count;
                       $($(item).find('span')[3]).show();
                    }
                });
            }
            switch (message.getMessageType()) {
                case RongIMClient.MessageType.TextMessage:
                    console.log('TextMessage');
                    break;
                case RongIMClient.MessageType.ImageMessage:
                    console.log('ImageMessage');
                    break;
                case RongIMClient.MessageType.VoiceMessage:
                    console.log('VoiceMessage');
                    break;
                case RongIMClient.MessageType.RichContentMessage:
                    console.log('RichContentMessage');
                    break;
                case RongIMClient.MessageType.LocationMessage:
                    console.log('LocationMessage');
                    break;
                case RongIMClient.MessageType.DiscussionNotificationMessage:
                    console.log('DiscussionNotificationMessage');
                    break;
                case RongIMClient.MessageType.InformationNotificationMessage:
                    console.log('InfomationNotificationMessage');
                    break;
                case RongIMClient.MessageType.ContactNotificationMessage:
                    console.log('ContactNotificationMessage');
                    break;
                case RongIMClient.MessageType.ProfileNotificationMessage:
                    console.log('ProfileNotificationMessage');
                    break;
                case RongIMClient.MessageType.CommandNotificationMessage:
                    console.log('CommandNotificationMessage');
                    break;
                case RongIMClient.MessageType.UnknownMessage:
                    console.log('UnknownMessage');
                    break;
                default:
                // 自定义消息
                // do something...
            }
        }
    });
    RongIMClient.connect(owner.token, {
        onSuccess: function (userId) {
            // 此处处理连接成功。
            console.log("Login successfully." + userId);

        },
        onError: function (errorCode) {
            // 此处处理连接错误。
            var info = '';
            switch (errorCode) {
                case RongIMClient.callback.ErrorCode.TIMEOUT:
                    info = '超时';
                    break;
                case RongIMClient.callback.ErrorCode.UNKNOWN_ERROR:
                    info = '未知错误';
                    break;
                case RongIMClient.ConnectErrorStatus.UNACCEPTABLE_PROTOCOL_VERSION:
                    info = '不可接受的协议版本';
                    break;
                case RongIMClient.ConnectErrorStatus.IDENTIFIER_REJECTED:
                    info = 'appkey不正确';
                    break;
                case RongIMClient.ConnectErrorStatus.SERVER_UNAVAILABLE:
                    info = '服务器不可用';
                    break;
                case RongIMClient.ConnectErrorStatus.TOKEN_INCORRECT:
                    info = 'token无效';
                    break;
                case RongIMClient.ConnectErrorStatus.NOT_AUTHORIZED:
                    info = '未认证';
                    break;
                case RongIMClient.ConnectErrorStatus.REDIRECT:
                    info = '重新获取导航';
                    break;
                case RongIMClient.ConnectErrorStatus.PACKAGE_ERROR:
                    info = '包名错误';
                    break;
                case RongIMClient.ConnectErrorStatus.APP_BLOCK_OR_DELETE:
                    info = '应用已被封禁或已被删除';
                    break;
                case RongIMClient.ConnectErrorStatus.BLOCK:
                    info = '用户被封禁';
                    break;
                case RongIMClient.ConnectErrorStatus.TOKEN_EXPIRE:
                    info = 'token已过期';
                    break;
                case RongIMClient.ConnectErrorStatus.DEVICE_ERROR:
                    info = '设备号错误';
                    break;
            }
            console.log("失败:" + info);
        }
    });
    $("#send").bind('click', function () {
        if ($('.send-content').html() == "") {
            alert("发送内容不能为空!!")
            return;
        }
        if(!owner.id){
            alert("请选择聊天对象!")
            return;
        }
        var strs = $(".send-content").html();
        var con = strs.replace(/\[.+?\]/g, function (x) {
            return RongIMClient.Expression.getEmojiObjByEnglishNameOrChineseName(x.slice(1, x.length - 1)).tag || x;
        }),msg=null;
        if($('#pictureuploadId').val()!=""){
            msg = RongIMClient.RichContentMessage.obtain('test',con,base64Str);
        }else{
            msg = RongIMClient.TextMessage.obtain(con);
        }
        var conversationtype = RongIMClient.ConversationType.PRIVATE;
        var content = $('#pictureuploadId').val()!=""?new RongIMClient.MessageContent(RongIMClient.RichContentMessage.obtain("test",myUtil.replaceSymbol(con),base64Str)):
                      new RongIMClient.MessageContent(RongIMClient.TextMessage.obtain(myUtil.replaceSymbol(con)));
console.log(msg.getContent())
        RongIMClient.getInstance().sendMessage(conversationtype, owner.id, msg, null, {
                // 发送消息成功
                onSuccess: function () {
                    console.log("Send successfully");
                },
                onError: function (errorCode) {
                    var info = '';
                    switch (errorCode) {
                        case RongIMClient.callback.ErrorCode.TIMEOUT:
                            info = '超时';
                            break;
                        case RongIMClient.callback.ErrorCode.UNKNOWN_ERROR:
                            info = '未知错误';
                            break;
                        case RongIMClient.SendErrorStatus.REJECTED_BY_BLACKLIST:
                            info = '在黑名单中，无法向对方发送消息';
                            break;
                        case RongIMClient.SendErrorStatus.NOT_IN_DISCUSSION:
                            info = '不在讨论组中';
                            break;
                        case RongIMClient.SendErrorStatus.NOT_IN_GROUP:
                            info = '不在群组中';
                            break;
                        case RongIMClient.SendErrorStatus.NOT_IN_CHATROOM:
                            info = '不在聊天室中';
                            break;
                        default :
                            info = x;
                            break;
                    }
                    console.alert('发送失败:' + info);
                }
            }
        );
        addHistoryMessages(content.getMessage());
        $('.send-content').text("");
        $('#pictureuploadId').val("");
        if ($('.expression').is(":visible")) {
            $("#btnExpressionId").trigger("click");
        }
    });
    function addHistoryMessages(item) {
        $(".msg-content").append(String.stringFormat(historyStr,
            item.getMessageDirection() == RongIMClient.MessageDirection.RECEIVE ? "other_user" : "self",
            item.getMessageDirection() == RongIMClient.MessageDirection.SEND ? owner.portrait : "images/head.png",
            "",
            item.getMessageDirection() == RongIMClient.MessageDirection.RECEIVE ? 'white_arrow.png' : 'blue_arrow.png',
            myUtil.msgType(item),
            item.getMessageId()));
    }
    //导航
    $('.left-box').delegate('li','click',function(){
        owner.id = $(this).find('span')[2].innerHTML;
        owner.name =  $(this).find('span')[1].innerHTML;
        $('.messageBody-btn').html(owner.name);
        $('.msg-content').html("")
        getHistory(owner.id,owner.name,this.getAttribute("targettype"),1,$(this));
        $(this).find('span')[3].innerHTML=0;
        $($(this).find('span')[3]).hide();
    });
    $('#pictureuploadId').bind('change',function(){
        var file = this.files[0];
        if(!/image\/\w+/.test(file.type)){
            alert("请确保文件为图像类型");
            return false;
        }
        var reader = new FileReader();
        reader.readAsDataURL(file);
        reader.onload = function(e){
            var imgStr = '<img src="'+this.result+'"  style="height:50px;width:50px">';
            $('.send-content').html( $('.send-content').html()+imgStr);
            base64Str=this.result;
        }

    });
    //加载历史记录
    function getHistory(id, name, type, again,obj) {
        RongIMClient.getInstance().getHistoryMessages(RongIMClient.ConversationType.setValue(type),id,10, {
            onSuccess: function (has, list) {
                console.log("是否有剩余消息：" + has);
                list.forEach(function(item){
                    addHistoryMessages(item);
                });
            }, onError: function () {
                console.log('获取历史消息失败');
            }
        });
    }
    function getBase64Image(img) {
        var canvas = document.createElement("canvas");
        canvas.width = img.width;
        canvas.height = img.height;
        var ctx = canvas.getContext("2d");
        ctx.drawImage(img, 0, 0, img.width, img.height);
        var ext = img.src.substring(img.src.lastIndexOf(".")+1).toLowerCase();
        var dataURL = canvas.toDataURL("image/"+ext);
        return dataURL;
    }
})(window, window.jQuery, window.angular)

String.stringFormat = function (str) {
    for (var i = 1; i < arguments.length; i++) {
        str = str.replace(new RegExp("\\{" + (i - 1) + "\\}", "g"), arguments[i] != undefined ? arguments[i] : "");
    }
    return str;
};