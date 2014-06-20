
/**
 * Custom Product Preview
 *
 * @category:    Aitoc
 * @package:     Aitoc_Aitcg
 * @version      11.2.2
 * @license:     n/a
 * @copyright:   Copyright (c) 2013 AITOC, Inc. (http://www.aitoc.com)
 */
Aitcg.Uploader = new Class.create();
Aitcg.Uploader.prototype =
{
    //unique Id of class, must be reloaded
    id: "",
    lastUploadedFile: {},
    imgData : {},
    hidden : false,
    editorEnabled: false,
    force_show : false,

    window: null,
    paper : null,
    raph : null,
    imgThumbSelector : null,
    
    updateUrl : '/aitcg/ajax/update',
    
    initialize : function(id) {
        this.id = id;
        this.imgThumbSelector = '#' +this.id + '_imagediv div.th';
    },
    
    toggleDelete: function() {},
    
    getThumbnailImgHtml: function(productUrl, previewUrl) {
        var template = new Template('<img src="{{productUrl}}" class="bg" /><div class="th"><div><img src="{{previewUrl}}" class="ith" /></div></div>', /(^|.|\r|\n)({{(\w+)}})/);
        return template.evaluate({productUrl:productUrl,previewUrl:previewUrl});        
    },
    
    getPopupTemplate: function() {
        var str = '<div id="message-popup-window-mask" onclick="opFile{{rand}}.closeEditor();"></div>'+
            '<div id="message-popup-window" class="message-popup print-area-editor">'+
                '<div class="message-popup-head">'+
                    '<a href="#" onclick="opFile{{rand}}.closeEditor(); return false;" title="{{close_text}}"><span>{{close_text}}</span></a>'+
                    '<h2></h2>'+
                '</div>'+
                '<div class="aitclear"></div>'+
                '<div class="message-popup-ait" style="width:{{img_width}}px;height:{{img_height}}px">'+
                    '<img src="{{full_image}}" id="printable-area-image" width="{{img_width}}" height="{{img_height}}" alt="" />'+
                    '<div id="{{option_id}}_raph" class="message-popup-aitraph" style="left:{{left}};top:{{top}};width:{{width}};height:{{height}};"></div>'+
                '</div>'+
                '<div class="message-popup-head"  id="qqq">'+
                '</div>'+
            '</div>';
        return str;
    },
    
   
    updateData: function(useOnSuccess) {
        var param = this.imgData;
        param.template_id = this.tempId;
        param.product_id = this.product_id;
        if(typeof(this.cart_option_id)!='undefined') {
            param.cart_option_id = this.cart_option_id;
            param.cart_item_id = this.cart_item_id;
        }
        var func;
        if(useOnSuccess) {
            func = function(response) {
                this.updateThumbnail(response.responseJSON).bind(this);
            }.bind(this);
        } else {
            func = function(response) {}.bind(this);
        }
        param.aittype = this.aittype;
        new Ajax.Request(this.updateUrl, {
            method: 'post',
            parameters: param,
            evalJSON: 'force',
            onSuccess: func
        });
        return false;
    },            
    
    loadThumbnailImage: function( tempId, tempThumbUrl, tempFullUrl, param  ) 
    {
        this.tempThumbUrl = tempThumbUrl;
        this.tempFullUrl = tempFullUrl;
        this.tempImageSizes = {full_x: param.full_x, full_y: param.full_y};
        var el = $(this.id + '_imagediv');
            el.update( this.getThumbnailImgHtml(this.productImageThumbnailUrl, tempThumbUrl) ).show();
        if(this.obs == 0) {
            el.observe('click', this.showEditor.bind(this));
            this.obs = 1;
        }
        this.applyImgData(param);
        this.setTemplateId(tempId);            
    },
    

    toggleFileChange: function(inputBox) {
        this.initializeFile(inputBox);
        inputBox.toggle();
        this.fileChangeFlag = this.fileChangeFlagForse ? true : (this.fileChangeFlag ? false : true);
        if (!this.fileDeleteFlag) {
            if (this.fileChangeFlag) {
                 this.inputFileAction.value = 'save_new';
                 this.inputFile.disabled = false;
             } else {
                 this.inputFileAction.value = 'save_old';
                 this.inputFile.disabled = true;
             }
        }
    },

    toggleFileDelete: function(fileDeleteFlag, inputBox) {
        this.initializeFile(inputBox);
        this.fileDeleteFlag = fileDeleteFlag.checked ? true : false;
        if (this.fileDeleteFlag) {
            this.inputFileAction.value = '';
            this.inputFile.disabled = true;
            this.previewFile.disabled = true;
            $(this.id + '_template_id').value = this.tempId;
            this.fileNameBox.setStyle({'text-decoration': 'line-through'});
        } else {
            this.inputFileAction.value = this.fileChangeFlag ? 'save_new' : 'save_old';
            this.inputFile.disabled = (this.fileChangeFlag == 'save_old');
            this.fileNameBox.setStyle({'text-decoration': 'none'});
            this.previewFile.disabled = false;
            $(this.id + '_template_id').value = 0;
        }
        $(this.id + '_template_type').value = this.aittype;
    },
    
    manageUploadedFile : function(response) 
    {
        if(response.error != '0') {
            if(response.message != "")
                $(this.id + "_error").update(response.message).show();
        } else {
            this.updateImage(response);
            this.toggleInputFileFields();
            this.lastUploadedFile = response;
            
            this.loadThumbnailImage(response.temp_id, response.temp_thumbnail_url, response.temp_image_url, {
                full_x: response.preview_width, 
                full_y: response.preview_height,
                x : response.x,
                y : response.y,
                scale_x : response.scale_x,
                scale_y : response.scale_y,
                angle :  response.angle
            });
            
            this.imgData.x = this.defaultCoords.x;
            this.imgData.y = this.defaultCoords.y;
            this.updateData(false);
        }
    },
    
    setTemplateId: function( id )
    {
        if($(this.id + '_template_id') != null)
            $(this.id + '_template_id').value = id;
        this.tempId = id;
    },
    
    updateImage: function(resp) 
    {
        $$('#'+this.id + '_filename span')[0].update(resp.file_name);
    },
    
    toggleInputFileFields: function() 
    {
        if(this.hidden) {                
            $(this.id + '_hidden').value='';
            $(this.id + '_id').show();
            $(this.id + '_filename').hide();
        } else {
            if(!$(this.id + '_full')||$(this.id + '_full').value=='') {
                $(this.id + '_hidden').value='1';
            }
            $(this.id + '_id').parentNode.innerHTML = $(this.id + '_id').parentNode.innerHTML;
            $(this.id + '_id').hide();
            $(this.id + '_filename').show();
        }
        this.hidden =  ! this.hidden;
    }, 
    
   
    closeEditor : function() {
        this.window.closeEditor( );
        this.window = null;
    },
    
    showEditor : function() {
        if( this.window != null ) {
            this.closeEditor();
        }
        this.window = new Aitcg.Popup(this.getPopupTemplate(), {            
            full_image : this.productImageFullUrl,
            rand: this.rand,
            option_id: this.id,
            close_text: this.text.close,
            apply_text: this.text.apply,
            reset_text: this.text.reset,
            areaSizeX: this.areaSizeX,
            areaSizeY: this.areaSizeY,
            areaOffsetX: this.areaOffsetX,
            areaOffsetY: this.areaOffsetY
        } );
        this.window.renderWindow( this.id, this.productImageFullUrl, this );
    },

    cached : function() {
        $('loading-mask').hide();
        var scr = this.window.showWindow( this.id );
        
        var optdata = {
            width  : Math.floor(this.areaSizeX * scr.mult),
            height : Math.floor(this.areaSizeY * scr.mult),
            left : Math.max(0, Math.round(this.areaOffsetX * scr.mult - 1)),
            top  : Math.max(0, Math.round(this.areaOffsetY * scr.mult - 1))
        };
        var options = {
            width  : optdata.width + 'px',
            height : optdata.height+ 'px',
            left :   optdata.left  + 'px',
            top  :   optdata.top   + 'px'
        };
        
        this.shownScr = scr;
        this.shownScr.opt = options;
        
        var el = $(this.id + "_raph");
        el.setStyle(options);
        this.raph = new Aitcg.Raph( el, optdata.width, optdata.height, 50, 50);
        scr.img_mult = this.raph.addThumbnail( this.tempFullUrl, this.tempImageSizes, scr.mult );
        if(this.editorEnabled) {
            this.raph.setImageDrag();
            this.raph.addResizeSquare(1,1, "se");//bottom,right, css
            
            this.raph.addRotate(1,0, 'pointer');//bottom-left
        }
        
        this.scr = scr;
        this.raph.applyImageData( this.getImgData(scr.mult, scr.img_mult) );
    },
    
    show : function(el) {
        var str = "";
        for(var i in el) {
            str += i+" -> "+el[i]+"\n";        
            if(typeof(el[i])=='object') {
                for(var j in el[i]) {
                    if(typeof(el[i][j])!='function')
                        str += '---'+j+' -> '+el[i][j]+"\n";
                }
            }
        }
        if(typeof(arguments[1])!='undefined') {
            str = arguments[1] + ": \n"+ str;
        }
        alert(str);
    },
    
    getImgData : function ( mult, img_mult ) {
        var data = Aitcg.clone(this.imgData);
        if(mult !== 1) {
            data.x = data.x * mult;
            data.y = data.y * mult;
            if(img_mult == 1) {
                data.scale_x *= mult;
                data.scale_y *= mult;
            }
        }
        return data;
    },
    
    formatImgData : function ( coords ) {
        if(typeof(this.scr.mult) != 'undefined' && this.scr.mult !== 1) {
            coords.x /= this.scr.mult;
            coords.y /= this.scr.mult;
            if(this.scr.img_mult == 1) {
                coords.scale_x /= this.scr.mult;
                coords.scale_y /= this.scr.mult;
            }
        }
        return coords;
    },

    /**
     *  if you've made changes in this method be sure to
     *  make same changes in Aitoc_Aitcg_Model_Image
     */
    getDivRotated : function( box ) { 
        var width, height, axis;
        if(typeof(box.width) !='undefined') {
            width = box.width;
            height = box.height;
            axis = 'b';
        } else {
            var size = Aitcg.checkSizes( box.full_x, box.full_y, this.areaSizeX, this.areaSizeY );
            width  = size.x * box.scale_x;
            height = size.y * box.scale_y;
            axis = size.axis;
        } 
                    
        if( box.angle != 0 ) {
            var degreesAsRadians = box.angle * Math.PI / 180,
                points = new Array(); 
                    points.push({x: 0, y: 0}); 
                    points.push({x: width, y: 0}); 
                    points.push({x: 0, y: height}); 
                    points.push({x: width, y: height}); 
            var bb = new Array();
            var newX, newY;
            bb['left'] = box.x+width;bb['right'] = 0;bb['top'] = box.y+height;bb['bottom'] = 0; 

            for (_px = 0; _px < points.length; _px++) { 
                var p = points[_px]; 
                newX = parseInt((p.x * Math.cos(degreesAsRadians)) + (p.y * Math.sin(degreesAsRadians))); 
                newY = parseInt((p.x * Math.sin(degreesAsRadians)) + (p.y * Math.cos(degreesAsRadians))); 
                bb['left'] = Math.min(bb['left'], newX); 
                bb['right'] = Math.max(bb['right'], newX); 
                bb['top'] = Math.min(bb['top'], newY); 
                bb['bottom'] = Math.max(bb['bottom'], newY); 
            } 

            box.newWidth = parseInt(Math.abs(bb['right'] - bb['left'])); 
            box.newHeight = parseInt(Math.abs(bb['bottom'] - bb['top'])); 
            box.newX = (box.x + (width) / 2) - box.newWidth / 2; 
            box.newY = (box.y + (height) / 2) - box.newHeight / 2; 
        } else {
            box.newWidth = width;
            box.newHeight = height;
            box.newX = box.x;
            box.newY = box.y;            
        }
        box.axis = axis;
        return box; 
    },
    
    applyImgData : function( data ) {
        data = this.getDivRotated(data);
        this.imgData = data;   
        this.setThumbnailPosition();
    },
    
    apply : function() {
        var coords = this.raph.getImageData();
        this.closeEditor();
        this.applyImgData( this.formatImgData(coords) );
        this.updateData(true);        
    },
    
    reset : function() {
        this.raph.reset();
    },

    updateThumbnail: function(resp)
    {
        this.loadThumbnailImage(resp.id, resp.temp_thumbnail_url, resp.temp_image_url, {
            full_x: resp.preview_width, 
            full_y: resp.preview_height,
            x : resp.x,
            y : resp.y,
            scale_x : resp.scale_x,
            scale_y : resp.scale_y,
            angle :  resp.angle
        });
    },
    
    /**
     *  if you've made changes in this method be sure to
     *  make same changes in Aitoc_Aitcg_Model_Image
     */
    setThumbnailPosition: function() {
        var el = $$(this.imgThumbSelector + ' div');
        var mult = Math.min(1, this.productImageThubnailSizeX / this.productImageSizeX, this.productImageThubnailSizeY / this.productImageSizeY);
        //setting box position
        el[0].setStyle({
            left: Math.max(0, Math.round( (this.areaOffsetX) * mult-1 )) + 'px',
            top:  Math.max(0, Math.round( (this.areaOffsetY) * mult-1 )) + 'px',
            width: Math.min(198, Math.round( this.areaSizeX * mult )) + 'px',
            height: Math.min(198, Math.round( this.areaSizeY * mult )) + 'px'
        });
        var windowData = Aitcg.countMult( 0, this.productImageSizeX, this.productImageSizeY );
        /*this.show(windowData,"windowData");*/
        var mult2 = mult / windowData.mult,
            box = {
                width:  Math.round( this.imgData.newWidth  * mult ),
                height: Math.round( this.imgData.newHeight * mult )
            },                                                   
            mult_x = this.areaSizeX * windowData.mult / this.imgData.full_x,
            mult_y = this.areaSizeY * windowData.mult  / this.imgData.full_y;
        var coords = {};
            
        if(typeof(this.imgData.force)!='undefined') {
            box = {
                width:  Math.round( this.imgData.newWidth  * mult2 ),
                height: Math.round( this.imgData.newHeight * mult2 )
            };            
            box.left= Math.round( (this.imgData.newX) * mult );
            box.top = Math.round( (this.imgData.newY) * mult );
            coords.y = this.imgData.newX;
            coords.x = this.imgData.newY;
            this.force_show = true;
        } else {
                //if anything happened to image - it was moved, resized, rotated - we use this calculation
                if(this.imgData.x != 0 || this.imgData.y!=0 || this.force_show == true || this.imgData.scale_x!=1 || this.imgData.scale_y != 1 || this.imgData.angle != 0) {

                if(mult_x >= 1 && mult_y >= 1) {
                    mult2 = mult;
                } else {
                    mult2 = mult; //Math.min(mult_x, mult_y, 1) * mult2;
                }
                box.width = Math.round(this.imgData.newWidth * mult2);
                box.height = Math.round(this.imgData.newHeight * mult2);
                /*box.width = box.width + 'px';
                box.height = box.height + 'px';                */
                box.left= Math.round( (this.imgData.newX) * mult );
                box.top = Math.round( (this.imgData.newY) * mult );
                coords.y = this.imgData.newX;
                coords.x = this.imgData.newY;
            } else {
                if(mult_x < 1 && mult_x < mult_y) {//x
             
                    box.width = this.areaSizeX * mult;
                    //box.height = mult_x * box.height;
                    box.left = coords.x = 0;
                    box.top  = Math.round( (this.areaSizeY * mult - box.height) /2 );
                    coords.y = Math.round( (this.areaSizeY - this.imgData.newHeight) /2 );
                } else if (mult_y < 1 && mult_y <= mult_x) {

                    box.height = this.areaSizeY * mult;
                    //box.width = mult_y * box.width;
                    box.top  = coords.y = 0;
                    box.left = Math.round( (this.areaSizeX * mult - box.width) /2 );
                    coords.x = Math.round( (this.areaSizeX - this.imgData.newWidth) /2 );                    
                } else {

                    box.width = this.imgData.newWidth * mult2;
                    box.height = this.imgData.newHeight * mult2;
                    box.top  = Math.round( (this.areaSizeY * mult - this.imgData.newHeight * mult2) /2 );
                    box.left = Math.round( (this.areaSizeX * mult - this.imgData.newWidth  * mult2) /2 );
                    
                    coords.y = Math.round( (this.areaSizeY - this.imgData.newHeight) /2 );
                    coords.x = Math.round( (this.areaSizeX - this.imgData.newWidth) /2 );
                }
            }
        }
        box.width++;
        box.height++;
        for(var i in box) {
            box[i] = box[i]+'px';
        }

        var el = $$(this.imgThumbSelector + ' img');
        el[0].setStyle(box);

        this.defaultCoords = coords;
    }
    
};