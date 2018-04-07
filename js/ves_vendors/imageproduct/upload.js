/**
 * @author vesasty Teves
 * @copyright Copyright (c) 2010-2011 vesasty (http://www.vesasty.com)
 * @package vesasty_Imgupload
 */

var vesUploader = new Class.create();

vesUploader.prototype = {

    initialize: function(urlPost , productId, reloadTabUrl)
    {
        if (this.supported())
        {
            this.productId    = productId;
            this.reloadTabUrl = reloadTabUrl;
            this.urlPost = urlPost;
        }
        this.countFilesToUpload = 0;
    },

    supported: function()
    {
        if (window.File && window.FileList)
        {
            return true;
        }
        return false;
    },

    upload: function(file, formData)
    {
    	
    	var _this = this;
		 'use strict';
		 _this.startUpload();
		 var url = _this.urlPost;
		 this.refeshProgress();
		 $fileupload('#file_select').fileupload({
			 formData: {form_key: FORM_KEY ,product_id: this.productId},
		     url: url,
		     dataType: 'json',
		     done: function (e, data) {
		    	_this.uploadComplete(e,data);
		     },
		     fail: function (e, data) {
		     	_this.uploadFailed(e,data);
		     },
		     progressall: function (e, data) {
		        _this.trackProgress(e, data);
		     }
		 }).prop('disabled', !$fileupload.support.fileInput)
		     .parent().addClass($fileupload.support.fileInput ? undefined : 'disabled');
      
    },

    uploadComplete: function(e,data)
    {
    	this.addNewImage(e,data);
    	//this.checkAllUploaded();
    	$fileupload('#progress').hide();
    },

    startUpload: function()
    {
        this.countFilesToUpload++;
    },

    
    refeshProgress: function()
    {
    	var progress = parseInt(0);
        $fileupload('#progress .progress-bar').css(
            'width',
            progress + '%'
        );
    },
    
    trackProgress: function(e, data)
    {
    	$fileupload('#progress').show();
    	var progress = parseInt(data.loaded / data.total * 100, 10);
        $fileupload('#progress .progress-bar').css(
            'width',
            progress + '%'
        );
    },
	

    uploadFailed: function(e,data)
    {
        this.checkAllUploaded();
    },


    checkAllUploaded: function()
    {
        this.countFilesToUpload--;

        if (this.productId != 0 && this.productId && this.countFilesToUpload <= 0)
        {
            myVar = setTimeout(function(){
            	vesUploaderObject.reloadTabContents();
            }, 1000);
        }
    },

    reloadTabContents: function()
    {
    	  var form = $("vesupload_form").wrap("form");
    	  new Ajax.Updater($("product_info_tabs_product_images_content"), this.reloadTabUrl, {
    		  parameters: form.serialize(true),
              evalScripts: true
          });
    },

    addNewImage: function(e, data)
    {
        var imageData = data.result;
		if(imageData.error){
			alert(imageData.error);
			return false;
		}
        if (!$('ves_images_grid'))
        {
            var grid = document.createElement('div');
            grid.id = 'ves_images_grid';
            grid.addClassNvese('ves_images_grid');
            $('ves_images_grid_new_container').appendChild(grid);
            this.imgNum = 1;
        }

        var item = imItemTemplate.replace(/{i}/g, this.imgNum);
        item = item.replace(/{url}/g, imageData.url);
        item = item.replace(/{file}/g, imageData.file);

        $('ves_images_grid').innerHTML += item;

        Sortable.create('ves_images_grid', {
            tag: 'div',
            only: 'ves_item',
            handles: $$('#ves_images_grid div.' + imDragHandler),
            overlap: 'horizontal',
            constraint: false,
            onUpdate: function(){
                Sortable.sequence("ves_images_grid").each(function(idNum, i){
                    $('ves_images_grid_vesitem_' + idNum).select('.img-position-input').each(function(input){
                        input.value = i + 1;
                    });
                });
            }
        });


        
        this.imgNum++;
    }
};
