function fixedlike(){
	jQuery( "article" ).each(function() {
	 	var article_height = jQuery(this).find('.entry-header').height() + jQuery(this).find('.entry-content').height() + jQuery(this).find('.entry-footer').height() - 30;
		if ( ( jQuery(window).scrollTop() - jQuery(this).offset().top + 45 > 0 ) && ( jQuery(window).scrollTop() - jQuery(this).offset().top < article_height ) ) {
			jQuery(this).find('#voteaction').addClass('fixed');
		} else {
			jQuery(this).find('#voteaction').removeClass('fixed');
		};		
	});	
};
if ( jQuery( window ).width() > 767 ) {
	jQuery(window).scroll(function() {
		fixedlike();
	})
};

	