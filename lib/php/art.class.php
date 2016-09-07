<?php
    class art{
        static function anti_loop(){
            $id = md5( md5( $_SERVER['HTTP_USER_AGENT'] ) );

            $time = time();

            $user = get_option('set_user_art');

            if( is_array( $user ) && array_key_exists( $id , $user ) ){
                if( (int) $user[ $id ] + 1  < (int) $time  ){
                    $user[ $id ]  = (int) $time;
                    update_option( 'set_user_art' , $user );
                    return false;
                }else{
                    $user[ $id ]  = (int) $time;
                    update_option( 'set_user_art' , $user );
                    return true;
                }
            }else{
                $user[ $id ]  = (int) $time;
                update_option( 'set_user_art' , $user );
                return true;
            }
        }
        static function set( $post_id = 0 ){

            if( $post_id == 0 ){
                $post_id = isset( $_POST['post_id' ]) ? (int) $_POST['post_id'] : exit;
                $ajax = true;
            }else{
                $ajax = false;
            }


            $arts = meta::get_meta( $post_id , 'art' );

            if( self::anti_loop() ){
                echo (int)count( $arts );
                exit;
            }

            $user       = true;
            $user_ip    = true;

            $ip     = $_SERVER['REMOTE_ADDR'];

            if( is_user_logged_in () ){
                $user_id = get_current_user_id();
            }else{
                $user_id = 0;
            }

            if( $user_id > 0 ){
                /* art by user */
                foreach( $arts as  $art ){
                    if( isset( $art['user_id'] ) && $art['user_id'] == $user_id ){
                       $user   = false;
                       $user_ip = false;
                    }
                }
            }else{
                if( options::logic( 'general' , 'art_register' ) ){
                    if( $ajax ){
                        exit;
                    }else{
                        return '';
                    }
                }
                foreach( $arts as  $art ){
                    if( isset( $art['ip'] ) && ( $art['ip'] == $ip ) ){
                        $user = false;
                        $user_ip = false;
                    }
                }
            }

            if( $user && $user_ip ){
                /* add art */
                $arts[] = array( 'user_id' => $user_id , 'ip' => $ip );
                meta::set_meta( $post_id , 'nr_art' , count( $arts ) );
                meta::set_meta( $post_id , 'art' ,  $arts );
                $date = meta::get_meta( $post_id , 'hot_date' );
                if( empty( $date ) ){
                    if( ( count( $arts ) >= (int)options::get_value( 'general' , 'min_arts' ) ) ){
                        meta::set_meta( $post_id , 'hot_date' , mktime() );
                    }
                }else{
                    if( ( count( $arts ) < (int)options::get_value( 'general' , 'min_arts' ) ) ){
                        delete_post_meta( $post_id, 'hot_date' );
                    }
                }
            }else{
                /* delete art */
                if( $user_id > 0 ){
                    foreach( $arts as $index => $art ){
                        if( isset( $art['user_id'] ) && $art['user_id'] == $user_id ){
                            unset( $arts[ $index ] );
                        }
                    }
                }else{
                    if( options::logic( 'general' , 'art_register' ) ){
                        if( $ajax ){
                            exit;
                        }else{
                            return '';
                        }
                    }
                    foreach( $arts as $index => $art ){
                        if( isset( $art['ip'] ) && isset( $art['user_id'] ) && ( $art['ip'] == $ip ) && ( $art['user_id'] == 0 ) ){
                            unset( $arts[ $index ] );
                        }
                    }
                }

                meta::set_meta( $post_id , 'art' ,  $arts );
                meta::set_meta( $post_id , 'nr_art' ,  count( $arts ) );
                if( count( $arts ) < (int)options::get_value( 'general' , 'min_arts' ) ){
                    delete_post_meta($post_id, 'hot_date' );
                }
            }

            if( $ajax ){
                echo (int)count( $arts );
                exit;
            }
        }

        static function is_voted( $post_id ){
            $ip     = $_SERVER['REMOTE_ADDR'];

            $arts = meta::get_meta( $post_id , 'art' );

            if( is_user_logged_in () ){
                $user_id = get_current_user_id();
            }else{
                $user_id = 0;
            }

            if( $user_id > 0 ){
                foreach( $arts as $art ){
                    if( isset( $art['user_id'] ) && $art['user_id'] == $user_id ){
                        return true;
                    }
                }
            }else{
                foreach( $arts as $art ){
                    if( isset( $art['ip'] ) && $art['ip'] == $ip ){
                        return true;
                    }
                }
            }

            return false;
        }

        static function can_vote( $post_id ){
            $ip     = $_SERVER['REMOTE_ADDR'];

            $arts = meta::get_meta( $post_id , 'art' );

            if( is_user_logged_in () ){
                $user_id = get_current_user_id();
            }else{
                $user_id = 0;
            }

            if( options::logic( 'general' , 'art_register' ) && $user_id == 0 ){
                return false;
            }

            if( $user_id == 0 ){
                foreach( $arts as $art ){
                    if( isset( $art['user_id'] ) && $art['user_id'] > 0  && $art['ip'] == $ip ){
                        return false;
                    }
                }
            }

            return true;
        }

		static function reset_arts(){
            global $wp_query;
            $paged      = isset( $_POST['page']) ? $_POST['page'] : exit;
            $wp_query = new WP_Query( array('posts_per_page' => 150 , 'post_type' => 'post' , 'paged' => $paged ) );

            foreach( $wp_query -> posts as $post ){
                delete_post_meta($post -> ID, 'nr_art' );
				delete_post_meta($post -> ID, 'art' );
				delete_post_meta($post -> ID, 'hot_date' );
            }

            if( $wp_query -> max_num_pages >= $paged ){
                if( $wp_query -> max_num_pages == $paged ){
                    echo 0;
                }else{
                    echo $paged + 1;
                }
            }

            exit();
        }

		static function sim_arts(){
            global $wp_query;
            $paged      = isset( $_POST['page']) ? $_POST['page'] : exit;
            $wp_query = new WP_Query( array('posts_per_page' => 150 , 'post_type' => 'post' , 'paged' => $paged ) );


            foreach( $wp_query -> posts as $post ){
                $arts = array();
                $ips = array();
                $nr = rand( 60 , 200 );
                while( count( $arts ) < $nr ){
                    $ip = rand( -255 , -100 ) .  rand( -255 , -100 )  . rand( -255 , -100 ) . rand( -255 , -100 );

                    $ips[ $ip ] = $ip;

                    if( count( $ips )  > count( $arts ) ){
                        $arts[] = array( 'user_id' => 0 , 'ip' => $ip );
                    }
                }

                meta::set_meta( $post -> ID , 'nr_art' , count( $arts ) );
                meta::set_meta( $post -> ID , 'art' ,  $arts );
                meta::set_meta( $post -> ID , 'hot_date' , mktime() );
            }

            if( $wp_query -> max_num_pages >= $paged ){
                if( $wp_query -> max_num_pages == $paged ){
                    echo 0;
                }else{
                    echo $paged + 1;
                }
            }

            exit();
        }

        static function min_arts(){
            global $wp_query;
            $new_limit  = isset( $_POST['new_limit']) ? $_POST['new_limit'] : exit;
            $paged      = isset( $_POST['page']) ? $_POST['page'] : exit;

            $wp_query = new WP_Query( array('posts_per_page' => 150 , 'post_type' => 'post' , 'paged' => $paged ) );
            foreach( $wp_query -> posts as $post ){
                $arts = meta::get_meta( $post -> ID , 'art' );
                meta::set_meta( $post -> ID , 'nr_art' , count( $arts ) );
                if( count( $arts ) < (int)$new_limit ){
                    delete_post_meta( $post -> ID, 'hot_date' );
                }else{
                    if( (int)meta::get_meta( $post -> ID , 'hot_date' ) > 0 ){

                    }else{
                        meta::set_meta( $post -> ID , 'hot_date' , mktime() );
                    }
                }
            }
            if( $wp_query -> max_num_pages >= $paged ){
                if( $wp_query -> max_num_pages == $paged ){
                    $general = options::get_value( 'general' );
                    $general['min_arts'] = $new_limit;
                    update_option( 'general' , $general );
                    echo 0;
                }else{
                    echo $paged + 1;
                }
            }

            exit();
        }
    }

?>
