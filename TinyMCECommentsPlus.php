<?php
/**
 * TinyMCE Comments Plus
 *
 * @package   tinymce-comments-plus
 * @author    Kentaro Fischer <webdev@kentarofischer.com>
 * @license   GPL-2.0+
 * @link      http://kentarofischer.com
 * @copyright 3-22-2015 Kentaro Fischer
 */

/**
 * TinyMCE Comments Plus class.
 *
 * @package TinyMCECommentsPlus
 * @author  Kentaro Fischer <webdev@kentarofischer.com>
 */
class TinyMCECommentsPlus {
	/**
	 * Plugin version, used for cache-busting of style and script file references.
	 *
	 * @since   1.0.0
	 *
	 * @var     string
	 */
	protected $version = "1.0.0";

	/**
	 * Unique identifier for your plugin.
	 *
	 * Use this value (not the variable name) as the text domain when internationalizing strings of text. It should
	 * match the Text Domain file header in the main plugin file.
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected $plugin_slug = "tinymce-comments-plus";

	/**
	 * Instance of this class.
	 *
	 * @since    1.0.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Slug of the plugin screen.
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected $plugin_screen_hook_suffix = null;

	/**
	 * Public Javascript Global Variables
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	private $tcp_javascript_globals = array();

	/**
	 * Initialize the plugin by setting localization, filters, and administration functions.
	 *
	 * @since     1.0.0
	 */
	private function __construct() {

		define( 'tcp_prefix', 'tcp_' );
		define( tcp_prefix . 'javascript_globals', 'tcpGlobals' );
		define( tcp_prefix . 'ajax_action_prefix', 'ajax_action_' );
		define( tcp_ajax_action_prefix . 'option_update_delay', 2000 );
		define( tcp_ajax_action_prefix . 'add_comment', tcp_prefix . 'add_comment' );
		define( tcp_ajax_action_prefix . 'update_comment', tcp_prefix . 'update_comment' );
		define( tcp_ajax_action_prefix . 'editing_enabled', tcp_prefix . 'editing_enabled' );
		define( tcp_ajax_action_prefix . 'editing_expiration', tcp_prefix . 'editing_expiration' );
		define( tcp_ajax_action_prefix . 'custom_classes_open', tcp_prefix . 'custom_classes_open' );
		define( tcp_ajax_action_prefix . 'wordpress_ids_open', tcp_prefix . 'wordpress_ids_open' );

		define( tcp_prefix . 'buttons1', 'bold,italic,strikethrough,bullist,numlist,blockquote,hr,alignleft,aligncenter,alignright,image,link,unlink,wp_more,spellchecker,wp_adv ' );
		define( tcp_prefix . 'buttons2', 'formatselect,underline,alignjustify,forecolor,pastetext,removeformat,charmap,outdent,indent,undo,redo,wp_help ' );


		$this->tcp_admin_javascript_globals = array(
			'ajaxUrl' => admin_url( 'admin-ajax.php' ),
			'optionUpdateDelay' => ajax_action_option_update_delay,
			'editingEnabledAction' => ajax_action_editing_enabled,
			'editingExpirationAction' => ajax_action_editing_expiration,
			'customClassesOpenAction' => ajax_action_custom_classes_open,
			'wordpressIdsOpenAction' => ajax_action_wordpress_ids_open
		);

		$this->tcp_plugin_javascript_globals = array(
			'ajaxUrl' => admin_url( 'admin-ajax.php' ),
			'optionUpdateDelay' => ajax_action_option_update_delay,
			'addCommentAction' => ajax_action_add_comment,
			'updateCommentAction' => ajax_action_update_comment,
		);

		$this->tcp_plugin_javascript_globals['commentFormSpan'] = '#tcpCommentFormSpan';
		$this->tcp_plugin_javascript_globals['commentsList'] = '#comments';


		// Load plugin text domain
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

		// Add the options page and menu item.
		add_action( 'admin_menu', array( $this, 'add_plugin_admin_menu' ) );

		// Load admin style sheet and JavaScript.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );

		// Load public-facing style sheet and JavaScript.
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		// Ajax methods
		add_action( 'wp_ajax_nopriv_' . ajax_action_add_comment, array( $this, 'action_ajax_request' ) );
		add_action( 'wp_ajax_' . ajax_action_add_comment, array( $this, 'action_ajax_request' ) );
		add_action( 'wp_ajax_nopriv_' . ajax_action_update_comment, array( $this, 'action_ajax_request' ) );
		add_action( 'wp_ajax_' . ajax_action_update_comment, array( $this, 'action_ajax_request' ) );
		add_action( 'wp_ajax_nopriv_' . ajax_action_editing_enabled, array( $this, 'action_ajax_request' ) );
		add_action( 'wp_ajax_' . ajax_action_editing_enabled, array( $this, 'action_ajax_request' ) );
		add_action( 'wp_ajax_nopriv_' . ajax_action_editing_expiration, array( $this, 'action_ajax_request' ) );
		add_action( 'wp_ajax_' . ajax_action_editing_expiration, array( $this, 'action_ajax_request' ) );
		add_action( 'wp_ajax_nopriv_' . ajax_action_custom_classes_open, array( $this, 'action_ajax_request' ) );
		add_action( 'wp_ajax_' . ajax_action_custom_classes_open, array( $this, 'action_ajax_request' ) );
		add_action( 'wp_ajax_nopriv_' . ajax_action_wordpress_ids_open, array( $this, 'action_ajax_request' ) );
		add_action( 'wp_ajax_' . ajax_action_wordpress_ids_open, array( $this, 'action_ajax_request' ) );

		add_action( 'comment_form', array( $this, 'action_comment_form' ), 11 );

		// Define custom functionality.
		add_filter( 'tiny_mce_before_init', array( $this, 'filter_format_tinymce' ), 11 );
		add_filter( 'preprocess_comment', array( $this, 'filter_customize_allowed_tags' ), 11 );
		add_filter( 'comment_form_defaults', array( $this, 'filter_comment_form_defaults' ), 11 );
		add_filter( 'comment_form_field_comment', array( $this, 'filter_tinymce_editor' ), 11 );
		add_filter( 'comment_reply_link_args', array( $this, 'filter_comment_reply_link_args' ), 10, 3 );
		add_filter( 'comment_text', array( $this, 'filter_comment_editing' ), 11, 2 );
	}

	/**
	 * Return an instance of this class.
	 *
	 * @since     1.0.0
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {

		// If the single instance hasn"t been set, set it now.
		if (null == self::$instance) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Fired when the plugin is activated.
	 *
	 * @since    1.0.0
	 *
	 * @param    boolean $network_wide    True if WPMU superadmin uses "Network Activate" action, false if WPMU is disabled or plugin is activated on an individual blog.
	 */
	public static function activate($network_wide) {
		// TODO: Define activation functionality here
	}

	/**
	 * Fired when the plugin is deactivated.
	 *
	 * @since    1.0.0
	 *
	 * @param    boolean $network_wide    True if WPMU superadmin uses "Network Deactivate" action, false if WPMU is disabled or plugin is deactivated on an individual blog.
	 */
	public static function deactivate($network_wide) {
		// TODO: Define deactivation functionality here
	}

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		$domain = $this->plugin_slug;
		$locale = apply_filters("plugin_locale", get_locale(), $domain);

		load_textdomain($domain, WP_LANG_DIR . "/" . $domain . "/" . $domain . "-" . $locale . ".mo");
		load_plugin_textdomain($domain, false, dirname(plugin_basename(__FILE__)) . "/lang/");
	}

	/**
	 * Register and enqueue admin-specific style sheet.
	 *
	 * @since     1.0.0
	 *
	 * @return    null    Return early if no settings page is registered.
	 */
	public function enqueue_admin_styles() {

		if (!isset($this->plugin_screen_hook_suffix)) {
			return;
		}

		$screen = get_current_screen();
		if ($screen->id == $this->plugin_screen_hook_suffix) {
			wp_enqueue_style( $this->plugin_slug . "-admin-styles", plugins_url( "css/" . $this->plugin_slug . "-admin.css", __FILE__ ), array(), $this->version );
		}

	}

	/**
	 * Register and enqueue admin-specific JavaScript.
	 *
	 * @since     1.0.0
	 *
	 * @return    null    Return early if no settings page is registered.
	 */
	public function enqueue_admin_scripts() {

		if (!isset($this->plugin_screen_hook_suffix)) {
			return;
		}

		$screen = get_current_screen();
		if ( $screen->id == $this->plugin_screen_hook_suffix ) {
			wp_enqueue_script( 'jquery-ui-core', array( 'jquery' ) );
			wp_enqueue_script( 'jquery-ui-spinner', array( 'jquery-ui-core' ) );
			wp_enqueue_script( $this->plugin_slug . "-humanize-duration", plugins_url( "js/humanize-duration.js", __FILE__) );
			wp_enqueue_script( $this->plugin_slug . "-livereload", "http://localhost:35729/livereload.js" );
			wp_enqueue_script( $this->plugin_slug . "-admin-script", plugins_url( "js/tinymce-comments-plus-admin.js", __FILE__), array( 'jquery', 'backbone', 'underscore' ), $this->version );

			wp_localize_script( $this->plugin_slug . '-admin-script', tcp_javascript_globals, json_encode( $this->tcp_admin_javascript_globals ) );
		}

	}

	/**
	 * Register and enqueue public-facing style sheet.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		global $wp_version;
		wp_enqueue_style( $this->plugin_slug . "-plugin-styles", plugins_url( "css/" . $this->plugin_slug . ".css", __FILE__ ) );
		wp_enqueue_style( $this->plugin_slug . "-dashicons-css", includes_url( "css/dashicons.min.css", __FILE__ ) );
		wp_enqueue_style( $this->plugin_slug . "-editor-buttons-css", includes_url( "css/editor.min.css", __FILE__ ) );
	}

	/**
	 * Register and enqueues public-facing JavaScript files.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( $this->plugin_slug . "-livereload", "http://localhost:35729/livereload.js", array() );
		wp_enqueue_script( $this->plugin_slug . "-plugin-script", plugins_url( "js/" . $this->plugin_slug . ".js", __FILE__ ), array( 'jquery', 'backbone', 'underscore' ),	$this->version );
		// Instantiate Javascript Globals for plugin script
		wp_localize_script( $this->plugin_slug . '-plugin-script', tcp_javascript_globals, json_encode( $this->tcp_plugin_javascript_globals ) );
	}

	/**
	 * Register the administration menu for this plugin into the WordPress Dashboard menu.
	 *
	 * @since    1.0.0
	 */
	public function add_plugin_admin_menu() {
		$this->plugin_screen_hook_suffix = add_options_page( __( "TinyMCE Comments Plus - Settings", $this->plugin_slug ),
			__( "TinyMCE Comments Plus", $this->plugin_slug ), "read", $this->plugin_slug, array( $this, "display_plugin_admin_page" ) );
	}

	/**
	 * Render the settings page for this plugin.
	 *
	 * @since    1.0.0
	 */
	public function display_plugin_admin_page() {
		include_once("views/admin.php");
	}


	/**
	* check if current user can edit comment
	* @since    1.0.0
	*/
	public function user_can_edit( $comment_user_id ) {
		global $current_user;
		if ( ! $current_user ) { get_currentuserinfo(); }
		$can_edit = current_user_can( 'moderate_comments' );

		// if user can moderate comments (admin) then user can edit
		if ( $can_edit ) { return true; }
		// else if user is comment author then user can edit
		else if ( $comment_user_id == $current_user->ID ) { return true; }
		// else user cannot edit
		else { return false; }
	}



	/**
	 * NOTE:  Actions are points in the execution of a page or process
	 *        lifecycle that WordPress fires.
	 *
	 *        WordPress Actions: http://codex.wordpress.org/Plugin_API#Actions
	 *        Action Reference:  http://codex.wordpress.org/Plugin_API/Action_Reference
	 *
	 * @since    1.0.0
	 */
	public function action_method_name() {
		// TODO: Define your action hook callback here
	}


	/**
	 * @since    1.0.0
	 */
	public function action_comment_form( $post_id ) {
		// marker for comment form
		$nonce = wp_create_nonce( ajax_action_add_comment . $post_id );

		echo '<span style="display:none;" id="tcpCommentFormSpan" data-tcp-post-id="' . $post_id. '" data-tcp-nc="' . $nonce . '"></span>' . PHP_EOL;

		// enable tinymce editor on comment form
		$this->filter_tinymce_editor();
	}


	/**
	 * @since    1.0.0
	 */
	public function tcp_add_comment( $post_id, $content ) {
		global 	$post,
				$current_user;

		get_currentuserinfo();

		if ( ! current_user_can( 'edit_posts' ) ) { wp_send_json_error( 'permission denied' ); }

		$add_comment = array(
			'comment_post_ID' => $post_id,
			'comment_content' => $content,
			'user_id' => $current_user->ID,
			'comment_author' => $current_user->display_name,
			'comment_author_url' => $current_user->user_url,
			'comment_author_email' => $current_user->user_email
		);

		if ( wp_new_comment( $add_comment ) ) {
			wp_send_json( $add_comment );
		} else {
			wp_send_json_error( 'failed to update comment' );
		}
	}

	/**
	 * @since    1.0.0
	 */
	public function tcp_update_comment( $post_id, $comment_id, $content ) {
		global 	$post,
				$current_user;

		get_currentuserinfo();
		$comment = get_comment( $comment_id );

		if ( ! current_user_can( 'edit_posts' ) &&
			 $current_user->ID != $comment->user_id ) { wp_send_json_error( 'permission denied' ); }

		$update = array(
			'comment_ID' => $comment_id,
			'comment_content' => stripslashes( $content )
		);

		if ( wp_update_comment( $update ) ) {
			wp_send_json( $update );
		} else {
			wp_send_json_error( 'failed to update comment' );
		}
	}

	/**
	 * @since    1.0.0
	 */
	public function tcp_save_option( $option, $value ) {
		if ( ! current_user_can( 'manage_options' ) ) { wp_send_json_error( 'permission denied' ); }
		return update_option( $option, $value );
	}

	/**
	 * @since    1.0.0
	 */
	public function action_ajax_request() {
		// validate ajax request variables
		$result = false;
		$action = sanitize_key( $_REQUEST[ 'action' ] );
		$security = sanitize_text_field( $_REQUEST[ 'security' ] );

		// check for valid ajax request variables
		if ( ! $action ||
			 ! $security ) { wp_send_json_error( 'bad request' ); }

		global $allowedtags;
 		// add additional tags to allowed tags in comments
 		$allowedtags = array_merge( $allowedtags, $this->tcp_new_tags() );

		switch ( $action ) {
			case ajax_action_add_comment:
				$post_id = intval( $_REQUEST[ 'postId' ] );
				$comment_id = intval( $_REQUEST[ 'commentId' ] );
				$content = wp_kses( $_REQUEST[ 'content' ], $allowedtags );
				// check ajax referer's security nonce
				check_ajax_referer( ajax_action_add_comment . $post_id, 'security' );

				$result = $this->tcp_add_comment( $post_id, $content );
			break;

			case ajax_action_update_comment:
				$post_id = intval( $_REQUEST[ 'postId' ] );
				$comment_id = intval( $_REQUEST[ 'commentId' ] );
				$content = wp_kses( $_REQUEST[ 'content' ], $allowedtags );
				if ( ! $comment_id ) { wp_send_json_error( 'bad request' ); }
				// check ajax referer's security nonce
				check_ajax_referer( ajax_action_update_comment . $comment_id, 'security' );

				$result = $this->tcp_update_comment( $post_id, $comment_id, $content );
			break;

			case ajax_action_editing_enabled:
				check_ajax_referer( ajax_action_editing_enabled, 'security' );
				$content = sanitize_key( $_REQUEST[ 'content' ] );
				$result = $this->tcp_save_option( ajax_action_editing_enabled, $content );
			break;

			case ajax_action_editing_expiration:
				check_ajax_referer( ajax_action_editing_expiration, 'security' );
				$content = sanitize_key( $_REQUEST[ 'content' ] );
				$result = $this->tcp_save_option( ajax_action_editing_expiration, $content );
			break;

			case ajax_action_custom_classes_open:
				check_ajax_referer( ajax_action_custom_classes_open, 'security' );
				$content = sanitize_key( $_REQUEST[ 'content' ] );
				$result = $this->tcp_save_option( ajax_action_custom_classes_open, $content );
			break;

			case ajax_action_wordpress_ids_open:
				check_ajax_referer( ajax_action_wordpress_ids_open, 'security' );
				$content = sanitize_key( $_REQUEST[ 'content' ] );
				$result = $this->tcp_save_option( ajax_action_wordpress_ids_open, $content );
			break;
		}

		wp_send_json( $result );
	}





	/**
	 * @since    1.0.0
	 */
	 public function filter_format_tinymce( $args ) {
	 	$args['remove_linebreaks'] = false;
	 	$args['gecko_spellcheck'] = true;
	 	$args['keep_styles'] = true;
	 	$args['accessibility_focus'] = true;
	 	$args['tabfocus_elements'] = 'major-publishing-actions';
	 	$args['media_strict'] = false;
	 	$args['paste_remove_styles'] = false;
	 	$args['paste_remove_spans'] = false;
	 	$args['paste_strip_class_attributes'] = 'none';
	 	$args['paste_text_use_dialog'] = true;
	 	$args['wpeditimage_disable_captions'] = true;
	 	$args['plugins'] = 'tabfocus,paste,fullscreen,wordpress,wplink,wpdialogs,wpfullscreen';
	 	//$args['content_css'] = get_template_directory_uri() . "/editor-style.css";
	 	$args['wpautop'] = true;
	 	$args['apply_source_formatting'] = false;
	    $args['block_formats'] = "Paragraph=p; Heading 3=h3; Heading 4=h4";
	 	$args['toolbar1'] = tcp_buttons1;
	 	$args['toolbar2'] = tcp_buttons2;
	 	$args['toolbar3'] = '';
	 	$args['toolbar4'] = '';

	 	return $args;
	 }

	/**
	 * @since    1.0.0
	 */
	public function filter_comment_form_defaults( $args ) {
		$args['comment_field'] = $this->filter_tinymce_editor();

		return $args;
	}

	/**
	 * @since    1.0.0
	 */
	public function filter_tinymce_editor() {
	  ob_start();

	  wp_editor( '', 'comment',
			array(
				'skin' => 'wp_theme',
			    'textarea_rows' => 12,
			    'teeny' => false,
				'tinymce' => array(
					'plugins' => 'inlinepopups, wordpress, wplink, wpdialogs',
					'theme_advanced_buttons1' => tcp_buttons1,
            		'theme_advanced_buttons2' => tcp_buttons2
				),
			    'quicktags' => false,
			    'media_buttons' => false
	  		)
	  );

	  $editor = ob_get_contents();

	  ob_end_clean();

	  return $editor;
	}

	/**
	 * @since    1.0.0
	 */
	public function filter_comment_editing( $content, $comment ) {

		if ( ! $this->user_can_edit( $comment->user_id ) ) { return $content; }

		$comment_id = $comment->comment_ID;
		$post_id = $comment->comment_post_ID;

		$tcp_content = sprintf(
			'<div class="tcp-comment-content" data-tcp-post-id="%d" data-tcp-comment-id="%d">%s</div>',
			$post_id,
			$comment_id,
			$content
		);

		return $tcp_content;
	}

	/**
	 * @since    1.0.0
	 */
	public function filter_comment_reply_link_args( $args, $comment, $post ) {
		global $current_user;

		if ( ( is_user_logged_in() &&
			$comment->user_id == $current_user->ID ) ||
			current_user_can( 'administrator' ) ) {
			$nonce = wp_create_nonce( ajax_action_update_comment . $comment->comment_ID );

			$tcp_reply_link = '<a href="javascript:void(0);" class="tcp-edit-comment comment-reply-link" data-tcp-post-id="' . $post->ID. '" ';
			$tcp_reply_link .= 'data-tcp-comment-id="' . $comment->comment_ID . '" data-tcp-nc="' . $nonce .'">Edit</a>' . PHP_EOL;

			$args[ 'before' ] .= $tcp_reply_link;
		}

		return $args;
	}

	/**
	* customise list of allowed HTML tags in comments
	* @since    1.0.0
	*/
	public function tcp_new_tags() {
		// additionally allowed tags
		$new_tags = array(
			'a' => array(
				'href' => true,
				'title' => true,
				'target' => true
			),
			'del' => true,
			'h1' => array(
				'style' => true
			),
			'h2' => array(
				'style' => true
			),
			'h3' => array(
				'style' => true
			),
			'h4' => array(
				'style' => true
			),
			'h5' => array(
				'style' => true
			),
			'h6' => array(
				'style' => true
			),
			'img' => array(
				'style' => true,
				'title' => true
			),
			'ol' => array(
				'style' => true,
				'li' => array(
					'style' => true
				)
			),
			'p' => array(
				'style' => true
			),
			'pre' => true,
			'span' => array(
				'style' => true
			),
			'ul' => array(
				'style' => true,
				'li' => array(
					'style' => true
				)
			)
		);

		return $new_tags;
	}

	/**
	* customise list of allowed HTML tags in comments
	* @since    1.0.0
	*/
	public function filter_customize_allowed_tags( $comment_data ) {
		global $allowedtags;

		$allowedtags = array_merge( $allowedtags, $this->tcp_new_tags() );

		return $comment_data;
	}

}
