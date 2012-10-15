<?php
/*
Plugin Name: AstridCTA
Description: A WordPress plugin that lets bloggers create Astrid Calls to Action reminders at the bottom of each post.
Version: 0.1
Author: Chris Lema (cflema@gmail.com) with Justin Kussow (jdkussow@gmail.com) and using Custom Meta Box code from others.
License: GPLv2
*/

/* Directories and URLs */
define( 'ACTA_URL', plugin_dir_url(__FILE__) );
define( 'ACTA_DIR', plugin_dir_path(__FILE__) );

class AstridCTA {
	static $instance = false;
	
	function __construct() {
		add_filter( 'cmb_meta_boxes', array( &$this, 'acta_meta_boxes' ) );
		add_action( 'init', array( &$this, 'init_acta' ), 9999 );
		add_action( 'wp_print_scripts', array( &$this, 'print_scripts' ) );
		
		add_action( 'cmb_render_acta_actions', array( &$this, 'render_acta_actions' ), 10, 2 );
		add_action( 'cmb_render_acta_button', array( &$this, 'render_acta_button' ), 10, 2 );
		
		add_action( 'cmd_validate_acta_action', array( &$this, 'validate_acta_action' ), 10, 3 );
		
		//add_action( 'wp_footer', array( &$this, 'acta_footer_actions' ), 100 );
		add_action( 'the_content', array( &$this, 'acta_content_footer' ), 100 );
	}
	
	function acta_meta_boxes( $meta_boxes ) {
		$prefix = 'acta_';

		$acta_title = '';
		$acta_title .= 'Suggested reminders';
		
		$meta_boxes[] = array(
			'id' => 'acta-options',
			'title' => $acta_title,
			'pages' => array('post'), 
			'context' => 'normal',
			'priority' => 'low',
			'show_names' => true, 
			'fields' => array(
				array (
					'id' => $prefix . 'actions',
					'type' => 'acta_actions',
					'name' => 'Actions'
				),
				array (
					'id' => $prefix . 'add_action',
					'type' => 'acta_button',
					'text' => 'Add New Action',
					'js_action' => 'return addActaAction();'
				)
			)
		);

		return $meta_boxes;
	}

	function init_acta() {
		if ( !class_exists( 'cmb_Meta_Box' ) ) {
			require_once( ACTA_DIR . '/metaboxes/init.php' );
		}
	}

	function print_scripts() {
		wp_register_script( 'astridcta', ACTA_URL . 'astridcta.js', array( 'jquery' ), '1.0' );
		wp_enqueue_script( 'astridcta' );

		wp_register_style( 'astridcta', ACTA_URL . 'astridcta.css' );
		wp_enqueue_style( 'astridcta' );
	}
	
	function encodeURIComponent($str) {
	    $revert = array('%21'=>'!', '%2A'=>'*', '%27'=>"'", '%28'=>'(', '%29'=>')');
	    return strtr(rawurlencode($str), $revert);
	}	

	function render_acta_actions( $field, $meta ) {
		echo '<ul id="' . $field['id'] . '" name="' . $field['id'] . '">';		
		if ( $meta && is_array( $meta ) ) {
			foreach ( $meta as $val ) {
				echo ('<script>addActaAction("'.
					self::encodeURIComponent($val['text']).'","'.
					self::encodeURIComponent($val['notes']).'","'.
					intval($val['reminder_days']).
					'");</script>');
			}
		} else {
			echo '<span id="acta_no_actions">Add your first action.</span>';
		}
		echo '</ul>';
	}
	
	function render_acta_button( $field, $meta ) {
		echo '<a name="' . $field['id'] . '" id="' . 
				$field['id'] . '" class="button" onclick="' . $field['js_action'] . '">' . $field['text'] . '</a>';
		echo $field['hidden_field'];
	}
	
	function validate_acta_action( $new, $post_id, $fields ) {
		return $new;
	}
	

	function acta_content_footer( $content ) {
		global $post;
		$author_username = get_option("astrid_author_username");
		if ( is_singular() && is_main_query() ) {
			$actions = get_post_meta( $post->ID, 'acta_actions', true );
			if ( $actions && is_array( $actions ) && count( $actions ) > 0 ) {
				$content .= '<div id="acta_actions_fe">';
				$content .= '<h2>' . get_astrid_cta_option('header') . '</h2>';
				$content .= '<p>' . get_astrid_cta_option('description') .'</p>';
				$content .= '<ul>';
				$siteurl = get_site_url();
				$step = 0; 
				foreach( $actions as $action ) { 
					$step += 1;
					if (!$action['text'])
						continue;
					$content .= '<li id="acta_action_fe_' . $step . '" class="acta_action_fe">';
					$content .= '<a href="http://astrid.com/new?title=' . urlencode($action['text']);
					$content .= '&due_in_days=' . $action['reminder_days'];
					$content .= '&notes='.urlencode($action['notes']);
					$content .= '&source_name='.urlencode(get_the_title());
					$content .= '&source_url='.urlencode(post_permalink());
					$content .= '&suggester_id=45';
					$content .= '" target="_blank">' . $action['text'] . '</a>';
					$content .= '</li>';
				}
				$content .= '</ul>';
				$content .= '</div>';
			}
		}

		return $content;
	}
	
	public static function get_instance() {
        if ( !self::$instance ) {
            self::$instance = new self;
        }
        return self::$instance;
    }
}

AstridCTA::get_instance();

/*** Admin Panel ***/
add_action('admin_init', 'astrid_cta_init' );
add_action('admin_menu', 'astrid_cta_add_page');

function get_astrid_cta_option($option) {
	$option_default = array(
    	"header" => "Don\'t forget!",
    	"description" => 'Get reminders by email or through <a href="http://astrid.com">Astrid</a> 
    						for iPhone, iPad, or Android.'
	);
	$options = get_option('astrid_cta');
	return $options[$option] ? $options[$option] : $option_default[$option];
}

// Init plugin options to white list our options
function astrid_cta_init(){
	register_setting( 'astrid_cta_options', 'astrid_cta', 'astrid_cta_validate' );
}

// Add menu page
function astrid_cta_add_page() {
	add_options_page('Astrid Calls To Action', 'Astrid Calls-to-Action', 'manage_options', 'astrid_cta', 'astrid_cta_do_page');
}

// Draw the menu page itself
function astrid_cta_do_page() {
	?>
	<div class="wrap">
		<h2>Astrid Calls To Action</h2>
		<form method="post" action="options.php">
			<?php settings_fields('astrid_cta_options'); ?>
		</p>
			<table class="form-table">
				<tr valign="top"><th scope="row">CTA Header</th>
					<td><input name="astrid_cta[header]" type="text" value="<?php echo get_astrid_cta_option('header'); ?>" /></td>
				</tr>
				<tr valign="top"><th scope="row">Description</th>
					<td><textarea name="astrid_cta[description]" rows="3"><?php echo get_astrid_cta_option('description'); ?></textarea></td>
				</tr>
			</table>
			<p class="submit">
			<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
			</p>
		</form>
	</div>
	<?php	
}

// Sanitize and validate input. Accepts an array, return a sanitized array.
function astrid_cta_validate($input) {
	// Say our second option must be safe text with no HTML tags
	$input['header'] =  wp_filter_nohtml_kses($input['header']);
	$input['description'] =  wp_filter_nohtml_kses($input['description']);
	
	return $input;
}
?>
