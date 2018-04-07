
var vesUploadSelect = new Class.create();

vesUploadSelect.prototype = {
    
    initialize: function(fileSelect)
    {
        this.fileSelect = fileSelect;
        this.attachListeners();
    },
    
    get: function()
    {
        return this;
    },
    
    attachListeners: function()
    {
        this.fileSelect.observe('change', this.onChange.bind(this));
    },
    
    onChange: function(event)
    {
        event.stopPropagation();
        event.preventDefault();
        
        this.uploader.handleFilesSelected(event);
    }
    
};
