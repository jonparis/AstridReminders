<?php
/*
Plugin Name: Astrid Call-To-Action Reminders
Description: A WordPress plugin that lets bloggers create Astrid Call-to-Action reminders with-in and at the bottom of each post
Version: 0.4
Author: Chris Lema (cflema@gmail.com) with Justin Kussow (jdkussow@gmail.com) using Custom Meta Box code from others.
License: GPLv2
*/

function decodeURIComponent($str) {
	$revert = array('!'=>'%21', '*'=>'%2A', "'"=>'%27', '('=>'%28', ')'=>'%29');
	return rawurldecode(strtr($str, $revert));
}
/* create localvariable from get variables */
foreach ($_GET as $key => $value){
	$arr_test = array("ar_source_name", "ar_site_title", "ar_source_url");
	if (in_array($key, $arr_test))
		$$key = str_replace('"', '\\"', $value);
	else
		$$key = str_replace('"', '&quot;', decodeURIComponent($value));
}

?><!DOCTYPE html>
	<head>
		<link rel="stylesheet" type="text/css" href="http://netdna.bootstrapcdn.com/twitter-bootstrap/2.1.0/css/bootstrap-combined.min.css" />
		<link rel="stylesheet" type="text/css" href="../astridcta.css?2" />
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
		<script type="text/javascript" src="../../../../wp-includes/js/tinymce/tiny_mce_popup.js"></script>
		<script type="text/javascript" src="../astridcta.js?2"></script>
		<script type="text/javascript">
			var AstridLink = {
			  e: '',
			  init: function(e) {
			      AstridLink.e = e;
			      tinyMCEPopup.resizeToInnerSize();
			  },
			  url_suffix: function() {
			  	var ar = {};
			    var ar_fields = ["title", "notes", "due_in_days", "button_title"];			  
			    for (var i = 0; i < ar_fields.length; i++)
			      ar[ar_fields[i]] = encodeURIComponent($('#ar_' + ar_fields[i]).val());

			  	ar.title = (ar.title) ? ar.title : "Test Remind Me";
			  	/* Get site parameters from GET variables */
				ar.source_url = encodeURIComponent("<?php echo $ar_source_url; ?>");
				ar.source_name = encodeURIComponent("<?php echo $ar_source_name; ?>");
				ar.site_name = encodeURIComponent("<?php echo $ar_site_name; ?>");
				ar.source_name = (ar.source_name) ? ar.source_name : ar.site_name;
				var url_suffix = "?title=" + ar.title + "&notes=" + ar.notes + "&due_in_days=" + ar.due_in_days +
			    	"&source_url=" + ar.source_url + "&source_name=" + ar.source_name;
			    return url_suffix;
			  },
			  ar_unshortened_url : function() {
			 	return "http://astrid.com/tasks/remind_me" + AstridLink.url_suffix();
			  },
			  preview : function (){
			  	jQuery("#ar_preview").attr("href", AstridLink.ar_unshortened_url());
			  	return false;
			  },
			  insert: function create_reminder_link(e) {

				/* Button and link title */
				var text_selection = "<?php echo $ar_text_selection; ?>";
			    var use_button = $('input[name=link_or_button]:checked').val() == "button";
				var button_size = $('input[name=ar_button_size]:checked').val();
				var button_style = $('input[name=ar_button_style]:checked').val();
			    var link_title = (text_selection == "") ? $('#ar_title').val() : text_selection;
			   	var link_class = (use_button) ? "astrid-reminder-link astrid-rm-btn " + button_size + " " + button_style : "astrid-reminder-link";
			    var display_title = (use_button) ? '<span class="a-chk-span">&#x2713;</span>' + $('#ar_button_title').val() : "&#x2713; Remind me: " + link_title;

			    function construct_link(href) {
			   		return '<a class="' + link_class + '"' + ' href="'+ href + '" title="get reminder via email, calendar or, to-do list"' + 
			              '">' + display_title + '</a>';
			   	}
			    jQuery.ajax({               
			      type: "POST",
			      url: "http://astrid.com/widgets/remind_me_link" + AstridLink.url_suffix(),
			      data: {}, 
			      success: function(data_returned){
			        tinyMCEPopup.execCommand('mceInsertContent',false, construct_link(data_returned.url));
			    	  tinyMCEPopup.close();
			    	
			      },
			      error : function(data){ 
			      	tinyMCEPopup.execCommand('mceInsertContent',false, construct_link(ar_unshortened_url()));
			    	  tinyMCEPopup.close();
			      }                 
			    });  
			  },
			  toggle_button_format: function () {
				$(".button-style-controls").toggle();
			  },toggle_date_picker: function(){
    			$("#relative_date").toggle();
    			$("#specific_date").toggle();
    			$("#specific_date input").val("");
  			  }
			}

			tinyMCEPopup.onInit.add(AstridLink.init, AstridLink);  

		</script>
	</head>

<body class="a_gl_page">
	<h3>Insert Remind Me Link</h3>
	<form action="" method="post" name="get_link_form" id="get_link_form">
		<div class="form-horizontal" id="task_fields">
			<div class="control-group">
				<label class="control-label" for="ar_title" title="Title is required">Reminder</label>
				<div class="controls">
					<input id="ar_title" name="ar_title" placeholder="Update LinkedIn profile w/tips from <?php echo $ar_site_name; ?>" type="text" value="<?php echo $ar_text_selection; ?>">
					(required)
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="notes">Description</label>
				<div class="controls">
					<textarea id="ar_notes" rows="3" name="notes" placeholder="Longer description with additional instruction"></textarea>
				</div>
			</div>
			<div class="control-group" id="relative_date">
				<label class="control-label" for="due_in_days">Send reminder</label>
				<div class="controls input-append thin-input" style="margin-left: 20px;">
					<input id="ar_due_in_days" name="due_in_days" type="text" value="2">
					<div class="add-on">
						days after added by user
					</div>
					<div class="control-hint">
						<a href="javascript:;" onclick="AstridLink.toggle_date_picker()">use specific date</a>
					</div>
				</div>
			</div>
			<div class="control-group hide" id="specific_date">
				<label class="control-label" for="due_date">Remind on</label>
				<div class="controls">
					<input id="date_picker" name="due_date" placeholder="yyyy-mm-dd" type="text" value="">
					<div class="control-hint">
						<a href="javascript:;" onclick="AstridLink.toggle_date_picker()">x days after</a>
					</div>
				</div>
			</div>
			<div class="control-group link-or-button-controls">
				<label class="control-label inline" for="link_or_button">Link or Button</label>
				<div class="controls">
					<div class="link-or-button" onclick="AstridLink.toggle_button_format()">
						<label class="inline radio">
							<input checked="checked" id="link_or_button_link" name="link_or_button" type="radio" value="link">
								link
						</label>
					</div>
					<div class="link-or-button" onclick="AstridLink.toggle_button_format()">
						<label class="inline radio" style="margin-left:30px">
							<input id="link_or_button_button" name="link_or_button" type="radio" value="button">
								button
						</label>
					</div>
				</div>
			</div>
			<div class="control-group button-style-controls hide">
				<label class="control-label inline" for="ar_button_size">Size</label>
				<div class="controls">
					<div class="button-size">
						<label class="inline radio">
							<input checked="checked" id="ar_button_size_mini" name="ar_button_size" type="radio" value="a-mini">
								mini
							<div style="margin-top:10px">
								<div class="astrid-rm-btn a-mini a-chk">
									<span class="a-chk-span">&#x2713;</span>
									Remind me
								</div>
							</div>
						</label>
					</div>
					<div class="button-size">
						<label class="inline radio">
							<input id="ar_button_size_small" name="ar_button_size" type="radio" value="a-small">
								small
							<div style="margin-top:10px">
								<div class="astrid-rm-btn a-small a-chk">
									<span class="a-chk-span">&#x2713;</span>
									Remind me
								</div>
							</div>
						</label>
					</div>
					<div class="button-size">
						<label class="inline radio">
							<input id="ar_button_size_large" name="ar_button_size" type="radio" value="a-large">
								large
							<div style="margin-top:10px">
								<div class="astrid-rm-btn a-large a-chk">
									<span class="a-chk-span">&#x2713;</span>
									Remind me
								</div>
							</div>
						</label>
					</div>
				</div>
			</div>
			<div class="control-group button-style-controls hide">
				<label class="control-label inline" for="ar_button_style">Style</label>
				<div class="controls">
					<div class="button-style">
						<label class="inline radio">
							<input id="ar_button_style_astrid" name="ar_button_style" type="radio" value="a-icn">
							<img alt="Astrid_btn_small" border="0" height="24" src="../images/astrid_btn_small.png" width="24">
								astrid
						</label>
					</div>
					<div class="button-style">
						<label class="inline radio">
							<input checked="checked" id="ar_button_style_checkmark" name="ar_button_style" type="radio" value="a-chk">
							<span class="check">✓</span>
								checkmark
						</label>
					</div>
					<div class="button-style">
						<label class="inline radio">
							<input id="ar_button_style_text_only" name="ar_button_style" type="radio" value="a-txt">
							text_only
						</label>
					</div>
				</div>
			</div>
			<div class="control-group button-style-controls hide">
				<label class="control-label" for="ar_button_title">Button text</label>
				<div class="controls">
					<input id="ar_button_title" name="ar_button_title" type="text" value="Remind me">
				</div>
			</div>
			<div class="control-group">
				<div class="controls">
					<a href="javascript:;" class="btn btn-primary" style="color:white" onclick="AstridLink.insert(AstridLink.e)">Add to Post</a>
					<div class="control-hint">
						<a href="" class="astrid-rm-link" id="ar_preview" onmouseover="AstridLink.preview(this)">Preview</a>
					</div>
				</div>
			</div>
		</div>
	</form>
	<hr/>
	<p>
		Reminder links help readers remember to act on suggestions. 
		By clicking the links they can quickly add your advice to their to-do lists, 
		calendar, and will get a reminder via email with a link back to your post.  <a href="http://astrid.com/widgets/create_rm_button" class="learn-more" target="_blank" >Learn more</a>.
		<br/><a href="http://astrd.co/QtM5gy" class="astrid-reminder-link astrid-rm-btn a-small a-chk" target="_blank"><span class="a-chk-span">✓</span> Example Button</a> 
	</p>
</body>
</html>