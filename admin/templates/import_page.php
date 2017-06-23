	<div class="wrap">
		<h2><?php _e( 'Import/Export data from CSV file', 'sha-wlc' ); ?></h2>
		<h3><?php _e( 'Import data form CSV file', 'sha-wlc' ); ?></h3>
		<?php if ( isset( $_SESSION['sha-wlc']) ): ?>
		<div id="message" class="notice <?php echo $_SESSION['sha-wlc']['class']; ?>"><p><strong><?php _e( $_SESSION['sha-wlc']['message'], 'sha-wlc' ); ?></strong></p></div>
		<?php endif; ?>
		<form method="post" enctype="multipart/form-data">
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><?php _e( 'CSV file', 'sha-wlc' ); ?></th>
					<td>
						<input type="file" name="<?php echo $sha_wlc_prefix; ?>import_csv" style="width: 300px;" />
						<input type="hidden" name="action" value="import" />
					</td>
				</tr>
			</table>
			<?php submit_button( __( 'Import data', 'sha-wlc' ) ); ?>
		</form>
		<hr />
		<h3><?php _e( 'Export data to CSV file', 'sha-wlc' ); ?></h3>
		<form method="post">
			<?php submit_button( __( 'Export data', 'sha-wlc' ) ); ?>
			<input type="hidden" name="action" value="export" /></td>
		</form>
	</div>