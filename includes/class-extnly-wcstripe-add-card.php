<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * WC_Gateway_Stripe_Add_New_Card class.
 */
class Extnly_Add_Card_Addon_WC_GW_Sripe extends WC_Gateway_Stripe_Saved_Cards {

	/**
	 * Constructor
	 */
	public function __construct() {

		add_action( 'wp', array( $this, 'add_new_card' ) );
		add_action( 'woocommerce_after_my_account', array( $this, 'output' ),999);

		$this->stripe_Error="";
	}

	/**
	 * Display saved cards
	 */
	public function output() {

		if ( ! is_user_logged_in() || ( ! $customer_id = get_user_meta( get_current_user_id(), '_stripe_customer_id', true ) ) || ! is_string( $customer_id ) ) {
			return;
		}


		wc_get_template( 'Add-new-card.php',array( 'stripe_Error' => $this->stripe_Error ), 'extnly-wc-stripe-add-card/', WC_EXTNLY_STRIPE_ADD_CARD_TEMPLATE_PATH);

	}


	/**
	 * Payment form on checkout page
	 */
	public function add_card_fields() {

		$stripe = new WC_Gateway_Stripe();

		$checked = 1;
		?>
		<fieldset>
			<?php
			$allowed = array(
				'a' => array(
					'href' => array(),
					'title' => array()
				),
				'br' => array(),
				'em' => array(),
				'strong' => array(),
				'span'	=> array(
					'class' => array(),
				),
			);
			if ( $stripe->testmode ) {
				$stripe->description =  sprintf( __( 'TEST MODE ENABLED. In test mode, you can use the card number 4242424242424242 with any CVC and a valid expiration date or check the documentation "<a href="%s">Testing Stripe</a>" for more card numbers.', 'extnly-add-card-addon-wc-gateway-stripe' ), 'https://stripe.com/docs/testing' );
				$stripe->description  = trim( $stripe->description );
				//$stripe->description  = "";
			}
			if ( $stripe->description ) {
				echo apply_filters( 'wc_stripe_description', wpautop( wp_kses( $stripe->description, $allowed ) ) );
			}
			echo $stripe->get_icon();
			?>
			<div class="stripe_card" <?php if ( $checked === 0 ) : ?>style="display:none;"<?php endif; ?>
				 data-description=""
				 data-amount="<?php echo esc_attr( $stripe->get_stripe_amount( WC()->cart->total ) ); ?>"
				 data-name="<?php echo esc_attr( sprintf( __( '%s', 'extnly-add-card-addon-wc-gateway-stripe' ), get_bloginfo( 'name' ) ) ); ?>"
				 data-label="<?php esc_attr_e( 'Confirm and Pay', 'extnly-add-card-addon-wc-gateway-stripe' ); ?>"
				 data-currency="<?php echo esc_attr( strtolower( get_woocommerce_currency() ) ); ?>"
				 data-image="<?php echo esc_attr( $stripe->stripe_checkout_image ); ?>"
				 data-bitcoin="<?php echo esc_attr( $stripe->bitcoin ? 'true' : 'false' ); ?>"
				>

				<?php $stripe->credit_card_form( array( 'fields_have_names' => true ) ); ?>

			</div>
		</fieldset>
		<?php
	}


/**
* Add a card to a customer via the API.
*

*/
	public function add_new_card() {


		if ( ! isset( $_POST['add-new-card'] ) || ! is_account_page() ) {
			return;
		}

		if ( ! is_user_logged_in() || ( ! $customer_id = get_user_meta( get_current_user_id(), '_stripe_customer_id', true ) )
			|| ! is_string( $customer_id ) ) {
			wp_die( __( 'Unable to add card, please try again.', 'woocommerce-gateway-stripe' ) );
		}
		$stripe = new WC_Gateway_Stripe();



		$dates = explode("/", $_POST['stripe-card-expiry']);
		$exp_month = $dates[0];
		$exp_year = $dates[1];

		try{

			$token_result = $stripe->stripe_request(
				array(
					"card" => array(
						'number' => $_POST['stripe-card-number'],
						'exp_month' => intval($exp_month),
						'exp_year' => intval($exp_year),
						'cvc' => $_POST['stripe-card-cvc'])
				),
				'tokens' );


			}catch (Exception $ex) {

			$this->show_message('<ul class="woocommerce_error woocommerce-error" style="font-size: large"><li>Unable to add the new card. '.$ex->getMessage().'</li></ul>');
			return;
		}
		//d($token_result);


		if ( is_wp_error( $token_result ) ) {
			$this->show_message('<ul class="woocommerce_error woocommerce-error" style="font-size: large"><li>Unable to add the new card. '.$token_result->get_error_message().'</li></ul>');
			return;
		}

		$stripe_token=$token_result->id;

		try {
			$stripe->add_card($customer_id, $stripe_token);

			$this->show_message('<div class="woocommerce-message" style="font-size:large">Card has been succesfully added.</div>');
		}catch (Exception $ex) {


			$this->show_message('<ul class="woocommerce_error woocommerce-error" style="font-size: large"><li>Unable to add the new card. '.$ex->getMessage().'</li></ul>');
			return;
		}

		add_action( 'woocommerce_after_my_account', array( $this, 'show_message' ),1);

		return new WP_Error( 'error', __( 'Unable to add card', 'woocommerce-gateway-stripe' ) );
	}


	public function show_message($message){
		$this->stripe_Error=$message;
		echo '<div style="width:50% !important;margin-right: auto  !important;margin-left: auto  !important">'.$this->stripe_Error=$message.'</div>';
	}



}
new Extnly_Add_Card_Addon_WC_GW_Sripe();