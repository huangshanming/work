/**
 * Created by Administrator on 2015/8/10.
 */
(function(win, doc, $,angular,undefined){
    $(".send-content").niceScroll({
        cursorcolor:"#3ABBCE",
        cursoropacitymax:1,
        touchbehavior:false,
        cursorwidth:"10px",
        cursorborder:"0",
        cursorborderradius:"5px"
    });
    $(".msg-content").niceScroll({
        cursorcolor:"#3ABBCE",
        cursoropacitymax:1,
        touchbehavior:false,
        cursorwidth:"10px",
        cursorborder:"0",
        cursorborderradius:"5px"
    });
    $(".expression").niceScroll({
        cursorcolor:"#3ABBCE",
        cursoropacitymax:1,
        touchbehavior:false,
        cursorwidth:"10px",
        cursorborder:"0",
        cursorborderradius:"5px"
    });
    $('#btnExpressionId').bind('click',function(){
        var expressonObj = $(".expression"),msgConment=$('.msg-content');
        if(!expressonObj.is(":visible")){
            expressonObj.show();
            msgConment.height(msgConment.height()-expressonObj.height());
        }else{
            expressonObj.hide();
            msgConment.height(msgConment.height()+expressonObj.height());
        }
    });

    /*** var myApp = angular.module('myApp',[]);
     myApp.controller('studentController',function($scope){
        $scope.showTitle=function(){
            return "adfasdfasdfasdf";
        }
    });*/
    $('.messageBody-btn').bind('click',function(){
        $('.detailBox').slideDown('slow');
        $('.messageBox').hide();
        $('.messageBody-btn').hide();
        $('.detailBody-btn').show();
    });
    $('.detailBody-btn').bind('click',function(){
        $('.messageBox').slideDown('slow');
        $('.detailBox').hide();
        $('.detailBody-btn').hide();
        $('.messageBody-btn').show();
    })

    //表情
    self.drawExpressionWrap = function () {
        var RongIMexpressionObj = $(".expression");
        var arrImgList = RongIMClient.Expression.getAllExpression(60, 0);
        if (arrImgList && arrImgList.length > 0) {
            for (var objArr in arrImgList) {
                var imgObj = arrImgList[objArr].img;
                if (!imgObj) {
                    continue;
                };
                imgObj.setAttribute('alt',arrImgList[objArr].chineseName);
                imgObj.setAttribute("title", arrImgList[objArr].chineseName);
//                    imgObj.alt = arrImgList[objArr].chineseName;
                var newSpan = $('<span class="RongIMexpression_' + arrImgList[objArr].englishName + '"></span>');
                newSpan.append(imgObj);
                RongIMexpressionObj.append(newSpan);
            }
        }
        $(".expression>span").bind('click', function (event) {
            //var str = $(this).children().first()[0].outerHTML.replace(/b/,"img");
            //str=str.replace(/&amp;nbsp;/,"&nbsp;") 表情点击事件不好使
            var str ="[" + $(this).children().first().attr("alt") + "]";
            $(".send-content").html($(".send-content").html()+str);
        });
        $(".expression").bind('mouseleave', function (event) {
            $("#btnExpressionId").trigger("click");
        });

    }
    self.drawExpressionWrap();
    $('#loginOut').on('click',function(){
          window.location.href="login.html";
    });
})(window, document, window.jQuery,window.angular)
