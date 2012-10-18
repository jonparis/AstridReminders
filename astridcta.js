/**
 * AstridCTA JS
 *
 * PHP version 5
 * 
 * @package   AstridCTA
 * @author    Chris Lema (cflema@gmail.com) and Justin Kussow (jdkussow@gmail.com)
 * @copyright Copyright (c)2012 ALL RIGHTS RESERVED
 */

function addActaAction(text,notes,reminder_days) {
	var text = text || "";
	var notes = notes || "";
	var reminder_days = reminder_days || 3;
	var next = jQuery('.acta_action').length;
		
	var html = '';
	html += '<li id="acta_actions_' + next + '" class="acta_action">';
	html += '<div class="acta_action_header">';
	html += '<label for="acta_actions[' + next + ']">Action Item</label>';
	
	html += '<a class="acta_remove_action" onclick="return removeActaAction(this);">Remove</a>';
	
	html += '</div>';
	html += '<div class="acta_action_field">';
	html += '<label>Reminder</label>';
	html += '<input type="text" class="acta_action_text" id="acta_actions[' + next + '][text]" name="acta_actions[' 
	html += next + '][text]" value="'+ decodeURIComponent(text) + '" placeholder="Check '+ jQuery("#wp-admin-bar-site-name > a").html() + ' in 3 days to see if I won" />';
	html += '</div>';
	html += '<div class="acta_action_field">';
	html += '<label>Description</label>';
	html += '<textarea rows="3" class="acta_action_description" id="acta_actions[' + next + '][notes]" name="acta_actions[' + next + '][notes]"';
	html += ' placeholder="optional instructions to help readers take this action">'+ decodeURIComponent(notes) + '</textarea>';
	html += '</div>';
	html += '<div class="acta_action_field">';
	html += '<label>Send reminder</label>';
	html += '<input type="text" class="acta_action_reminder_days" id="acta_actions[' + next + '][reminder_days]" name="acta_actions[' + next + '][reminder_days]" value="'+ decodeURIComponent(reminder_days) +'" />';
	html += ' days after added by user</div>';	
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
		addActaAction(jQuery(value).html(), "", 3);
	});
	if(potential_tasks.length == 0)
		alert('You have no h2 headers in this post.');
}

function removeActaAction(e) {
	jQuery(e).closest('li').remove();
	jQuery('#acta_actions .acta_remove_action:last').show();
	if (jQuery('.acta_action').length < 10) {
		jQuery('#acta_add_action').closest('tr').show();
	}
	return false;
}

function astrid_popover_size(){
	jQuery(".astrid-reminder-link").click(function() {
		var DEFAULT_POPUP_WIDTH = 600;
		var DEFAULT_POPUP_HEIGHT = 535;
		var l = window.screenX + (window.outerWidth - DEFAULT_POPUP_WIDTH) / 2;
		var t = window.screenY + (window.outerHeight - DEFAULT_POPUP_HEIGHT) / 3;
		window.open(this.href, "_blank", "width="+DEFAULT_POPUP_WIDTH+", height="+DEFAULT_POPUP_HEIGHT+", top="+t+", left="+l+", toolbar=1, resizable=0");
		return false;
	});
}


window.addEventListener("load", astrid_popover_size, false); 