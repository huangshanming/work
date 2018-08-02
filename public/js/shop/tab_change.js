	$(function(){
		$(".log>li>a").hover(function(){
			$(this).addClass("shouye");
		},function(){
			$(this).removeClass("shouye");
			$("#sy").addClass("shouye");
		})
		$(".small li a").click(function(){
			$(this).addClass("all").siblings().removeClass("all");
			var index =$(this).index();
			var newUl=$(this).parents('.cosplay').siblings('.tt').find('ul');
			newUl.eq(index).addClass('active').siblings().removeClass('active');
		})
		$('.small li a:eq(0)').click(function(){
			// $('.tt .tabchange').addClass('active');
		})
		$('.answer a').click(function() {
			var n=$(this).index();
			var e=$(this).parents('.cosplay').siblings('.clothes').find('.que');
			// console.log(e.eq(n).html())
			e.eq(n).addClass('active').siblings().removeClass('active');
		});
		$(".sure a").click(function(){
			$(this).addClass("bold").siblings( ).removeClass("bold");
		})
		$(".pinglun a").click(function(){
			$(this).css("font-weight",700).siblings().css("font-weight","normal");
			var i =$(this).index();
			$(".tb-revhd .display").eq(i).show().siblings().hide();
			
		})
		$(".xia a").click(function(){
			$(this).addClass("rr").siblings().removeClass("rr");
			var i =$(this).index();
			$(".many .taber").eq(i).show().siblings().hide();
		})
		$(".onbu").click(function(){
			$(".open-page").show();
			$(".ctr-con").hide();
			$(".next-step-item-wait").css("background-color","#eee");
			$(".next-step-item-process").css("background-color","#75B0DF");
			// $(this).css("background-color","#EBF1F5");
			$(".onup").css("background-color","#EBF1F5");
		})
		$(".onup").click(function(){
			$(".open-page").hide();
			$(".ctr-con").show();
			$(".next-step-item-wait").css("background-color","#75B0DF");
			$(".next-step-item-process").css("background-color","#eee");
			// $(this).css("background-color","#EBF1F5");
			$(".onbu").css("background-color","#EBF1F5");
		})
		$(".backto").click(function(){
			$(".open-page").show();
			$(".ctr-con").hide();
		})
	});



