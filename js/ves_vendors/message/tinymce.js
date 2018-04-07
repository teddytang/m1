/**
 * 
 */
tinyMceWysiwygSetup.prototype.getSettings=function(mode){
	var plugins = '';

    var settings = {
        mode : (mode != undefined ? mode : 'none'),
        elements : this.id,
        theme : 'advanced',
        plugins : plugins,
        theme_advanced_buttons2 : '',
        //theme_advanced_buttons1 : 'magentowidget,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,fontselect,fontsizeselect',
        //theme_advanced_buttons2 : 'cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,forecolor,backcolor',
        //theme_advanced_buttons3 : 'tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,iespell,media,advhr,|,ltr,rtl,|,fullscreen',
        //theme_advanced_buttons4 : 'insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,pagebreak',
        theme_advanced_toolbar_location : 'top',
        theme_advanced_toolbar_align : 'left',
        theme_advanced_statusbar_location : 'bottom',
        theme_advanced_resizing : true,
        convert_urls : false,
        relative_urls : false,
        content_css: this.config.content_css,
        custom_popup_css: this.config.popup_css,
        magentowidget_url: this.config.widget_window_url,
        doctype : '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">',

        setup : function(ed) {
            ed.onSubmit.add(function(ed, e) {
                varienGlobalEvents.fireEvent('tinymceSubmit', e);
            });

            ed.onPaste.add(function(ed, e, o) {
                varienGlobalEvents.fireEvent('tinymcePaste', o);
            });

            ed.onBeforeSetContent.add(function(ed, o) {
                varienGlobalEvents.fireEvent('tinymceBeforeSetContent', o);
            });

            ed.onSetContent.add(function(ed, o) {
                varienGlobalEvents.fireEvent('tinymceSetContent', o);
            });

            ed.onSaveContent.add(function(ed, o) {
                varienGlobalEvents.fireEvent('tinymceSaveContent', o);
            });

            ed.onChange.add(function(ed, l) {
                varienGlobalEvents.fireEvent('tinymceChange', l);
            });

            ed.onExecCommand.add(function(ed, cmd, ui, val) {
                varienGlobalEvents.fireEvent('tinymceExecCommand', cmd);
            });
        }
    };

    // Set the document base URL
    if (this.config.document_base_url) {
        settings.document_base_url = this.config.document_base_url;
    }

    if (this.config.files_browser_window_url) {
        settings.file_browser_callback = function(fieldName, url, objectType, w) {
            varienGlobalEvents.fireEvent("open_browser_callback", {win:w, type:objectType, field:fieldName});
        };
    }

    if (this.config.width) {
        settings.width = this.config.width;
    }

    if (this.config.height) {
        settings.height = this.config.height;
    }

    return settings;
}