/**
 * Created by YuHongDa on 2015/7/29.
 */
(function ($, angluar, undefined) {
    var sdkApp = angular.module('sdkApp', []);
    sdkApp.controller('sdkController', function ($scope, $http,$filter) {
        //$http.get("http://localhost:63342/workspace/Bootstrap/data/data.json").success(function (response) {
        //    $scope.friendlist = response;
        //});


        $scope.friendlist=[{"code":200,"username":"张三","targetId":"zhangsan","token":"dXOJIInqKDahrpig+TJcq3U1lgYP6zEv1OpCrfDse9JiBi4BNyqa2MRus3mUdaZlHmSaXaVmp5/yPASY0/fWWKnbNZUuYfcE"},
            {"code":200,"username":"李四","targetId":"lisi","token":"0Qs6YHRj2p45jxfKS40Io3U1lgYP6zEv1OpCrfDse9JiBi4BNyqa2E2dH7xIEfEE9lfCByjdxCqYNAuDFMk66A=="},
            {"code":200,"username":"王五","targetId":"wangwu","token":"YDzes+B4OSriNTYeLDC7BNW7KjoFgbvTq2niPU2WAjOFaE5xQ4uoUaoBdBWJ5BKUEvTxDLlTxn16ULwUYj+kwQ=="},
            {"code":200,"username":"赵六","targetId":"zhaoliu","token":"VSbhlnY4D1iHI1J55slcudW7KjoFgbvTq2niPU2WAjOFaE5xQ4uoUQyqzS9F3hrhDe+2JBV8YMyEe0rO3YzbFCUCwvJsNpzO"}]
    });
    sdkApp.directive('myButton',function(){
        return {
            restrict:'E',
            //transclude:true,
            scope:{
                name: "@",                  // name 值传递 （字符串，单向绑定）
                amount: "=amount",        // amount 引用传递（双向绑定）
                search: "&"  //操作
            },
            template: "<div class='search-box'>" +
            "<img src='images/search.png'>" +
            "<input ng-model='amount' class='search-plguin'/>" +
            "<img src='images/edit.png'>" +
            "</div>",
            controller:["$scope",function($scope){

            }],
            link:function(scope){
            }
        };
    });

    sdkApp.directive('daLi', function () {
        return function (scope, element, attrs) {
            element.bind('mouseenter', function () {
                $(element.parent()).css("background","#6389A8")
            });
            element.bind('mouseleave', function () {
                $(element.parent()).css("background","none")
            });
        }
    });
})(window.jQuery, window.angular);
