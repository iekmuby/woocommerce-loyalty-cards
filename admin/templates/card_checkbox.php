<div id='loyaltycard_options' class='panel woocommerce_options_panel'>
	<div class='options_group'>
		<?php
			woocommerce_wp_checkbox( array(
				'id' 		=> '_disallow_loyaltycard',
				'label' 	=> __( 'Exclude from discount', 'woocommerce' ),
			) );
		?>
	</div>
</div>