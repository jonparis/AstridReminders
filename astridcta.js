/**
 * AstridCTA JS
 *
 * PHP version 5
 * 
 * @package   AstridCTA
 * @author    Chris Lema (cflema@gmail.com) and Justin Kussow (jdkussow@gmail.com)
 * @copyright Copyright (c)2011 ALL RIGHTS RESERVED
 */

function addActaAction() {
	var next = jQuery('.acta_action').length;
	
	jQuery('.acta_remove_action').hide();
	
	var html = '';
	html += '<li id="acta_actions_' + next + '" class="acta_action">';
	html += '<div class="acta_action_header">';
	html += '<label for="acta_actions[' + next + ']">#' + (next + 1) + '</label>';
	
	if (next > 0) {
		html += '<a class="acta_remove_action" onclick="return removeActaAction(this);">Remove</a>';
	}
	
	html += '</div>';
	html += '<div class="acta_action_field">';
	html += '<label>Action</label>';
	html += '<input type="text" class="acta_action_text" id="acta_actions[' + next + '][text]" name="acta_actions[' + next + '][text]" value="" />';
	html += '</div>';
	html += '<div class="acta_action_field">';
	html += '<label>Reminder Days</label>';
	html += '<input type="text" class="acta_action_reminder_days" id="acta_actions[' + next + '][reminder_days]" name="acta_actions[' + next + '][reminder_days]" value="" />';
	html += '</div>';	
	html += '</li>';
	
	jQuery('#acta_actions').append(html);
	
	if (next >= 9) {
		jQuery('#acta_add_action').closest('tr').hide();
	}
	
	jQuery('#acta_no_actions').hide();
	
	return false;
}

function removeActaAction(e) {
	jQuery(e).closest('li').remove();
	jQuery('#acta_actions .acta_remove_action:last').show();
	if (jQuery('.acta_action').length < 10) {
		jQuery('#acta_add_action').closest('tr').show();
	}
	return false;
}