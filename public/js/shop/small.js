		// 轮播js
		$(".small li").mouseenter(function(){
				$(this).css("opacity","1").siblings().css("opacity","0.4")
				var point=$(this).index();
				now=point;
				$(".pic li").eq(point).stop().fadeIn().siblings().fadeOut();
			});
		
			var now=0; 
			var idz=setInterval(autolunbo,3000);
			function autolunbo(){
				now++;
				if(now==3)
				{
					now=0;
				}
				$(".small li").eq(now).css("opacity","1").siblings().css("opacity","0.4")
				$(".pic li").eq(now).fadeIn().siblings().fadeOut();
			}
			$(".smallbanner").eq(0).mouseenter(function(){
				clearInterval(idz);
			});
			$(".smallbanner").eq(0).mouseleave(function(){
				idz=setInterval(autolunbo,3000);
			});
			
				$(".small li").eq(now).css("opacity","1").siblings().css("opacity","0.4")
				$(".pic li").eq(now).fadeIn().siblings().fadeOut();