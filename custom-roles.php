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
    }

    public function roles_shortcode( $atts = array() ) {

        $args = array();
        // set up default parameters
        $arr_atts = shortcode_atts(array(
            'staff'  => 1,
            'manager'   => 1            
        ), $atts);

        $paged = max(1, is_front_page() ? get_query_var('page') : get_query_var('paged'));         
        $user_per_page = 5;
                
        ($arr_atts['staff'] == 1 ) ? array_push($args, 'staff') : $args;
        ($arr_atts['manager'] == 1 ) ? array_push($args, 'manager') : $args;
        
        $user_query = new WP_User_Query( 
            array( 
                'role__in'  => $args, 
                'number'    => $user_per_page,
                'paged'     => $paged
            )
        );    
        
        $total_users = $user_query->get_total();
        $total_query = count($user_query->get_results());
        $total_pages = ($total_users / $user_per_page);
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
        
        if ( ! empty( $user_query->get_results() ) ) {                
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
                    $current_page = max(1, is_front_page() ? get_query_var('page') : get_query_var('paged')); // key
                    echo paginate_links( 
                        array(                        
                            'current' => $current_page,
                            'total' => $total_pages,
                            'prev_next' => false,
                        )
                    );  
                       
            ?>            
            </p>
        <?php  
            } else { 
            ?>
                <tr>
                    <td colspan="3">No user founded.</td>
                </tr>
            <?
            }    
            wp_reset_postdata();      
        }        
}
     

$custom_roles = new Custom_Roles();

register_activation_hook( __FILE__, array($custom_roles, 'update_custom_rules'));

add_shortcode( 'wp7_users', array($custom_roles, 'roles_shortcode') );