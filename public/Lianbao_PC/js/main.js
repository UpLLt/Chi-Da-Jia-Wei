// JavaScript Document
//动画效果
function scroll_start(select_id) {
	var scroll_length=$(window).scrollTop();
	var window_length=$(window).height();
	var middle_box_Top=$(select_id).offset().top;
	var middle_box_start=((parseInt(middle_box_Top/window_length))-1)*window_length+middle_box_Top%window_length+window_length/4;
	if (scroll_length>middle_box_start) {
		switch(select_id)
		{
		case ".middle_box":
		  $(select_id).animate({marginTop:30},1000);
		  $(select_id+" ul li i img").slideDown("slow");
		  break;
		case ".section_365":
		  $(".box_365:first").animate({opacity:1,left:0},1000);
		  $(".box_365:last").animate({opacity:1,left:0},1500);
		  
		  break;
		case ".news":
		  $(".news").animate({opacity:1},1500);
		  $(".news .C ul li").each(function(i){
			  $(this).animate({marginTop:0,opacity:1},1000+300*i)
			  })
		  
		  break;
		case ".case":
		  $(".case .index_line").animate({width:"100%"},2000);
		  $(".case .index_T img").animate({opacity:1,top:0},1000);
		  $(".case .index_T .more").animate({opacity:1,left:0},1000);
		  $(".case .case_banner").animate({opacity:1},2500);
			if (navigator.userAgent.match(/iPhone/i) || navigator.userAgent.match(/Android/i)) {
				if ($(window).scrollTop()>middle_box_start) {
				$(".case ul li").animate({marginTop:"1.5%",opacity:1},1000,function(){
					$(".case ul li p").css("color","#000");
				});
		        }
			} else {
			  if ($(window).scrollTop()>middle_box_start+400) {
				$(".case ul li").each(function(i){
				  $(this).animate({marginTop:"1.5%",opacity:1},1000+300*i);
				  })
		      }
				  
		   }
		  break;
		case ".service":
		  $(".service .index_line").animate({width:"100%"},2000);
		  $(".service .index_T img").animate({opacity:1,top:0},1000);
		  $(".service .index_T .more").animate({opacity:1,left:0},1000);
		  $(".service ul li").each(function(i){
			  $(this).animate({top:0,opacity:1},1000+300*i)
			  })
			  
		  break;
		case ".join_us":
		  $(".join_us .index_line").animate({width:"100%"},2000);
		  $(".join_us .index_T img").animate({opacity:1,top:0},1000);
		  $(".join_us .index_T .more").animate({opacity:1,left:0},1000);
		  $(".join_us .join_banner").animate({opacity:1},2500);
		  $(".join_us .join_right").animate({opacity:1},"fast");
		  $(".join_right ul li").each(function(i){
			  $(this).animate({top:0,opacity:1},1000+300*i)
			  })
			  
		  break;
		case ".partner":
		  $(".partner .index_line").animate({width:"100%"},2000);
		  $(".partner .index_T img").animate({opacity:1,top:0},1000);
		  $(".partner .index_T .more").animate({opacity:1,left:0},1000);
		  $(".partner ul li").each(function(i){
			  $(".partner ul li").slideDown("slow");
			  })
			  
		  break;
		 
		}
		
	}

}
$(function(){
	/*图片处理*/
	$(".article_C img").css("margin","4px 0");
	//二维码
	$(".erweima_btn").each(function(){
		$(this).mouseover(function(){
			$(this).find(".show_box").show();
			}).mouseleave(function(){
				$(this).find(".show_box").hide();
				})
		})
	/*about_us*/
	$(".S_son_nav ul li:last").css("background","none");
	$(".S_son_son_nav ul li a:eq(0)").addClass("li1");
	$(".S_son_son_nav ul li a:eq(1)").addClass("li2");
	$(".S_son_son_nav ul li a:eq(2)").addClass("li3");
	$(".S_son_son_nav ul li a:eq(3)").addClass("li4");
	$(".S_son_son_nav ul li a:eq(4)").addClass("li5");
	$(".S_son_son_nav ul li a:eq(5)").addClass("li6");
	$(".team_list ul li:odd").addClass("odd");
	//内页载入位置
	$("html,body").animate({scrollTop:"800px"},1000);
	//加盟店
	
	$(".jmd_link table td").each(function(){
		var jmd_len=$(this).find("a").length;
		if (jmd_len>=6) {
			$(this).find(".jt_btn").show();
			$(this).find("a:gt(5)").hide();
		}
		})
	
	$(".jmd_link td .open_btn").live("click",function(){
		$(this).parent("td").find("a:gt(5)").show();
		$(this).addClass("close_btn").removeClass("open_btn");
		})
	$(".jmd_link td .close_btn").live("click",function(){
		$(this).parent("td").find("a:gt(5)").hide();
		$(this).addClass("open_btn").removeClass("close_btn");
		})
	/*导航*/
	$(".nav ul li").find(".nav_box").hide();
	$(".nav ul li").each(function(i) {
		
		$(this).animate({top:0},1000);
		if ($(this).find(".nav_box").has("a").length) {
			$(this).find(".li_first").addClass("jiantou");
			}
		$(this).mouseover(function(){
			$(this).find(".nav_box").width($(this).find("a.li_first").outerWidth()+20);
			$(this).find(".nav_box a").width($(this).find("a.li_first").outerWidth()+20);
			$(this).find(".li_first").addClass("ahover");
			$(this).find(".nav_box").stop(false,true).slideDown();
			}).mouseleave(function(){
				$(this).find(".li_first").removeClass("ahover");
				$(this).find(".nav_box").stop(false,true).slideUp();
				})
    });
	//学校
	$(".S_fc_box  .S_fc_C").each(function(){
		$(this).find("ul li:eq(2)").css("border","none");
		})
	$(".S_fc_list .S_fc_C").each(function(){
		$(this).find("ul li:eq(2)").css("border-bottom","1px dotted #a9a9a9");
		})
	//兼容样式
	$(".article_C").find("img").closest("p").css("text-indent",0);
	//动画效果
	
	$(".logo").animate({left:0},1000);
	$(".top_word .tel").animate({marginTop:30},1000);
	$(window).scroll(function(){
		if (!navigator.userAgent.match(/iPhone/i) || !navigator.userAgent.match(/Android/i)) {
		scroll_start(".middle_box");
		scroll_start(".section_365");
		scroll_start(".news");
		scroll_start(".case");
		scroll_start(".service");
		scroll_start(".join_us");
		scroll_start(".partner");
		} 
		})
	
})