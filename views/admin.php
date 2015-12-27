<?php
/**
 * Represents the view for the administration dashboard.
 *
 * This includes the header, options, and other information that should provide
 * The User Interface to the end user.
 *
 * @package   tinymce-comments-plus
 * @author    Kentaro Fischer <webdev@kentarofischer.com>
 * @license   GPL-2.0+
 * @link      http://kentarofischer.com
 * @copyright 3-22-2015 Kentaro Fischer
 */
?>
<div class="wrap">

	<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>

	<div class="tcp-settings">
		<div class="tcp-option">
			<fieldset class="comment-editing">
				<?php
					$nonce = wp_create_nonce( tcp_ajax_editing_enabled );
					$editing_option = get_option( tcp_ajax_editing_enabled );
				?>
				<legend><span class="dashicons dashicons-welcome-write-blog"></span> Comment Editing</legend>
				<p>Edit comments for logged in users</p>
				<div class="editing-control">
					<label for="editing"><?php if ( $editing_option == 'on' ) { ?>Enabled<?php } else { ?>Disabled<?php } ?></label>
					<input name="editing" type="checkbox" <?php if ( $editing_option == 'on' ) { ?>checked="checked"<?php } ?> data-tcp-nc="<?php echo $nonce ?>" />
				</div>
			</fieldset>
			<fieldset class="comment-expiration">
				<?php
					$nonce = wp_create_nonce( tcp_ajax_editing_expiration );
					$expiration_option = get_option( tcp_ajax_editing_expiration );
				?>
				<legend><span class="dashicons dashicons-clock"></span> Comment Editing Period</legend>
				<p>Time to allow comments to be edited</p>
				<div class="expiration-control">
					<output></output>
				</div>
				<input class="years" type="range" step="1" min="1" max="262981" data-tcp-nc="<?php echo $nonce ?>" <?php echo "value=\"" . $expiration_option . "\"" ?> />
			</fieldset>
		</div>
		<div class="tcp-option">
			<fieldset class="custom-classes">
				<?php
					$nonce = wp_create_nonce( tcp_ajax_custom_classes );
					$classes_option0 = get_option( tcp_ajax_custom_classes .'0' );
					$classes_option1 = get_option( tcp_ajax_custom_classes .'1' );
					$classes_option2 = get_option( tcp_ajax_custom_classes .'2' );
					$classes_option3 = get_option( tcp_ajax_custom_classes .'3' );
					$classes_option4 = get_option( tcp_ajax_custom_classes .'4' );
				?>
				<legend><span class="dashicons dashicons-media-code"></span> Custom CSS</legend>
				<p>Add additional CSS classes for TinyMCE Comments Plus buttons and inputs</p>

				<div class="box" data-tcp-nc="<?php echo $nonce ?>">
					<div class="confirmed">
						<span class="dashicons dashicons-yes"></span>
						<span class="message">CSS Classes Saved</span>
					</div>
					<label><span>All Buttons</span> <input type="text" value="<?php echo $classes_option0 ?>" /></label>
					<label><span>WordPress Reply Button</span> <input type="text" value="<?php echo $classes_option1 ?>" /></label>
					<label><span>Edit Button</span> <input type="text" value="<?php echo $classes_option2 ?>" /></label>
					<label><span>Submit Edit Button</span> <input type="text" value="<?php echo $classes_option3 ?>" /></label>
					<label><span>Cancel Edit Button</span> <input type="text" value="<?php echo $classes_option4 ?>" /></label>
				</div>

			</fieldset>
			<fieldset class="wordpress-ids">
				<?php
					$nonce = wp_create_nonce( tcp_ajax_wordpress_ids_open );
					$ids_option = get_option( tcp_ajax_wordpress_ids_open );
				?>
				<legend><span class="dashicons dashicons-media-code"></span> WordPress IDs &amp; Classes</legend>
				<p>Some themes may use different element IDs or classes in comments.                                                                                                                                                           </p>

				<div class="box">
					<label><span>Comments List</span> <input type="text" placeholder="#comments" /></label>
					<!-- <label><span>Comment ID Prefix</span> <input type="text" placeholder="" /></label>
					<label><span>Comment</span> <input type="text" placeholder="" /></label> -->
					<label><span>Respond</span> <input type="text" placeholder="#respond" /></label>
					<label><span>Comment Form</span> <input type="text" placeholder="#commentform" /></label>
					<label><span>Comment Reply Link</span> <input type="text" placeholder=".comment-reply-link" /></label>
					<label><span>Cancel Comment Reply Link</span> <input type="text" placeholder="#cancel-comment-reply-link" /></label>
					<label><span>Submit Comment</span> <input type="text" placeholder="#submit" /></label>
				</div>
			</fieldset>
		</div>
		<div class="tcp-option">
			<fieldset class="customize-buttons">
				<?php
					$nonce = wp_create_nonce( tcp_ajax_custom_classes );
					$classes_option = get_option( tcp_ajax_custom_classes );
				?>
				<legend><span class="dashicons dashicons-editor-kitchensink"></span> Customize TinyMCE Toolbar Buttons</legend>
				<p>Configure toolbar row buttons in TinyMCE for comments</p>

				<div class="box">
					<label><span>Toolbar row 1</span> <input type="text"></label>
					<label><span>Toolbar row 2</span> <input type="text"></label>
					<label><span>Toolbar row 3</span> <input type="text"></label>
					<label><span>Toolbar row 4</span> <input type="text"></label>
				</div>
			</fieldset>
		</div>
	</div>

</div>
