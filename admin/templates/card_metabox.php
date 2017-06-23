<table id="card-data">
	<tr>
		<td>
			<?php _e( 'User ID', 'sha-wlc' ); ?>
		</td>
		<td>
			<input type="text" id="<?php echo $sha_wlc_prefix; ?>user_id" name="<?php echo $sha_wlc_prefix; ?>user_id" value="<?php echo $user_id ; ?>" />
		</td>
	</tr>
	<tr>
		<td>
			<?php _e( 'Discount amount (%)', 'sha-wlc' ); ?>
		</td>
		<td>
			<input type="text" id="<?php echo $sha_wlc_prefix; ?>discount" name="<?php echo $sha_wlc_prefix; ?>discount" value="<?php echo $discount ; ?>" />
		</td>
	</tr>
</table>
