
(function() {
    var astrid_source_title = document.getElementById('title').value;
    var astrid_source_url = ""
    if(document.getElementById('sample-permalink') != null)
        astrid_source_url =  document.getElementById('sample-permalink').innerHTML;
    tinymce.create('tinymce.plugins.AstridRemindMe', {
        init : function(ed, url) {
            ed.addCommand('add_link_form', function() {
                ed.windowManager.open({
                    file : url + '/astrid_get_link.php?source_url=' + encodeURIComponent(astrid_source_url) + '&source_name=' +  encodeURIComponent(astrid_source_title),
                    width : 600 + parseInt(ed.getLang('AstridRemindMe.delta_width', 0)),
                    height : 450 + parseInt(ed.getLang('AstridRemindMe.delta_height', 0)),
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