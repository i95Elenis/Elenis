
/**
 * Custom Product Preview
 *
 * @category:    Aitoc
 * @package:     Aitoc_Aitcg
 * @version      11.2.2
 * @license:     n/a
 * @copyright:   Copyright (c) 2013 AITOC, Inc. (http://www.aitoc.com)
 */
Aitcg.AreaEditor = new Class.create();
Aitcg.AreaEditor.prototype =
{    
    // current option id
    optionId : null,

    // selected option image id
    imgId : null,
    
    // prefix used to access coords elements
    pfx : null,
    
    // information about image
    imgInfo : {},
    
    // some data
    currentImage : {},
    
    croper : null,
    
    initialize: function(optionId) {
        this.optionId = optionId;
        this.pfx = 'product_option_'+optionId+'_coords_';
    },
    
    loadEditor : function() {
        $('loading-mask').show();
        this.imgId = $(aitcgImageSelector.id+'_'+this.optionId).value;
        this.imgInfo = aitcgImageSelector.customImages[this.imgId];
        Aitcg.cacheImage(this.imgId, this.imgInfo['full_image'], this);
    },
    
    cached : function() {
        $('loading-mask').hide();
        this.showEditor();
    },
    
    setOnloadCoords : function()
    {
        return {
                x1 : Math.round(parseInt($(this.pfx+'offset_x').value?(parseInt($(this.pfx+'offset_x').value)-1):0)*this.currentImage.mult),
                y1 : Math.round(parseInt($(this.pfx+'offset_y').value?(parseInt($(this.pfx+'offset_y').value)-1):0)*this.currentImage.mult),
                x2 : Math.round((parseInt($(this.pfx+'size_x').value)?(parseInt($(this.pfx+'offset_x').value)+parseInt($(this.pfx+'size_x').value)+1):(Aitcg.cachedImages[this.imgId].width))*this.currentImage.mult),
                y2 : Math.round((parseInt($(this.pfx+'size_y').value)?(parseInt($(this.pfx+'offset_y').value)+parseInt($(this.pfx+'size_y').value)+1):(Aitcg.cachedImages[this.imgId].height))*this.currentImage.mult)
            }
    },
    setCurrentImageValue : function()
    {
        this.currentImage.origW=Aitcg.cachedImages[this.imgId].width;
        this.currentImage.origH=Aitcg.cachedImages[this.imgId].height;
        
        this.currentImage.mult = this.countMult();
        // calculating image view size
        //if (this.mult !== 1) { //!!!!!!!!!!! - to check
        if (this.currentImage.mult !== 1) {
            this.currentImage.width = Math.round(this.currentImage.origW*this.currentImage.mult);
            this.currentImage.height = Math.round(this.currentImage.origH*this.currentImage.mult);
        } else {
            this.currentImage.width = this.currentImage.origW;
            this.currentImage.height = this.currentImage.origH;
        }
        
        this.imgInfo['img_width'] = this.currentImage.width;
        this.imgInfo['img_height'] = this.currentImage.height;
        this.imgInfo['option_id'] = this.optionId;
        
    },
    showEditor : function() {
        //this.setCurrentImageValue();
        this.currentImage.origW=Aitcg.cachedImages[this.imgId].width;
        this.currentImage.origH=Aitcg.cachedImages[this.imgId].height;
        
        this.currentImage.mult = this.countMult();
        // calculating image view size
        //if (this.mult !== 1) { //!!!!!!!!!!! - to check
        if (this.currentImage.mult !== 1) {
            this.currentImage.width = Math.round(this.currentImage.origW*this.currentImage.mult);
            this.currentImage.height = Math.round(this.currentImage.origH*this.currentImage.mult);
        } else {
            this.currentImage.width = this.currentImage.origW;
            this.currentImage.height = this.currentImage.origH;
        }
        
        this.imgInfo['img_width'] = this.currentImage.width;
        this.imgInfo['img_height'] = this.currentImage.height;
        this.imgInfo['option_id'] = this.optionId;
        
        // some window rendering variables

        // window rendering
        var window = new Aitcg.Popup(PrintableAreaEditor, this.imgInfo);
        html = window.render();
        Element.insert($('anchor-content'),{before : html});

        var height = $('html-body').getHeight();
        $('message-popup-window-mask').setStyle({'height':height+'px'});
        $('message-popup-window').setStyle({
            'width'      : this.currentImage.width + 'px',
            'top'        : '' + parseInt((this.currentImage.scr.dim.height-this.currentImage.height-10)/2 + this.currentImage.scr.off.top) + 'px',
            'marginLeft' : '-'+(parseInt(this.currentImage.width/2+4))+'px'
        });
        Element.show('message-popup-window-mask');
        $('message-popup-window').addClassName('show');

        Draggables.drags = [];
        this.cropper = new Cropper.Img('printable-area-image', {
            onloadCoords : this.setOnloadCoords(),
            displayOnInit : true
        });
    },

    countMult : function() {
        this.currentImage.scr = {
            dim: document.viewport.getDimensions(),
            off: document.viewport.getScrollOffsets()
        };
        var mult = 1;
        var offsetX = 30;
        var offsetY = 100;

        if(this.currentImage.origW > (this.currentImage.scr.dim.width-offsetX)) {
            mult = (this.currentImage.scr.dim.width-offsetX) / this.currentImage.origW;
        }

        if(this.currentImage.origH > (this.currentImage.scr.dim.height-offsetY)) {
            var mY = (this.currentImage.scr.dim.height-offsetY) / this.currentImage.origH;
            if (mY < mult) {
                mult = mY;
            }
        }
        return mult;
    },
    
    applyCoords : function() {
        var coords = this.cropper.areaCoords;
        $(this.pfx+'offset_x').value = Math.round((coords.x1+1)/this.currentImage.mult);
        $(this.pfx+'offset_y').value = Math.round((coords.y1+1)/this.currentImage.mult);
        $(this.pfx+'size_x').value = Math.round((coords.x2-coords.x1-2)/this.currentImage.mult);
        $(this.pfx+'size_y').value = Math.round((coords.y2-coords.y1-2)/this.currentImage.mult);
        this.closeEditor();
    },
    
    closeEditor : function() {
        this.cropper.remove();
        Draggables.drags = [];
        $('message-popup-window').remove();
        $('message-popup-window-mask').remove();
        aitcgImageSelector.reloadImage(this.optionId);
    }
};