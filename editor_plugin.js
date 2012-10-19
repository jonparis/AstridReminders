
(function() {
    tinymce.create('tinymce.plugins.AstridRemindMe', {
        init : function(ed, url) {
            ed.addCommand('add_link_form', function() {
                var astrid_source_title = jQuery('#title').val();
                var astrid_source_url = "";
                jQuery("#view-post-btn > a").attr("href")
                var selection = tinyMCE.activeEditor.selection.getContent();
                if(jQuery("#view-post-btn > a") && jQuery("#view-post-btn > a").attr("href") != null)
                    astrid_source_url =  jQuery("#view-post-btn > a").attr("href");
                else if (jQuery("#referredby") && jQuery("#post_ID")){
                    var post_id = jQuery("#post_ID").val();
                    var referredby = jQuery("#referredby").val();
                    var until_str =  referredby.indexOf("/wp-admin");
                    var astrid_source_url = referredby.substring(0, until_str) + "?p=" + post_id;
                }
                ed.windowManager.open({
                    title : 'Astrid - "Remind Me" link or button',
                    file : url + '/astrid_get_link.php?source_url=' + encodeURIComponent(astrid_source_url) + 
                                '&source_name=' +  encodeURIComponent(astrid_source_title) +
                                '&selection=' + encodeURIComponent(selection),
                    width : 600 + parseInt(ed.getLang('AstridRemindMe.delta_width', 0)),
                    height : 520 + parseInt(ed.getLang('AstridRemindMe.delta_height', 0)),
                    inline : 1
                }, {
                    plugin_url : url
                });
            });

            ed.addButton('astrid_reminder', {
                title : 'astrid_reminder.astrid_remind_me',
                image : url+'/images/astrid_32.png',
                cmd : 'add_link_form'
            });
        },
        createControl : function(n, cm) {
            return null;
        },
        getInfo : function() {
            return {
                longname : "Astrid Remind Me Link Creator",
                author : 'Jon Paris',
                authorurl : 'http://astrid.com/',
                infourl : 'http://astrid.com/',
                version : "1.0"
            };
        }
    });
    tinymce.PluginManager.add('astrid_reminder', tinymce.plugins.AstridRemindMe);
})();