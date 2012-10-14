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
define('DEFAULT_REMINDER', 3);

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

	if ( is_singular() && is_main_query() ) {
		$actions = get_post_meta( $post->ID, 'acta_actions', true );
		if ( $actions && is_array( $actions ) && count( $actions ) > 0 ) {
			$content .= '<div id="acta_actions_fe">';
			$content .= '<h2>Don\'t forget!</h2>';
			$content .= '<p>Get reminders by email or through <a href="http://astrid.com">Astrid</a> for iPhone, iPad, or Android.</p>';
			$content .= '<ul>';
			$siteurl = get_site_url();
			$step = 0; 
			foreach( $actions as $action ) { 
				$step += 1;
				$content .= '<li id="acta_action_fe_' . $step . '" class="acta_action_fe">';
				$content .= $action['text'];
				$content .= '&nbsp;&nbsp;';
				$content .= '<iframe allowtransparency="true" frameborder="0" scrolling="no"height="21px" width="116px" title="Astrid Remind Me"';
				$content .= 'src="http://astrid.com/widgets/remind_me?title=' . urlencode($action['text']);
				$content .= '&due_in_days=' . $action['reminder_days'];
				$content .= '&notes='.urlencode($action['notes']);
				$content .= '&source_name=';
				$content .= '&source_url='.urlencode(post_permalink());
				$content .= '&suggester_id=45&button_size=mini&button_style=astrid&button_title=Remind%20me"></iframe>';
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

?>
