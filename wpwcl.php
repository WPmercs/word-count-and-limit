<?php
/*
Plugin Name: WPWCL - WordPress Word Count and Limit
Text Domain: wpwcl
Plugin URI: https://wordpress.org/plugins/word-count-and-limit/
Description: Dynamically counts the words in edit post window and limit the character count if needed for one or more user roles.
Author: Jojaba
Version: 1.1
Author URI: http://perso.jojaba.fr/
*/

/**
 * Language init
 */
function wpwcl_lang_init() {
 load_plugin_textdomain( 'wpwcl', false, basename(dirname(__FILE__)) );
}
add_action('plugins_loaded', 'wpwcl_lang_init');

add_action( 'admin_menu', 'wpwcl_options_add_page' );
/**
 * Load up the options page
 */
if( !function_exists('wpwcl_options_add_page'))  {
	function wpwcl_options_add_page() {
		add_options_page( 
			__( 'Word Count and Limit', 'wpwcl' ), // Title for the page
			__( 'Word Count and Limit', 'wpwcl' ), //  Page name in admin menu
			'manage_options', //  Minimum role required to see the page
			'wpwcl_options_page', // unique identifier
			'wpwcl_options_do_page'  // name of function to display the page
		);
		add_action( 'admin_init', 'wpwcl_options_settings' );	
	}
}
/**
 * Create the options page
 */

if( !function_exists('wpwcl_options_do_page'))  {
	function wpwcl_options_do_page() { ?>

<div class="wrap">

        <h2><?php _e( 'Word Count and Limit Options', 'wpwcl' ) ?></h2>  
        
        <?php 
        /*** To debug, here we can print the plugin options **/
        /* 
        echo '<pre>';
        $options = get_option( 'wpwcl_settings_options' );
        print_r($options); 
        echo '</pre>';
        */
         ?>
        
        <form method="post" action="options.php">
        		<?php settings_fields( 'wpwcl_settings_options' ); ?>
		  	<?php do_settings_sections('wpwcl_setting_section'); ?>
		  	<p><input class="button-primary"  name="Submit" type="submit" value="<?php esc_attr_e(__('Save Changes','wpwcl')); ?>" /></p>		
        </form>
        <script>
        jQuery(document).ready(function() {
            if (jQuery("input#limit_true").prop("checked")) {
                jQuery("#impacted_users_option").parent().parent().show();
                jQuery("#impacted_post_types_option").parent().parent().show();
                jQuery("#maxchars_option").parent().parent().show();
                jQuery("#warning_option").parent().parent().show();
            }
            else {
                jQuery("#impacted_users_option").parent().parent().hide();
                jQuery("#impacted_post_types_option").parent().parent().hide();
                jQuery("#maxchars_option").parent().parent().hide();
                jQuery("#warning_option").parent().parent().hide();
            }
            jQuery("input[name*='ask_limitation_option']").on("change", function() {
                if (jQuery("input#limit_true").prop("checked")) {
                    jQuery("#impacted_users_option").parent().parent().show("slow");
                    jQuery("#impacted_post_types_option").parent().parent().show("slow");
                    jQuery("#maxchars_option").parent().parent().show("slow");
                    jQuery("#warning_option").parent().parent().show("slow");
                }
                else {
                    jQuery("#impacted_users_option").parent().parent().hide("slow");
                    jQuery("#impacted_post_types_option").parent().parent().hide("slow");
                    jQuery("#maxchars_option").parent().parent().hide("slow");
                    jQuery("#warning_option").parent().parent().hide("slow");
                }
            });
        });
        </script>
</div>

<?php
	} // end wpc_options_do_page
}

/**
 * Init plugin options to white list our options
 */
if( !function_exists('wpwcl_options_settings'))  {
	function wpwcl_options_settings(){
		/* Register wpwcl settings. */
		register_setting( 
			'wpwcl_settings_options',  //$option_group , A settings group name. Must exist prior to the register_setting call. This must match what's called in settings_fields()
			'wpwcl_settings_options', // $option_name The name of an option to sanitize and save.
			'wpwcl_options_validate' // $sanitize_callback  A callback function that sanitizes the option's value.
        );

		/** Add a section **/
		add_settings_section(
			'wpwcl_option_main', //  section name unique ID
			'&nbsp;', // Title or name of the section (to be output on the page), you can leave nbsp here if not wished to display
			'wpwcl_option_section_text',  // callback to display the content of the section itself
			'wpwcl_setting_section' // The page name. This needs to match the text we gave to the do_settings_sections function call 
        );

		/** Register each option **/
		add_settings_field(
			'ask_limitation_option', 
			__( 'Set a limit?', 'wpwcl' ), 
			'wpwcl_func_ask_limitation_option', 
			'wpwcl_setting_section',  
			'wpwcl_option_main' 
        ); 
		
		add_settings_field(
			'impacted_users_option', 
			__( 'Impacted users', 'wpwcl' ), 
			'wpwcl_func_impacted_users_option', 
			'wpwcl_setting_section',  
			'wpwcl_option_main' 
        ); 
        
        add_settings_field(
			'impacted_post_types_option', 
			__( 'Impacted post types', 'wpwcl' ), 
			'wpwcl_func_impacted_post_types_option', 
			'wpwcl_setting_section',  
			'wpwcl_option_main' 
        ); 
			
		add_settings_field(
			'maxchars_option', 
			__( 'Max characters allowed', 'wpwcl' ), 
			'wpwcl_func_maxchars_option', 
			'wpwcl_setting_section',  
			'wpwcl_option_main' 
        ); 
	
		add_settings_field(
			'warning_option', 
			__( 'Warning', 'wpwcl' ), 
			'wpwcl_func_warning_option', 
			'wpwcl_setting_section',  
			'wpwcl_option_main' 
        ); 
			
		add_settings_field(
			'format_option',  //$id a unique id for the field 
			__( 'Output Format', 'wpwcl' ), // the title for the field
			'wpwcl_func_format_option',  // the function callback, to display the input box
			'wpwcl_setting_section',  // the page name that this is attached to (same as the do_settings_sections function call).
			'wpwcl_option_main' // the id of the settings section that this goes into (same as the first argument to add_settings_section).
        );
        
        add_settings_field(
			'warning_message_option',  //$id a unique id for the field 
			__( 'Warning message', 'wpwcl' ), // the title for the field
			'wpwcl_func_warning_message_option',  // the function callback, to display the input box
			'wpwcl_setting_section',  // the page name that this is attached to (same as the do_settings_sections function call).
			'wpwcl_option_main' // the id of the settings section that this goes into (same as the first argument to add_settings_section).
        );
        
        add_settings_field(
			'contributor_message_option',  //$id a unique id for the field 
			__( 'Contributor message', 'wpwcl' ), // the title for the field
			'wpwcl_func_contributor_message_option',  // the function callback, to display the input box
			'wpwcl_setting_section',  // the page name that this is attached to (same as the do_settings_sections function call).
			'wpwcl_option_main' // the id of the settings section that this goes into (same as the first argument to add_settings_section).
        );
    }
}

/** the theme section output**/
if( !function_exists('wpwcl_option_section_text'))  {
	function wpwcl_option_section_text(){
	echo '<p>'.__( 'Here you can set the options of WP Word Count and Limit plugin. If you set a limit, the author of the post will be warned if he exceeds the character count limit by changing the characters/words display color (<span style="color: darkorange">orange</span>: near the limit, <span style="color: red">red</span>: over the limit). If he tries to submit his post as it exceeds the character limit, he will be prompted a message while hovering the submission div and submission will be refused.', 'wpwcl' ).'</p>';
	}
}

/** The Limitation (yes or no) radio buttons **/
if( !function_exists('wpwcl_func_ask_limitation_option'))  {
	function wpwcl_func_ask_limitation_option() {
		 /* Get the option value from the database. */
		$options = get_option( 'wpwcl_settings_options' );
		$ask_limitation_option = ($options['ask_limitation_option'] != '') ? $options['ask_limitation_option'] : 0 ;
		
		/* Echo the field. */ ?>
		<label for="limit_true" > <?php _e( 'Yes', 'wpwcl' ); ?></label>
		<input type="radio" <?php if ($ask_limitation_option == 1) echo'checked="checked"' ; ?> id="limit_true" name="wpwcl_settings_options[ask_limitation_option]" value="1" /> 
		<label for="limit_false" > <?php _e( 'No', 'wpwcl' ); ?></label>
		<input type="radio" id="limit_false" <?php if ($ask_limitation_option == 0) echo'checked="checked"' ; ?> name="wpwcl_settings_options[ask_limitation_option]" value="0" /> 
    <?php }
}

/** The Impacted users Checkboxes **/
if( !function_exists('wpwcl_func_impacted_users_option'))  {
	function wpwcl_func_impacted_users_option(){
	/* Get the option value from the database. */
		$options = get_option( 'wpwcl_settings_options' );
		$impacted_users_option =  (is_array($options['impacted_users_option'])) ? $options['impacted_users_option'] : array('contributor');
		/* Echo the field. */ ?>
		<div id="impacted_users_option">
		<input type="checkbox" id="impacted_users_option_contributor" name="wpwcl_settings_options[impacted_users_option][]" value="contributor"<?php if (in_array('contributor', $impacted_users_option)) echo ' checked'; ?> /> <?php _e( 'Contributors', 'wpwcl' ); ?><br>
		<input type="checkbox" id="impacted_users_option_author" name="wpwcl_settings_options[impacted_users_option][]" value="author"<?php if (in_array('author', $impacted_users_option)) echo ' checked'; ?> /> <?php _e( 'Authors', 'wpwcl' ); ?><br>
		<input type="checkbox" id="impacted_users_option_editor" name="wpwcl_settings_options[impacted_users_option][]" value="editor"<?php if (in_array('editor', $impacted_users_option)) echo ' checked'; ?> /> <?php _e( 'Editors', 'wpwcl' ); ?><br>
		<input type="checkbox" id="impacted_users_option" name="wpwcl_settings_options[impacted_users_option][]" value="administrator"<?php if (in_array('administrator', $impacted_users_option)) echo ' checked'; ?> /> <?php _e( 'Administrators', 'wpwcl' ); ?><br>
		<p class="description">
		    <?php _e( 'The users that should be limited (multiple users role possible).', 'wpwcl' ); ?>
        </p>
        </div>
	<?php }
}

/** The Impacted post types Checkboxes **/
if( !function_exists('wpwcl_func_impacted_post_types_option'))  {
	function wpwcl_func_impacted_post_types_option(){
	/* Get the option value from the database. */
		$options = get_option( 'wpwcl_settings_options' );
		$impacted_post_types_option =  (is_array($options['impacted_post_types_option'])) ? $options['impacted_post_types_option'] : array('post');
		/* Echo the field. */ ?>
		<div id="impacted_post_types_option">
		<input type="checkbox" id="impacted_post_types_option_contributor" name="wpwcl_settings_options[impacted_post_types_option][]" value="post"<?php if (in_array('post', $impacted_post_types_option)) echo ' checked'; ?> /> post<br>
		<input type="checkbox" id="impacted_post_types_option_author" name="wpwcl_settings_options[impacted_post_types_option][]" value="page"<?php if (in_array('page', $impacted_post_types_option)) echo ' checked'; ?> /> page<br>
		<?php /* listing of public custom post types */
		    $custom_post_types = get_post_types( array('public' => true, '_builtin' => false) ); 
            if (!empty($custom_post_types)) {
                foreach ( $custom_post_types  as $custom_post_type ) { ?>
                    <input type="checkbox" id="impacted_post_type_option_author" name="wpwcl_settings_options[impacted_post_types_option][]" value="page"<?php if (in_array($custom_post_type, $impacted_post_types_option)) echo ' checked'; ?> /> <?php echo $custom_post_type ?><br>
                <?php } // end foreach
            } // end if post_types
        ?>
		<p class="description">
		    <?php _e( 'The post types that should be limited.', 'wpwcl' ); ?>
        </p>
        </div>
	<?php }
}

/** The Max characters field **/
if( !function_exists('wpwcl_func_maxchars_option'))  {
	function wpwcl_func_maxchars_option(){
	/* Get the option value from the database. */
		$options = get_option( 'wpwcl_settings_options' );
		$maxchars_option = ($options['maxchars_option'] != '') ? $options['maxchars_option'] : '1000';
		/* Echo the field. */ ?>
		<input type="number" id="maxchars_option" name="wpwcl_settings_options[maxchars_option]" value="<?php echo esc_attr($maxchars_option); ?>" />
		<p class="description">
		    <?php _e( 'The max count of characters the author of a post can write.', 'wpwcl' ); ?>
        </p>
	<?php }
}

/** The warning field */
if( !function_exists('wpwcl_func_warning_option'))  {
	function wpwcl_func_warning_option(){
	/* Get the option value from the database. */
		$options = get_option( 'wpwcl_settings_options' );
		$warning_option = ($options['warning_option'] != '') ? $options['warning_option'] : '100';
		/* Echo the field. */ ?>
		<input type="number" id="warning_option" name="wpwcl_settings_options[warning_option]" value="<?php echo esc_attr($warning_option); ?>" />
		<p class="description">
		    <?php _e( 'The number of characters before the max value the warning is fired.', 'wpwcl' ); ?>
        </p>
	<?php }
}

/** The format field **/
if( !function_exists('wpwcl_func_format_option'))  {
	function wpwcl_func_format_option(){
	/* Get the option value from the database. */
		$options = get_option( 'wpwcl_settings_options' );
		$format_option = ($options['format_option'] != '') ? $options['format_option'] : '#input characters | #words words';	
		/* Echo the field. */ ?>
		<input type="text" style="width: 40%;"  id="format_option" name="wpwcl_settings_options[format_option]" value="<?php echo esc_attr($format_option); ?>" />
		<p class="description">
		    <?php _e( 'You can define the output display using the following variables: <code>#input</code> (the number of characters), <code>#words</code> (the number of words). When limitaion is set, two additionnal variables are available: <code>#max</code> (the max characters allowed), <code>#left</code> (the remaining characters). The HTML tags are allowed', 'wpwcl' ) ?>
        </p>
        <p class="description"><strong><?php _e( 'Regular use:', 'wpwcl'); ?></strong></p>
        <p><?php _e( '<code>&lt;b&gt;#input&lt;/b&gt; characters | &lt;b&gt;#words&lt;/b&gt; words</code> will display <q style="padding: 2px 4px; border: #e0e0e0 1px solid; background: #f7f7f7;"><b>123</b> characters | <b>36</b> words.</q>.', 'wpwcl' ) ?></p>
        <p class="description"><strong><?php _e( 'When limit set:', 'wpwcl'); ?></strong></p>
        <p><?php _e( '<code>#input/#max &lt;i&gt;characters&lt;/i&gt;, #left &lt;i&gt;left&lt;/i&gt; | #words &lt;i&gt;words&lt;/i&gt;</code> will display <q style="padding: 2px 4px; border: #e0e0e0 1px solid; background: #f7f7f7;">123/250 <i>characters</i>, 127 <i>left</i> | 36 <i>words</i></q> or <q style="padding: 2px 4px; border: #e0e0e0 1px solid; background: #f7f7f7; color: red;">256/250 <i>characters</i>, 0 <i>left</i> | 68 <i>words</i></q>.', 'wpwcl' ) ?></p>
    <?php }
}

/** The warning message field **/
if( !function_exists('wpwcl_func_warning_message_option'))  {
	function wpwcl_func_warning_message_option(){
	/* Get the option value from the database. */
		$options = get_option( 'wpwcl_settings_options' );
		$warning_message_option = ($options['warning_message_option'] != '') ? $options['warning_message_option'] : __( 'Sorry, but you exceeded the characters limit!', 'wpwcl');	
		/* Echo the field. */ ?>
		<input style="width: 95%;" type="text" id="warning_message_option" name="wpwcl_settings_options[warning_message_option]" value="<?php echo esc_attr($warning_message_option); ?>" />
		<p class="description">
		    <?php _e( 'The message that will display when user exceeded the allowed characters count (no HTML tags allowed).', 'wpwcl' ) ?>
        </p>
    <?php }
}

/** The contributor message field **/
if( !function_exists('wpwcl_func_contributor_message_option'))  {
	function wpwcl_func_contributor_message_option(){
	/* Get the option value from the database. */
		$options = get_option( 'wpwcl_settings_options' );
		$contributor_message_option = ($options['contributor_message_option'] != '') ? $options['contributor_message_option'] : __( 'Your Post has been submitted to the editorial team for validation and publish. Thanks for your contribution!', 'wpwcl');	
		/* Echo the field. */ ?>
		<input style="width: 95%;" type="text" id="contributor_message_option" name="wpwcl_settings_options[contributor_message_option]" value="<?php echo esc_attr($contributor_message_option); ?>" />
		<p class="description">
		    <?php _e( 'The message that will display when a contributor submit a post (no HTML tags allowed).', 'wpwcl' ) ?>
        </p>
    <?php }
}


/**
 * Sanitize and validate input. Accepts an array, return a sanitized array.
 */
if( !function_exists('wpwcl_options_validate'))  {
	function wpwcl_options_validate( $input ) {
	$options = get_option( 'wpwcl_settings_options' );
	
	/** Radio buttons (ask limit) validation **/
	$options['ask_limitation_option'] = $input['ask_limitation_option'];

	// Our radio option must actually be in our array of radio options
	if ( ! isset( $input['ask_limitation_option'] ) )
		$input['ask_limitation_option'] = null;
	if ( ! array_key_exists( $input['ask_limitation_option'], $ask_limitation_option) )
		$input['ask_limitation_option'] = null;
	
	/** Impacted Users	validation **/
	$options['impacted_users_option'] = $input['impacted_users_option'];
	
	/** Impacted post types	validation **/
	$options['impacted_post_types_option'] = $input['impacted_post_types_option'];
		
	/** maxchars number validation */
	$options['maxchars_option'] = wp_filter_nohtml_kses( intval( $input['maxchars_option'] ) );
	
	/** warning number validation */
	$options['warning_option'] = wp_filter_nohtml_kses( intval( $input['warning_option'] ) );

	/** clean text field, HTML allowed for the format */
	$options['format_option'] = wp_filter_kses($input['format_option'] );
	
	/** warning message validation */
	$options['warning_message_option'] = wp_filter_nohtml_kses( $input['warning_message_option'] );
	
	/** contributor message validation */
	$options['contributor_message_option'] = wp_filter_nohtml_kses( $input['contributor_message_option'] );

	return $options;	
	}
}

/**
 * Adds the script after tinymce script
 */
if (!function_exists('wpwcl_scripts')) {
    function wpwcl_scripts($post) {
    // Retrieving settings values
    $options = get_option( 'wpwcl_settings_options' );
    $set_limit = ($options['ask_limitation_option'] == 1) ? 1 : 0;
    $imp_user = (is_array($options['impacted_users_option'])) ? $options['impacted_users_option'] : array('contributor'); // This is an array of roles
    $imp_p_types = (is_array($options['impacted_post_types_option'])) ? $options['impacted_post_types_option'] : array('post'); // This is an array of post types
    $max = ($options['maxchars_option'] > 0) ? $options['maxchars_option'] : 1000;
    $warn = ($options['warning_option'] > 0) ? $options['warning_option'] : 100;
    $format = ($options['format_option'] != '') ? $options['format_option'] : '#input characters | #words words';
    $w_message = ($options['warning_message_option'] != '') ? $options['warning_message_option'] : __( 'Sorry, but you exceeded the characters limit!', 'wpwcl');
    $c_message = ($options['contributor_message_option'] != '') ? $options['contributor_message_option'] : __( 'Your Post has been submitted to the editorial team for validation and publish. Thanks for your contribution!', 'wpwcl');
    // post type fetching
    $p_id = get_the_ID();
    $post_type = get_post_type($p_id);
    // Only if no limit set or if the post type is in impacted post types
    if ($set_limit == 0 || ($set_limit > 0 && in_array($post_type, $imp_p_types))) :
        
        // Looking if user is impacted by limitation
        $c_user = wp_get_current_user();
		$user_r = $c_user->roles; // User roles array
		$user_role = $user_r[0];
		$is_impacted = count(array_intersect($user_r, $imp_user)); // > 0 if impacted
        echo "<script>\n";
        echo "jQuery(window).load(function() {\n";
        // This counter doesn't work if the textarea field is first opened (I don't know why...)
        echo "switchEditors.switchto(jQuery('#content-tmce').get(0));";
        // Printing the scripts
        echo "/* The textarea and the iframe */
		var textarea_cont = jQuery('#content');
		var wysiwyg_cont = jQuery('#content_ifr').contents();
				
		/* Variables Initial define */
		var setLimit = ".$set_limit."; // Limit = 1, no limit = 0
		var maxCharacters = ".$max."; // max characters count if limit is set
		var warningNumb = ".$warn."; // number of characters before limit where the user is warned
		var formatString = '".$format."'; // The syntax used to display the output
		var charInfo = jQuery('#wp-word-count'); // Output container, same as Default WP Word count 
		var contentLength = 0;
		var numLeft = 0;
		var numWords = 0;
		
		/* The events on each container */
		textarea_cont.on('keyup', function(event){getTheCharacterCount('textarea');})
                     .on('paste', function(event){setTimeout(function(){getTheCharacterCount('textarea');}, 10);});
		
        wysiwyg_cont.on('keyup', 'body', function(event){getTheCharacterCount('wysiwig');})
                    .on('paste', 'body', function(event){setTimeout(function(){getTheCharacterCount('wysiwig');}, 10);});
        
		
		/* Function to find the characters count */ 
        function getTheCharacterCount(cont){
			charInfo.html(countByCharacters(cont));
		}
		
		/* Counting the characters and the words */
		function countByCharacters(cont){
		    if (cont == 'textarea') {
		        // Textarea case
		        var raw_content = textarea_cont.val();
		    } else {
                // WysiWyg case
                var raw_content = jQuery('#content_ifr').contents().find('body').html();
            }
            var content = raw_content.replace(/(\\r\\n)+|\\n+|\\r+|\s+|(&nbsp;)+/gm,' '); // Replace newline, tabulations, &nbsp; by space to preserve word count
            content = content.replace(/<[^>]+>/ig,''); // Remove HTML tags
            content = content.replace(/(&lt;)[^(\&gt;)]+(\&gt;)/ig,''); // Remove HTML tags (when entities)
            content = content.replace(/\[[^\]]+\]/ig,''); // Remove shortcodes
			contentLength = content.length;
			
            // All cases var definitions
            numInput = contentLength;
            numWords = getCleanedWordStringLength(content);
            
            // Treatment if limit set (change color by status)
            if(setLimit > 0){
                if (contentLength <= maxCharacters - warningNumb)
                    charInfo.css('color', 'inherit');
                else if (contentLength < maxCharacters && contentLength >= maxCharacters - warningNumb && ".$is_impacted." > 0)
                    charInfo.css('color', 'orange');
                else if(contentLength > maxCharacters && ".$is_impacted." > 0)
                    charInfo.css('color', 'red');
                numLeft = (maxCharacters - numInput > 0) ? maxCharacters - numInput : 0;
            }
            
            // Output the result
            return formatDisplayInfo();
			    
		}
		
		/* Displaying the result in the defined format */
		function formatDisplayInfo(){
		    var output = formatString;
		    if (output.indexOf('#input') != -1)
			    output = output.replace('#input', numInput);
            if (output.indexOf('#words') != -1)
			    output = output.replace('#words', numWords);
			//When no limit set, #max, #left cannot be substituted.
			if(setLimit > 0){
			    if (output.indexOf('#max') != -1)
				    output = output.replace('#max', maxCharacters);
                if (output.indexOf('#left') != -1)
				    output = output.replace('#left', numLeft);
			}
			return output;
		}
		
		/* Cleaning content to count the words */	
		function getCleanedWordStringLength(content){
		    // Cleaning and splitting wordstring (tags and shortcodes are already stripped)
			var rawContent = content;
			var cleanedContent = rawContent.replace(/[\.,:!\?;\)\]…\"]+/gi, ' '); //Replacing ending ponctuation with spaces to get right word number.
			var cleanedContent = cleanedContent.replace(/\s+/ig,' ') // Multiple spaces case (after punctuation replacement) replaced by one space
			var splitString = cleanedContent.split(' ');
			// Word Count defining
			var wordCount = splitString.length - 1;
			return wordCount;
		}";
		
		// Launching word count on load
        echo "getTheCharacterCount('wysiwig');";
       
		// Refuse saving if too many characters only if for the defined users
		if ($set_limit > 0 && $is_impacted > 0) {
        echo "jQuery('#submitdiv').on('mouseover', function() {
            if (contentLength > maxCharacters) {
                alert('".$w_message."');
            }
        });\n
        jQuery('form#post').on('submit', function() {
        if (contentLength < maxCharacters && '".$user_role."' == 'contributor') {
                alert('".$c_message."');
            }
        });\n";
        } // End if limit set and user must be impacted
        
        echo "});\n"; // End jQuery handling
        echo "</script>\n";
    endif; // End if post-type = post
    }
add_action( 'after_wp_tiny_mce', 'wpwcl_scripts');
}

/**
 * Function to avoid post save if characters limit is reached
 */
 if (!function_exists('wpwcl_maxcharreached')) {
    function wpwcl_maxcharreached(){ 
        // Get some options values
        $options = get_option( 'wpwcl_settings_options' );
        $setLimit = $options['ask_limitation_option'];
        $imp_user = (is_array($options['impacted_users_option'])) ? $options['impacted_users_option'] : array('contributor'); // This is an array of roles
        $maxchars = ($options['maxchars_option'] != '') ? $options['maxchars_option'] : '1000';
        // See if current user belongs to impacted users
        $c_user = wp_get_current_user();
        $user_r = $c_user->roles; // User roles array
        $inters = array_intersect($user_r, $imp_user);
        if ($setLimit == 1 && count($inters) > 0) {
            global $post;
            $content = wp_filter_nohtml_kses($post->post_content);
            if (strlen($content) > $maxchars) 
                wp_die( $w_message ); 
        }
    } 
    add_action('draft_to_publish', 'wpwcl_maxcharreached');
    add_action('pending_to_publish', 'wpwcl_maxcharreached'); 
    add_action('draft_to_pending', 'wpwcl_maxcharreached');
}
