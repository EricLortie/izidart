<?php
    class options{
		static $menu;
		static $register;
		static $default;
		static $fields;

        static function menu( ){

            if( is_array( self::$menu ) && !empty( self::$menu) ){
                

                foreach( self::$menu as $main => $items ){
                    
                    foreach( $items as $slug => $item ){
                        
                        switch( $main ){ 
                            default :{
								if( isset( $item['type'] ) ){
									if( $item['type'] == 'main' ){
										add_menu_page( $item['main_label'] , $item['main_label'] , 'administrator' , $main . '__' . $slug  , array( get_class() , $main . '__' . $slug ) , get_template_directory_uri() . '/lib/images/megusta.png' );
                                        
                                        //call_user_func_array( get_class() . '::' . $main . '__' . $slug, array_slice( array( 'name' , 'arguments' ) , 0, (int) 2 ) );
										$main_slug =  $main . '__' . $slug;
									}else{
                                        add_submenu_page( $main_slug , $item['label'] , $item['label'] , 'administrator' , $main . '__' . $slug , array( get_class() , $main . '__' . $slug )  );
									}
								}else{ 
                                    add_submenu_page( $main_slug , $item['label'] , $item['label'] , 'administrator' , $main . '__' . $slug , array( get_class() , $main . '__' . $slug )  );
								}
                                break;
                            }
                        }
                    }
                }
            }
        }

        static function cosmothemes__general(){
            self::CallMenu( 'cosmothemes__general' );
        }
        static function cosmothemes__front_page(){
            self::CallMenu( 'cosmothemes__front_page' );
        }
        static function cosmothemes__layout(){
            self::CallMenu( 'cosmothemes__layout' );
        }
        static function cosmothemes__menu(){
            self::CallMenu( 'cosmothemes__menu' );
        }
        static function cosmothemes__styling(){
            self::CallMenu( 'cosmothemes__styling' );
        }
        static function cosmothemes__conference(){
            self::CallMenu( 'cosmothemes__conference' );
        }
        static function cosmothemes__blog_post(){
            self::CallMenu( 'cosmothemes__blog_post' );
        }

        static function cosmothemes__advertisement(){
            self::CallMenu( 'cosmothemes__advertisement' );
        }
		static function cosmothemes__upload(){
            self::CallMenu( 'cosmothemes__upload' );
        }
        static function cosmothemes__social(){
            self::CallMenu( 'cosmothemes__social' );
        }

        static function cosmothemes__slider(){
            self::CallMenu( 'cosmothemes__slider' );
        }

        static function cosmothemes___sidebar(){
            self::CallMenu( 'cosmothemes___sidebar' );
        }

        static function cosmothemes__stylos(){
            self::CallMenu( 'cosmothemes__stylos' );
        }
		
        static function CallMenu( $name ) {

            $slug           = $name;
            $items 			= explode( '__' , $slug );

            if( !isset( $items[1] ) ){
                exit();
            }

            $label          = isset( self::$menu[ $items[0] ][$items[1]]['label'] ) ? self::$menu[ $items[0] ][$items[1]]['label'] : '';
            $title          = isset( self::$menu[ $items[0] ][$items[1]]['title'] ) ? self::$menu[ $items[0] ][$items[1]]['title'] : '';
            $description    = isset( self::$menu[ $items[0] ][$items[1]]['desctiption'] ) ? self::$menu[ $items[0] ][$items[1]]['desctiption'] : '';
            $update         = isset( self::$menu[ $items[0] ][$items[1]]['update'] ) ? self::$menu[ $items[0] ][$items[1]]['update'] : true ;

            includes::load_css(  );
            includes::load_js(  );
            echo '<div class="admin-page">';
            self::get_header( $items[0] , $items[1] );
            self::get_page( $title , $slug, $description, $update );
            echo '</div>';
        }

        static function register( ){
            if( is_array( self::$register ) && !empty( self::$register ) ){
                foreach( self::$register as $page => $groups ){
                    if( is_array( $groups ) && !empty( $groups ) ){
                        foreach( $groups as $group => $side ){
                            if( substr( $group , 0 , 1 ) != '_'){
                                register_setting( $page . '__' . $group , $group );
                            }
                        }
                    }
                }

            }
        }


        static function box(){
            if( is_array( self::$menu ) && !empty( self::$menu ) ){
                foreach( self::$menu  as $key => $value ){
                    switch( count( $value )  ){
                        case 7 : {
                            $value[0]( $value[1] , $value[2] , $value[3] , $value[4] , $value[5] , $value[6] );
                            break;
                        }
                    }
                }
            }
        }

		static function get_header( $item , $current ){
			$result = '';
            $menu = self::$menu[ $item ];

			if(BRAND == ''){
				$brand_logo = get_template_directory_uri().'/images/freetotryme.png';
			}else{
				$brand_logo = get_template_directory_uri().'/images/cosmothemes.png';
			}
			
            $ct = wp_get_theme();
			
			$result .= '<div class="mythemes-intro">';
            $result .= '<img src="'.$brand_logo.'" />';
			$result .= '<span class="theme">'.$ct->title.' '.__('Version' , 'cosmotheme').': '.$ct->version.'</span>';
            $result .= '</div>';
			
			if( is_array( $menu ) ){
				$result .= '<div class="admin-menu">';
				$result .= '<ul>';
				foreach( $menu as $slug => $info){
                    $result .= '<li '. self::get_class( $slug , $current ) .'><a href="' . self::get_path( $item . '__' . $slug ) . '">' . get_item_label( $info['label'] ) . '</a></li>';
				}
				$result .= '</ul>';
				$result .= '</div>';
			}

            echo $result;
		}
		
		static function get_path( $slug ){
            $path = '?page=' . $slug;
            return $path;
        }
		
		static function get_class( $slug , $current ){
            
            if( $current == $slug ){
                if( substr( $slug , 0 , 1 ) == '_' ){
                    $slug = substr( $slug , 1 , strlen( $slug ) );
                }
            
                $slug = str_replace( '_' , '-' , $slug  );
                
                return 'class="current ' . $slug . '"';
            }else{
                if( substr( $slug , 0 , 1 ) == '_' ){
                    $slug = substr( $slug , 1 , strlen( $slug ) );
                }
            
                $slug = str_replace( '_' , '-' , $slug  );
                
                return ' class="' . $slug . '"';
            }

        }

        static function get_page( $title , $slug ,  $description = '' , $update = true ){
?>
            <div class="admin-content">
                <div class="title">
                    <h2><?php echo $title; ?></h2>
                    <?php
                        if( strlen( $description ) ){
                    ?>
                            <p><?php echo $description; ?></p>
                    <?php
                        }
                    ?>
                </div>
            <?php
                if( $update ){
            ?>
                    <form action="options.php" method="post">
            <?php
                        
                }
                        settings_fields( $slug );
						$items = explode( '__' , $slug );

                        _e(self::get_fields( $items[1] ),'cosmotheme');
                if( $update ){
            ?>
                        <div class="standard-generic-field submit">
                            <div class="field">
                                <input type="submit" value="Update Settings" />
                            </div>
                            <div class="clear"></div>
                        </div>
                    </form>
            <?php
                }else{
            ?>
                    <div class="record submit"></div>
            <?php
                }
            ?>
			</div>
<?php
        }

        static function get_fields( $group ){
            $result = '';
            if( isset( self::$fields[ $group ] ) ){
                foreach( self::$fields[ $group ] as $side => $field ){
                    if (is_array($field)) {
                        $field['topic'] = $side;
                        $field['group'] = $group;
                        if( !isset( $field['value'] ) ){
                            $field['value'] = self::get_value( $group , $side );
                        }

                        $field['ivalue'] = self::get_value( $group , $side );
                    }
                    /* special for upload-id*/
                    if( isset( $field['type'] ) ){
                        $type = explode( '--' , $field['type'] );
                        if( isset( $type[1] ) && $type[1] == 'upload-id' ){
							$option = self::get_value( $group );
                            $value_id = isset( $option[ $side .'_id' ] ) ? $option[ $side .'_id' ] : 0;
                            $field['value_id'] = $value_id;
                        }
                    }

                    $result .= fields::layout( $field );
                }
            }
			
            return $result;
        }

        

        static function get_digit_array( $to , $from = 0 , $twodigit = false ){
            $result = array();
            for( $i = $from; $i < $to + 1; $i ++ ){
                if( $twodigit ){
                    $i = (string)$i;
                    if( strlen( $i ) == 1 ){
                        $i = '0' . $i;
                    }
                    $result[$i] = $i;
                }else{
                    $result[$i] = $i;
                }
            }

            return $result;
        }

        static function get_value( $group , $side = null , $id = null){
			
            $values = @get_option( $group );
            if( is_array( $values ) ){
                if( strlen( $side ) ){
                    if( isset( $values[ $side ] ) ){
                        if( is_int( $id ) ){
                            if( isset( $values[ $side ][ $id ] ) ){
                                return $values[ $side ][ $id ];
                            }else{
                                if( isset( self::$default[ $group ][ $side ][ $id ] )){
                                    return self::$default[ $group ][ $side ][ $id ];
                                }else{
                                    return '';
                                }
                            }
                        }else{
                            return $values[ $side ];
                        }
                    }else{
                        if( isset( self::$default[ $group ][ $side ])){
                            return self::$default[ $group ][ $side ];
                        }else{
                            return '';
                        }
                    }
                }else{
                    return $values;
                }
            }else{
                if( strlen( $side ) ){
                    if( isset( self::$default[ $group ][ $side ] ) ){
                        if( is_int( $id ) ){
                            if( isset( self::$default[ $group ][ $side ][ $id ] ) ){
                                return self::$default[ $group ][ $side ][ $id ];
                            }else{
                                return '';
                            }
                        }else{
                            return self::$default[ $group ][ $side ];
                        }
                    }else{
                        return '';
                    }
                }else{
                    if( isset( self::$default[ $group ])){
                        return self::$default[ $group ];
                    }else{
                        return '';
                    }
                }
            }
        }

        static function logic( $group , $side = null , $id = null ){
            $values = self::get_value( $group , $side , $id );
            
            if( !is_array( $values ) ){
                if( $values == 'yes' ){
                    return  true;
                }

                if( $values == 'no' ){
                    return false;
                }
            }

            return $values;
        }
        
    	static function my_categories( $nr = -1  , $exclude = array() ){
            $categories = get_categories();

            $result = array();
            foreach($categories as $key => $category){
                if( $key == $nr ){
                    break;
                }
                if( $nr > 0 ){
                    if( !in_array( $category -> term_id , $exclude ) ){
                        $result[ $category -> term_id ] = $category -> term_id;
                    }
                }else{
                    if( !in_array( $category -> term_id , $exclude ) ){
                        $result[ $category -> term_id ] = $category -> cat_name;
                    }
                }
            }

            return $result;
        }
    }
?>
