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
        function widget($args, $instance) {
            extract($args, EXTR_SKIP);
            global $post;
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
            
            if(count($pages)){
                echo $before_widget;
                $title = empty($instance['title']) ? '' : apply_filters('widget_title', $instance['title']);
                if ( !empty( $title ) ) { echo $before_title . $title . $after_title; }
                echo "<ul>";
                foreach($pages as $page){
                    echo "<li><a href='".get_permalink($page->ID)."'>".get_the_title($page->ID)."</a></li>";
                }
                echo "</ul>";
                echo $after_widget;
            }elseif($post -> post_parent) { // Show the sibling pages if there are no children
                if(count($siblings)) {
                    echo $before_widget;
                    $title = empty($instance['title']) ? '' : apply_filters('widget_title', $instance['title']);
                    if ( !empty( $title ) ) { echo $before_title . $title . $after_title; }
                    echo "<ul>";
                    foreach($siblings as $page){
                        echo "<li><a href='".get_permalink($page->ID)."'>".get_the_title($page->ID)."</a></li>";
                    }
                    echo "</ul>";
                    echo $after_widget;
                }
            }
        }
    }
    function register_theme_navigation_widget(){
        register_widget('theme_navigation');
    }
    add_action('widgets_init','register_theme_navigation_widget');
?>