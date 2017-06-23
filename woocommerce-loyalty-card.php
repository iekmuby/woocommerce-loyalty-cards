<?php
/*
Plugin Name: Woocommerce Loyalty Cards
Description: Allow users use loyalty card codes
Text-domain: sha-wlc
Domain Path: /languages
Version: 1.0
Author: Andrew Shulgin
License: MIT
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

//Start session, if isn't startedse
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Variables
$sha_wlc_module_slug = 'sha-wlc';
$sha_wlc_prefix = 'sha_wlc_';
$sha_wlc_plugin_dir = plugin_dir_path( __FILE__ );

//* Custom functions *//

//* End custom functions *//

//*	Admin area *//
// Create new post type
function sha_wlc_create_post_type() {
	
	global $sha_wlc_module_slug, $sha_wlc_prefix;
	
	register_post_type( $sha_wlc_module_slug,
		array(
			'labels' 				=>	array(
				'name'					=> __( 'Loyalty cards', 'sha-wlc' ),
				'all_items'				=> __( 'All loyalty cards', 'sha-wlc' ),
				'singular_name' 		=> __( 'Loyalty card', 'sha-wlc' ),
				'add_new'				=> __( 'Add New Card', 'sha-wlc' ),
				'add_new_item'			=> __( 'Add New Card', 'sha-wlc' ),
				'edit'					=> __( 'Edit Card', 'sha-wlc' ),
				'edit_item'				=> __( 'Edit Card', 'sha-wlc' ),
				'new_item'				=> __( 'New Card', 'sha-wlc' ),
				'view'					=> __( 'View Card', 'sha-wlc' ),
				'view_item'				=> __( 'View Card', 'sha-wlc' ),
				'search_items'			=> __( 'Search Cards', 'sha-wlc' ),
				'not_found'				=> __( 'No Cards found', 'sha-wlc' ),
				'not_found_in_trash'	=> __( 'No Cards found in Trash', 'sha-wlc' ),
				'parent'				=> __( 'Parent Card', 'sha-wlc' ),
			),
			'public' 				=>	true,
			'publicly_queryable'	=>	false,
			'menu_position'			=>	29,
			'supports'				=>	array(
				'title',
			),
			'menu_icon'				=> 'dashicons-tickets-alt',
			'has_archive'			=> false
		)
	);
	
}
add_action( 'init', 'sha_wlc_create_post_type' );

// Load plugin text-domain
function sha_wlc_load_plugin_textdomain() {
	
    load_plugin_textdomain( 'sha-wlc', FALSE, basename( dirname( __FILE__ ) ) . '/languages/' );
    
}
add_action( 'plugins_loaded', 'sha_wlc_load_plugin_textdomain' );


// Change 'Enter title here for cpt
function sha_wlc_change_enter_title( $input ) {

    global $sha_wlc_module_slug, $post_type;

    if( is_admin() && 'Enter title here' == $input && $sha_wlc_module_slug == $post_type ) {
        return __( 'Enter Loyalty Card number', 'sha-wlc' );
	}
	
    return $input;
}
add_filter('gettext','sha_wlc_change_enter_title');

// Add user id field to new post type form
function sha_wlc_add_card_metabox( $item_data ) {
	
	global $sha_wlc_module_slug, $sha_wlc_prefix, $sha_wlc_plugin_dir;
	
	$user_id = (int)get_post_meta( $item_data->ID, $sha_wlc_prefix . 'user_id', true );
	$discount = (int)get_post_meta( $item_data->ID, $sha_wlc_prefix . 'discount', true );

	require_once $sha_wlc_plugin_dir . 'admin/templates/card_metabox.php';
	
}

// Show discount amount and user id metaboxes on cpt edit page
function sha_wlc_add_meta_fields() {

	global $sha_wlc_module_slug;

	add_meta_box(
		'sha-wlc-card-metbox',
		__( 'Card data', 'sha-wlc' ),
		'sha_wlc_add_card_metabox',
		$sha_wlc_module_slug,
		'normal',
		'high'
	);
	
}
add_action( 'admin_init', 'sha_wlc_add_meta_fields' );

// Enqueue admin styles and scripts
function sha_wlc_enqueue_styles( $hook ) {
    
    global $sha_wlc_module_slug, $post_type;
    
    $hooked_pages = array(
		'post-new.php',
		'post.php',
		'edit.php',
		'sha-wlc_page_import'
	);
	
	$hooked_slugs = array(
		'sha-wlc_page_import',
		$sha_wlc_module_slug
	);
		
		
	$post_type = !empty( $post_type ) ? $post_type : $_GET['post_type'];

	if ( ( in_array( $hook, $hooked_pages ) && ( in_array( $post_type, $hooked_slugs ) ) ) ) {
		wp_enqueue_script( 'sha-wlc-scripts', plugins_url( '/admin/js/scripts.js', __FILE__ ), array( 'jquery' ), false, true );
		wp_enqueue_script( 'jquery-mask', plugins_url( '/admin/js/jquery.mask/jquery.mask.js', __FILE__ ), array( 'jquery' ), false, true );
		wp_enqueue_style( 'sha-wlc-admin-css', plugins_url( '/admin/css/admin-styles.css', __FILE__ ) );
	}
}
add_action( 'admin_enqueue_scripts', 'sha_wlc_enqueue_styles' );

// Save/update meta fields
add_action( 'save_post', 'sha_wlc_save_update', 10, 2 );

function sha_wlc_save_update( $item_id, $item_data ) {
	global $sha_wlc_module_slug, $sha_wlc_prefix;
	
	if ( $item_data->post_type == $sha_wlc_module_slug ) {
		
		// Update user id
		if ( isset( $_POST[ $sha_wlc_prefix . 'user_id' ] ) ) {
			update_post_meta( $item_id, $sha_wlc_prefix . 'user_id', (int)$_POST[ $sha_wlc_prefix . 'user_id' ] );
		}
		
		// Update discount amount
		if ( isset( $_POST[ $sha_wlc_prefix . 'discount' ] ) ) {
			update_post_meta( $item_id, $sha_wlc_prefix . 'discount', (int)$_POST[ $sha_wlc_prefix . 'discount' ] );
		}
		
		//Update card id
		if ( isset( $_POST['post_title'] ) ) {
			update_post_meta( $item_id, $sha_wlc_prefix . 'card', sanitize_title_for_query( $_POST['post_title'] ) );
		}
	}
}

// Add discount amount and user id content to admin grid
function sha_wlc_add_content_to_admin_grid($column_name, $post_ID) {
    global $sha_wlc_prefix;
        
    // Show user id value
    if ($column_name == $sha_wlc_prefix . 'user_id') {
		echo get_post_meta( $post_ID , $sha_wlc_prefix . 'user_id', true );
    }
    
    // Show discount value
	if ($column_name == $sha_wlc_prefix . 'discount') {
		echo get_post_meta( $post_ID , $sha_wlc_prefix . 'discount', true );
    }
}
add_action( 'manage_' . $sha_wlc_module_slug . '_posts_custom_column', 'sha_wlc_add_content_to_admin_grid', 10, 2);

// Add discount amount and user id fileds to admin grid
function sha_wlc_add_colums_to_admin_grid( $defaults ) {
	global $sha_wlc_prefix;
    $defaults[ $sha_wlc_prefix . 'user_id' ] = __('User ID');
    $defaults[ $sha_wlc_prefix . 'discount' ] = __('Discount (%)');
    
    return $defaults;
}
add_filter( 'manage_' . $sha_wlc_module_slug . '_posts_columns', 'sha_wlc_add_colums_to_admin_grid');

// Add options pages to admin menu
function sha_wlc_create_admin_menu() {
	
	global $sha_wlc_module_slug;
	
	add_submenu_page(
		'edit.php?post_type=' . $sha_wlc_module_slug,
		'Import/Export',
		'Import/Export',
		'manage_options',
		'import_page',
		'sha_wlc_import_page_html'
	); 

	// call register settings function
	add_action( 'admin_init', 'sha_wlc_register_import_settings' );
	
}
add_action( 'admin_menu', 'sha_wlc_create_admin_menu' );

// Import/export page html
function sha_wlc_import_page_html() {
	
	global $sha_wlc_plugin_dir, $sha_wlc_prefix, $sha_wlc_module_slug;
	
	$action = isset( $_POST['action'] ) ? trim( $_POST['action'] ) : '';
	
	switch ( $action ) {
		
		// Import cards from CSV
		case 'import':
			if ( isset( $_FILES[ $sha_wlc_prefix . 'import_csv' ] ) && ( $_FILES[ $sha_wlc_prefix . 'import_csv' ]['error'] == 0 ) ) {
				$row = 1;
				if ( ( $handle = fopen( $_FILES[ $sha_wlc_prefix . 'import_csv' ]['tmp_name'], "r" ) ) !== FALSE ) {
					while ( ( $data = fgetcsv( $handle, 1000, "," ) ) !== FALSE ) {
						$row++;
						if ( $row > 2 ) {
							list( $card_id, $card_number, $user_id, , $discount ) = $data;
							
							// If card exists, update. If not, insert new
							$post = get_post( $card_id );
							if ( isset( $post->post_type ) && ( $post->post_type == $sha_wlc_module_slug ) ) {
								$data = array(
									'ID'           => $card_id,
									'post_title'   => $card_number,
								);

								wp_update_post( $data );
								
								// Update defined user ID
								update_post_meta( $card_id, $sha_wlc_prefix . 'user_id', $user_id );
								
								// Update discount
								update_post_meta( $card_id, $sha_wlc_prefix . 'discount', $discount );
							} else {
								$data = array(
									'post_title'	=> $card_number,
									'post_type'		=> $sha_wlc_module_slug,
									'post_status'	=> 'publish'
								);

								$card_id = wp_insert_post( $data );
								
								// Update defined user ID
								update_post_meta( $card_id, $sha_wlc_prefix . 'user_id', $user_id );
								
								// Update discount
								update_post_meta( $card_id, $sha_wlc_prefix . 'discount', $discount );
							}
						}
					}
					fclose($handle);
				}
				$_SESSION['sha-wlc'] = array(
					'class'		=> 'notice-success',
					'message'	=> 'Cards successfully imported'
				);
			}
		break;
		
		// Export cards as CSV
		case 'export':			
			
			ob_clean();
			
			$output = fopen( 'php://output', 'w' );
			
			fputcsv( $output,
				array(
					'ID',
					'Card number',
					'User ID',
					'User email',
					'Discount'
				)
			);
			
			// Get all existing cards
			$cards = get_posts(
				array(
					'post_type'			=> $sha_wlc_module_slug,
					'posts_per_page'	=> -1,
				)
			);
			
			foreach ( $cards as $card ) {
				$post_meta = get_post_meta( $card->ID );
				$user_id = (int)$post_meta[ $sha_wlc_prefix . 'user_id' ][0];
				$user_discount = (int)$post_meta[ $sha_wlc_prefix . 'discount' ][0];
				$user_email = '';
				
				if ( $user_id > 0 ) {
					$user_data = get_userdata( $user_id );
					$user_email = $user_data->user_email;
				}
				
				$row = array( 
					$card->ID,
					$card->post_title,
					$user_id,
					$user_email,
					$user_discount
				);
				
				fputcsv($output, $row);
			}

			header( 'Content-Type: text/csv; charset=utf-8' );
			header( 'Content-Disposition: attachment; filename=' . sprintf( 'export_cards_%s.csv', date( 'd-m-Y' ) ) );
			header( 'Pragma: no-cache' );
			header( 'Expires: 0' );
			exit();

		break;
	}
	
	require_once $sha_wlc_plugin_dir . 'admin/templates/import_page.php';
	unset( $_SESSION['sha-wlc'] );
	
}

// Add checkbox to product, which allow exclude products from loaylty card program
// Add a loyalty card product tab.
function sha_wlc_loyaltycard_tab( $tabs ) {
	
	$tabs['loyaltycard'] = array(
		'label'		=> __( 'Loyalty card', 'woocommerce' ),
		'priority'	=> 1000,
		'target'	=> 'loyaltycard_options',
		'class'		=> array( 'show_if_simple', 'show_if_variable'  ),
	);
	
	return $tabs;
}
add_filter( 'woocommerce_product_data_tabs', 'sha_wlc_loyaltycard_tab' );

// HTML of the loyalty card product tab
function sha_wlc_loyaltycard_options_product_tab_content() {
	global $sha_wlc_plugin_dir, $post;
	
	require_once $sha_wlc_plugin_dir . 'admin/templates/card_checkbox.php';
}
add_action( 'woocommerce_product_data_panels', 'sha_wlc_loyaltycard_options_product_tab_content' );

// Process loyalty card checkbox value on save
function sha_wlc_save_loyaltycard_options_fields( $post_id ) {
	
	$allow_personal_message = isset( $_POST['_disallow_loyaltycard'] ) ? 'yes' : 'no';
	update_post_meta( $post_id, '_disallow_loyaltycard', $allow_personal_message );
}

add_action( 'woocommerce_process_product_meta_simple', 'sha_wlc_save_loyaltycard_options_fields'  );
add_action( 'woocommerce_process_product_meta_variable', 'sha_wlc_save_loyaltycard_options_fields'  );

// Ajax processor for loyalty card
function sha_wlc_loyalty_card_ajax() {
	global $sha_wlc_prefix, $sha_wlc_module_slug;
	
	// Check security key before
	check_ajax_referer( 'sha_wlc_nonce', 'security' );
	
	$sha_wlc_loyalty_card_data = WC()->session->get( $sha_wlc_prefix . 'data' );
	$sha_wlc_loyalty_card_data = is_array( $sha_wlc_loyalty_card_data ) ? $sha_wlc_loyalty_card_data : array();
	
	// Add loyalty card
	if ( $_POST['act'] == 'add_card' ) {
		if ( isset( $_POST['card'] ) && !empty( $_POST['card'] ) ) {
			
			// If user try to add loyalty card discount when coupon applied, deny
			$applied_coupons = WC()->cart->applied_coupons;
			
			if ( count( $applied_coupons ) > 0 ) {
				$sha_wlc_loyalty_card_data['type'] = 'notice';
				$sha_wlc_loyalty_card_data['message'] = __( 'You can not apply loyalty discount with any other discounts.', 'sha-wlc' );
			} else {
				// Search for posts with given card number and user id (but not 0)
				$current_user_id = get_current_user_id();
				$args = array(
					'title'				=> trim( $_POST['card'] ),
					'post_type'			=> $sha_wlc_module_slug,
					'post_status'		=> 'publish',
					'posts_per_page'	=> 1,
					'meta_query' => array(
						array(
							'key' 	=> $sha_wlc_prefix . 'user_id',
							'value'	=> $current_user_id
						),
						array(
							'key'		=> $sha_wlc_prefix . 'user_id',
							'value'		=> 0,
							'compare'	=> '!='
						)
					)
				);
				
				$query = new WP_Query($args);
				// If card exist, save to WC session
				if ( $query->have_posts() ) {
					$query->the_post();

					$card_id = get_the_ID();
					$loyalty_card_meta = get_post_meta( $card_id );
					$loyalty_card_discount = (float)$loyalty_card_meta[ $sha_wlc_prefix . 'discount' ][0];
					$loyalty_card_user_id = (int)$loyalty_card_meta[ $sha_wlc_prefix . 'user_id' ][0];

					wp_reset_postdata();

					$sha_wlc_loyalty_card_data = array(
						'type'		=> 'success',
						'discount'	=> $loyalty_card_discount,
						'message'	=> __( 'Loyalty card discount applied sucessfully ', 'sha-wlc' ),
						'card_id'	=> $card_id
					);
				} else {
					$sha_wlc_loyalty_card_data['type']		= 'error';
					$sha_wlc_loyalty_card_data['message']	= __( 'This loyalty card number is not valid', 'sha-wlc' );
				}
			}
		} else {
			$sha_wlc_loyalty_card_data['type']		= 'error';
			$sha_wlc_loyalty_card_data['message']	= __( 'Loyalty card number can not be empty', 'sha-wlc' );
		}
	}
	
	// Remove loyalty card
	if ( $_POST['act'] == 'remove_card' ) {
		$sha_wlc_loyalty_card_data = array(
			'type'		=> 'success',
			'message'	=> __( 'Loyalty card was successfuly removed', 'sha-wlc' )
		);
	}
	
	// Save loyalty card data into Woocommerce session
	WC()->session->set( $sha_wlc_prefix . 'data', $sha_wlc_loyalty_card_data );
	
	header( 'Content-Type: application/json' );
	die( json_encode( array( 'status' => 'success' ), JSON_UNESCAPED_UNICODE ) );
	
}
add_action( 'wp_ajax_loyalty_card_ajax', 'sha_wlc_loyalty_card_ajax' );
add_action( 'wp_ajax_nopriv_loyalty_card_ajax', 'sha_wlc_loyalty_card_ajax' );

// Add loyalty discount to total
function sha_wlc_add_loyalty_discount_to_total() {
	global $sha_wlc_prefix;

	$sha_wlc_loyalty_card_data = WC()->session->get( $sha_wlc_prefix . 'data' );

	if ( !empty( $sha_wlc_loyalty_card_data ) ) {
		// Add discount to total, if set
		if ( isset( $sha_wlc_loyalty_card_data['discount'] ) ) {
			$discounted_products_total = 0;
			foreach( WC()->cart->get_cart() as $cart_item ) {
				$exclude_from_discount_calculation = get_post_meta( $cart_item['product_id'], '_disallow_loyaltycard', true );
				if ( $exclude_from_discount_calculation != 'yes' ) {
					$discounted_products_total += (float)$cart_item['line_total'];
				}
			}
			$discount = $sha_wlc_loyalty_card_data['discount'];
			$calculated_discount = ( $discounted_products_total / 100 ) * $discount;
			WC()->cart->add_fee( sprintf( __( 'Loyalty card discount (%s%%)', 'sha-wlc' ), $discount ), (float)( $calculated_discount * -1 ) );
		}
		
		// Show notice and remove it from session
		if ( isset( $sha_wlc_loyalty_card_data['message'] ) ) {
			// Prevent duplication of notices with the same text
			$notices = wc_get_notices( $sha_wlc_loyalty_card_data['type'] );
			$notice_already_exist = 0;
			foreach ( $notices as $notice ) {
				if ( $notice == $sha_wlc_loyalty_card_data['message'] ) {
					$notice_already_exist = 1;
				}
			}

			// Add notice only if not exist
			if ( $notice_already_exist == 0 ) {
				wc_set_notices();
				wc_add_notice( __( $sha_wlc_loyalty_card_data['message'], 'sha-wlc' ), $sha_wlc_loyalty_card_data['type'] );
			}
			unset( $sha_wlc_loyalty_card_data['type'] );
			unset( $sha_wlc_loyalty_card_data['message'] );

			// Override WC session data for loyalty card
			WC()->session->set( $sha_wlc_prefix . 'data', $sha_wlc_loyalty_card_data );
		}
	}
}
add_action( 'woocommerce_cart_calculate_fees','sha_wlc_add_loyalty_discount_to_total' );

// Show notice, if user try to apply coupon with applied loyalty card
function sha_wlc_disallow_card_discount_if_has_coupon( $valid ){
	global $sha_wlc_prefix;
	$sha_wlc_loyalty_card_data = WC()->session->get( $sha_wlc_prefix . 'data' );
	if ( isset( $sha_wlc_loyalty_card_data['discount'] ) ) {
		add_filter( 'woocommerce_coupon_error', 'sha_wlc_show_coupon_disalow_message' );
		$valid = false;
	}
	return $valid ; 
}
add_filter( 'woocommerce_coupon_is_valid', 'sha_wlc_disallow_card_discount_if_has_coupon', 10, 1 );

// Show message, if user try to apply card whit applied coupon
function sha_wlc_show_coupon_disalow_message( $err ) {
	return __( 'You can not apply loyalty card with any other discounts', 'sha-wlc' );
}

// Prevent add card with the same number
function sha_wlc_prevent_card_duplication( $maybe_empty, $postarr ) {
    global $sha_wlc_module_slug;
	if ( ( $postarr['post_type'] == $sha_wlc_module_slug ) && ( !in_array( $postarr['post_status'], array( 'draft', 'auto-draft' ) ) ) && ( isset( $_POST['post_title'] ) ) ) {
		
		// Search for the card with same number and publish status
		$same_number_card = get_page_by_title( trim( $_POST['post_title'] ), ARRAY_A, $sha_wlc_module_slug );
		if ( isset( $same_number_card['post_status'] ) && ( $same_number_card['post_status'] == 'publish' ) && ( $_POST['post_ID'] != $same_number_card['ID'] ) ) {
			// Register notice
			$_SESSION[ 'sha_wlc' ] = __( 'This card is already exists', 'sha-wlc' );
			return true;
		}
	}
    
    return false;
}
add_filter( 'wp_insert_post_empty_content', 'sha_wlc_prevent_card_duplication', PHP_INT_MAX -1, 2 );

// Show admin notice
function sha_wlc_replace_admin_messages( $messages ) {
	if ( $screen->id == $sha_wlc_module_slug ) {
		if ( isset( $_SESSION[ 'sha_wlc' ] ) ) {
			$messages['post'][6] = $_SESSION[ 'sha_wlc' ];
			unset( $_SESSION[ 'sha_wlc' ] );
		}
	}
	return $messages;
}

add_filter( 'post_updated_messages', 'sha_wlc_replace_admin_messages', 10, 1 );

//Clear loyalty card data when order placed
function sha_wlc_clear_session( $order_id ) {
	global $sha_wlc_prefix;
	WC()->session->__unset( $sha_wlc_prefix . 'data' );
}
add_action( 'woocommerce_checkout_order_processed', 'sha_wlc_clear_session', 10, 1 );
//* End admin area *//

//* Begin frontend area *//
// Enqueue css and js for cart and checkout page only
function sha_wlc_enqueue_scripts() {
	
	if ( function_exists( 'is_cart' ) ) {
	
		if ( is_cart() || is_checkout() ) {
			wp_enqueue_style( 'sha-wlc-frontend-css', plugins_url( '/frontend/css/styles.css', __FILE__ ), array(), '1.0.0', 'all' );
			wp_enqueue_script( 'sha-wlc-frontend-js',  plugins_url( '/frontend/js/scripts.js', __FILE__ ), array( 'jquery' ), false, true );
			wp_enqueue_script( 'jquery-mask', plugins_url( '/admin/js/jquery.mask/jquery.mask.js', __FILE__ ), array( 'jquery' ), false, true );
		}
	}
}
add_action( 'wp_enqueue_scripts', 'sha_wlc_enqueue_scripts' );

// Output loyalty card input field
function sha_wlc_loyalty_card_field() {
	global $sha_wlc_plugin_dir, $sha_wlc_prefix;
	$upload_dir = wp_upload_dir();
	$sha_wlc_nonce = wp_create_nonce( 'sha_wlc_nonce' );
	
	require_once $sha_wlc_plugin_dir . 'frontend/templates/loyalty_input.php';
}
// Add loyalty card input field to cart page
add_action('woocommerce_cart_collaterals', 'sha_wlc_loyalty_card_field');
// Add loyalty card input field to checkout page
add_action( 'woocommerce_before_checkout_form', 'sha_wlc_loyalty_card_field', 20, 1 );

// Add remove card link
function sha_wlc_add_remove_discount_link( $data ) {
	return $data . ' <a href="javascript:;" onclick="removeLoyaltyCard();">[remove]</a>';
}

add_filter( 'woocommerce_cart_totals_fee_html', 'sha_wlc_add_remove_discount_link' );

//* End frontend area *//