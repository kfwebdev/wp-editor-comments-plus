<?php
/**
 * Represents the view for the administration dashboard.
 *
 * @package   wp-editor-comments-plus
 * @author    Kentaro Fischer <webdev@kentarofischer.com>
 * @license   GPL-2.0+
 * @link      http://kentarofischer.com
 * @copyright 3-22-2015 Kentaro Fischer
 */
?>
<div class="wrap">

	<h2><?php echo $this->plugin_name ?> Settings</h2>

	<div class="wpecp-settings">
		<div class="wpecp-option">
			<fieldset class="comment-options">
				<?php
					$deleting_nonce = wp_create_nonce( wpecp_ajax_deleting_enabled );
					$deleting_option = get_option( wpecp_ajax_deleting_enabled );
					$deleting_option = ( $deleting_option == 'off' ) ? 'off' : 'on';
					$editing_nonce = wp_create_nonce( wpecp_ajax_editing_enabled );
					$editing_option = get_option( wpecp_ajax_editing_enabled );
					$editing_option = ( $editing_option == 'off' ) ? 'off' : 'on';
					$image_upload_nonce = wp_create_nonce( wpecp_ajax_image_upload_setting );
					$image_upload_option = get_option( wpecp_ajax_image_upload_setting );
					$image_upload_option = ( $image_upload_option == wpecp_image_upload_logged_in ) ? wpecp_image_upload_logged_in : wpecp_image_upload_disabled;
					$oembed_support_nonce = wp_create_nonce( wpecp_ajax_oembed_support_enabled );
					$oembed_support_option = get_option( wpecp_ajax_oembed_support_enabled );
					$oembed_support_option = ( $oembed_support_option === 'off' ) ? 'off' : 'on';
				?>
				<legend><span class="dashicons dashicons-welcome-write-blog"></span> Comment Options</legend>
				<div class="comment-editing option-row">
					<p>Edit comments for logged in users</p>
					<div class="options-control">
						<label for="editing"><?php if ( $editing_option == 'on' ) { ?>Enabled<?php } else { ?>Disabled<?php } ?></label>
						<input name="editing" type="checkbox" <?php if ( $editing_option == 'on' ) { ?>checked="checked"<?php } ?> data-wpecp-nc="<?php echo esc_attr( $editing_nonce ) ?>" />
					</div>
				</div>
				<div class="comment-deleting option-row">
					<p>Delete comments for logged in users</p>
					<div class="options-control">
						<label for="deleting"><?php if ( $deleting_option == 'on' ) { ?>Enabled<?php } else { ?>Disabled<?php } ?></label>
						<input name="deleting" type="checkbox" <?php if ( $deleting_option == 'on' ) { ?>checked="checked"<?php } ?> data-wpecp-nc="<?php echo esc_attr( $deleting_nonce ) ?>" />
					</div>
				</div>
				<div class="comment-oembed-support option-row">
					<p>oEmbed support in comments</p>
					<div class="options-control">
						<label for="oembed-support"><?php if ( $oembed_support_option == 'on' ) { ?>Enabled<?php } else { ?>Disabled<?php } ?></label>
						<input name="oembed-support" type="checkbox" <?php if ( $oembed_support_option == 'on' ) { ?>checked="checked"<?php } ?> data-wpecp-nc="<?php echo esc_attr( $oembed_support_nonce ) ?>" />
					</div>
				</div>
				<div class="comment-image-upload option-row">
					<p>Upload images in comments</p>
					<div class="options-control">
						<select class="saved" name="image-uploads" data-wpecp-nc="<?php echo esc_attr( $image_upload_nonce ) ?>">
							<option value="<?php echo wpecp_image_upload_disabled; ?>" <?php if ( $image_upload_option == wpecp_image_upload_disabled ) { ?>selected="selected"<?php } ?>>Disabled</option>
							<option value="<?php echo wpecp_image_upload_logged_in; ?>" <?php if ( $image_upload_option == wpecp_image_upload_logged_in ) { ?>selected="selected"<?php } ?>>Logged In Only</option>
						</select>
					</div>
				</div>
			</fieldset>
			<fieldset class="comment-expiration">
				<?php
					$editing_nonce = wp_create_nonce( wpecp_ajax_editing_expiration );
					$editing_expiration_option = get_option( wpecp_ajax_editing_expiration );
					$editing_expiration_option = ( intval( $editing_expiration_option ) > 0 ) ? $editing_expiration_option : 0;
					$editing_dtF = new DateTime( "@0" );
    				$editing_dtT = new DateTime( "@$editing_expiration_option" );
					$editing_expirations = $editing_dtF->diff( $editing_dtT );
					$deleting_nonce = wp_create_nonce( wpecp_ajax_deleting_expiration );
					$deleting_expiration_option = get_option( wpecp_ajax_deleting_expiration );
					$deleting_expiration_option = ( intval( $deleting_expiration_option ) > 0 ) ? $deleting_expiration_option : 0;
					$deleting_dtF = new DateTime( "@0" );
    				$deleting_dtT = new DateTime( "@$deleting_expiration_option" );
					$deleting_expirations = $deleting_dtF->diff( $deleting_dtT );
				?>
				<legend><span class="dashicons dashicons-clock"></span> Edit Comment Expiration</legend>
				<div class="confirmed">
					<span class="dashicons dashicons-yes"></span>
					<span class="message"></span>
				</div>
				<p>Leave values at 0 to always allow comment edits.</p>
				<div class="expiration-control editing-expiration" data-wpecp-nc="<?php echo $editing_nonce ?>">
					<label for="editing_days" class="days">Days
						<input name="editing_days" value="<?php echo $editing_expirations->format('%a'); ?>">
					</label>
					<label for="editing_hours" class="hours">Hours
						<input name="editing_hours" value="<?php echo $editing_expirations->format('%h'); ?>">
					</label>
					<label for="editing_minutes" class="minutes">Minutes
						<input name="editing_minutes" value="<?php echo $editing_expirations->format('%i'); ?>">
					</label>
					<label for="editing_seconds" class="seconds">Seconds
						<input name="editing_seconds" value="<?php echo $editing_expirations->format('%s'); ?>">
					</label>
				</div>
				<legend class="expiration-divider"><span class="dashicons dashicons-clock"></span> Delete Comment Expiration</legend>
				<p>Leave values at 0 to always allow comment deleting.</p>
				<div class="expiration-control deleting-expiration" data-wpecp-nc="<?php echo $deleting_nonce ?>">
					<label for="deleting_days" class="days">Days
						<input name="deleting_days" value="<?php echo $deleting_expirations->format('%a'); ?>">
					</label>
					<label for="deleting_hours" class="hours">Hours
						<input name="deleting_hours" value="<?php echo $deleting_expirations->format('%h'); ?>">
					</label>
					<label for="deleting_minutes" class="minutes">Minutes
						<input name="deleting_minutes" value="<?php echo $deleting_expirations->format('%i'); ?>">
					</label>
					<label for="deleting_seconds" class="seconds">Seconds
						<input name="deleting_seconds" value="<?php echo $deleting_expirations->format('%s'); ?>">
					</label>
				</div>
			</fieldset>
		</div>
		<div class="wpecp-option">
			<fieldset class="custom-toolbars">
				<?php
					$nonce = wp_create_nonce( wpecp_ajax_custom_toolbars );
					$wpecp_toolbar1 = get_option( wpecp_ajax_custom_toolbars .'_toolbar1' );
					$wpecp_toolbar2 = get_option( wpecp_ajax_custom_toolbars .'_toolbar2' );
					$wpecp_toolbar3 = get_option( wpecp_ajax_custom_toolbars .'_toolbar3' );
					$wpecp_toolbar4 = get_option( wpecp_ajax_custom_toolbars .'_toolbar4' );
				?>
				<legend><span class="dashicons dashicons-editor-kitchensink"></span> Customize TinyMCE Toolbar Buttons</legend>
				<p>Configure toolbar row buttons in TinyMCE for comments. Leave blank for default layout. Type none to hide any toolbar.</p>

				<div class="box" data-wpecp-nc="<?php echo $nonce ?>">
					<div class="confirmed">
						<span class="dashicons"></span>
						<span class="message"></span>
					</div>
					<label><span>Toolbar row 1</span> <input type="text" placeholder="bold italic strikethrough bullist numlist blockquote hr alignleft aligncenter alignright image link unlink wp_more spellchecker wp_adv" value="<?php echo esc_attr( $wpecp_toolbar1 ) ?>" data-wpecp-field="_toolbar1"></label>
					<label><span>Toolbar row 2</span> <input type="text" placeholder="formatselect underline alignjustify forecolor pastetext removeformat charmap outdent indent undo redo wp_help" value="<?php echo esc_attr( $wpecp_toolbar2 ) ?>" data-wpecp-field="_toolbar2"></label>
					<label><span>Toolbar row 3</span> <input type="text" placeholder="fontselect fontsizeselect" value="<?php echo esc_attr( $wpecp_toolbar3 ) ?>" data-wpecp-field="_toolbar3"></label>
					<label><span>Toolbar row 4</span> <input type="text" placeholder="" value="<?php echo esc_attr( $wpecp_toolbar4 ) ?>" data-wpecp-field="_toolbar4"></label>
				</div>
			</fieldset>
		</div>
		<div class="wpecp-option">
			<fieldset class="custom-classes">
				<?php
					$nonce = wp_create_nonce( wpecp_ajax_custom_classes );
					$wpecp_classes_all = get_option( wpecp_ajax_custom_classes . '_all' );
					$wpecp_classes_cancel = get_option( wpecp_ajax_custom_classes . '_cancel' );
					$wpecp_classes_delete = get_option( wpecp_ajax_custom_classes . '_delete' );
					$wpecp_classes_edit = get_option( wpecp_ajax_custom_classes . '_edit' );
					$wpecp_classes_reply = get_option( wpecp_ajax_custom_classes . '_reply' );
					$wpecp_classes_submit = get_option( wpecp_ajax_custom_classes . '_submit' );
				?>
				<legend><span class="dashicons dashicons-media-code"></span> Custom CSS</legend>
				<p>Add custom CSS classes to <?php echo $this->plugin_name ?> buttons</p>

				<div class="box" data-wpecp-nc="<?php echo $nonce ?>">
					<div class="confirmed">
						<span class="dashicons dashicons-yes"></span>
						<span class="message"></span>
					</div>
					<label><span>All Buttons</span> <input type="text" placeholder="wpecp-button" value="<?php echo esc_attr( $wpecp_classes_all ); ?>" data-wpecp-field="_all"></label>
					<label><span>WordPress Reply Button</span> <input type="text" placeholder="wpecp-reply-comment" value="<?php echo esc_attr( $wpecp_classes_reply ); ?>" data-wpecp-field="_reply"></label>
					<label><span>Edit Button</span> <input type="text" placeholder="wpecp-edit" value="<?php echo esc_attr( $wpecp_classes_edit ); ?>" data-wpecp-field="_edit"></label>
					<label><span>Delete Button</span> <input type="text" placeholder="wpecp-delete" value="<?php echo esc_attr( $wpecp_classes_delete ); ?>" data-wpecp-field="_delete"></label>
					<label><span>Submit Edit Button</span> <input type="text" placeholder="wpecp-submit-edit" value="<?php echo esc_attr( $wpecp_classes_submit ); ?>" data-wpecp-field="_submit"></label>
					<label><span>Cancel Edit Button</span> <input type="text" placeholder="wpecp-cancel-edit" value="<?php echo esc_attr( $wpecp_classes_cancel ); ?>" data-wpecp-field="_cancel"></label>
				</div>

			</fieldset>
			<fieldset class="wordpress-ids">
				<?php
					$nonce = wp_create_nonce( wpecp_ajax_wordpress_ids );
					$wp_id_comments = get_option( wpecp_ajax_wordpress_ids .'_comments' );
					$wp_id_comment = get_option( wpecp_ajax_wordpress_ids .'_comment' );
					$wp_id_respond = get_option( wpecp_ajax_wordpress_ids .'_respond' );
					$wp_id_comment_form = get_option( wpecp_ajax_wordpress_ids .'_comment_form' );
					$wp_id_comment_textarea = get_option( wpecp_ajax_wordpress_ids .'_comment_textarea' );
					$wp_id_reply = get_option( wpecp_ajax_wordpress_ids .'_reply' );
					$wp_id_cancel = get_option( wpecp_ajax_wordpress_ids .'_cancel' );
					$wp_id_submit = get_option( wpecp_ajax_wordpress_ids .'_submit' );
				?>
				<legend><span class="dashicons dashicons-media-code"></span> WordPress IDs &amp; Classes</legend>
				<p>Some themes may use different element IDs or classes in comments. Leave blank for WordPress defaults.                                                                                                                                              </p>

				<div class="box" data-wpecp-nc="<?php echo $nonce ?>">
					<div class="confirmed">
						<span class="dashicons"></span>
						<span class="message"></span>
					</div>
					<label><span>Comments List</span> <input type="text" placeholder="#comments" value="<?php echo esc_attr( $wp_id_comments ); ?>" data-wpecp-field="_comments"></label>
					<label><span>Comment ID Prefix</span> <input type="text" placeholder="#comment-" value="<?php echo esc_attr( $wp_id_comment ); ?>" data-wpecp-field="_comment" /></label>
					<label><span>Respond</span> <input type="text" placeholder="#respond" value="<?php echo esc_attr( $wp_id_respond ); ?>" data-wpecp-field="_respond" /></label>
					<label><span>Comment Form</span> <input type="text" placeholder="#commentform" value="<?php echo esc_attr( $wp_id_comment_form ); ?>" data-wpecp-field="_comment_form" /></label>
					<label><span>Comment Textarea</span> <input type="text" placeholder="#comment" value="<?php echo esc_attr( $wp_id_comment_textarea ); ?>" data-wpecp-field="_comment_textarea" /></label>
					<label><span>Comment Reply Link</span> <input type="text" placeholder=".comment-reply-link" value="<?php echo esc_attr( $wp_id_reply ); ?>" data-wpecp-field="_reply" /></label>
					<label><span>Cancel Comment Reply Link</span> <input type="text" placeholder="#cancel-comment-reply-link" value="<?php echo esc_attr( $wp_id_cancel ); ?>" data-wpecp-field="_cancel" /></label>
					<label><span>Submit Comment</span> <input type="text" placeholder="#submit" value="<?php echo esc_attr( $wp_id_submit ); ?>" data-wpecp-field="_submit" /></label>
				</div>
			</fieldset>
		</div>
	</div>

</div>
