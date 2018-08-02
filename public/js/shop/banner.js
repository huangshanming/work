			//点击焦点
			var n = 0;
			$(".jiaodian i").click(function() {
				$(this).addClass("fff").siblings().removeClass("fff");
				var i = $(this).index();
				if(i < now) {
					$(".banner li").eq(now).css({
						"left": "0px",
						"z-index": n
					}).stop().animate({
						"left": "100%"
					});
					$(".banner li").eq(i).css({
						"left": "-100%",
						"z-index": n + 1
					}).stop().animate({
						"left": "0px"
					})
				}
				if(i > now) {
					$(".banner li").eq(now).css({
						"left": "0px",
						"z-index": n
					}).stop().animate({
						"left": "-100%"
					});
					$(".banner li").eq(i).css({
						"left": "100%",
						"z-index": n + 1
					}).stop().animate({
						"left": "0px"
					})
				}
				now = i;
			});

			
			var now = 0;
			var idz = setInterval(autolunbo, 2000);

			function autolunbo() {
				now++; //now的索引值号：012345
				$(".banner li").eq(now - 1).stop().animate({
					"left": "-100%",
					"z-index": n
				});
				$(".jiaodian i").eq(now - 1).addClass("fff").siblings().removeClass("fff");

				if(now == 3) {
					now = 0;
				}
				//此时now-1代表now，  now代表next
				$(".banner li").eq(now).css({
					"left": "100%",
					"z-index": n + 1
				}).stop().animate({
					"left": "0px"
				})
				$(".jiaodian i").eq(now).addClass("fff").siblings().removeClass("fff");
			}


			$(".banner").eq(0).mouseenter(function() {
				clearInterval(idz);
			});
			$(".banner").eq(0).mouseleave(function() {
				idz = setInterval(autolunbo, 4000);
			});
			$(".jiantou i").click(function() {
				autolunbo();
			});

