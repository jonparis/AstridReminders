<?php
/*
Plugin Name: Astrid Call-To-Action Reminders
Description: A WordPress plugin that lets bloggers create Astrid Calls to Action reminders at the bottom of each post.
Version: 0.3
Author: Chris Lema (cflema@gmail.com) with Justin Kussow (jdkussow@gmail.com) and using Custom Meta Box code from others.
License: GPLv2
*/

function decodeURIComponent($str) {
	$revert = array('!'=>'%21', '*'=>'%2A', "'"=>'%27', '('=>'%28', ')'=>'%29');
	return rawurldecode(strtr($str, $revert));
}
/* create localvariable from get variables */
foreach ($_GET as $key => $value)
	$$key = str_replace('"', '&quot;', decodeURIComponent($value));		

?><!DOCTYPE html>
	<head>
		<link rel="stylesheet" type="text/css" href="http://netdna.bootstrapcdn.com/twitter-bootstrap/2.1.0/css/bootstrap-combined.min.css" />
		<link rel="stylesheet" type="text/css" href="astridcta.css" />
		<style>
			#due_in_days { width: 20px }
			a.learn-more:link, a.link  { color: #08c}
		    #created_task_link{ word-wrap:break-word; }
		    #task_fields.form-horizontal .control-group::before, .form-horizontal .control-group::after { display: inline; }
		    body.static .main-center #task_fields input { text-align: left }
		    .control-hint { width: 180px; margin-left: 10px; font-size: 13px; display: inline-block }
		    .control-hint h4 { margin-top: 20px; margin-bottom: 10px}
		    .k-widget.k-datepicker.k-header { width: 215px }
		    .button-size, .button-style, .link-or-button { display:inline }
		    .button-style label.inline { white-space:nowrap; padding-right: 30px }
		    .button-style label.inline img { border:none; width:24px; margin:0 }
		    .button-size label.inline { vertical-align:top }
		    .button-size label.inline input[type="radio"] { margin:0 auto;width:30px }
		   	#ar_notes { height:70px }
		    #ar_title, #ar_notes { width: 330px }
		</style>
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
		<script type="text/javascript" src="../../../wp-includes/js/tinymce/tiny_mce_popup.js"></script>
		<script type="text/javascript" src="astridcta.js"></script>
		<script type="text/javascript">
			var AstridLink = {
			  e: '',
			  init: function(e) {
			      AstridLink.e = e;
			      tinyMCEPopup.resizeToInnerSize();
			  },
			  insert: function create_reminder_link(e) {
			    
			    var ar = {};
			    var ar_fields = ["title", "notes", "due_in_days", "button_title"];			  
			    for (var i = 0; i < ar_fields.length; i++)
			      ar[ar_fields[i]] = encodeURIComponent($('#ar_' + ar_fields[i]).val());

			  	/* Get site parameters from get variables */
				ar.source_url = "<?php echo $ar_source_url; ?>";
				ar.source_name = "<?php echo $ar_source_name; ?>";
				ar.site_name = "<?php echo $ar_site_name; ?>";
				ar.source_name = (ar.source_name) ? ar_source_name : ar.site_name;
				ar.text_selection = "<?php echo $ar_text_selection; ?>";

				/* Button and link title */
			    var use_button = $('input[name=link_or_button]:checked').val() == "button";
				var button_size = $('input[name=ar_button_size]:checked').val();
				var button_style = $('input[name=ar_button_style]:checked').val();
			    var link_title = (ar.text_selection == "") ? $('#ar_title').val() : ar.text_selection;
			   	var link_class = "astrid-reminder-link";
			   	link_class += (use_button) ? " astrid-rm-btn " + button_size + " " + button_style : "";
			    var display_title = (use_button) ? '<span class="a-chk-span">&#x2713;</span>' + $('#ar_button_title').val() : "&#x2713; " + link_title;

			    
			    function construct_link(href) {
			   		return '<a class="astrid-reminder-link"' + ' href="'+ href + '" title="get reminder via email, calendar or, to-do list"' + 
			              '">' + display_title + '</a>';
			   	}

			    var url_suffix = "?title=" + ar.title + "&notes=" + ar.notes + "&due_in_days=" + ar.due_in_days +
			              "&source_url=" + ar.source_url + "&source_name=" + ar.source_name;
			    jQuery.ajax({               
			      type: "POST",
			      url: "http://astrid.com/widgets/remind_me_link" + url_suffix,
			      data: {}, 
			      success: function(data){  
			        tinyMCEPopup.execCommand('mceInsertContent',false, construct_link(data.url));
			    	  tinyMCEPopup.close();
			    	
			      },
			      error : function(data){ 
			        var fallback_url = "http://astrid.com/new?title=" + url_suffix;
			      	tinyMCEPopup.execCommand('mceInsertContent',false, construct_link(fallback_url));
			    	  tinyMCEPopup.close();
			      }                 
			    });  
			  },
			  toggle_button_format: function () {
				$(".button-style-controls").toggle();
			  }
			}

			tinyMCEPopup.onInit.add(AstridLink.init, AstridLink);  

		</script>
	</head>

<body>
	<h3>Add "Remind Me" Link</h3>
	<form action="" method="post" name="get_link_form" id="get_link_form">
		<div class="form-horizontal" id="task_fields">
			<div class="control-group">
				<label class="control-label" for="ar_title" title="Title is required">Reminder</label>
				<div class="controls">
					<input id="ar_title" name="ar_title" placeholder="eg Update LinkedIn profile with tips from <?php echo $ar_site_name; ?>" type="text" value="<?php echo $ar_text_selection; ?>">
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
					<a href="javascript:;" onclick="toggle_date_picker()" style="font-size:13px" class="help-inline hide">remind on specific date instead</a>
				</div>
			</div>
			<div class="control-group hide" id="specific_date">
				<label class="control-label" for="due_date">Send reminder on</label>
				<div class="controls">
					<input id="date_picker" name="due_date" placeholder="yyyy-mm-dd" type="text" value="">
					<div class="control-hint">
						<a href="javascript:;" onclick="toggle_date_picker()">remind x days after added</a>
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
				<label class="control-label inline" for="button_size">Size</label>
				<div class="controls">
					<div class="button-size">
						<label class="inline radio">
							<input checked="checked" id="button_size_mini" name="button_size" type="radio" value="a-mini">
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
							<input id="button_size_small" name="button_size" type="radio" value="a-small">
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
							<input id="button_size_large" name="button_size" type="radio" value="a-large">
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
				<label class="control-label inline" for="button_style">Style</label>
				<div class="controls">
					<div class="button-style">
						<label class="inline radio">
							<input checked="checked" id="button_style_astrid" name="button_style" type="radio" value="a-icn">
							<img alt="Astrid_btn_small" border="0" height="24" src="images/astrid_btn_small.png" width="24">
								astrid
						</label>
					</div>
					<div class="button-style">
						<label class="inline radio">
							<input id="button_style_checkmark" name="button_style" type="radio" value="a-chk">
							<span class="check">✓</span>
								checkmark
						</label>
					</div>
					<div class="button-style">
						<label class="inline radio">
							<input id="button_style_text_only" name="button_style" type="radio" value="a-txt">
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
				</div>
			</div>
		</div>
	</form>
	<hr/>
	<small>
		Reminder links help others remember and act on your suggestions. It gives 
		visitors the ability to get a reminder via email a couple days after reading, 
		allows them to add it to their calendar and put it in Astrid 
		(a popular to-do list for Android, iPhone and the web). A link link back to 
		your site will accompany the reminders so readers can remember that you inspired 
		them and come back for reference. The description is an optional way to provide 
		additional details, ideas, and links to resources.
		<br/>Want more infomation? <a href="http://astrid.com/widgets/create_rm_button" class="learn-more" target="_blank">Learn more</a>.
		<br/><a href="http://astrd.co/QtM5gy" class="astrid-reminder-link astrid-rm-btn a-small a-chk" target="_blank"><span class="a-chk-span">✓</span> Example Button</a> 
	</small>
</body>
</html>