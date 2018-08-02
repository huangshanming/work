$(function() {
        $(".filepath").on("change",function() {
            var srcs = getObjectURL(this.files[0]);   //获取路径
            $(this).nextAll(".img1").hide();   //this指的是input
            $(this).nextAll(".img2").show();  //fireBUg查看第二次换图片不起做用
            $(this).nextAll('.close').show();   //this指的是input
            $(this).nextAll(".img2").attr("src",srcs);    //this指的是input
            $(".close").on("click",function() {
                $(this).hide();     //this指的是span
                $(this).nextAll(".img2").hide();
                $(this).nextAll(".img1").show();
            })
        })
    })




    function getObjectURL(file) {
        var url = null;
        if (window.createObjectURL != undefined) {
            url = window.createObjectURL(file)
        } else if (window.URL != undefined) {
            url = window.URL.createObjectURL(file)
        } else if (window.webkitURL != undefined) {
            url = window.webkitURL.createObjectURL(file)
        }
        return url
    };





    $(function() {
        $("#img").on("change",".filepath1",function() {
            var srcs = getObjectURL(this.files[0]);   //获取路径
            var htmlImg='<div class="imgbox1">'+
                    '<div class="imgnum1">'+
                    '<input type="file" class="filepath1" />'+
                    '<span class="close1">X</span>'+
                    '<img src="images/btn.png" class="img11" />'+
                    '<img src="'+srcs+'" class="img22" />'+
                    '</div>'+
                    '</div>';

            $(this).parent().parent().before(htmlImg);
            $(this).parent().parent().prev().find(".img11").hide();   //this指的是input
            $(this).parent().parent().prev().find('.close1').show();
            $(".close1").on("click",function() {
                $(this).hide();     //this指的是span
                $(this).nextAll(".img22").hide();
                $(this).nextAll(".img11").show();
                if($('.imgbox1').length>1){
                    $(this).parent().parent().remove();
                }

            })
        })
    })
