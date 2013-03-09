<?php

/*
 * Functions to include a META box on pages - it allows for the title of the widget to be overriden by page
 */

/* Define the custom box */

add_action( 'add_meta_boxes', 'sp_sidebar_navi_add_meta' );

/* Do something with the data entered */
add_action( 'save_post', 'sp_sidebar_navi_save_meta' );

/* Adds a box to the main column on the Post and Page edit screens */
function sp_sidebar_navi_add_meta() {
    $screens = array( 'page' );
    foreach ($screens as $screen) {
        add_meta_box(
            'myplugin_sectionid',
            __( 'Sidebar Navigation Settings', 'sp_sidebar_navi' ),
            'sp_sidebar_navi_options',
            $screen,
            'side',
            'core'
        );
    }
}

/* Prints the box content */
function sp_sidebar_navi_options( $post ) {

  // Use nonce for verification
  wp_nonce_field( plugin_basename( __FILE__ ), 'sp_sidebar_navi_nonce' );
  
  // The actual fields for data entry
  // Use get_post_meta to retrieve an existing value from the database and use the value for the form
  if( isset($post->ID) && get_post_meta( $post->ID, 'sp_sidebar_navi_title', true ) != '' ) {
    $title = get_post_meta( $post->ID, 'sp_sidebar_navi_title', true );
  } else {
    $title = '';   
  }
  
  if( isset($post->ID) && get_post_meta( $post->ID, 'sp_sidebar_navi_label', true ) != '' ) {
    $label = get_post_meta( $post->ID, 'sp_sidebar_navi_label', true );
  } else {
    $label = '';   
  }
  
  echo '<label for="sp_sidebar_navi_title">';
       _e("<strong>Widget Title</strong>", 'sp_sidebar_navi' );
  echo '</label><br /> ';
  echo '<input type="text" id="sp_sidebar_navi_title" name="options[sp_sidebar_navi_title]" value="'.esc_attr($title).'" style="width:100%;"/>';
  echo '<br /><small>Use this field to override the sidebar navigation title when the widget appears on this page.</small>';
  
  echo '<br /><br /><label for="sp_sidebar_navi_label">';
       _e("<strong>Label</strong>", 'sp_sidebar_navi' );
  echo '</label><br />';
  echo '<input type="text" id="sp_sidebar_navi_label" name="options[sp_sidebar_navi_label]" value="'.esc_attr($label).'" style="width:100%;" />';
  echo '<br /><small>If specified, this label will be used when this page appears in a sidebar navigation.</small>';

}

/* When the post is saved, saves our custom data */
function sp_sidebar_navi_save_meta( $post_id ) {

  // First we need to check if the current user is authorised to do this action. 
  if ( isset($_POST['post_type']) && 'page' == $_POST['post_type'] ) {
    if ( ! current_user_can( 'edit_page', $post_id ) )
        return;
  } else {
    if ( ! current_user_can( 'edit_post', $post_id ) )
        return;
  }

  // Secondly we need to check if the user intended to change this value.
  if ( ! isset( $_POST['sp_sidebar_navi_nonce'] ) || ! wp_verify_nonce( $_POST['sp_sidebar_navi_nonce'], plugin_basename( __FILE__ ) ) )
      return;

  // Thirdly we can save the value to the database

  //if saving in a custom table, get post_ID
  $post_ID = $_POST['post_ID'];
  //sanitize user input
  foreach($_POST['options'] as $key => $value) {
    $data = sanitize_text_field( $value );
    //Update data
    update_post_meta($post_ID, $key, $data);
  }

}

?>