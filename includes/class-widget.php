<?php
/*
 * Class to build the display widget
 */

    class theme_navigation extends WP_Widget {
        function theme_navigation() {
            parent::WP_Widget('theme_navigation', 'Sidebar Navigation', array('description' => '', 'class' => 'sidebar-navigation'));
        }
        function form($instance) {
            $default =     array( 'title' => __('Navigation') );
            $instance = wp_parse_args( (array) $instance, $default );
            $field_id = $this->get_field_id('title');
            $field_name = $this->get_field_name('title');
            echo "\r\n".'<p><label for="'.$field_id.'">'.__('Title').': <input type="text" class="widefat" id="'.$field_id.'" name="'.$field_name.'" value="'.attribute_escape( $instance['title'] ).'" /><label></p>';
            $field_id = $this->get_field_id('page');
        }
        function update($new_instance, $old_instance) {
            $instance = $old_instance;
            $instance['title'] = strip_tags($new_instance['title']);
            return $instance;
        }
        
        function display($instance, $obj) {
            
            global $post;
            
            if(get_post_meta($post->ID, 'sp_sidebar_navi_title', true) != '') {
                $title = apply_filters('widget_title', get_post_meta($post->ID, 'sp_sidebar_navi_title', true));
            } else {
                $title = empty($instance['title']) ? '' : apply_filters('widget_title', $instance['title']);
            }

            isset($before_widget) ? print( $before_widget ) : print( '<div class="widget clearfix">' );
            
            if( isset($before_widget) && isset($after_widget) ) {
                print( $before_title . $title . $after_title );
            } else {
                print('<h3 class="widget-title">'.$title.'</h3>');
            }
            
            print('<ul class="sidebar-navigation-widget widget">');
                foreach($obj as $o) {
                    print('<li class="nav-item"><a href="'.get_permalink($o->ID).'">'.get_the_title($o->ID).'</a></li>');
                }
            print( '</ul>' );
           isset($after_widget) ? print( $after_widget ) : print( '</div>' );
        }
        
        function widget($args, $instance) {
            extract($args, EXTR_SKIP);
            global $post;
            
            if($post->post_type == 'page') {
            
                $pages = get_pages(array(
                    'child_of' => $post->ID,
                    'parent' => $post->ID,
                    'sort_column' => 'menu_order'
                ));

                // Check if the current page has a parent
                if ($post -> post_parent) {
                    // Get sibling pages
                    $siblings = get_pages(array(
                        'child_of' => $post -> post_parent,
                        'parent' => $post -> post_parent,
                        'sort_column' => 'menu_order'
                    ));
                }

                if(count($pages)) {
                    
                    $this->display($instance, $pages, $before_widget, $before_title, $after_title, $after_widget);
         
                } elseif($post -> post_parent) { // Show the sibling pages if there are no children
                    
                    if(count($siblings)) {
                        $this->display($instance, $siblings, $before_widget, $before_title, $after_title, $after_widget);
                    }
                    
                }
            } 
        }
    }
    
    function register_theme_navigation_widget(){
        register_widget('theme_navigation');
    }
    
    add_action('widgets_init','register_theme_navigation_widget');
?>