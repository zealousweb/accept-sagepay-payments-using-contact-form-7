<?php
/**
* CFSPZW_Admin_Filter Class
*
* Handles the admin functionality.
*
* @package WordPress
* @subpackage Accept Sagepay Payments Using Contact Form 7
* @since 1.0
*/

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'CFSPZW_Admin_Filter' ) ) {

	/**
	*  The CFSPZW_Admin_Filter Class
	*/
	class CFSPZW_Admin_Filter {

		function __construct() {

			// Adding Sagepay setting tab
			add_filter( 'wpcf7_editor_panels',							array( $this, 'filter__cfspzw_wpcf7_editor_panels' ), 10, 3 );
			add_filter( 'post_row_actions',								array( $this, 'filter__cfspzw_post_row_actions' ), 10, 3 );
			add_filter( 'plugin_action_links_'.CFSPZW_PLUGIN_BASENAME,	array( $this,'filter__cfspzw_admin_plugin_links'), 10, 2 );

			add_filter( 'manage_edit-'.CFSPZW_POST_TYPE.'_sortable_columns', 	array( $this, 'filter__cfspzw_manage_data_sortable_columns' ), 10, 3 );
			add_filter( 'manage_'.CFSPZW_POST_TYPE.'_posts_columns',			array( $this, 'filter__cfspzw_manage_data_posts_columns' ), 10, 3 );
			add_filter( 'bulk_actions-edit-'.CFSPZW_POST_TYPE.'',				array( $this, 'filter__cfspzw_bulk_actions_edit_data' ) );

		}

		/*
		######## #### ##       ######## ######## ########   ######
		##        ##  ##          ##    ##       ##     ## ##    ##
		##        ##  ##          ##    ##       ##     ## ##
		######    ##  ##          ##    ######   ########   ######
		##        ##  ##          ##    ##       ##   ##         ##
		##        ##  ##          ##    ##       ##    ##  ##    ##
		##       #### ########    ##    ######## ##     ##  ######
		*/
		/**
		* Sagepay tab
		* Adding tab in contact form 7
		*
		* @param $panels
		*
		* @return array
		*/
		public function filter__cfspzw_wpcf7_editor_panels( $panels ) {

			$panels[ 'sagepay-add-on' ] = array(
				'title'		=> __( 'SagePay', 'accept-sagepay-payments-using-contact-form-7' ),
				'callback'	=> array( $this, 'wpcf7_cfspzw_admin_after_additional_settings' )
			);

			return $panels;
		}

		/**
		* Filter: post_row_actions
		*
		* - Used to modify the post list action buttons.
		*
		* @method filter__cfspzw_post_row_actions
		*
		* @param  array $actions
		*
		* @return array
		*/
		function filter__cfspzw_post_row_actions( $actions ) {

			if ( get_post_type() === CFSPZW_POST_TYPE ) {
				unset( $actions['view'] );
				unset( $actions['inline hide-if-no-js'] );
			}

			return $actions;
		}


		/**
		* Filter: plugin_action_links
		*
		* - Used to add links on Plugins listing page.
		*
		* @method filter__cfspzw_admin_plugin_links
		*
		* @param  array $actions
		*	
		* @return string
		*/
		function filter__cfspzw_admin_plugin_links( $links, $file ) {
			if ( $file != CFSPZW_PLUGIN_BASENAME ) {
				return $links;
			}
		
			if ( ! current_user_can( 'wpcf7_read_contact_forms' ) ) {
				return $links;
			}
			
			$licencePage = admin_url("admin.php?page=cfspzw-license-activation");

			$licencepageLink = '<a  href="'.$licencePage.'">' . __( 'Licensing Page', 'accept-sagepay-payments-using-contact-form-7' ) . '</a>';
			array_unshift( $links , $licencepageLink);

			$documentLink = '<a target="_blank" href="'.CFSPZW_DOCUMENT.'">' . __( 'Document Link', 'accept-sagepay-payments-using-contact-form-7' ) . '</a>';
			array_unshift( $links , $documentLink);

			$supportPageLink = '<a  href="'.CFSPZW_SUPPORT.'">' . __( 'Support Link', 'accept-sagepay-payments-using-contact-form-7' ) . '</a>';
			array_unshift( $links , $supportPageLink);

			return $links;
		}

		/**
		* Filter: manage_edit-cfspzw_data_sortable_columns
		*
		* - Used to add the sortable fields into "cfspzw_data" CPT
		*
		* @method filter__cfspzw_manage_data_sortable_columns
		*
		* @param  array $columns
		*
		* @return array
		*/
		function filter__cfspzw_manage_data_sortable_columns( $columns ) {
			$columns['total'] = '_total';
			return $columns;
		}

		/**
		* Filter: manage_cfspzw_data_posts_columns
		*
		* - Used to add new column fields for the "cfspzw_data" CPT
		*
		* @method filter__cfspzw_manage_data_posts_columns
		*
		* @param  array $columns
		*
		* @return array
		*/
		function filter__cfspzw_manage_data_posts_columns( $columns ) {
			unset( $columns['date'] );
			$columns['user_name']			= __( 'User Name', 'accept-sagepay-payments-using-contact-form-7' );
			$columns['invoice_no']			= __( 'Invoice ID', 'accept-sagepay-payments-using-contact-form-7' );
			$columns['transaction_status']	= __( 'Transaction Status', 'accept-sagepay-payments-using-contact-form-7' );
			$columns['total'] 				= __( 'Total Amount', 'accept-sagepay-payments-using-contact-form-7' );
			$columns['date'] 				= __( 'Submitted Date', 'accept-sagepay-payments-using-contact-form-7' );
			return $columns;
		}

		/**
		* Filter: bulk_actions_edit_data
		*
		* - Add/Remove bulk actions for "cfspzw_data" CPT
		*
		* @method filter__cfspzw_bulk_actions_edit_data
		*
		* @param  array $actions
		*
		* @return array
		*/
		function filter__cfspzw_bulk_actions_edit_data( $actions ) {
			unset( $actions['edit'] );
			return $actions;
		}

		/*
		######## ##     ## ##    ##  ######  ######## ####  #######  ##    ##  ######
		##       ##     ## ###   ## ##    ##    ##     ##  ##     ## ###   ## ##    ##
		##       ##     ## ####  ## ##          ##     ##  ##     ## ####  ## ##
		######   ##     ## ## ## ## ##          ##     ##  ##     ## ## ## ##  ######
		##       ##     ## ##  #### ##          ##     ##  ##     ## ##  ####       ##
		##       ##     ## ##   ### ##    ##    ##     ##  ##     ## ##   ### ##    ##
		##        #######  ##    ##  ######     ##    ####  #######  ##    ##  ######
		*/

		/**
		* Adding Sagepay fields in Sagepay tab
		*
		* @param $cf7
		*/
		public function wpcf7_cfspzw_admin_after_additional_settings( $cf7 ) {

			wp_enqueue_script( CFSPZW_PREFIX . '_admin_js' );

			require_once( CFSPZW_DIR .  '/inc/admin/template/' . CFSPZW_PREFIX . '.template.php' );

		}
	}
}
