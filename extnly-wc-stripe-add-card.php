<?php
/**
 * Plugin Name: Woo AddOn For Stripe
 * Plugin URI: http://www.extensionly.com/
 * Description: Allow users to add a new card for later use without ordering first. Requires http://www.woothemes.com/products/stripe/
 * Version: 1.0
 * Author: Extensionly

 * Copyright: © 2009-2014 Extensionly.
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html

 * Stripe Docs: https://stripe.com/docs
**/

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

add_action( 'plugins_loaded', 'extnly_add_card_stripe_init',999 );

/**
 * Required functions
 */
if ( ! function_exists( 'woothemes_queue_update' ) ) {
    require_once( 'woo-includes/woo-functions.php' );
}



/**
 * Define some useful constants
 **/
define('extnly_stripe_add_card_VERSION', '1.0');
define('extnly_stripe_add_card_DIR', plugin_dir_path(__FILE__));
define('extnly_stripe_add_card_URL', plugin_dir_url(__FILE__));
define( 'WC_EXTNLY_STRIPE_ADD_CARD_TEMPLATE_PATH', untrailingslashit( plugin_dir_path( __FILE__ ) ) . '/templates/' );



/**
 * Load files
 * 
 **/
function extnly_add_card_stripe_init(){

    if( !class_exists( 'WC_Stripe' )  ) {
        return;
    }

        if (is_admin()) {
            require_once(extnly_stripe_add_card_DIR . 'includes/admin.php');
        }

    require_once(extnly_stripe_add_card_DIR . 'includes/class-extnly-wcstripe-add-card.php');


}

extnly_add_card_stripe_init();


/**
 * Activation, Deactivation and Uninstall Functions
 * 
 **/
register_activation_hook(__FILE__, 'extnly_add_card_stripe_activation');
register_deactivation_hook(__FILE__, 'extnly_add_card_stripe_deactivation');


function extnly_add_card_stripe_activation() {

    //register uninstaller
    register_uninstall_hook(__FILE__, 'extnly_add_card_stripe_uninstall');
}

function extnly_add_card_stripe_deactivation() {
    
	// actions to perform once on plugin deactivation go here
	    
}

function extnly_add_card_stripe_uninstall(){
    
    //actions to perform once on plugin uninstall go here
	    
}


?>