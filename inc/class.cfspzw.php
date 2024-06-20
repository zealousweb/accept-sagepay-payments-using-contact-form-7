<?php
/**
* CFSPZW Class
*
* Handles the plugin functionality.
*
* @package WordPress
* @subpackage Accept Sagepay Payments Using Contact Form 7
* @since 1.2
*/

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'CFSPZW' ) ) {

	include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	
	/**
	* The main CFSPZW class
	*/
	class CFSPZW {

		private static $_instance = null;

		var $admin = null,
			$front = null,
			$lib   = null;

		public static function instance() {

			if ( is_null( self::$_instance ) )
				self::$_instance = new self();

			return self::$_instance;
		}

		function __construct() {
			
			// Action to load plugin text domain
			add_action( 'plugins_loaded', array( $this, 'action__cfspzw_plugins_loaded' ), 1 );
		}

		/**
		* Action: plugins_loaded
		*
		* @return [type] [description]
		*/
		function action__cfspzw_plugins_loaded() {

			if ( !is_plugin_active( 'contact-form-7/wp-contact-form-7.php' ) ) {
				// Check contact form 7 is active or not and respectively message display.
				add_action( 'admin_notices', array( $this, 'action__cfspzw_admin_notices_deactive' ) );
				deactivate_plugins( CFSPZW_PLUGIN_BASENAME );
				if ( isset( $_GET['activate'] ) ) {
					unset( $_GET['activate'] );
				}
			}

			if ( is_plugin_active( 'contact-form-7/wp-contact-form-7.php' ) ) {				

				add_action( 'init', array( $this, 'action__cfspzw_init' ) );

				global $wp_version;

				// Set filter for plugin's languages directory
				$cfspzw_lang_dir = dirname( CFSPZW_PLUGIN_BASENAME ) . '/languages/';
				$cfspzw_lang_dir = apply_filters( 'cfspzw_languages_directory', $cfspzw_lang_dir );

				// Traditional WordPress plugin locale filter.
				$get_locale = get_locale();

				if ( $wp_version >= 4.7 ) {
					$get_locale = get_user_locale();
				}

				// Traditional WordPress plugin locale filter
				$locale = apply_filters( 'plugin_locale',  $get_locale, 'accept-sagepay-payments-using-contact-form-7' );
				$mofile = sprintf( '%1$s-%2$s.mo', 'accept-sagepay-payments-using-contact-form-7', $locale );

				// Setup paths to current locale file
				$mofile_global = WP_LANG_DIR . '/plugins/' . basename( CFSPZW_DIR ) . '/' . $mofile;

				if ( file_exists( $mofile_global ) ) {
					// Look in global /wp-content/languages/plugin-name folder
					load_textdomain( 'accept-sagepay-payments-using-contact-form-7', $mofile_global );
				} else {
					// Load the default language files
					load_plugin_textdomain( 'accept-sagepay-payments-using-contact-form-7', false, $cfspzw_lang_dir );
				}
			}
		}

		/**
		* Register Post type and load in admin for payment
		*/
		function action__cfspzw_init() {

			/* Initialize backend tags*/
			add_action('wpcf7_admin_init', array( $this, 'action__cfspzw_admin_init' ), 15, 0 );
			add_rewrite_rule( '^cfspzw-phpinfo(/(.*))?/?$', 'index.php?cfspzw-phpinfo=$matches[2]', 'top' );
			flush_rewrite_rules(); //phpcs:ignore

			/**
			* Post Type: sagepay Add-on.
			*/

			$labels = array(
				'name' 			=> __( 'Sagepay Payment Details', 'accept-sagepay-payments-using-contact-form-7' ),
				'singular_name' => __( 'Sagepay Payment Details', 'accept-sagepay-payments-using-contact-form-7' ),
				'edit_item'		=> __( 'Transaction Detail', 'accept-sagepay-payments-using-contact-form-7' ),
			);

			$args = array(
				'label' => __( 'Sagepay Payment Details', 'accept-sagepay-payments-using-contact-form-7' ),
				'labels' => $labels,
				'description' => '',
				'public' => false,
				'publicly_queryable' => false,
				'show_ui' => true,
				'delete_with_user' => false,
				'show_in_rest' => false,
				'rest_base' => '',
				'has_archive' => false,
				'show_in_menu' => 'wpcf7',
				'show_in_nav_menus' => false,
				'exclude_from_search' => true,
				'capability_type' => 'post',
				'capabilities' => array(
					'read' => true,
					'create_posts'  => false,
					'publish_posts' => false,
				),
				'map_meta_cap' => true,
				'hierarchical' => false,
				'rewrite' => false,
				'query_var' => false,
				'supports' => array( 'title' ),
			);

			register_post_type( 'cfspzw_data', $args );

		}
		
		/**
		* CF7 plugin required error
		*
		* @method action__cfspzw_admin_notices_deactive
		*
		* @return  string 
		*/
		function action__cfspzw_admin_notices_deactive() {
			echo '<div class="error">' .				
					sprintf(
						__( '<strong><a href="https://wordpress.org/plugins/contact-form-7/" target="_blank">Contact Form 7</a></strong> is required to use <strong>%s</strong>.', 'accept-sagepay-payments-using-contact-form-7' ),
						'Accept Sagepay Payments using Contact Form 7'
					) .
			'</div>';
		}


		/**
		* Gnerare Tag Callback function load option which want to display
		*
		* @method action__cfspzw_admin_init
		*
		* @return  html 
		*/
		function action__cfspzw_admin_init() {

			$tag_generator = WPCF7_TagGenerator::get_instance();
			$tag_generator->add(
				'sagepay_country',
				esc_html__( 'Sagepay Country', 'accept-sagepay-payments-using-contact-form-7' ),
				array( $this, 'wpcf7_sagepay_country_tag_generator_checkout' )
			);
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
		* Render CF7 Shortcode settings into backend.
		*
		* @method wpcf7_sagepay_country_tag_generator_checkout
		*
		* @param  object $contact_form
		* @param  array  $args
		*/
		function wpcf7_sagepay_country_tag_generator_checkout( $contact_form, $args = '' ) {

			$args = wp_parse_args( $args, array() );
			$type = $args['id'];

			$description = __( "Generate a form-tag for to display Sagepay Country", 'accept-sagepay-payments-using-contact-form-7' );
			?>
			<div class="control-box">
				<fieldset>
					<legend><?php echo esc_html( $description ); ?></legend>

					<table class="form-table">
						<tbody>
							<tr>
							<th scope="row"><label for="<?php echo esc_attr( $args['content'] . '-name' ); ?>"><?php echo esc_html( __( 'Name', 'accept-sagepay-payments-using-contact-form-7' ) ); ?></label></th>
							<td>
								<legend class="screen-reader-text"><input type="checkbox" name="required" value="on" checked="checked" /></legend>
								<input type="text" name="name" class="tg-name oneline" id="<?php echo esc_attr( $args['content'] . '-name' ); ?>" /></td>
							</tr>
						</tbody>
					</table>

				</fieldset>
			</div>

			<div class="insert-box">
				<input type="text" name="<?php echo esc_attr($type); ?>" class="tag code" readonly="readonly" onfocus="this.select()" />

				<div class="submitbox">
					<input type="button" class="button button-primary insert-tag" value="<?php echo esc_attr( __( 'Insert Tag', 'accept-sagepay-payments-using-contact-form-7' ) ); ?>" />
				</div>

				<br class="clear" />

				<p class="description mail-tag">
					<label for="<?php echo esc_attr( $args['content'] . '-mailtag' ); ?>">
						<?php echo sprintf( esc_html( __( "To use the value input through this field in a mail field, you need to insert the corresponding mail-tag (%s) into the field on the Mail tab.", 'accept-sagepay-payments-using-contact-form-7' ) ), '<strong><span class="mail-tag"></span></strong>' ); ?><input type="text" class="mail-tag code hidden" readonly="readonly" id="<?php echo esc_attr( $args['content'] . '-mailtag' ); ?>" />
					</label>
				</p>
			</div>
			<?php
		}

	}
}

function CFSPZW() {
	return CFSPZW::instance();
}

CFSPZW();
