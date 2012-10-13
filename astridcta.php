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
		$acta_title .= 'AstridCTA Options';
		
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
	
	function render_acta_actions( $field, $meta ) {
		echo '<ul id="' . $field['id'] . '" name="' . $field['id'] . '">';		
		if ( $meta && is_array( $meta ) ) {
			$i = 0;
			foreach ( $meta as $val ) {
				echo '
<li id="acta_actions_' . $i . '" class="acta_action">
	<div class="acta_action_header">
	<label for="acta_actions[' . $i . ']">#' . ( $i + 1 ) . '</label>';
				
				if ( $i > 0 ) {
					$visible = 'display: none';
					if ( ( $i + 1 ) >= count( $meta ) ) {
						$visible = '';
					}
					echo '<a class="acta_remove_action" style="' . $visible . ';" onclick="return removeActaAction(this);">Remove</a>';
				}
				
				echo '
	</div>
	<div class="acta_action_field">
		<label>Action</label>
		<input type="text" class="acta_action_text" id="acta_actions[' . $i . '][text]" name="acta_actions[' . $i . '][text]" value="' . $val['text'] . '" />
	</div>	
	<div class="acta_action_field">
		<label>Reminder Days</label>
		<input type="text" class="acta_action_reminder_days" id="acta_actions[' . $i . '][reminder_days]" name="acta_actions[' . $i . '][reminder_days]" value="' . $val['reminder_days'] . '" />
	</div>
</li>
				';
				$i += 1;
			}
		} else {
//			echo '
//<li id="acta_actions_0" class="acta_action">
//	<div class="acta_action_header"><label for="acta_actions[0]">#1</label></div>
//	<div class="acta_action_field">
//		<label>Action</label>
//		<input type="text" class="acta_action_text" id="acta_actions[0][text]" name="acta_actions[0][text]" value="" />
//	</div>	
//	<div class="acta_action_field">
//		<label>Reminder Days</label>
//		<input type="text" class="acta_action_reminder_days" id="acta_actions[0][reminder_days]" name="acta_actions[0][reminder_days]" value="" />
//	</div>
//</li>
//			';
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
			$content .= '<h2>Want a Reminder?</h2>';
			$content .= '<p>If you use <a href="//astrid.com">Astrid</a>, you can create reminders so that you remember to act on this post.</p>';
			$content .= '<ul>';
			$siteurl = get_site_url();
			$step = 0; 
			foreach( $actions as $action ) { 
				$step += 1;
				$content .= '<li id="acta_action_fe_' . $step . '" class="acta_action_fe">';
				$content .= $action['text'];
				$content .= '&nbsp;&nbsp;';
				$content .= '<iframe allowtransparency="true" frameborder="0" scrolling="no"height="21px" width="116px" title="Astrid Remind Me"';
				$content .= 'src="http://astrid.com/widgets/remind_me?title=';
				$content .= urlencode($action['text']);
				$content .= '&due_in_days=';
				$content .= $action['reminder_days'];
				$content .= '&source_name=ChrisLema';
				$content .= '&source_url=';
				$content .= $siteurl;
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
