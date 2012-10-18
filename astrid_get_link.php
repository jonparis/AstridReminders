<!DOCTYPE html>
	<head>
		<link rel="stylesheet" type="text/css" href="http://netdna.bootstrapcdn.com/twitter-bootstrap/2.1.0/css/bootstrap-combined.min.css" />
		<style>
			#due_in_days { width: 20px }
		</style>
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
		<script type="text/javascript" src="../../../wp-includes/js/tinymce/tiny_mce_popup.js"></script>

		<script>
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
			        var url = "http://pre.act.fm/widgets/remind_me_link";
			        var fallback_url = "http://astrid.com/new?title="+ title + "&notes=" + notes + "&due_in_days=" + due_in_days +
			        					"&source_url=" + source_url + "&source_name=" + source_name;
			       	var data = { title: title, notes: notes, due_in_days : due_in_days };
			       	var url_title = "get reminder via email, calendar or, to-do list";

					jQuery.ajax({               
		                type: "POST",
		                url: url,
		                data: data, 
		                success: function(data){  
		                    tinyMCEPopup.execCommand('mceInsertContent',false,
		                    	'<a class="astrid_reminder_link" href="'+ data.url + '" title="' + url_title + '">'+$("#title").val()+'</b>');
				        	tinyMCEPopup.close();
		                },
		                error : function(data){ 
		                	tinyMCEPopup.execCommand('mceInsertContent',false,
		                		'<a class="astrid_reminder_link" href="'+ fallback_url + '" title="' + url_title + '">'+$("#title").val()+'</b>');
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
		Add reminder link
		<small style="display:none">
			Reminder links help others remember and act on your suggestions. It gives visitors the ability to get a 
			reminder via email a couple days after reading, allows them to add it to their calendar and put it in Astrid 
			(a popular to-do list for Android, iPhone and the web) Add a title for the reminder, 
			a short note/description to help the user act (ideas, implementation details, links to resources etc,).
		</small>
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
			<div class="control-group hide">
				<div class="controls">
					<a href="javascript:;" onclick="toggle_button_format()">Edit button style</a>
				</div>
			</div>
			<div class="control-group button-style-controls hide">
				<label class="control-label inline" for="button_size">Size</label>
				<div class="controls">
					<div class="button-size">
						<label class="inline radio">
							<input checked="checked" id="button_size_mini" name="button_size" type="radio" value="mini">
								mini
							<div style="margin-top:10px">
								<div class="astrid btn btn-mini remind-me">
									Remind me
								</div>
							</div>
						</label>
					</div>
					<div class="button-size">
						<label class="inline radio">
							<input id="button_size_small" name="button_size" type="radio" value="small">
								small
							<div style="margin-top:10px">
								<div class="astrid btn btn-small remind-me">
									Remind me
								</div>
							</div>
						</label>
					</div>
					<div class="button-size">
						<label class="inline radio">
							<input id="button_size_large" name="button_size" type="radio" value="large">
								large
							<div style="margin-top:10px">
								<div class="astrid btn btn-large remind-me">
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
							<input checked="checked" id="button_style_astrid" name="button_style" type="radio" value="astrid">
							<img alt="Astrid_btn_small" border="0" height="24" src="//dui76s50qcnvl.cloudfront.net/assets/widgets/astrid_btn_small-c73b7fd10cd94d701419421b217391bf.png" width="24">
								astrid
						</label>
					</div>
					<div class="button-style">
						<label class="inline radio">
							<input id="button_style_checkmark" name="button_style" type="radio" value="checkmark">
							<span class="check">✓</span>
								checkmark
						</label>
					</div>
					<div class="button-style">
						<label class="inline radio">
							<input id="button_style_text_only" name="button_style" type="radio" value="text_only">
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
					<a href="javascript:;" class="btn btn-primary" style="color:white" onclick="AstridLink.insert(AstridLink.e)">Add reminder link</a>
				</div>
			</div>
		</div>
	</form>
</body>
</html>