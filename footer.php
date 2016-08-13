	        
			<footer id="colophon" role="contentinfo" class="b_body_f row clearfix">
				
                <script type="text/javascript">
                    (function() {
                        var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
                        po.src = 'https://apis.google.com/js/plusone.js';
                        var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
                    })();
                </script>

                <?php
					/*$pattern = explode( '.' , options::get_value( 'styling' , 'background' ) ) ;
					if( isset( $pattern[ count( $pattern ) - 1 ] ) && $pattern[ count( $pattern ) - 1 ] == 'none' ){
						$background_img = '';
					}else{
						$background_img = "url(" . str_replace( 's.pattern.' , 'pattern.' , options::get_value( 'styling' , 'background' ) ) . ")";

					}*/
                	if(get_background_image() == '' && !isset($_COOKIE["megusta_day_night"]) && get_bg_image() != 'pattern.day' && trim(get_bg_image()) != '' && trim(get_bg_image()) != 'pattern.night' && !strpos(get_bg_image(),'.jpg') ){	
						//echo get_bg_image();
		            	$background_img = 'background-image: url('.get_template_directory_uri().'/lib/images/pattern/'.get_bg_image().');';
		            }else{
		            	$background_img = '';
		            }	
                	$footer_background_color = "background-color: " . get_footer_bg_color() . "; ";

                	ob_start();
                	ob_clean();
                	$post_item_page = get_page_by_title( 'Post Item' );
					echo menu( 'footer_menu' , array( 'class' => 'footer-menu' ,'exclude' => array($post_item_page->ID) , 'number-items' => options::get_value( 'menu' , 'footer' )  , 'current-class' => 'active' , 'more-label' => '' ) );
                	$footer = ob_get_clean();

                	$f_classes = strlen( $footer ) > 5 ? true : false;
				?>
				
				<div class="b_f_c columns small-12" style="<?php echo $footer_background_color; ?> <?php echo $background_img; ?>" >
				
					<div class="b_page clearfix footer-area row">
						<div class="b w_300 columns small-12 large-4">
                            <?php get_sidebar( 'footer-first' ); ?>
                        </div>
                        <div class="b w_300 columns small-12 large-4">
                            <?php get_sidebar( 'footer-second' ); ?>
                        </div>
                        <div class="b w_300 columns small-12 large-4">
                            <?php get_sidebar( 'footer-third' ); ?>
                        </div>
					</div>
					<div class="bottom row">
						<div class="b_page clearfix columns small-12">
							<div class="row">
								<div class="b w_460 columns small-12 <?php if ( $f_classes ) { echo 'large-6'; } else { echo 'large-12'; } ?>">
									<p class="copyright"><?php echo str_replace('%year%',date('Y') , options::get_value('general' , 'copy_right') ); ?></p>
								</div>
								<?php if ( $f_classes ): ?>
									<div class="b w_460 columns small-12 large-6">
										<?php echo $footer; ?>
									</div>
								<?php endif; ?>
							</div>
						</div>
					</div>
				</div>
			</footer>
			<?php
					if( options::logic( 'general' , 'enb_keyboard' ) ){
					if (!wp_is_mobile()){
				?>		

						<div id="lightbox-shadow" onclick="javascript:keyboard.hide();"></div>
				<?php } ?>
						<div id="keyboard-container">
							<div id="img">
							<img src="<?php echo get_template_directory_uri()?>/images/keyboard.png"  alt=""/>
							<p class="hint">
								<?php _e( 'Use advanced navigation for a better experience.' , 'cosmotheme' ); ?>
								<br />
								<?php _e( 'You can quickly scroll through posts by pressing the above keyboard keys. Now press <strong>Esc</strong> to close this window.' , 'cosmotheme' ); ?>
							</p>
							</div>
						</div>
				<?php
					}
				?>
				<?php
                    if( options::logic( 'general' , 'enb_keyboard' ) ){
                ?>
                        <div class="keyboard-demo" style=" cursor:pointer;" onclick="javascript:keyboard.show();">
                            <img src="<?php echo get_template_directory_uri()?>/images/small-keyboard.png" alt="small_keyboard" />
                        </div>
                <?php
                    }
                ?>    
            <script type="text/javascript" src="//platform.twitter.com/widgets.js"></script>
            <div id="toTop"><?php _e('Back to Top','cosmotheme'); ?> <span class="arrow">&uarr;</span></div>
         
			
		</div>
	</div>
	<?php
        wp_footer();
        echo options::get_value('general' , 'tracking_code');
    ?>
</body>
</html>