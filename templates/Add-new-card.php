<h2 id="add-cards" style="margin-top:40px;"><?php _e( 'Add a new card', 'extnly-add-card-addon-wc-gateway-stripe' ); ?></h2>
<table class="shop_table">
	<thead>

	</thead>
	<tbody>

		<tr>

			<td>
<form action="" method="POST">
	<?php $this_customer_id = get_user_meta( get_current_user_id()); ?>
	<?php  echo $stripe_Error;  ?>
<?php
$add_card = new Extnly_Add_Card_Addon_WC_GW_Sripe();
$add_card->add_card_fields();
?>


	<input type="hidden" name="this_customer_id" value="<?php echo esc_attr( $this_customer_id ); ?>">

	<input type="submit" class="button" id="add-new-card" name="add-new-card" value="Add card">
</form>



			</td>
		</tr>

	</tbody>
</table>