
/**
 * Custom Product Preview
 *
 * @category:    Aitoc
 * @package:     Aitoc_Aitcg
 * @version      11.2.2
 * @license:     n/a
 * @copyright:   Copyright (c) 2013 AITOC, Inc. (http://www.aitoc.com)
 */
Product.AitGallery = Class.create(Product.Gallery,  
    {
    handleUploadComplete : function(files) {
        files.each( function(item) {
            if (!item.response.isJSON()) {
                try {
                    console.log(item.response);
                } catch (e2) {
                    alert(item.response);
                }
                return;
            }
            var response = item.response.evalJSON();
            if (response.error) {
                return;
            }
            var newImage = {};
            newImage.url = response.url;
            newImage.file = response.file;
            newImage.label = '';
            newImage.position = this.getNextPosition();
            newImage.disabled = 0;
            newImage.removed = 0;
            newImage.cgimage = 0;
            this.images.push(newImage);
            this.uploader.removeFile(item.id);
        }.bind(this));
        this.container.setHasChanges();
        this.updateImages();
    },
        
    confirmationAsked: false,    
    updateImage : function(file) {
        var index = this.getIndexByFile(file);
        this.images[index].label = this
                .getFileElement(file, 'cell-label input').value;
        this.images[index].position = this.getFileElement(file,
                'cell-position input').value;
        this.images[index].removed = (this.getFileElement(file,
                'cell-remove input').checked ? 1 : 0);
        this.images[index].disabled = (this.getFileElement(file,
                'cell-disable input').checked ? 1 : 0);
        this.images[index].cgimage = (this.getFileElement(file,
                'cell-cgimage input').checked ? 1 : 0);
        if(this.confirmationAsked == false && this.images[index].removed==1 && this.images[index].cgimage==1) {
            if(!window.confirm(removeAitcgConfirmText)) {
                this.images[index].removed = 0;
                this.getFileElement(file,'cell-remove input').checked = false;
            } else {
                this.confirmationAsked = true;                
            }
        }
        this.getElement('save').value = Object.toJSON(this.images);
        this.updateState(file);
        this.container.setHasChanges();
    },
    updateVisualisation : function(file) {
        var image = this.getImageByFile(file);
        this.getFileElement(file, 'cell-label input').value = image.label;
        this.getFileElement(file, 'cell-position input').value = image.position;
        this.getFileElement(file, 'cell-remove input').checked = (image.removed == 1);
        this.getFileElement(file, 'cell-disable input').checked = (image.disabled == 1);
        this.getFileElement(file, 'cell-cgimage input').checked = (image.cgimage == 1);
        $H(this.imageTypes)
                .each(
                        function(pair) {
                            if (this.imagesValues[pair.key] == file) {
                                this.getFileElement(file,
                                        'cell-' + pair.key + ' input').checked = true;
                            }
                        }.bind(this));
        this.updateState(file);
    }
    
});