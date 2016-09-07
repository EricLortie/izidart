<?php
wp_enqueue_script( 'chosen' , get_template_directory_uri() . '/js/chosen.jquery.min.js' , array( 'jquery' ), false, true );
wp_enqueue_style( 'chosen' , get_template_directory_uri() . '/lib/css/chosen.min.css' );

if(is_user_logged_in()){
    $post_format = '';
    if(isset ($_GET['post']) && is_numeric($_GET['post'])){
        $post_id = $_GET['post'];

        $the_source = '';
        $source_meta = meta::get_meta( $post_id , 'source' );
        if(is_array($source_meta) && sizeof($source_meta) && isset($source_meta['post_source']) && trim($source_meta['post_source']) != ''){
            $the_source = $source_meta['post_source'];

        }

        $post_edit = get_post($post_id);

        $post_categories = wp_get_post_categories( $post_id );
        switch(get_post_format( $post_id )){
            case 'video':
                $post_format = 'video';
                $action_edit_video = true;
                break;
            case 'audio':
                $post_format = 'audio';
                $action_edit_audio = true;
                break;
            case 'link':
                $post_format = 'link';
                $action_edit_link = true;
                break;
            case 'image':
                $post_format = 'image';
                $action_edit_image = true;

                break;
            default:
                $post_format = 'default';
                $action_edit_text = true;


        }

        if(has_post_thumbnail( $post_id )){
            $thumb_id = get_post_thumbnail_id($post_id);
        }

    }
    CosmoUploader::init();
    ?>
    <div class="cosmo-box error medium hidden" id="video_error_msg_box">
        <span class="cosmo-ico"></span>
        <span id="video_error_msg" ></span>
    </div>
    <div class="cosmo-tabs submit large-12 small-12 columns" id="d39">
        <?php if(!isset($post_id)) { ?>
            <ul class="tabs-nav" style="display:none;">
                <?php if( (options::logic( 'upload' , 'enb_image' ) )  ){	?>
                    <li class="first image tabs-selected"><a href="#pic_upload"><span><?php _e('Image','cosmotheme'); ?></span></a></li>
                <?php } ?>
                <?php if( options::logic( 'upload' , 'enb_video' ) ){	?>
                    <li class="video <?php if( isset($post_id) && $post_format =='video'){echo 'first tabs-selected'; } ?>"> <a href="#video_upload"><span><?php _e('Video','cosmotheme'); ?></span></a></li>
                <?php } ?>
                <?php if( options::logic( 'upload' , 'enb_text' ) && !isset($post_id)  ){	?>
                    <li class="text <?php if( isset($post_id) && $post_format =='standard'){echo 'first tabs-selected'; } ?>"> <a href="#text_post"><span><?php _e('Text','cosmotheme'); ?></span></a></li>
                <?php } ?>
                <?php if( options::logic( 'upload' , 'enb_audio' ) && !isset($post_id)  ){	?>
                    <li class="audio <?php if( isset($post_id) && $post_format =='audio'){echo 'first tabs-selected'; } ?>"> <a href="#audio_post"><span><?php _e('Audio','cosmotheme'); ?></span></a></li>
                <?php } ?>
                <?php if( options::logic( 'upload' , 'enb_file' ) && !isset($post_id)  ){	?>
                    <li class="attach <?php if( isset($post_id) && $post_format =='link'){echo 'first tabs-selected'; } ?>"> <a href="#file_post"><span><?php _e('File','cosmotheme'); ?></span></a></li>
                <?php } ?>
            </ul>
        <?php } ?>
        <?php if( (options::logic( 'upload' , 'enb_image' ) && !isset($post_id) ) || ( isset($post_id) && $post_format == 'image')  ){	?>
            <div class="tabs-container" id="pic_upload">
                <h3><?php if( isset($post_id) && $post_format == 'image'){ _e('EDIT ART','cosmotheme'); }else{ _e('ADD ART','cosmotheme'); } ?></h3>
                <?php CosmoUploader::print_form("Attached images","image",true,true)?>
                <form method="post" action="/post-item?phase=post" id="form_post_image" >
                    <div class="row">
                        <div class="field large-12 small-12 columns">
                            <label>
                                <h4><?php _e('Title','cosmotheme')?></h4>
                                <input type="text" class="text tipped front_post_input" name="title" id="img_post_title"  value="<?php if(isset($action_edit_image)){echo $post_edit -> post_title; } ?>">
                                <p class="info"  id="img_post_title_info">
                                    <span class="warning" style="display:none; " id="img_post_title_warning"></span>
                                    <?php _e('Be descriptive or interesting!','cosmotheme'); ?>
                                </p>

                            </label>
                        </div>
                        <div class="field large-12 small-12 columns">
                            <h4><?php _e('Text content','cosmotheme')?></h4>
                            <?php
                            if(isset($action_edit_image)){
                                wp_editor($post_edit -> post_content,'image_content');
                            }else{
                                wp_editor('','image_content');
                            }
                            ?>
                        </div>
                        <div class="field large-12 small-12 columns">
                            <label class="select-cat">
                                <h4><?php _e('Gallery','cosmotheme')?></h4>
                                <?php
                                if(isset($action_edit_image) && is_array($post_categories) && sizeof($post_categories) ){
                                    //$cat = get_category( $post_categories[0] );
                                    $args = array(  'orderby'            => 'ID',
                                        'order'              => 'ASC',
                                        'hide_empty'         => 0,
                                        'selected'           => $post_categories[0],
                                        'id'                 => 'img_post_cat',
                                    );
                                }else{
                                    $args = array(  'orderby'            => 'ID',
                                        'order'              => 'ASC',
                                        'hide_empty'         => 0,
                                        'id'                 => 'img_post_cat',
                                    );
                                }
                                $select_cats = wp_dropdown_categories( array('echo' => 0) );
                                $select_cats = str_replace( 'id=', 'multiple="multiple" data-placeholder="Choose a gallery..." id=', $select_cats );
                                echo $select_cats;
                                ?>

                            </label>
                            <p class="info" id="text_source_input_info">If this is a part of a gallery please select the gallery here. This will override individual map markers for this post and place it with the gallery.</p>

                        </div>
                        <div class="field large-12 small-12 columns">
                            <label>
                                <h4><?php _e('Tags','cosmotheme'); ?> <span>(<?php _e('recommended','cosmotheme'); ?>)</span></h4>
                                <input id="photo_tag_input" type="text" class="text tag_input tipped front_post_input" name="tags" value="<?php if(isset($action_edit_image)){ echo post::list_tags($post_id); } ?>" placeholder="tag 1, tag 2, tag 3, tag 4, tag 5" autocomplete="off">
                            </label>
                            <p class="info"  id="photo_tag_input_info"><?php _e('Use comma to separate each tag. E.g. design, wtf, awesome.','cosmotheme'); ?></p>
                        </div>
                        <?php if(options::logic( 'blog_post' , 'show_source' )){ ?>
                            <div class="field small-12 large-12 columns">
                                <label>
                                    <h4><?php _e('Source','cosmotheme')?></h4>
                                    <input type="text" class="text tipped front_post_input" name="source" id="img_post_source"  value="<?php if(isset($action_edit_image)){ echo $the_source; } ?>">
                                </label>
                                <p class="info" id="image_source_input_info"><?php _e('Example: http://cosmothemes.com','cosmotheme'); ?></p>
                            </div>
                        <?php } ?>
                        <div class="field large-12 small-12 columns">
                            <label class="nsfw">
                                <input type="checkbox" class="checkbox" <?php if(isset($action_edit_image) && meta::logic( $post_edit , 'settings' , 'safe' )){ echo 'checked'; } ?> name="nsfw" value="1"> <?php _e('This is NSFW (Not Safe For Work)','cosmotheme'); ?>

                            </label>
                        </div>

                        <input type="hidden" value="image"  name="post_format">
                        <?php if(isset($post_id)) { ?>
                            <input type="hidden" value="<?php echo $post_id; ?>"  name="post_id">
                        <?php } ?>

                        <div class="field large-12 small-12 columns">
                          <label>
                              <h4>Address</h4>
                              <input type="text" id="acf-field-street_address" class="text" name="fields[field_57d04ff871d2b]" value="" placeholder="" />
                          </label>
                          <label>
                              <h4>City</h4>
                                <input type="text" id="acf-field-city" class="text" name="fields[field_57d0500571d2c]" value="" placeholder="">
                          </label>
                          <label>
                              <h4>Region (Province / State)</h4>
                                <input type="text" id="acf-field-province_state" class="text" name="fields[field_57d0500e71d2d]" value="" placeholder="">
                          </label>
                          <label>
                              <h4>Postal Code / Zip Code</h4>
                                <input type="text" id="acf-field-postal_code" class="text" name="fields[field_57d0502871d2e]" value="" placeholder="">
                          </label>
                          <label>
                              <h4>Country</h4>
                                <input type="text" id="acf-field-postal_code" class="text" name="fields[field_57d0502871d2e]" value="" placeholder="">
                          </label>
                          <label>
                              <h4>Latitude</h4>
                                <input type="text" id="acf-field-latitude" class="text" name="fields[field_57d05cfec3e4c]" value="" placeholder="">
                          </label>
                          <label>
                              <h4>Longitude</h4>
                                <input type="text" id="acf-field-longitude" class="text" name="fields[field_57d05d04c3e4d]" value="" placeholder="">
                          </label>
                        </div>
                        <div id="location_display"></div>
                        <script>

                          var x = document.getElementById("location_display");
                          function getLocation() {
                              if (navigator.geolocation) {
                                  navigator.geolocation.getCurrentPosition(showPosition);
                              } else {
                                  x.innerHTML = "Geolocation is not supported by this browser.";
                              }
                          }
                          function showPosition(position) {
                              x.innerHTML = "Latitude: " + position.coords.latitude +
                              "<br>Longitude: " + position.coords.longitude;
                          }
                        </script>


                        <div class="field large-12 small-12 columns">
                            <p class="button blue">
                                <input type="button" id="submit_img_btn"  onclick="add_image_post()" value="<?php if(isset($post_id)){ _e('Update post','cosmotheme'); }else{ _e('Submit art','cosmotheme'); } ?>"/>
                            </p>
                        </div>
                    </div>
                </form>
            </div>
        <?php } ?>
        <?php if( (options::logic( 'upload' , 'enb_video' ) && !isset($post_id) ) || ( isset($post_id) && $post_format =='video') ){	?>
            <div class="tabs-container" id="video_upload">
                <h3><?php if( isset($post_id) && $post_format == 'video'){ _e('Edit video','cosmotheme'); }else{ _e('Add video','cosmotheme'); } ?></h3>
                <?php CosmoUploader::print_form("Attached video","video",true,true)?>
                <form method="post" action="/post-item?phase=post" id="form_post_video" >
                    <div class="row">
                        <div class="field large-12 small-12 columns">
                            <label>
                                <h4><?php _e('Title','cosmotheme')?></h4>
                                <input type="text" class="text tipped front_post_input" name="title" id="video_post_title"  value="<?php if(isset($action_edit_video)){echo $post_edit -> post_title; } ?>">
                                <p class="info"  id="video_post_title_info">
                                    <span class="warning" style="display:none; " id="video_post_title_warning"></span>
                                    <?php _e('Be descriptive or interesting!','cosmotheme'); ?>
                                </p>

                            </label>
                        </div>
                        <div class="field large-12 small-12 columns">
                            <label>
                                <h4><?php _e('Category','cosmotheme')?></h4>
                                <?php
                                if(isset($action_edit_video) && is_array($post_categories) && sizeof($post_categories) ){

                                    $args = array(  'orderby'            => 'ID',
                                        'order'              => 'ASC',
                                        'hide_empty'         => 0,
                                        'selected'           => $post_categories[0],
                                        'id'                 => 'video_post_cat',
                                    );
                                }else{
                                    $args = array(  'orderby'            => 'ID',
                                        'order'              => 'ASC',
                                        'hide_empty'         => 0,
                                        'id'                 => 'video_post_cat',
                                    );
                                }
                                $select_cats = wp_dropdown_categories( array('echo' => 0) );
                                $select_cats = str_replace( 'id=', 'multiple="multiple" data-placeholder="Choose a category..." id=', $select_cats );
                                echo $select_cats;
                                ?>

                            </label>
                            <p class="info" id="text_source_input_info">If this is a part of a gallery please select the gallery here. This will override individual map markers for this post and place it with the gallery.</p>

                        </div>
                        <div class="field large-12 small-12 columns">
                            <h4><?php _e('Text content','cosmotheme')?></h4>
                            <?php
                            if(isset($action_edit_video)){
                                wp_editor($post_edit -> post_content,'video_content');
                            }else{
                                wp_editor('','video_content');
                            }
                            ?>
                        </div>
                        <div class="field large-12 small-12 columns">
                            <label>
                                <h4><?php _e('Tags','cosmotheme'); ?> <span>(<?php _e('recommended','cosmotheme'); ?>)</span></h4>
                                <input id="video_tag_input" type="text" class="text tag_input tipped front_post_input" name="tags" value="<?php if(isset($action_edit_video)){ echo post::list_tags($post_id); } ?>" placeholder="tag 1, tag 2, tag 3, tag 4, tag 5" autocomplete="off">
                            </label>
                            <p class="info" id="video_tag_input_info"><?php _e('Use comma to separate each tag. E.g. design, wtf, awesome.','cosmotheme'); ?></p>
                        </div>
                        <?php if(options::logic( 'blog_post' , 'show_source' )){ ?>
                            <div class="field large-12 small-12 columns">
                                <label>
                                    <h4><?php _e('Source','cosmotheme')?></h4>
                                    <input type="text" class="text tipped front_post_input" name="source" id="video_post_source"  value="<?php if(isset($action_edit_video)){ echo $the_source; } ?>">
                                </label>
                                <p class="info" id="video_source_input_info"><?php _e('Example: http://cosmothemes.com','cosmotheme'); ?></p>
                            </div>
                        <?php } ?>
                        <div class="field large-12 small-12 columns">
                            <label class="nsfw">
                                <input type="checkbox" class="checkbox" <?php if(isset($action_edit_video) && meta::logic( $post_edit , 'settings' , 'safe' )){ echo 'checked'; } ?> name="nsfw" value="1"> <?php _e('This is NSFW (Not Safe For Work)','cosmotheme'); ?>
                            </label>
                        </div>
                        <input type="hidden" value="video"  name="post_format">
                        <?php if(isset($post_id)) { ?>
                            <input type="hidden" value="<?php echo $post_id; ?>"  name="post_id">
                        <?php } ?>
                        <div class="field large-12 small-12 columns">
                            <p class="button blue">
                                <input type="button" id="submit_video_btn"  onclick="add_video_post()" value="<?php if(isset($post_id)){ _e('Update post','cosmotheme'); }else{ _e('Submit post','cosmotheme'); } ?>" />
                            </p>
                        </div>
                    </div>
                </form>
            </div>
        <?php } ?>
        <?php if( (options::logic( 'upload' , 'enb_text' ) && !isset($post_id) ) || ( isset($post_id) && $post_format == 'default') ){	?>
            <div class="tabs-container" id="text_post">
                <form method="post" action="/post-item?phase=post" id="form_post_text" >
                    <div class="row">
                        <div class="large-12 small-12 columns">
                            <h3><?php if( isset($post_id) && $post_format == 'default'){ _e('Edit text','cosmotheme'); }else{ _e('Add text','cosmotheme'); } ?></h3>
                        </div>
                        <div class="field large-12 small-12 columns">
                            <label>
                                <h4><?php _e('Title','cosmotheme')?></h4>
                                <input type="text" class="text tipped front_post_input" name="title" id="text_post_title"  value="<?php if(isset($action_edit_text)){echo $post_edit -> post_title; } ?>">
                                <p class="info"  id="text_post_title_info">
                                    <span class="warning" style="display:none; " id="text_post_title_warning"></span>
                                    <?php _e('Be descriptive or interesting!','cosmotheme'); ?>
                                </p>

                            </label>
                        </div>
                        <div class="field large-12 small-12 columns">
                            <h4><?php _e('Text content','cosmotheme')?></h4>
                            <?php
                            if(isset($action_edit_text)){
                                wp_editor($post_edit -> post_content,'text_content');
                            }else{
                                wp_editor('','text_content');
                            }
                            ?>
                        </div>
                        <div class="field large-12 small-12 columns">
                            <label>
                                <h4><?php _e('Gallery','cosmotheme')?></h4>
                                <?php

                                if(isset($action_edit_text) && is_array($post_categories) && sizeof($post_categories) ){

                                    $args = array(  'orderby'            => 'ID',
                                        'order'              => 'ASC',
                                        'hide_empty'         => 0,
                                        'selected'           => $post_categories[0],
                                        'id'                 => 'text_post_cat',
                                    );
                                }else{
                                    $args = array(  'orderby'            => 'ID',
                                        'order'              => 'ASC',
                                        'hide_empty'         => 0,
                                        'id'                 => 'text_post_cat',
                                    );
                                }
                                $select_cats = wp_dropdown_categories( array('echo' => 0) );
                                $select_cats = str_replace( 'id=', 'multiple="multiple" data-placeholder="Choose a category..." id=', $select_cats );
                                echo $select_cats;
                                ?>

                            </label>
                            <p class="info" id="text_source_input_info">If this is a part of a gallery please select the gallery here. This will override individual map markers for this post and place it with the gallery.</p>

                        </div>
                        <div class="field large-12 small-12 columns">
                            <label>
                                <h4><?php _e('Tags','cosmotheme'); ?> <span>(<?php _e('recommended','cosmotheme'); ?>)</span></h4>
                                <input id="text_tag_input" type="text" class="text tag_input tipped front_post_input" name="tags" value="<?php if(isset($action_edit_text)){ echo post::list_tags($post_id); } ?>" placeholder="tag 1, tag 2, tag 3, tag 4, tag 5" autocomplete="off">
                            </label>
                            <p class="info"  id="text_tag_input_info"><?php _e('Use comma to separate each tag. E.g. design, wtf, awesome.','cosmotheme'); ?></p>
                        </div>
                        <?php if(options::logic( 'blog_post' , 'show_source' )){ ?>
                            <div class="field large-12 small-12 columns">
                                <label>
                                    <h4><?php _e('Source','cosmotheme')?></h4>
                                    <input type="text" class="text tipped front_post_input" name="source" id="text_post_source"  value="<?php if(isset($action_edit_text)){ echo $the_source; } ?>">
                                </label>
                                <p class="info" id="text_source_input_info"><?php _e('Example: http://cosmothemes.com','cosmotheme'); ?></p>
                            </div>
                        <?php } ?>
                        <div class="field large-12 small-12 columns">
                            <label class="nsfw">
                                <input type="checkbox" class="checkbox" <?php if(isset($action_edit_text) && meta::logic( $post_edit , 'settings' , 'safe' )){ echo 'checked'; } ?> name="nsfw" value="1"> <?php _e('This is NSFW (Not Safe For Work)','cosmotheme'); ?>

                            </label>
                        </div>
                        <input type="hidden" value=""  name="post_format">
                        <?php if(isset($post_id)) { ?>
                            <input type="hidden" value="<?php echo $post_id; ?>"  name="post_id">
                        <?php } ?>
                        <div class="field large-12 small-12 columns">
                            <p class="button blue">
                                <input type="button" id="submit_text_btn"  onclick="add_text_post()" value="<?php if(isset($post_id)){ _e('Update post','cosmotheme'); }else{ _e('Submit post','cosmotheme'); } ?>"/>
                            </p>
                        </div>
                    </div>
                </form>
            </div>
        <?php } ?>
        <?php if( (options::logic( 'upload' , 'enb_audio' ) && !isset($post_id) ) || ( isset($post_id) && $post_format == 'audio') ){	?>

            <div class="tabs-container" id="audio_post">

                <h3><?php if( isset($post_id) && $post_format == 'audio'){ _e('Edit audio file','cosmotheme'); }else{ _e('Add mp3 audio file','cosmotheme'); } ?></h3>

                <?php CosmoUploader::print_form("Attached audio","audio",false,false);
                CosmoUploader::print_feat_img_form("audio")?>

                <form method="post" action="/post-item?phase=post" id="form_post_audio" >
                    <div class="row">
                        <div class="field large-12 small-12 columns">
                            <label>
                                <h4><?php _e('Title','cosmotheme')?></h4>
                                <input type="text" class="text tipped front_post_input" name="title" id="audio_post_title"  value="<?php if(isset($action_edit_audio)){echo $post_edit -> post_title; } ?>">
                                <p class="info"  id="audio_img_post_title_info">
                                    <span class="warning" style="display:none; " id="audio_img_post_title_warning"></span>
                                    <?php _e('Be descriptive or interesting!','cosmotheme'); ?>
                                </p>

                            </label>
                        </div>

                        <div class="field large-12 small-12 columns">
                            <h4><?php _e('Text content','cosmotheme')?></h4>
                            <?php
                            if(isset($action_edit_audio)){
                                wp_editor($post_edit -> post_content,'audio_content');
                            }else{
                                wp_editor('','audio_content');
                            }
                            ?>
                        </div>
                        <div class="field large-12 small-12 columns">
                            <label>
                                <h4><?php _e('Category','cosmotheme')?></h4>
                                <?php

                                if(isset($action_edit_audio) && is_array($post_categories) && sizeof($post_categories) ){

                                    $args = array(  'orderby'            => 'ID',
                                        'order'              => 'ASC',
                                        'hide_empty'         => 0,
                                        'selected'           => $post_categories[0],
                                        'id'                 => 'audio_post_cat',
                                    );
                                }else{
                                    $args = array(  'orderby'            => 'ID',
                                        'order'              => 'ASC',
                                        'hide_empty'         => 0,
                                        'id'                 => 'audio_post_cat',
                                    );
                                }
                                $select_cats = wp_dropdown_categories( array('echo' => 0) );
                                $select_cats = str_replace( 'id=', 'multiple="multiple" data-placeholder="Choose a category..." id=', $select_cats );
                                echo $select_cats;
                                ?>

                            </label>
                            <p class="info" id="text_source_input_info">If this is a part of a gallery please select the gallery here. This will override individual map markers for this post and place it with the gallery.</p>

                        </div>
                        <div class="field large-12 small-12 columns">
                            <label>
                                <h4><?php _e('Tags','cosmotheme'); ?> <span>(<?php _e('recommended','cosmotheme'); ?>)</span></h4>
                                <input id="audio_photo_tag_input" type="text" class="text tag_input tipped front_post_input" name="tags" value="<?php if(isset($action_edit_audio)){ echo post::list_tags($post_id); } ?>" placeholder="tag 1, tag 2, tag 3, tag 4, tag 5" autocomplete="off">
                            </label>
                            <p class="info"  id="audio_photo_tag_input_info"><?php _e('Use comma to separate each tag. E.g. design, wtf, awesome.','cosmotheme'); ?></p>
                        </div>
                        <?php if(options::logic( 'blog_post' , 'show_source' )){ ?>
                            <div class="field large-12 small-12 columns">
                                <label>
                                    <h4><?php _e('Source','cosmotheme')?></h4>
                                    <input type="text" class="text tipped front_post_input" name="source" id="audio_img_post_source" value="<?php if(isset($action_edit_audio)){ echo $the_source; } ?>">
                                </label>
                                <p class="info" id="audio_image_source_input_info"><?php _e('Example: http://cosmothemes.com','cosmotheme'); ?></p>
                            </div>
                        <?php } ?>
                        <div class="field large-12 small-12 columns">
                            <label class="nsfw">
                                <input type="checkbox" class="checkbox" <?php if(isset($action_edit_audio) && meta::logic( $post_edit , 'settings' , 'safe' )){ echo 'checked'; } ?> name="nsfw" value="1"> <?php _e('This is NSFW (Not Safe For Work)','cosmotheme'); ?>

                            </label>
                        </div>

                        <input type="hidden" value="audio"  name="post_format">
                        <?php if(isset($post_id)) { ?>
                            <input type="hidden" value="<?php echo $post_id; ?>"  name="post_id">
                        <?php } ?>
                        <div class="field large-12 small-12 columns">
                            <p class="button blue">
                                <input type="button" id="submit_audio_btn"  onclick="add_audio_post()" value="<?php if(isset($post_id)){ _e('Update post','cosmotheme'); }else{ _e('Submit post','cosmotheme'); } ?>"/>
                            </p>
                        </div>
                    </div>
                </form>
            </div>
        <?php } ?>
        <?php if( (options::logic( 'upload' , 'enb_file' ) && !isset($post_id) ) || ( isset($post_id) && $post_format == 'link') ){	?>
            <div class="tabs-container" id="file_post">
                <h3><?php if( isset($post_id) && $post_format == 'link'){ _e('Edit file','cosmotheme'); }else{ _e('Add file','cosmotheme'); } ?></h3>

                <?php CosmoUploader::print_form("Attached files","link",false,false);
                CosmoUploader::print_feat_img_form("link")?>
                <form method="post" action="/post-item?phase=post" id="form_post_file" >
                    <div class="row">
                        <div class="field large-12 small-12 columns">
                            <label>
                                <h4><?php _e('Title','cosmotheme')?></h4>
                                <input type="text" class="text tipped front_post_input" name="title" id="file_post_title" value="<?php if(isset($action_edit_link)){echo $post_edit -> post_title; } ?>">
                                <p class="info"  id="file_img_post_title_info">
                                    <span class="warning" style="display:none; " id="file_img_post_title_warning"></span>
                                    <?php _e('Be descriptive or interesting!','cosmotheme'); ?>
                                </p>

                            </label>
                        </div>

                        <div class="field large-12 small-12 columns">
                            <h4><?php _e('Text content','cosmotheme')?></h4>
                            <?php
                            if(isset($action_edit_link)){
                                wp_editor($post_edit -> post_content,'file_content');
                            }else{
                                wp_editor('','file_content');
                            }
                            ?>
                        </div>
                        <div class="field large-12 small-12 columns">
                            <label>
                                <h4><?php _e('Category','cosmotheme')?></h4>
                                <?php



                                if(isset($action_edit_link) && is_array($post_categories) && sizeof($post_categories) ){

                                    $args = array(  'orderby'            => 'ID',
                                        'order'              => 'ASC',
                                        'hide_empty'         => 0,
                                        'selected'           => $post_categories[0],
                                        'id'                 => 'file_post_cat',
                                    );
                                }else{
                                    $args = array(  'orderby'            => 'ID',
                                        'order'              => 'ASC',
                                        'hide_empty'         => 0,
                                        'id'                 => 'file_post_cat',
                                    );
                                }
                                $select_cats = wp_dropdown_categories( array('echo' => 0) );
                                $select_cats = str_replace( 'id=', 'multiple="multiple" data-placeholder="Choose a category..." id=', $select_cats );
                                echo $select_cats;
                                ?>

                            </label>
                            <p class="info" id="text_source_input_info">If this is a part of a gallery please select the gallery here. This will override individual map markers for this post and place it with the gallery.</p>

                        </div>
                        <div class="field large-12 small-12 columns">
                            <label>
                                <h4><?php _e('Tags','cosmotheme'); ?> <span>(<?php _e('recommended','cosmotheme'); ?>)</span></h4>
                                <input id="file_photo_tag_input" type="text" class="text tag_input tipped front_post_input" name="tags" value="<?php if(isset($action_edit_link)){ echo post::list_tags($post_id); } ?>" placeholder="tag 1, tag 2, tag 3, tag 4, tag 5" autocomplete="off">
                            </label>
                            <p class="info"  id="file_photo_tag_input_info"><?php _e('Use comma to separate each tag. E.g. design, wtf, awesome.','cosmotheme'); ?></p>
                        </div>
                        <?php if(options::logic( 'blog_post' , 'show_source' )){ ?>
                            <div class="field large-12 small-12 columns">
                                <label>
                                    <h4><?php _e('Source','cosmotheme')?></h4>
                                    <input type="text" class="text tipped front_post_input" name="source" id="file_img_post_source"  value="<?php if(isset($action_edit_link)){ echo $the_source; } ?>">
                                </label>
                                <p class="info" id="file_image_source_input_info"><?php _e('Example: http://cosmothemes.com','cosmotheme'); ?></p>
                            </div>
                        <?php } ?>
                        <div class="field large-12 small-12 columns">
                            <label class="nsfw">
                                <input type="checkbox" class="checkbox" <?php if(isset($action_edit_link) && meta::logic( $post_edit , 'settings' , 'safe' )){ echo 'checked'; } ?> name="nsfw" value="1"> <?php _e('This is NSFW (Not Safe For Work)','cosmotheme'); ?>

                            </label>
                        </div>
                        <?php if(isset($post_id)) { ?>
                            <input type="hidden" value="<?php echo $post_id; ?>"  name="post_id">
                        <?php } ?>
                        <input type="hidden" value="link"  name="post_format">
                        <div class="field large-12 small-12 columns">
                            <p class="button blue">
                                <input type="button" id="submit_file_btn"  onclick="add_file_post()" value="<?php if(isset($post_id)){ _e('Update post','cosmotheme'); }else{ _e('Submit post','cosmotheme'); } ?>"/>
                            </p>
                        </div>
                    </div>
                </form>
            </div>
        <?php } ?>

    </div>
    <div id="not_logged_msg" style="display:none"><?php _e('You must be logged in to submit an post','cosmotheme'); ?></div>
    <div id="success_msg" style="display:none"></div>
    <div id="loading_" style="display:none"><object width="100" height="100" type="application/x-shockwave-flash" data="<?php echo get_template_directory_uri() ?>/images/preloader.swf" id="ajax-indicator-swf" style="visibility: visible;">
            <param name="quality" value="high"><param name="allowscriptaccess" value="always">
            <param name="wmode" value="transparent">
            <param name="scale" value="noborder">
        </object></div>
<?php
}else{
    _e('You must be <a href="#" class="simplemodal-login simplemodal-none link">logged in</a> to submit a post.','cosmotheme');
}
?>
