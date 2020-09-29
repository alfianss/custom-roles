<?php
/*
Plugin Name: Custom Rules Plugin
Plugin URI: http://example.com
Description: Simple WordPress Custom Rules
Version: 1.0
Author: Alfian SS
Author URI: http://example.com
*/

class Custom_Roles {

    public $custom_roles_version = '1.0';

	public function custom_roles_version() {
		return $this->custom_roles_version;		
	}

    public function update_custom_rules() {
        if( get_option( 'custom_roles_version') < 1) {
            
            add_role( 'staff', 'Staff', 
                array( 
                    'read' => true,
                    'delete_posts' => true, 
                    'edit_posts' => true, 
                    'publish_posts' => true 
                )
            );

            add_role( 'manager', 'Manager',
                array( 
                    'read' => true, 
                    'delete_posts' => true, 
                    'edit_posts' => true, 
                    'publish_posts' => true,
                    'list_users' => true,
                    'promote_users' => true,
                    'remove_users' => true
                )
            );

            update_option( 'custom_roles_version', $this->custom_roles_version() );
        }
    }

    public function roles_shortcode( $atts = array() ) {

        // set up default parameters
        $arr_atts = shortcode_atts(array(
            'staff'  => 1,
            'manager'   => 1            
        ), $atts);

        if( $arr_atts['staff'] == 1 && $arr_atts['manager'] == 1) {
            $user_query = new WP_User_Query( array( 'role__in' => array('staff', 'manager') ) );
        } else if( $arr_atts['staff'] == 1) {
            $user_query = new WP_User_Query( array( 'role' => 'staff' ) );
        } else {
            $user_query = new WP_User_Query( array( 'role' => 'manager' ) );
        }
        // The Query
        

        // User Loop
        if ( ! empty( $user_query->get_results() ) ) {
            echo "<ul>";
            foreach ( $user_query->get_results() as $user ) {
                echo '<li>' . $user->display_name . '</li>';
            }
            echo "</ul>";
        } else {
            echo 'No users found.';
        }
    }

}

$custom_roles = new Custom_Roles();

add_action('init', array($custom_roles, 'update_custom_rules'));

add_shortcode( 'wp7_users', array($custom_roles, 'roles_shortcode') );