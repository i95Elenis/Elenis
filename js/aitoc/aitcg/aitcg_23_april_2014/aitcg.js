
/**
 * Custom Product Preview
 *
 * @category:    Aitoc
 * @package:     Aitoc_Aitcg
 * @version      11.2.2
 * @license:     n/a
 * @copyright:   Copyright (c) 2013 AITOC, Inc. (http://www.aitoc.com)
 */
Aitcg = 
{
    editors: {},
    cachedImages : {},
    
    getImageData : function( imgId ) {
        return {
            origW:this.cachedImages[imgId].width,
            origH:this.cachedImages[imgId].height
        }        
    }, 
    
    getDefault : function() {
        return {
            dim: document.viewport.getDimensions(),
            off: document.viewport.getScrollOffsets(),
            curr: {}
        };        
    },
    
    loadAreaEditor : function(optionId) {
        if(typeof(this.editors['ed_'+optionId]) == 'undefined') {
            this.editors['ed_'+optionId] = new Aitcg.AreaEditor(optionId);
        }
        var editor = this.editors['ed_'+optionId];
        editor.loadEditor();
    },    
    
    clone : function(el) {
        if(typeof(JSON)!='undefined') {
            return eval('(' + JSON.stringify(el) + ')');
        } else {
            return this.deepCloneJSON(el);
        }
    },
    
    deepCloneJSON: function(obj) {
        var outpurArr = new Array();
        for (var i in obj) {
            outpurArr[i] = typeof (obj[i]) == 'object' ? this.deepCloneJSON(obj[i]) : obj[i];
        }
        return outpurArr;
    },        
    
    cacheImage : function(imgId, path, callback) {
        if(typeof(this.cachedImages[imgId]) == 'undefined') {
            
            this.cachedImages[imgId] = document.createElement('img');
            var img = this.cachedImages[imgId];
    
            Event.observe(img,'load',callback.cached.bind(callback))
            img.src = path;
            
        }else{
            callback.cached();
        }
    },     
    
    countMult : function( imgId ) {
        scr = this.getDefault();
        if(imgId != 0 && typeof(this.cachedImages[imgId])!='undefined') {
            scr.orig = this.getImageData(imgId);
        } else {
            scr.orig = {origW:arguments[1],
                origH:arguments[2]};
        }
        var mult = 1;
        var offsetX = 30;
        var offsetY = 75;

        if(scr.orig.origW > (scr.dim.width-offsetX)) {
            mult = (scr.dim.width-offsetX) / scr.orig.origW;
        
        }

        if(scr.orig.origH > (scr.dim.height-offsetY)) {
            var mY = (scr.dim.height-offsetY) / scr.orig.origH;
            if (mY < mult) {
                mult = mY;
            }
        }
        
        if(typeof(arguments[3])!='undefined')
        {
            mult=1;
        }
        scr.mult = mult;
        scr.curr.width = Math.round(scr.orig.origW * mult);
        scr.curr.height = Math.round(scr.orig.origH * mult);
        /*if (mult !== 1) {
            scr.curr.width = Math.round(scr.orig.origW * mult);
            scr.curr.height = Math.round(scr.orig.origH * mult);
        } else {
            scr.curr.width = scr.orig.origW;
            scr.curr.height = scr.orig.origH;
        }*/
        return scr;

    },
    
    sizesCache: {},

    /**
     *  if you've made changes in this method be sure to
     *  make same changes in Aitoc_Aitcg_Model_Image
     */        
    checkSizes : function(imgX, imgY, maxX, maxY) {
        imgX = imgX * 2;
        imgY = imgY * 2;
        var id = 'id'+imgX + '_'+imgY+'_'+maxX+'_'+maxY;
        if(typeof(this.checkSizes[id]) != 'undefined') {
            return this.checkSizes[id];
        }
        var minpx = 0;
        var x = imgX/maxX,
            y = imgY/maxY;
        var ret = {};
        if( x > y && x > 1) {
            ret =  this.calcSizes(imgX,imgY, maxX, maxY);
            ret.axis = 'x';
        } else if( y > 1 ){
            var t = this.calcSizes(imgY,imgX, maxY, maxX);
            ret = {x:t.y,y:t.x};
            ret.axis = 'y';
        } else {
            ret = {x:imgX,y:imgY};
            ret.axis = 'b';
        }
        if(ret.x > maxX || ret.y > maxY) {
            var scale_x = maxX / ret.x,
                scale_y = maxY / ret.y;
            if( scale_x < scale_y ) {
                ret.y = ret.y * ret.x / maxX;
                ret.x = maxX;
                ret.axis = 'y';
            } else {
                ret.x = ret.x * ret.y / maxY;
                ret.y = maxY;
                ret.axis = 'x';
            }
        }
        ret["posX"] = Math.round((maxX-ret.x)/2);
        ret["posY"] = Math.round((maxY-ret.y)/2);
        ret.mult = ret.x / imgX;
        return ret;
    },

    calcSizes : function(a, b, maxa, maxb) {
        maxa = maxa;
        maxb = b * maxa / a;
        return {x : maxa,y : maxb};
    }
};

function getEventTarget(e)
{
    if(e)return e.target;
    return window.event.srcElement;
}
function toggleNextElement(elementThis)
{
    elementThis.next().toggle(); 
    elementThis.descendants().invoke('toggle');
}
function saveAsSvg(elementCimage)
{
    if (!Validation.validate('print_scale'))
    {
        return false;
    }
    elementCimage.editor.e.unselect();
    var data = elementCimage.getPrintableVersion($('print_scale').getValue());
    $('savedisc_type'+ elementCimage.rand).setValue(Raphael.type);
    $('savedisc'+ elementCimage.rand).setValue(data);
}

function saveAsPdf(elementCimage)
{
    if (!Validation.validate('print_scale') && $('print_scale').getValue() > 15)
    {
        return false;
    }
    elementCimage.editor.e.unselect();
    $('print_scale_pdf').setValue($('print_scale').getValue());
    var data = elementCimage.getPrintableVersion(1);
    $('savedisc_type'+ elementCimage.rand+'_pdf').setValue(Raphael.type);
    $('savedisc'+ elementCimage.rand+'_pdf').setValue(data);
}