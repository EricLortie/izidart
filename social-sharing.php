<?php
    /* social sharing  */
    if( options::logic( 'blog_post' , 'post_sharing' ) ){
        if ( meta::logic( $post , 'settings' , 'sharing' ) ){
    	if ( class_exists( 'wpsociallikes' ) ){
    		social_likes();
    	}else{
?>      <div class="share">
			<iframe src="http://www.facebook.com/plugins/like.php?href=<?php echo urlencode( get_permalink( $post->ID ) ); ?>&amp;layout=button_count&amp;show_faces=false&amp;&amp;action=like&amp;colorscheme=light" scrolling="no" frameborder="0" height="20" width="80"></iframe>
            <a href="https://twitter.com/share" class="twitter-share-button" data-url="<?php echo get_permalink( $post -> ID ); ?>" data-text="<?php echo $post -> post_title; ?>" data-count="horizontal">Tweet</a>
            <g:plusone size="medium" href="<?php echo get_permalink( $post -> ID ); ?>"></g:plusone>
            <a href="http://pinterest.com/pin/create/button/?url=<?php the_permalink(); ?>&media=<?php if(function_exists('the_post_thumbnail')) echo wp_get_attachment_url(get_post_thumbnail_id()); ?>&description=<?php echo get_the_title(); ?>" class="pin-it-button" always-show-count="true" count-layout="horizontal"><img border="0" src="//assets.pinterest.com/images/PinExt.png" title="<?php _e('Pin It','cosmotheme'); ?>" /></a>
            <script type="text/javascript" src="http://assets.pinterest.com/js/pinit.js"></script>
		</div>
<?php
        }
    }
}
?>