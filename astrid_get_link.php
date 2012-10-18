<!DOCTYPE html>
	<head>
		<link rel="stylesheet" type="text/css" href="http://netdna.bootstrapcdn.com/twitter-bootstrap/2.1.0/css/bootstrap-combined.min.css" />
		<link rel="stylesheet" type="text/css" href="astridcta.css" />
		<style>
			#due_in_days { width: 20px }
			a.learn-more:link, a.link  { color: #08c}
		    #created_task_link{ word-wrap:break-word; }
		    #notes { height:70px }
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
		</style>
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
		<script type="text/javascript" src="../../../wp-includes/js/tinymce/tiny_mce_popup.js"></script>
		<script type="text/javascript" src="astridcta.js"></script>

		<script>
			var toggle_button_format = function(){
    			$(".button-style-controls").toggle();
  			}
			var AstridLink = {
			    e: '',
			    init: function(e) {
			        AstridLink.e = e;
			        tinyMCEPopup.resizeToInnerSize();
			    },
			    insert: function createGalleryShortcode(e) {
			        //Create gallery Shortcode
			        var title = encodeURIComponent($('#title').val());
			        var notes = encodeURIComponent($('#notes').val());
			        var source_url = $('#source_url').val();
			        var source_name = $('#source_name').val();
			        var due_in_days = encodeURIComponent($('#due_in_days').val());
					var use_button = $('input[name=link_or_button]:checked').val() == "button";
					var button_size = $('input[name=button_size]:checked').val();
					var button_style = $('input[name=button_style]:checked').val();
					var button_title = $('#button_title').val();

			        var url = "http://astrid.com/widgets/remind_me_link";
			        var fallback_url = "http://astrid.com/new?title="+ title + "&notes=" + notes + "&due_in_days=" + due_in_days +
			        					"&source_url=" + source_url + "&source_name=" + source_name;
			       	var data = { title: title, notes: notes, due_in_days : due_in_days };
			       	var url_title = "get reminder via email, calendar or, to-do list";
			       	var link_class = "astrid-reminder-link";

			       	var display_title = use_button ? '<span class="a-chk-span">&#x2713;</span>' + button_title : "&#x2713; " + $("#title").val();
			       	if (use_button)
			       		link_class += " astrid-rm-btn " + button_size + " " + button_style

			       	function construct_link(href) {
			       		return '<a class="'+ link_class + '" href="'+ href + '" title="' + url_title + '">' + display_title + '</a>';
			       	}
					jQuery.ajax({               
		                type: "POST",
		                url: url,
		                data: data, 
		                success: function(data){  
		                    tinyMCEPopup.execCommand('mceInsertContent',false, construct_link(data.url));
				        	tinyMCEPopup.close();
		                },
		                error : function(data){ 
		                	tinyMCEPopup.execCommand('mceInsertContent',false, construct_link(fallback_url));
				        	tinyMCEPopup.close();
		                }                 
		            });  
			    }
			}
			tinyMCEPopup.onInit.add(AstridLink.init, AstridLink);   
		</script>
	</head>

<body>
	<h3>
		Add "Remind Me" Link
	</h3>
	<form action="" method="post" name="get_link_form" id="get_link_form">
		<input type="hidden" value = "<?php echo $_GET['source_url']; ?>" id="source_url" name="source_url"/>
		<input type="hidden" value = "<?php echo $_GET['source_name']; ?>" id="source_name" name="source_name" />
		<div class="form-horizontal" id="task_fields">
			<div class="control-group">
				<label class="control-label" for="title" title="Title is required">Reminder title</label>
				<div class="controls">
					<input id="title" name="title" placeholder="Name of the reminder" type="text" value="">
					(required)
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="notes">Description</label>
				<div class="controls">
					<textarea id="notes" name="notes" placeholder="Longer description with additional instruction"></textarea>
				</div>
			</div>
			<div class="control-group" id="relative_date">
				<label class="control-label" for="due_in_days">Send reminder</label>
				<div class="controls input-append thin-input" style="margin-left: 20px;">
					<input id="due_in_days" name="due_in_days" type="text" value="2">
					<div class="add-on">
						days after added
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
					<div class="link-or-button" onclick="toggle_button_format()">
						<label class="inline radio">
							<input checked="checked" id="link_or_button_link" name="link_or_button" type="radio" value="link">
								link
						</label>
					</div>
					<div class="link-or-button" onclick="toggle_button_format()">
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
				<label class="control-label" for="button_title">Button text</label>
				<div class="controls">
					<input id="button_title" name="button_title" type="text" value="Remind me">
				</div>
			</div>
			<div class="control-group">
				<div class="controls">
					<a href="javascript:;" class="btn btn-primary" style="color:white" onclick="AstridLink.insert(AstridLink.e)">Add Remind Me link</a>
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