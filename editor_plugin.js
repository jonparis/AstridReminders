/**
 * AstridReminders JS
 *
 * PHP version 5
 * 
 * @package   AstridCTA
 * @author    Jon Paris (jon@astrid.com), Chris Lema (cflema@gmail.com) and Justin Kussow (jdkussow@gmail.com)
 * @copyright Copyright (c)2012 ALL RIGHTS RESERVED
 */

(function() {
    tinymce.create('tinymce.plugins.AstridRemindMe', {
        init : function(ed, url) {
            ed.addCommand('add_link_form', function() {
                var astrid_source_title = jQuery('#title').val();
                var astrid_source_url = "";
                var site_name = jQuery('#wp-admin-bar-site-name > a').html();
                var selection = tinyMCE.activeEditor.selection.getContent();
                if(jQuery("#view-post-btn > a") && jQuery("#view-post-btn > a").attr("href") != null)
                    astrid_source_url =  jQuery("#view-post-btn > a").attr("href");
                else if (jQuery("#referredby") && jQuery("#post_ID")){
                    var post_id = jQuery("#post_ID").val();
                    var source_base = jQuery("#editor-buttons-css").attr('href');
                    var until_str =  source_base.indexOf("/wp-includes");
                    var astrid_source_url = source_base.substring(0, until_str) + "?p=" + post_id;
                }
                ed.windowManager.open({
                    title : 'Astrid - "Remind Me" link or button',
                    file : url + '/link_editor/astrid_get_link.php?ar_source_url=' + encodeURIComponent(astrid_source_url) + 
                                '&ar_source_name=' +  encodeURIComponent(astrid_source_title) +
                                '&ar_text_selection=' + encodeURIComponent(selection) +
                                '&ar_site_name=' + encodeURIComponent(site_name),
                    width : 600 + parseInt(ed.getLang('AstridRemindMe.delta_width', 0)),
                    height : 520 + parseInt(ed.getLang('AstridRemindMe.delta_height', 0)),
                    inline : 1
                }, {
                    plugin_url : url
                });
            });

            ed.addButton('astrid_reminder', {
                title : 'Create a "remind me" link to create action item reminders',
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