/**
 * Vendor Import Export Product
 */

var VendorProductImages = new Class.create();

VendorProductImages.prototype = {
    initialize : function(tableId,config, data){
        this.table 	= $(tableId);
        this.massAction = $(tableId+'-massaction');
        this.config = config;
        this.data	= data;
        this.config.current_page = 1;
        this.rowTemplate 	= this.config.row_template;
        this.pagerTemplate 	= this.config.pager_template + ' | '+Translator.translate('View')+' <select id="pager-limit" name="limit"><option selected="selected" value="20">20</option><option value="30">30</option><option value="50">50</option><option value="100">100</option><option value="200">200</option></select> '+Translator.translate('per page');
        this.templateSyntax = /(^|.|\\r|\\n)({{(\w+)}})/;
        
        this.initPager();
        this.reset();
        this.loadImage();
        
    },
    /**
     * Init the pager
     */
    initPager: function(){
    	this.massAction.insert({before:'<div class="image-pager" id="product-images-pager"></div><div class="pager-controll"><a href="javascript: void(0);" id="pager-previous">'+Translator.translate('Previous')+'</a> | <a href="javascript: void(0);" id="pager-next">'+Translator.translate('Next')+'</a></div></div>'});
    	var _this = this;
    	$('pager-next').observe('click',function(){_this.nextPage();});
    	$('pager-previous').observe('click',function(){_this.previousPage();});
    	
    	this.pager = $('product-images-pager');
    },
    
    /**
     * Add new image to the image list
     */
    addImage: function(image){
    	var data = new Array();
    	var added = false;
    	if(this.data.size()){
	    	this.data.each(function(img, index){
	    		var tmpArr = new Array(img.file_name,image.file_name);
	    		tmpArr.sort();
	    		if(tmpArr[0] != img.file_name && !added){
	    			data.push(image);
	    			added = true;
	    		}
	    		
	    		data.push(img);
	    	});
	    	if(!added) data.push(image);
    	}else{
    		data.push(image);
    	}
    	/*Reset image list*/
    	this.data = data;
    	this.reset();
    	this.loadImage();
    },
    
    /**
     * Get the number of pages.
     */
    getPageCount: function(){
    	var tmp = this.data.size()/this.config.image_per_page;
    	var tmpInt = parseInt(tmp);
    	return (tmp > tmpInt?tmpInt+1:(tmpInt>0?tmpInt:1));
    },
    
    /**
     * Remove all current image
     */
    reset: function(){
    	this.table.select('tbody').first().update('');
    	
    	/*reload pager*/
    	var template = new Template(this.pagerTemplate, this.templateSyntax);
        var html = template.evaluate({current_page: this.config.current_page, page_count: this.getPageCount(), total_items: this.data.size()});
        this.pager.update(html);
        
        /*reset image per page*/
        var _this = this;
        $('pager-limit').observe('change',function(){_this.config.image_per_page = this.value;_this.loadImage();});
        $('pager-limit').value = this.config.image_per_page;
    },
    
    /**
     * Load all image of current page to the screen.
     */
    loadImage: function(){
    	this.reset();
    	if(this.data.size()){
	    	var i = 1;
	    	var currentPage = this.config.current_page;
	    	var imagesPerPage = this.config.image_per_page;
	    	var _this = this;
	    	this.data.each(function(image){
	    		if(i > (currentPage-1)*imagesPerPage && i <= (currentPage)*imagesPerPage){
	    			_this.insert(image);
	    		}
	    		i ++;
	    	});
    	}else{
    		this.table.select('tbody').first().update('<tr><td colspan="5" class="empty-text a-center">'+Translator.translate('No records found.')+'</td></tr>');
    	}
    	decorateTable(this.table);
    },
    
    /**
     * Load first page.
     */
    firstPage: function(){
    	this.config.current_page = 1;
    	this.loadImage();
    },
    
    /**
     * Load the next page.
     */
    nextPage: function(){
    	var pageCount = this.getPageCount();
    	if(this.config.current_page >= pageCount) {this.config.current_page = pageCount;return;}
    	this.config.current_page++;
    	this.loadImage();
    	
    },
    
    /**
     * Load the previous page.
     */
    previousPage: function(){
    	if(this.config.current_page <= 1) {this.config.current_page = 1;return;}
    	this.config.current_page--;
    	this.loadImage();
    	
    },
    
    /**
     * Insert an image
     */
    insert: function(image){
    	var template = new Template(this.rowTemplate, this.templateSyntax);
        var html = template.evaluate(image);
    	this.table.select('tbody').first().insert(html);
    },
    
    selectVisible:function(){
    	this.table.select('.image-checkbox').each(function(s){
    		s.checked = "checked";
    	});
    },
    unselectVisible: function(){
    	this.table.select('.image-checkbox').each(function(s){
    		s.checked = false;
    	});
    },
    
    /**
     * Delete images
     */
    deleteImages:function(){
    	var _this = this;
    	var files = new Array();
    	this.table.select('.image-checkbox').each(function(s){
    		if(s.checked) files.push(s.value);
    	});
    	console.log(files);
    	if(!files.size()) {alert(Translator.translate('Please select items.'));return;}
    	
    	/*Submit selected files to delete*/
    	new Ajax.Request(this.config.delete_url, {
            parameters :{files:files.join(',')},
            method :'post',
            onComplete :function(transport){
            	var result = transport.responseText.evalJSON();
            	if(result.success){
            		_this.data = result.images;
            		_this.loadImage();
            	}else{
            		alert(result.error); return;
            	}
            }
        });
    }
}