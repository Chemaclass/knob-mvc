/*
 * Author: Jose Maria Valera Reales
 */

var MINIMUM_HEIGHT_FOR_TO_LOAD = 200;

$(document).ready(function() {

});

/**
 * Window scroll
 */
$(window).scroll(function() {
	doScroll();
});

/**
 * Window resize
 */
$(window).on("resize", function() {
	doScroll();
});

function doScroll() {	
	var scroll = $(window).scrollTop();
	var windowHeight = $( window ).height();
	var documentHeight = $(document).height();

	var heightLessScroll = (documentHeight - windowHeight)-scroll;

	if (heightLessScroll <= MINIMUM_HEIGHT_FOR_TO_LOAD) {
		//$('.show-more').trigger('click');
	}
}

$(document).on('click', '.show-more', function(e) {
	e.preventDefault();
	var $this = $(this);
	var postsWhereKey = $this.attr('posts-where-key');
	var postsWhereValue = $this.attr('posts-where-value');
	var limit = $this.attr('limit');
	var posts = $('#all-posts');
	var offset = posts.find('.post').size();
	var url = $('#page').attr('ajax-url');
	var data = {
		submit : 'show-more',
		postsWhereKey: postsWhereKey,
		postsWhereValue: postsWhereValue,
		limit: limit,
		offset: offset
	};
	$.ajax({
		url : url,
		type : "POST",
		data : data,
		dataType : "json",
		beforeSend: function() {
			$this.find('i .fa-spinner').removeClass('hidden');
			$this.find('i .fa-plus').addClass('hidden');
			$this.attr("disabled", true);
		},
		success : function(json) {
			if(json.code == 200 ) {
				content = json.content;
				posts.append(content);
				if( content.length == 0 || json.limit < limit ) {
					$this.text("No more");
					return;
				}
			}
			$this.attr("disabled", false);
		},
		error: function (xhr, ajaxOptions, thrownError) {
			 console.log("status: "+xhr.status + ",\n responseText: "+xhr.responseText 
			 + ",\n thrownError "+thrownError);
			$this.addClass("hidden");
	     }
	});
});

$(document).on('click','.url-redirect', function(e) {
	e.preventDefault();
	window.location.replace($(this).attr('href')+'&redirect='+window.location.pathname);
});