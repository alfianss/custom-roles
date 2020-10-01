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

        if ( get_query_var('paged') ) {
            $paged = get_query_var('paged'); 
        } elseif ( get_query_var('page') ) { 
            $paged = get_query_var('page'); 
        } else { 
            $paged = 1; 
        }
        

        $user_per_page = 2;
        
        if( $arr_atts['staff'] == 1 && $arr_atts['manager'] == 1) {
            $users = get_users(array('role__in'  => array('staff', 'manager')));
            $user_query = new WP_User_Query( 
                array( 
                    'role__in'  => array('staff', 'manager'), 
                    'number'    => $user_per_page,
                    'paged'     => $paged
                )
            );
        } else if( $arr_atts['staff'] == 1) {
            $users = get_users(array('role' => 'staff'));
            $user_query = new WP_User_Query( 
                array( 
                    'role' => 'staff', 
                    'number'    => $user_per_page,
                    'paged'     => $paged,
                ) 
            );
        } else {
            $users = get_users(array('role' => 'manager'));
            $user_query = new WP_User_Query( 
                array(
                    'role' => 'manager', 
                    'number'    => $user_per_page,
                    'paged'     => $paged,
                ) 
            );
        }
        
        $total_users = count($users);
        $total_query = count($user_query->get_results());
        $total_pages = ($total_users / $user_per_page);
        
        if ( ! empty( $user_query->get_results() ) ) {
        ?>
            <table>
                <thead>
                    <tr>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Email</th>
                    </tr>
                </thead>
                <tbody>
        <?php
            foreach ( $user_query->get_results() as $user ) {
                $first_name = $user->first_name;
                $last_name  = $user->last_name;
                $email      = $user->user_email;
                ?>
                    <tr>
                        <td><?php echo esc_html($first_name); ?></td>
                        <td><?php echo esc_html($last_name); ?></td>
                        <td><?php echo esc_html($email); ?></td>
                    </tr>   
                <?php
            }
        ?>
                </tbody>
            </table>

            <p>
            <?php                 

                if($total_users > $total_query) {
                    $current_page = max(1, is_front_page() ? get_query_var('page') : get_query_var('paged')); // key
                    echo paginate_links( 
                        array(                        
                            'current' => $paged,
                            'total' => $total_pages,
                            'prev_next' => false,
                        )
                    );                    
                }           
            ?>            
            </p>
        <?php
        } else {
        ?>
            <table>
                <thead>
                    <tr>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Email</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="3">No users found.</td>
                    </tr>
                </tbody>
            </table>
        <?php            
        }
        wp_reset_postdata();
    }
      

}

$custom_roles = new Custom_Roles();

add_action('init', array($custom_roles, 'update_custom_rules'));

add_shortcode( 'wp7_users', array($custom_roles, 'roles_shortcode') );