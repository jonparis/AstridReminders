/**
 * AstridCTA JS
 *
 * PHP version 5
 * 
 * @package   AstridCTA
 * @author    Chris Lema (cflema@gmail.com) and Justin Kussow (jdkussow@gmail.com)
 * @copyright Copyright (c)2011 ALL RIGHTS RESERVED
 */

function addActaAction(text,notes,reminder_days) {
	var text = text || "";
	var notes = notes || "";
	var reminder_days = reminder_days || 3;
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
	html += '<label>Title</label>';
	html += '<input type="text" class="acta_action_text" id="acta_actions[' + next + '][text]" name="acta_actions[' + next + '][text]" value="'+ decodeURIComponent(text) + '" />';
	html += '</div>';
	html += '<div class="acta_action_field">';
	html += '<label>Description</label>';
	html += '<textarea rows="3" class="acta_action_description" id="acta_actions[' + next + '][notes]" name="acta_actions[' + next + '][notes]"';
	html += ' placeholder="optional instructions to help readers take this action">'+ decodeURIComponent(notes) + '</textarea>';
	html += '</div>';
	html += '<div class="acta_action_field">';
	html += '<label>Send reminder</label>';
	html += '<input type="text" class="acta_action_reminder_days" id="acta_actions[' + next + '][reminder_days]" name="acta_actions[' + next + '][reminder_days]" value="'+ decodeURIComponent(reminder_days) +'" />';
	html += ' days after reader clicks</div>';	
	html += '</li>';
	
	jQuery('#acta_actions').append(html);
	
	if (next >= 9) {
		jQuery('#acta_add_action').closest('tr').hide();
	}
	
	jQuery('#acta_no_actions').hide();
	
	return false;
}

function getTasksFromPost() {
	var data = jQuery('#content').text();
	var test = jQuery(data).find('h2');
	var container = document.createElement('div');
	container.innerHTML = data;
	var potential_tasks = jQuery(container).find('h2');
	jQuery.each(potential_tasks, function(index, value) {
		console.log(jQuery(value).html());
		addActaAction(jQuery(value).html(), "", 3);
	});
}

function removeActaAction(e) {
	jQuery(e).closest('li').remove();
	jQuery('#acta_actions .acta_remove_action:last').show();
	if (jQuery('.acta_action').length < 10) {
		jQuery('#acta_add_action').closest('tr').show();
	}
	return false;
}