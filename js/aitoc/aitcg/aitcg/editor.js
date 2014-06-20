
/**
 * Custom Product Preview
 *
 * @category:    Aitoc
 * @package:     Aitoc_Aitcg
 * @version      11.2.2
 * @license:     n/a
 * @copyright:   Copyright (c) 2013 AITOC, Inc. (http://www.aitoc.com)
 */
Aitcg.Editor = new Class.create();
Aitcg.Editor.prototype =
{
    e : null, //editor
    attr : "cx,cy,fill,fill-opacity,font,font-family,font-size,font-weight,gradient,height,opacity,path,r,rotation,rx,ry,src,stroke,stroke-dasharray,stroke-opacity,stroke-width,width,x,y,text,preserveAspectRatio".split(","),
    socialWidgetsReservedImgId: 0,
    maskCreated: 0,
    maskDelete: 0,
    getMaskUrl: 0,
    delMaskUrl: 0,
    optionId: 0,
    type: 0, //0 - editor, 1 - preview, 2 - save as
    shapeMask: [],

    initialize : function(el, sizeX, sizeY, imgAspectRatio, optionId, type)
    {
        this.e = new VectorEditor(el, sizeX, sizeY);
        this.sizeX = sizeX;
        this.sizeY = sizeY;
        this.imgAspectRatio = imgAspectRatio; 
        this.optionId = optionId;
        this.type = type;
        //this.demo();
    },
    
    addUploadedImage: function(url){
        if(!url.error)
        {
            var img = /*new Image();*/$$('.techimg')[0];
            img.onload = function(e){
                    this.iinstance.editor.addImage(getEventTarget(e));
                    this.iinstance.hideLoader();
                }.bind(this);
            img.src = url.src;    
        }
        else
        {   
            this.iinstance.hideLoader();
            jQuery('body').append('<div class="error-overlay"><div class="error-box"><span>'+url.error+'</span><p><a href="#" onclick="closeButton();" class="close_btn">Ok</a></p></div></div>');
            //alert(url.error);
        }
    },
    
    addUploadedText: function(url){
        
        var img = /*new Image();//*/$$('.techimg')[0];
        img.onload = function(e){this.addImage(getEventTarget(e));}.bind(this);
        img.src = url;    
        
    },    
        
    addMask: function(mask, scale){
        this.maskCreated = mask;
        //alert(typeof this.maskCreated);
        if (this.type == 1)
        {
            name_area=this.optionId+'_inverted-mask-printable-area-image_preview';
        }
        else if (this.type == 0)
        {
            name_area=this.optionId+'_inverted-mask-printable-area-image';
            try {
                $('delMask_'+this.optionId).show();
            } catch (error) {
                // код обработки ошибки
            }
        }
        else if (this.type == 2)
        {
            //name_area=this.optionId+'_inverted-mask-printable-area-image';
            
        }
        width_image = $(name_area).getWidth();
        height_image = $(name_area).getHeight();
        if(width_image == 0)
            width_image = $(name_area).getStyle('width').slice(0,-2);
        if(height_image == 0)
            height_image = $(name_area).getStyle('height').slice(0,-2);
        mask.x = Math.round(mask.x*scale);
        mask.y = Math.round(mask.y*scale);
        mask.width = Math.round(mask.width*scale);
        mask.height = Math.round(mask.height*scale);
        var useragent=navigator.userAgent;
        if (useragent.indexOf('MSIE')!= -1)
        {
            opacity = '-ms-filter:\'progid:DXImageTransform.Microsoft.Alpha(Opacity=50)\';zoom:1;';
        }
        else
        {
            opacity = '';
        }
        $(name_area).innerHTML = '<img src="'+mask.url+'" width='+mask.width+'px height='+mask.height+'px style="position:absolute; top:'+mask.y+'px; '+opacity+'left:'+mask.x+'px" id="mask_image">'+
            '<img src="'+mask.url_base+'1x1_black.png" width='+mask.x+'px height='+height_image+'px style="position:absolute;'+opacity+' top:0px; left:0px; background-color:#000;">'+
            '<img  src="'+mask.url_base+'1x1_black.png"  width='+(width_image-mask.x*1-mask.width*1)+'px height='+height_image+'px style="position:absolute; '+opacity+'top:0px; left:'+(mask.width*1+mask.x*1)+'px; background-color:#000;">'+
            '<img  src="'+mask.url_base+'1x1_black.png"  width='+mask.width+'px height='+mask.y+'px style="position:absolute; top:0px; left:'+mask.x+'px; '+opacity+'background-color:#000;">'+
            '<img  src="'+mask.url_base+'1x1_black.png"  width='+mask.width+'px height='+(height_image-mask.y*1-mask.height*1)+'px style="position:absolute; '+opacity+'top:'+(mask.height*1+mask.y*1)+'px; left:'+mask.x+'px; background-color:#000;">';
        
        $(name_area).show();
    },    
        
    addMaskPrint: function(mask, scale){
        this.maskCreated = mask;
        //alert(typeof this.maskCreated);
        name_area=this.optionId+'_inverted-mask-printable-area-image';
        name_area_raph = this.optionId+'_raph';
        width_image = $(name_area).getWidth();
        height_image = $(name_area).getHeight();
        if(width_image == 0)
            width_image = $(name_area).getStyle('width').slice(0,-2);
        if(height_image == 0)
            height_image = $(name_area).getStyle('height').slice(0,-2);
        left_image = Math.round($(name_area_raph).getStyle('left').slice(0,-2));
        top_image = Math.round($(name_area_raph).getStyle('top').slice(0,-2));
        width_image_raph = Math.round($(name_area_raph).getWidth());
        height_image_raph = Math.round($(name_area_raph).getHeight());
        if(width_image_raph == 0)
            width_image_raph = Math.round($(name_area_raph).getStyle('width').slice(0,-2));
        if(height_image_raph == 0)
            height_image_raph = Math.round($(name_area_raph).getStyle('height').slice(0,-2));
        mask.x = Math.round((mask.x-left_image)*scale);
        mask.y = Math.round((mask.y-top_image)*scale);
        mask.width = Math.round(mask.width*scale);
        mask.height = Math.round(mask.height*scale);
        
        
        switch(Raphael.type)
        {
            case 'SVG':
                newshape = this.e.draw.image(mask.url_black, mask.x, mask.y, mask.width, mask.height, this.imgAspectRatio);
                break;
            case 'VML':
                newshape = this.e.draw.image(mask.url_black, mask.x, mask.y, mask.width, mask.height, this.imgAspectRatio);
//                this.e.on('addedshape', function(a,b,c,d){
//                    return;
//                });
                break;
            default :
                document.write("Error: undefined image type"); 
        }
        this.shapeMask[0] = newshape;
        if(mask.x > 0)
        {
            this.shapeMask[1] = this.e.draw.image(''+mask.url_base+'1x1_black.png', 0 , 0, mask.x, height_image_raph, this.imgAspectRatio);
            this.shapeMask[2] = this.e.draw.image(''+mask.url_base+'1x1_black.png', mask.x*1+mask.width*1 , 0, (width_image_raph*1 - mask.x*1 - mask.width*1), height_image_raph, this.imgAspectRatio);
        }
        if(mask.y > 0)
        {
            this.shapeMask[2] = this.e.draw.image(''+mask.url_base+'1x1_black.png', mask.x , 0, mask.width, mask.y, this.imgAspectRatio);
            this.shapeMask[3] = this.e.draw.image(''+mask.url_base+'1x1_black.png', mask.x , mask.y*1 + mask.height*1 ,  mask.width, (height_image_raph*1 - mask.y*1 - mask.height*1), this.imgAspectRatio);
        }
        
        //this.shapeMask[1] = newshape;
        //this.e.addShape(newshape,true);
        
       /* $(name_area).innerHTML = '<img src="'+mask.url+'" width='+mask.width+'px height='+mask.height+'px style="position:absolute; top:'+mask.y+'px; '+opacity+'left:'+mask.x+'px" id="mask_image">'+
            '<img src="/media/custom_product_preview/mask/alpha/1x1_black.png" width='+mask.x+'px height='+height_image+'px style="position:absolute;'+opacity+' top:0px; left:0px; background-color:#000;">'+
            '<img  src="/media/custom_product_preview/mask/alpha/1x1_black.png"  width='+(width_image-mask.x*1-mask.width*1)+'px height='+height_image+'px style="position:absolute; '+opacity+'top:0px; left:'+(mask.width*1+mask.x*1)+'px; background-color:#000;">'+
            '<img  src="/media/custom_product_preview/mask/alpha/1x1_black.png"  width='+mask.width+'px height='+mask.y+'px style="position:absolute; top:0px; left:'+mask.x+'px; '+opacity+'background-color:#000;">'+
            '<img  src="/media/custom_product_preview/mask/alpha/1x1_black.png"  width='+mask.width+'px height='+(height_image-mask.y*1-mask.height*1)+'px style="position:absolute; '+opacity+'top:'+(mask.height*1+mask.y*1)+'px; left:'+mask.x+'px; background-color:#000;">';
        */
        
    },    
    
    
    addImage : function(img){
        var scale = 1;
        if((img.getWidth()>(this.sizeX-40))||(img.getHeight()>(this.sizeY-40)))
        {
            scale = Math.min((this.sizeX-40)/img.getWidth(),(this.sizeY-40)/img.getHeight());
        }

        switch(Raphael.type)
        {
            case 'SVG':
                newshape = this.e.draw.image(img.src, 20, 20, img.getWidth()*scale, img.getHeight()*scale, this.imgAspectRatio);
                break;
            case 'VML':
                newshape = this.e.draw.image(img.src, 20, 20, img.getWidth()*scale, img.getHeight()*scale, this.imgAspectRatio);
//                this.e.on('addedshape', function(a,b,c,d){
//                    return;
//                });
                break;
            default :
                document.write("Error: undefined image type"); 
        }
        
        this.e.addShape(newshape,true);
        
    },
    
    load: function(data, noattachlistener, scale)
    {
        if (data != '')
        {
            try {
                var json = eval("("+data+")");
                this.maskCreated = 0;
                $(json).each(function(item) {
                    this.loadShape(item,noattachlistener, scale);
                    /*if (shape.maskCreated !=0 )
                    {
                        this.maskCreated = shape.maskCreated;
                    }*/
                }.bind(this));
            
                if (typeof this.shapeMask == 'object' && this.type == 2)
                {

                    edit = this.e
                    this.shapeMask.each(function(item){
                        edit.addShape(item,true)
                        item.toFront();
                    });
                    edit = 0;
                    this.shapeMask = 0;
                }
            } catch(err) {
                alert(err.message)
            }
        }
    },
    
    loadShape : function(shape, noattachlistener, scale){
        var instance = this.e;
        if(!shape || !shape.type || !shape.id)return;
        
        
        if(shape.maskCreated !=0 && this.getMaskUrl != 0 && shape.maskCreated != undefined && this.getMaskUrl != undefined)
        {
            if (shape.maskCreated != this.maskCreated )
            {
                new Ajax.Request(this.getMaskUrl,
                {
                    method:'post',
                    parameters: {id:shape.maskCreated},
                    asynchronous:false,
                    onSuccess: function(transport){
                    var response = eval("("+transport.responseText+")");
                        //alert('ok');
                        if  (this.type != 2)
                            this.addMask(response.mask, scale);
                        else
                            if  (this.type == 2)
                                this.addMaskPrint(response.mask, scale);
                        this.maskCreated = response.mask.id;
                    }.bind(this)
                }); 
            }
        }
        else
        {

            try {
                if (!this.e.container.hasClassName('message-popup-aitraph'))
                {
                    name_area=this.optionId+'_inverted-mask-printable-area-image_preview';
                }
                else
                {
                    name_area=this.optionId+'_inverted-mask-printable-area-image';
                }
                $(name_area).innerHTML = '';
            } catch(err) {
                alert(err.message)
            }
        }
            
            
        var newshape = null, draw = instance.draw;
        if (!(newshape = this.e.getShapeById(shape.id))) {
            switch  (shape.type ) 
            {
                case 'rect':
                {
                    newshape = draw.rect(0, 0, 0, 0);
                    break;
                }
                case "path":
                {
                    newshape = draw.path("");
                    break;
                }
                case "image":
                {
                    newshape = draw.image(shape.src, 0, 0, 0, 0);
                    break;
                }
                case  "ellipse":
                {
                    newshape = draw.ellipse(0, 0, 0, 0);
                    break;
                }
                case "text":
                {
                    newshape = draw.text(0, 0, shape.text);
                    break;
                }
            }
        }

        if (scale != 1)
        {
            shape.x = shape.x*scale;
            shape.y = shape.y*scale;
            shape.height = shape.height*scale;
            shape.width = shape.width*scale;
        }
        
        newshape.attr(shape);
        newshape.id = shape.id;
        newshape.subtype = shape.subtype;
    
        if (!noattachlistener) {
            instance.addShape(newshape,true);
        }
    },
    
    save : function()
    {
        //dumpshape_vars =$(this.e.shapes).collect(this.dumpshape.bind(this));
        var widthheight=jQuery("#zoom-in-id").attr("value");
		if (typeof widthheight != 'undefined' && widthheight!=''){
			var splitids=widthheight.split("-");
this.e.shapes[0].attrs.width=splitids[0];
this.e.shapes[0].attrs.height=splitids[1];
		}
        return Object.toJSON($(this.e.shapes).collect(this.dumpshape.bind(this)));
    },

    dumpshape : function(shape){
        if (typeof this.maskCreated == 'object')
        {
            new Ajax.Request(this.maskCreated.createMaskUrl,
            {
                method:'post',
                parameters: this.maskCreated,
                asynchronous:false,
                onSuccess: function(transport){
                var response = eval("("+transport.responseText+")");
                    //alert('ok');
                    this.maskCreated = response.id;
                    //dumpshape_vars = this.dumpshapeAfterMaskCreated(shape);
                }.bind(this)
            });
            
        }
        else
        {
            //dumpshape_vars = this.dumpshapeAfterMaskCreated(shape);
        }
        if (this.maskDelete > 0)
        {
            new Ajax.Request(this.delMaskUrl,
            {
                method:'post',
                parameters: {id:this.maskDelete},
                asynchronous:false,
                onSuccess: function(transport){
                var response = eval("("+transport.responseText+")");
                    //alert('ok');
                    if (this.maskCreated == this.maskDelete)
                        this.maskCreated = 0;
                    this.maskDelete = 0;
                    //dumpshape_vars = this.dumpshapeAfterMaskCreated(shape);
                }.bind(this)
            });
            
        }
        else
        {
            //dumpshape_vars = this.dumpshapeAfterMaskCreated(shape);
        }
        return this.dumpshapeAfterMaskCreated(shape);
    },
    dumpshapeAfterMaskCreated : function(shape){
        var info = {
          type: shape.type,
          id: shape.id,
          subtype: shape.subtype,
          maskCreated:this.maskCreated,
          social_widgets_reserved_img_id: this.socialWidgetsReservedImgId//social widgets functionality
         }
        //fix for ie
        info.id++;
        for(var i = 0; i < this.attr.length; i++){
          var tmp = shape.attr(this.attr[i]);
          if(tmp){
            if(this.attr[i] == "path"){
              tmp = tmp.toString()
            }
            info[this.attr[i]] = tmp
          }
        }
        return info;
    }
    
};
function closeButton(){
 jQuery('.error-overlay').remove();
 return;
}