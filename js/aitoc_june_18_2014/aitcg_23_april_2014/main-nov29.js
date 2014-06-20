
/**
 * Custom Product Preview
 *
 * @category:    Aitoc
 * @package:     Aitoc_Aitcg
 * @version      11.2.2
 * @license:     n/a
 * @copyright:   Copyright (c) 2013 AITOC, Inc. (http://www.aitoc.com)
 */
Aitcg.Main = new Class.create();
Aitcg.Main.prototype =
{
    id: '',
    editorEnabled: false,
    window: null,
    cp: false,
    popupHtml:'',
    addImageUrl : '/aitcg/ajax/addImage',
        
    initialize : function(id) {
        this.id = id;
        this.imgThumbSelector = '#' +this.id + '_imagediv div.th';

        if(typeof(AitPopupHtml)!= 'undefined') {
            //$$('body')[0].insert( {bottom:AitPopupHtml} );
            //start  ie < 8.016 fix
            Event.observe(document, 'dom:loaded', function(){
                $$('body')[0].insert( {bottom:AitPopupHtml} );
            });
            //end ie < 8.016 fix
            
        }
    },

    getControlsHtml: function() 
    {
        return '<div style="cursor: pointer;" class="popup-separator popup-separator-title"' +
                ' onclick="toggleNextElement($(this));">' +
                '<span> + </span><span style="display:none"> - </span>';
    },
    
    getPopupHtmlIsPredefinedImage: function() 
    {
        popupHtml = '';
        if (this.usePredefinedImage)
            {
                popupHtml = 
                    this.getControlsHtml() +
                    '{{predefined_title}}</div>'+
                    '<span style="display:none;">' +
                    '<select onchange="opCimage{{rand}}.categoryPreview();" id="category-selector{{rand}}">' +
                    '<option value="0">Select Category...</option>' + 
                    this.predefinedOptions +
                    '</select>' +
                    '<button type="button" onclick="opCimage{{rand}}.addPredefined();">{{addimage_text}}</button>' +
                    '<div class="popup-separator"></div>' +
                    '<span id="predefined-images{{rand}}"></span>' +
                    '<div style="display:none;" id="add_predefined_{{rand}}_error" class="validation-advice">{{required_text}}</div></span>';
            }
            return popupHtml;
    },
    
    getPopupHtmlIsMask: function() 
    {
        popupHtml = '';
        if (this.useMasks)
            {
                popupHtml = 
                    this.getControlsHtml() +
                    '{{masks_title}}</div>'+
                    '<span style="display:none;">' +
                    '<select onchange="opCimage{{rand}}.masksCategoryPreview();" id="masks-category-selector{{rand}}">' +
                    '<option value="0">Select Category...</option>' + 
                    this.masksOptions +
                    '</select>' +
                    '<button type="button" onclick="opCimage{{rand}}.addMasks();">{{addmasks_text}}</button>' +
                    '<button type="button" onclick="opCimage{{rand}}.delMasks();" id="delMask_options_'+this.optionId+'" style="display:none;">{{delmasks_text}}</button>' +
                    '<div class="popup-separator"></div>' +
                    '<span id="masks{{rand}}"></span>' +
                    '<div style="display:none;" id="add_masks_{{rand}}_error" class="validation-advice">{{required_text}}</div></span>';
            }
            return popupHtml;
    },
    getPopupHtmlIsUserImage: function() 
    {
        popupHtml = '';
        if (this.useUserImage)
            {
                popupHtml+=
                    this.getControlsHtml() +
                    '{{user_title}}</div>' +
                    '<span style="display:none;"><input type="file" id="add_image_{{rand}}" name="new_image">' +
                    '<button type="button" onclick="opCimage{{rand}}.addImage(\'add_image_{{rand}}\');">{{addimage_text}}</button>'+
                    '<div style="display:none;" id="add_image_{{rand}}_error" class="validation-advice">{{required_text}}</div></span>';
            }
         return popupHtml;
    },
    
    getPopupHtmlIsText: function() 
    {
        popupHtml = '';
        if (this.useText)
        {
            // controls header
            popupHtml +=
                this.getControlsHtml() + '{{text_title}}</div>';

            // begin form
            popupHtml +=
                '<span style="display:none;"><form id="add_text_form{{rand}}">';

            // begin table
            popupHtml +=
                '<table class="form-list">';

            // text
            popupHtml +=
                '<tr>' +
                '<td class="label"><label for="add_text_{{rand}}">{{texttoadd_text}}</label></td>' +
                '<td class="value">' +
                '<input type="text" class="required-entry input-text" id="add_text_{{rand}}" name="text" value=""' +
                ' onkeyup="$(this.id).next().innerHTML = this.getValue().length;"' +
                (this.textLength ? (' maxlength="' + this.textLength + '"> <span>0</span>/' + this.textLength) :
                ('"> <span>0</span>')) +
                '</td>' +
                '</tr>';

            // font
            popupHtml +=
                '<tr>' +
                '<td class="label"><label for="font-selector{{rand}}">{{font_text}}</label></td>' +
                '<td class="value">' +
                '<select onchange="opCimage{{rand}}.fontPreview();" id="font-selector{{rand}}" name="font" class="required-entry select">{{fontOptions}}</select>' +
                '</td>' +
                '</tr>';

            // font preview
            popupHtml +=
                '<tr>' +
                '<td class="label"><label for="font-preview{{rand}}">{{fontpreview_text}}</label></td>' +
                '<td class="value">' +
                '<span><img id="font-preview{{rand}}" src="{{empty_img_url}}"></span>' + 
                '</td>' +
                '</tr>';

            
            popupHtml += this.getPopupHtmlIsColorpick();
            popupHtml += this.getPopupHtmlIsOutline();
            popupHtml += this.getPopupHtmlIsShadow();
            // end table
            popupHtml +=
                '</table>';

            // add text button
            popupHtml +=
                '<div class="form-buttons">' +
                '<button type="button" class="scalable add" onclick="opCimage{{rand}}.addText(\'add_text_{{rand}}\');">{{addtext_text}}</button>' +
                '</div>';

            // end form
            popupHtml +=
                '</form>' +
                '</span>';
        }
        
        return popupHtml;
    },
    
    getPopupHtmlIsColorpick: function() 
    {
        popupHtml = '';
        if (this.allowColorpick)
            {
                if(this.allowOnlyPredefColor)
                {
                    popupHtml +=
                    '<tr>' +
                    '<td class="label"><label for="font-selector{{rand}}">{{pickcolor_text}}</label></td>' +
                    '<td class="value">' +
                    '<input id="colorfield{{rand}}" class="jscolorpicker {pickerOnfocus:false}" readonly="readonly" name="color" value="#000000" style="width: 100px; background-color:#000000;">' +
                    '<div id="aitcg_colorset_container{{rand}}" class="aitcg_colorset_container" ></div>'+
                    '</td>' +
                    '</tr>';
                }
                else
                {
                    popupHtml +=
                    '<tr>' +
                    '<td class="label"><label for="font-selector{{rand}}">{{pickcolor_text}}</label></td>' +
                    '<td class="value">' +
                    '<input id="colorfield{{rand}}" name="color" class="jscolorpicker" value="#000000" style="width: 100px;">' +
                    '</td>' +
                    '</tr>';
                }

            }
        return popupHtml;
    },

    getPopupHtmlIsOutline: function()
    {
        popupHtml = '';
        if (this.outline > 0)
        {
            popupHtml += '<tr>'+
                '<td class="label"><label for="outline{{rand}}">{{outline_text}}</label></td>'+
                '<td class="value">' +
                '<input id="outline{{rand}}" name="outline" type="checkbox" onchange="DisableNextLine($(this))" onclick="DisableNextLine($(this))">'+
                '</td>';
            popupHtml += '<tr style="display: none;">'+
                        '<td></td><td class="label">'+
                '<table  class="form-list">'+
                '<tr>'+
                '<td class="label">';
            if(this.allowOnlyPredefColor)
            {
                popupHtml +='<label for="font-selector{{rand}}">{{pickcoloroutline_text}}</label>' +
                        '</td>'+
                        '<td class="value">'+
                        '<input id="coloroutline{{rand}}" class="jscolorpicker {pickerOnfocus:false}" readonly="readonly" name="coloroutline" value="#000000" style="width: 100px; background-color:#000000;">' +
                        '<div id="aitcg_colorset_container_outline{{rand}}" class="aitcg_colorset_container" ></div>';
            }
            else
            {
                popupHtml +='<label for="font-selector{{rand}}">{{pickcoloroutline_text}}</label>' +
                        '</td>'+
                        '<td class="value">'+
                        '<input id="coloroutline{{rand}}" name="coloroutline" class="jscolorpicker" value="#000000" style="width: 100px;">';
            }

            popupHtml +='</td>'+
                    '</tr>'+
                    '<tr>'+
                    '<td class="label">'+
                    '<label for="widthoutline{{rand}}">{{widthoutline_text}}</label>' +
                    '</td>'+
                    '<td class="value">'+
                    '<input id="widthoutline{{rand}}" name="widthoutline" type="text" value="1" size=5>' +
                    '</td>'+
                    '</tr>'+
                    '</table>'+
                    '</td>' +
                    '</tr>';

        }
        return popupHtml;
    },


    getPopupHtmlIsShadow: function()
    {
        popupHtml = '';
        if (this.shadow > 0)
        {
            popupHtml += '<tr>'+
                '<td class="label"><label for="shadow{{rand}}">{{shadow_text}}</label></td>'+
                '<td class="value">' +
                '<input id="shadow{{rand}}" name="shadow" type="checkbox"  onchange="DisableNextLine($(this))"  onclick="DisableNextLine($(this))">'+
                '</td>'+
                '<tr style="display: none;">' +
                '<td></td><td class="label">'+
                '<table  class="form-list">'+
                '<tr>'+
                '<td class="label">';
            if(this.allowOnlyPredefColor)
            {
                popupHtml +='<label for="font-selector{{rand}}">{{pickcolorshadow_text}}</label>' +
                    '</td>'+
                    '<td class="value">'+
                    '<input id="colorshadow{{rand}}" class="jscolorpicker {pickerOnfocus:false}" readonly="readonly" name="colorshadow" value="#000000" style="width: 50px; background-color:#000000;">' +
                    '<div id="aitcg_colorset_container_shadow{{rand}}" class="aitcg_colorset_container" ></div>';
            }
            else
            {
                popupHtml +='<label for="font-selector{{rand}}">{{pickcolorshadow_text}}</label>' +
                    '</td>'+
                    '<td class="value">'+
                    '<input id="colorshadow{{rand}}" name="colorshadow" class="jscolorpicker" value="#000000" style="width: 50px;">';
            }

            popupHtml +='</td>'+
                '</tr>'+
                '<tr>'+
                '<td class="label">'+
                '<label for="shadowalpha{{rand}}">{{shadowalpha_text}}</label>' +
                '</td>'+
                '<td class="value">'+
                '<input id="shadowalpha{{rand}}"  name="shadowalpha" value="50" type="text" size=5>' +
                '</td>'+
                '</tr>'+
                '<tr>'+
                '<td class="label">'+
                '<label for="shadowoffsetx{{rand}}">{{shadowoffsetx_text}}</label>' +
                '</td>'+
                '<td class="value">'+
                '<input id="shadowoffsetx{{rand}}"  name="shadowoffsetx" value="20" type="text" size=5>' +
                '</td>'+
                '</tr>'+
                '<tr>'+
                '<td class="label">'+
                '<label for="shadowoffsety{{rand}}">{{shadowoffsety_text}}</label>' +
                '</td>'+
                '<td class="value">'+
                '<input id="shadowoffsety{{rand}}"  name="shadowoffsety" value="20" type="text" size=5>' +
                '</td>'+
                '</tr>'+
                '</table>'+
                '</td>' +
                '</tr>';

        }
        return popupHtml;
    },

    getPopupHtmlIsEditor: function() 
    {
        popupHtml = '';
        if (this.editorEnabled)
        {
            popupHtml +=
                '<a href="#" onclick="return false;" title="' + this.editorHelp + '" class="help2 tooltip-help">&nbsp;</a>';
        }

        popupHtml +=
            '</div><div class="aitclear"></div>';

        if (this.editorEnabled)
        {    
            popupHtml +=
                '<div class="message-popup-ait" style="text-align: left;">';
            
            popupHtml += this.getPopupHtmlIsPredefinedImage();
            popupHtml += this.getPopupHtmlIsMask();
            popupHtml += this.getPopupHtmlIsUserImage();
            popupHtml += this.getPopupHtmlIsText();
            

            
        }
        return popupHtml;
    },
    getPopupTemplateControlPanel: function() 
    {
        popupHtml = '<div class="message-popup-head" id="qqq">' +
                '<div id="saveas-buttons">' +
                ((/MSIE ([0-7]+.\d+);/.test(navigator.userAgent) && (document.documentMode <=7))? '' : '' +
                '<form target="_blank" method="post" action="{{saveSvgUrl}}">'+
                    '<input type="hidden" name="data" value="" id="savedisc{{rand}}">'+
                    '<input type="hidden" name="type" value="" id="savedisc_type{{rand}}">'+
                    '<input type="hidden" name="background" value="{{full_image}}" id="savedisc_backgorund{{rand}}">'+
                    '<input type="hidden" name="areaOffsetX" value="{{areaOffsetX}}" id="savedisc_areaOffsetX{{rand}}">'+
                    '<input type="hidden" name="areaOffsetY" value="{{areaOffsetY}}" id="savedisc_areaOffsetY{{rand}}">'+                    
                    '{{scale_text}}'+
                    '<input type="text" name="print_scale" id="print_scale" value="1" className="validate-number" class="validate-number" size="1"> ' +
                    '<button title="'+this.buttonHelp+'" class="tooltip-help" onclick="saveAsSvg(opCimage{{rand}});">'+
                        '{{svg_text}}'+
                    '</button>'+
                '</form>'+
                (this.savePdfUrl?'<form target="_blank" method="post" action="{{savePdfUrl}}">'+
                    '<input type="hidden" name="data" value="" id="savedisc{{rand}}_pdf">'+
                    '<input type="hidden" name="type" value="" id="savedisc_type{{rand}}_pdf">'+
                    '<input type="hidden" name="background" value="{{full_image}}" id="savedisc_backgorund{{rand}}">'+
                    '<input type="hidden" name="areaOffsetX" value="{{areaOffsetX}}" id="savedisc_areaOffsetX{{rand}}">'+
                    '<input type="hidden" name="areaOffsetY" value="{{areaOffsetY}}" id="savedisc_areaOffsetY{{rand}}">'+                       
                    '<input type="hidden" name="print_scale" id="print_scale_pdf" value="1" className="validate-number" class="validate-number" size="1"> ' +
                    '<button title="'+this.buttonHelp+'" class="tooltip-help" onclick="saveAsPdf(opCimage{{rand}});">'+
                        '{{pdf_text}}'+
                    '</button>'+
                '</form>':'')+
                ((Prototype.Browser.IE) ? '' : '<button type="button" onclick="opCimage{{rand}}.editor.e.unselect();var data = opCimage{{rand}}.getPrintableVersion($(\'print_scale\').getValue());opCimage{{rand}}.savePng(data,\'{{full_image}}\',\'{{areaOffsetX}}\',\'{{areaOffsetY}}\');" title="'+this.buttonHelp+'" class="tooltip-help">{{png_text}}</button>'+
                '<canvas id="canvas{{rand}}" style="display:none;"></canvas>' +
                '<a id="canvas_link{{rand}}" style="display:none;" href="" target="_blank"></a>'))+
                '</div>'+

                (this.editorEnabled? '<a class="apply-but" href="#" onclick="opCimage{{rand}}.apply(); return false;" title="{{apply_text}}">{{apply_text}}</a>'+
                    '<a class="reset-but" href="#" onclick="opCimage{{rand}}.reset(); return false;" title="{{reset_text}}">{{reset_text}}</a>':'')
        return popupHtml;
    },
    getPopupTemplate: function() 
    {
        if (this.popupHtml == '')
        {
            
            var popupHtml = 
                '<div id="message-popup-window-mask" onclick="opCimage{{rand}}.closeEditor();"></div>' +
                '<div id="message-popup-window" class="message-popup print-area-editor">' +
                '<div class="message-popup-head">' +
                '<a href="#" onclick="opCimage{{rand}}.closeEditor(); return false;" title="{{close_text}}" class="close2">&nbsp;</a>';

            popupHtml += this.getPopupHtmlIsEditor();

            popupHtml += '<hr>';

            var useragent=navigator.userAgent;
            if (useragent.indexOf('MSIE')!= -1)
            {
                opacity = '-ms-filter:\'progid:DXImageTransform.Microsoft.Alpha(Opacity=50)\';zoom:1;';
                onclick = 'onclick=\'passThrough()\'';
            }
            else
            {
                opacity = '-moz-opacity: 0.4;opacity: 0.4;';
                onclick = '';

            }
            
            if (useragent.indexOf('MSIE')!= -1 || Prototype.Browser.Opera)
            {
                z_index = 3;
                onck = 'onclick="opCimage{{rand}}.fireEvertFromMask($(\'{{option_id}}_inverted-mask-printable-area-image\'))" ';
            }
            else
            {
                z_index = 10;
                onck = '';
            }
            popupHtml +=
                '<div id="aitcg_popup_image_container" class="message-popup-ait" style="width:{{img_width}}px;height:{{img_height}}px;position:relative;">' +
                '<img src="{{full_image}}" id="printable-area-image" width="{{img_width}}" height="{{img_height}}" alt="" style="position:absolute;left:0px;top:0px; z-index:5; pointer-events:none;"/>' +
                '<div id="{{option_id}}_inverted-mask-printable-area-image" '+onck+' style="display:none; position:absolute;left:0px;top:0px; z-index:'+z_index+';pointer-events:none;'+opacity+';"> </div>' +
                '<div id="{{option_id}}_raph" class="message-popup-aitraph" style="z-index:1;"></div>' +
                '</div> '+
                this.getPopupTemplateControlPanel() +
                '<script type="text/javascript">$$(".tooltip-help").each( function(link) {new Tooltip(link, {delay: 100, opacity: 1.0});});</script>'+
                '</div>'+
                '</div>';
            this.popupHtml = popupHtml;
        }
        else
            popupHtml = this.popupHtml;
        return popupHtml;
    },
    
    getTextPopupTemplate: function() {
        var str = '<div id="message-popup-window-mask" onclick="opCimage{{rand}}.agree(false);"></div>'+
                '<div id="message-popup-window" class="aitcgpopup">'+
                    '<div class="aitcgpopInner"><a class="close_btn" onclick="opCimage{{rand}}.agree(false); return false;" ></a></div>'+
                    '<div class="aitcgpop_text">{{text}}</div>'+
                    '<div>'+
                     '<a class="aitcgpop_btn" onclick="opCimage{{rand}}.agree(true); return false;"><div class="pop_btn_right"></div><p>{{agree_text}}</p></a>'+
                     '<a class="aitcgpop_btn" onclick="opCimage{{rand}}.agree(false); return false;"><div class="pop_btn_right"></div><p>{{disagree_text}}</p></a>'+
                    '</div>'+
                  '</div>';
        return str;
    },

    agree : function( data ) {
        $(this.id+'_checkbox').checked = data;
        this.closeEditor();
    },    
    
    closeEditor : function() {
        this.window.closeEditor( );
        this.window = null;
    },
    getArrayTemplateSetting : function()
    {
      return {
            full_image : this.productImageFullUrl,
            rand: this.rand,
            option_id: this.id,
            close_text: this.text.close,
            apply_text: this.text.apply,
            reset_text: this.text.reset,
            required_text: this.text.required,
            texttoadd_text: this.text.texttoadd,
            addtext_text: this.text.addtext,
            pickcolor_text: this.text.pickcolor,
            pickcoloroutline_text: this.text.pickcoloroutline,
            pickcolorshadow_text: this.text.pickcolorshadow,
            widthoutline_text: this.text.widthoutline,
            outline_text: this.text.outline,
            shadow_text: this.text.shadow,
            shadowalpha_text: this.text.shadowalpha,
            shadowoffsetx_text: this.text.shadowoffsetx,
            shadowoffsety_text: this.text.shadowoffsety,
            addimage_text: this.text.addimage,
            addmasks_text: this.text.addmasks,
            delmasks_text: this.text.delmasks,
            svg_text: this.text.svg,
            pdf_text: this.text.pdf,
            png_text: this.text.png,
            font_text: this.text.font,
            fontpreview_text: this.text.fontpreview,
            scale_text:this.text.scale,

            masks_title: this.text.masks_title,            
            predefined_title: this.text.predefined_title,            
            user_title: this.text.user_title,            
            text_title: this.text.text_title,            
            
            areaSizeX: this.areaSizeX,
            areaSizeY: this.areaSizeY,
            areaOffsetX: this.areaOffsetX,
            areaOffsetY: this.areaOffsetY,
            fontOptions: this.fontOptions,
            saveSvgUrl: this.saveSvgUrl,
            savePdfUrl: this.savePdfUrl,
            empty_img_url: this.emptyImgUrl            
        }  
        
    },
    
    addPxToValue : function(arrays)
    {
        array_new = {};
        for(var item in array_new)
        {
            array_new[item] = arrays[item]+'px';

        }
        return array_new;
    },
    startEditor : function()
    {
       /* if (this.window != null)
        {
            this.closeEditor();
        }*/

        this.showLoader();
        this.window = new Aitcg.Popup(this.getPopupTemplate(), this.getArrayTemplateSetting());
        
        var scr = this.window.showWindow( this.id, this.productImageSizeX, this.productImageSizeY, 1 );

        var optdata = {
            width  : Math.floor(this.areaSizeX * scr.mult),
            height : Math.floor(this.areaSizeY * scr.mult),
            left : Math.max(0, Math.round(this.areaOffsetX * scr.mult - 1)),
            top  : Math.max(0, Math.round(this.areaOffsetY * scr.mult - 1))
        };
        
       /* var options = {
            width  : optdata.width + 'px',
            height : optdata.height+ 'px',
            left :   optdata.left  + 'px',
            top  :   optdata.top   + 'px'
        };*/
        var options =this.addPxToValue(optdata);
        
        this.shownScr = scr;
        this.shownScr.opt = options;
        
        var el = $(this.id + '_raph');
        el.setStyle(options);
        this.editor = new Aitcg.Editor(el, optdata.width, optdata.height, this.textImgAspectRatio, this.id, 0);
        this.editor.getMaskUrl = this.getMaskUrl;
        this.editor.delMaskUrl = this.delMaskUrl;
        this.editor.load($('options_' + this.optionId).getValue(), !this.editorEnabled, 1);

        jscolor.init();
        if(this.allowOnlyPredefColor)
        {
            eval('aitcgColorset'+this.rand+'.renderSet()');
        }
        
        this.hideLoader();
    },
    
    reset : function()
    {
        this.editor.e.deleteAll();
        $('options_'+this.optionId+'_inverted-mask-printable-area-image').innerHTML = '';
        this.editor.maskCreated = 0;
        this.editor.load($('options_'+this.optionId).getValue(),!this.editorEnabled,1);
    },

    apply : function()
    {
        var value = this.editor.save();

        $('options_'+this.optionId).setValue((value=='[]')?'':value);

        if((this.optionIsRequired=='0')&&(this.checkboxEnabled=='1'))
        {
            if($('options_'+this.optionId).getValue())
            {
                $('options_'+this.optionId+'_checkbox').addClassName('required-entry');
            }
            else
            {
                $('options_'+this.optionId+'_checkbox').removeClassName('required-entry');
            }
        }
        /*if($('options_'+this.optionId+'_inverted-mask-printable-area-image').innerHTML != '')
        {
            $('mask_image');
        }*/
        this.preview.e.deleteAll();
        this.preview.load($('options_'+this.optionId).getValue(),1,this.previewScale);
        opConfig.reloadPrice();
        this.closeEditor();
    },
    
    savePng : function(svg, background, areaOffsetX, areaOffsetY)
    {
        if (typeof(svg) == 'undefined') {
            var svg = $(this.id+'_raph').innerHTML;
        }
        svg = svg.replace(/>\s+/g, ">")
            .replace(/\s+</g, "<")
            .replace(/<svg/g,'<svg xmlns:xlink="http://www.w3.org/1999/xlink"');
            //only for ie (if will add convert to png support 
            //.replace(/xmlns="http:\/\/www.w3.org\/2000\/svg"/g,'');
        
        if(svg.match(/xlink\s*:\s*href\s*=/ig) == null && svg.match(/href\s*=/ig))
        {
            svg = svg.replace(/href\s*=/g,'xlink:href=');
        }   
        if(this.normalizateSvgToPngUrl !=undefined)
        {
            new Ajax.Request(this.normalizateSvgToPngUrl,
            {
                method:'post',
                parameters: {svg: svg, type: Raphael.type, background: background, areaOffsetX: areaOffsetX, areaOffsetY: areaOffsetY, print_scale: $('print_scale').getValue()},
                asynchronous:false,
                onSuccess: function(transport){
                    //var response = eval("("+transport.responseText+")");
                    svg = transport.responseText;
                }.bind(this)
            });
        }
        
        var c = $('canvas'+this.rand);
		c.width = this.areaSizeX;
		c.height = this.areaSizeY;
        var obj = this;        
        canvg(c, svg, { ignoreMouse: true, ignoreAnimation: true, renderCallback: function()
        {
            var img = $('canvas'+this.rand).toDataURL("image/png");
            $('canvas_link'+this.rand).href = img;
            obj.simulatedClick($('canvas_link'+this.rand), {type: 'click'});
        }.bind(this) });

    },
    
    simulatedClick: function (target, options) {

        var event = target.ownerDocument.createEvent('MouseEvents'),
            options = options || {};

        //Set your default options to the right of ||
        var opts = {
            type: options.type                  || 'click',
            canBubble:options.canBubble             || true,
            cancelable:options.cancelable           || true,
            view:options.view                       || target.ownerDocument.defaultView, 
            detail:options.detail                   || 1,
            screenX:options.screenX                 || 0, //The coordinates within the entire page
            screenY:options.screenY                 || 0,
            clientX:options.clientX                 || 0, //The coordinates within the viewport
            clientY:options.clientY                 || 0,
            ctrlKey:options.ctrlKey                 || false,
            altKey:options.altKey                   || false,
            shiftKey:options.shiftKey               || false,
            metaKey:options.metaKey                 || false, //I *think* 'meta' is 'Cmd/Apple' on Mac, and 'Windows key' on Win. Not sure, though!
            button:options.button                   || 0, //0 = left, 1 = middle, 2 = right
            relatedTarget:options.relatedTarget     || null
        }

        //Pass in the options
        event.initMouseEvent(
            opts.type,
            opts.canBubble,
            opts.cancelable,
            opts.view, 
            opts.detail,
            opts.screenX,
            opts.screenY,
            opts.clientX,
            opts.clientY,
            opts.ctrlKey,
            opts.altKey,
            opts.shiftKey,
            opts.metaKey,
            opts.button,
            opts.relatedTarget
        );

        //Fire the event
        target.dispatchEvent(event);
    },
    
    addImage : function(id) {
        if(!$(id).value)
        {   
            $('add_image_'+this.rand+'_error').show();
            return;
        }
        $('add_image_'+this.rand+'_error').hide();
        this.showLoader();
        AIM.upload(
        this.addImageUrl,
        id,
        {
            onComplete:this.editor.addUploadedImage,
            iinstance:this
        }
        );    
    
    },
    
    fontPreview : function()
    {
        if($('font-selector' + this.rand).getValue() > 0)
        {
            this.showLoader();
            new Ajax.Request(this.fontPreviewUrl,
            {
                method:'post',
                parameters: {font_id: $('font-selector'+this.rand).getValue(), rand: this.rand},
                onSuccess: function(transport){
                  var response = eval("("+transport.responseText+")");
                  $('font-preview'+response.rand).src = response.src;
                  this.hideLoader();                  
                }.bind(this)
            });
        }    
    },
    
    masksCategoryPreview : function() {
        
        if($('masks-category-selector'+this.rand).getValue()>0)
        {
            this.showLoader();
            new Ajax.Request(this.masksCategoryUrl,
            {
                method:'post',
                parameters: {category_id: $('masks-category-selector'+this.rand).getValue(), rand: this.rand},
                onSuccess: function(transport){
                   // alert(transport.responseText);
                  var response = eval("("+transport.responseText+")");
                  $('masks'+response.rand).update(response.images);
                  this.hideLoader();                  
                }.bind(this)
            });
        }
        else        
        {
            $('masks'+this.rand).update();
        }
    
    },   
    
    categoryPreview : function() {
        
        if($('category-selector'+this.rand).getValue()>0)
        {
            this.showLoader();
            new Ajax.Request(this.categoryPreviewUrl,
            {
                method:'post',
                parameters: {category_id: $('category-selector'+this.rand).getValue(), rand: this.rand},
                onSuccess: function(transport){
                  var response = eval("("+transport.responseText+")");
                  $('predefined-images'+response.rand).update(response.images);
                  this.hideLoader();                  
                }.bind(this)
            });
        }
        else        
        {
            $('predefined-images'+this.rand).update();
        }
    
    },    
    
    addText : function ()
    {
        var addTextForm = new VarienForm('add_text_form' + this.rand);

        if (!addTextForm.validator.validate())
        {
            return false;
        }

        this.showLoader();
        var params = $('add_text_form' + this.rand).serialize();
        new Ajax.Request(this.addTextUrl,
        {
            method:'post',
            parameters: params,
            onSuccess: function(transport){
              var response = eval("("+transport.responseText+")");
              this.editor.addUploadedText(response);
              this.hideLoader();
            }.bind(this)
        });
    
    },
    
    addPredefined : function() {
        $('add_predefined_'+this.rand+'_error').hide();
        //var radios = $$('input:checked[type="radio"][name="predefined-image'+this.rand+'"]');

        var selection = document.getElementsByName('predefined-image'+this.rand);

        for (i=0; i<selection.length; i++)

        if (selection[i].checked == true){
            var radios=selection[i];
        }
        
        if(typeof(radios)=='undefined')
        {   
            $('add_predefined_'+this.rand+'_error').show();
            return;
        }        
    
        var img_id = radios.getValue();
       
       this.showLoader();
        new Ajax.Request(this.addPredefinedUrl,
        {
            method:'post',
            parameters: {img_id: img_id},
            onSuccess: function(transport){
              var response = eval("("+transport.responseText+")");
              this.editor.addUploadedText(response.url);
              this.hideLoader();                  
            }.bind(this)
        }); 
        
    },        
    
    addMasks : function() {
        $('add_masks_'+this.rand+'_error').hide();
        //var radios = $$('input:checked[type="radio"][name="predefined-image'+this.rand+'"]');

        var selection = document.getElementsByName('mask'+this.rand);

        for (i=0; i<selection.length; i++)

        if (selection[i].checked == true){
            var radios=selection[i];
        }
        
        if(typeof(radios)=='undefined')
        {   
            $('add_masks_'+this.rand+'_error').show();
            return;
        }        
    
        var mask_id = radios.getValue();
       
       this.showLoader();
        new Ajax.Request(this.addMaskUrl,
        {
            method:'post',
            parameters: {mask_id: mask_id},
            onSuccess: function(transport){
              var response = eval("("+transport.responseText+")");
              var img = /*new Image();//*/$$('.techimg')[0];
              img.onload = function(e){this.addMaskImage(getEventTarget(e), response);}.bind(this);
              img.src = response.url;    
              //this.editor.maskCreatedId = response;
              //this.editor.addMask(response.url);
              this.hideLoader();                  
            }.bind(this)
        }); 
        
    },        
    
    delMasks : function() {
        $('options_'+this.optionId+'_inverted-mask-printable-area-image').innerHTML = '';
        if(typeof this.editor.maskCreated != 'object' && this.editor.maskCreated > 0)
        {
        this.editor.maskDelete = this.editor.maskCreated ;
        this.editor.maskCreated = 0;
        //alert(this.editor.maskDelete);
        }
        else
        {
            this.editor.maskCreated = 0;
        }
    },        
    
    addMaskImage : function(img, response){
        var scale = 1;
        if (this.masksLocation == 0)
        {
            sizeX=Math.round(this.editor.sizeX);
            sizeY=Math.round(this.editor.sizeY);
            
        }
        else
        {
            
            sizeX=Math.round(this.productImageSizeX);
            sizeY=Math.round(this.productImageSizeY);
            
        }
        x=0;
        y=0;
        if (response.resize == 0)
        {
            width=sizeX;
            height=sizeY;
        }
        else
        {
            scale = Math.min((sizeX)/img.getWidth(),(sizeY)/img.getHeight());

            width=Math.round(img.getWidth()*scale);
            height=Math.round(img.getHeight()*scale);
            x = Math.round((sizeX-width)/2);
            y = Math.round((sizeY-height)/2);
        }
        if (this.masksLocation == 0)
        {
            
            x = x+this.areaOffsetX;
            y = y+this.areaOffsetY;
        }
        mask = {
            x: x,
            y: y,
            width: width,
            height: height,
            url: response.url,
            url_base: response.url_base,
            createMaskUrl: this.createMaskUrl,
            mask_id: response.id
        };
        
         if(typeof this.editor.maskCreated != 'object' && this.editor.maskCreated > 0)
         {
            this.editor.maskDelete = this.editor.maskCreated ;
            //alert(this.editor.maskDelete);
         }
        this.editor.addMask(mask, 1);
        
        /*
        $('options_'+this.optionId+'_inverted-mask-printable-area-image').innerHTML = '<img src="'+img.src+'" width='+width+'px height='+height+'px style="position:absolute; top:'+y+'px; left:'+x+'px" id="mask_image">'+
            '<img  width='+x+'px height='+this.productImageSizeY+'px style="position:absolute; top:0px; left:0px; background-color:#000;">'+
            '<img  width='+(this.productImageSizeX-x*1-width*1)+'px height='+this.productImageSizeY+'px style="position:absolute; top:0px; left:'+(width*1+x*1)+'px; background-color:#000;">'+
            '<img  width='+width+'px height='+y+'px style="position:absolute; top:0px; left:'+x+'px; background-color:#000;">'+
            '<img  width='+width+'px height='+(this.productImageSizeY-y*1-height*1)+'px style="position:absolute; top:'+(height*1+y*1)+'px; left:'+x+'px; background-color:#000;">';
        
        $('options_'+this.optionId+'_inverted-mask-printable-area-image').show();*/
        //inverted-mask-printable-area-image
        
        //this.e.addShape(newshape,true);
    }, 
    
    preview : function(elementId)
    {
        scale = this.calcScale();
        this.previewScale = scale;
        thumbEditorParams = {
            areaSizeX:parseInt(this.areaSizeX*scale),
            areaSizeY:parseInt(this.areaSizeY*scale),
            areaOffsetX:parseInt(this.areaOffsetX*scale),
            areaOffsetY:parseInt(this.areaOffsetY*scale)
            }
        //-moz-opacity: 0.4;opacity: 0.4;
        //document.write(navigator.appName);
        var useragent=navigator.userAgent;
        if (useragent.indexOf('MSIE')!= -1)
        {
            opacity = '-ms-filter:\'progid:DXImageTransform.Microsoft.Alpha(Opacity=50)\';zoom:1;';
            onclick = 'onclick=\'passThrough()\'';
        }
        else
        {
            opacity = '-moz-opacity: 0.4;opacity: 0.4;';
            onclick = '';
        }
        //    alert(opacity);
        html = '<img class="bg" src="'+this.productImageThumbnailUrl+'">'+
        '<div id="'+this.id+'_inverted-mask-printable-area-image_preview"  style="display:none; position:absolute;left:0px;top:0px; z-index:10;pointer-events:none; '+opacity+'width:'+this.productImageThubnailSizeX+'px;height:'+this.productImageThubnailSizeY+'px;"> </div>'+
        '<div class="th"><div id="preview'+this.optionId+'" style="left: '+thumbEditorParams.areaOffsetX+'px; top: '+thumbEditorParams.areaOffsetY+'px; width: '+
        thumbEditorParams.areaSizeX+'px; height: '+thumbEditorParams.areaSizeY+'px;"></div></div>';
        document.getElementById(elementId).innerHTML = html;
        this.preview = new Aitcg.Editor( $('preview'+this.optionId), thumbEditorParams.areaSizeX, thumbEditorParams.areaSizeY, this.textImgAspectRatio, this.id, 1);
        this.preview.getMaskUrl = this.getMaskUrl;
        this.preview.delMaskUrl = this.delMaskUrl;
        this.preview.load($('options_'+this.optionId).getValue(),1,scale);
    },
    
    calcScale : function()
    {
        return 1/Math.max(this.productImageSizeX/this.productImageThubnailSizeX,this.productImageSizeY/this.productImageThubnailSizeY);
    },

    showLoader : function() 
    {
        try{
            if( $('loading-mask') == null ) 
            {
                if(typeof(AitPopupHtml)!= 'undefined') {
                    $$('body')[0].insert( {bottom:AitPopupHtml} );
                } else {
                    document.body.appendChild( '<div id="loading-mask">Please wait...</div>' );
                }
            }
            $('loading-mask').show();
        }
        catch (err){};
    },

    hideLoader : function() {
        try{
            $('loading-mask').hide();
        }
        catch (e){};
    },   
    
    checkConfirmBox : function(el) {
        el = $(el).previous();
        if(typeof(this.text.confirm)=='undefined' || this.text.confirm=='') {
            return false;
        }
        this.window = new Aitcg.Popup(this.getTextPopupTemplate(true), {            
            full_image : this.productImageFullUrl,
            rand: this.rand,
            option_id: this.id,
            close_text: this.text.close,
            agree_text: this.text.agree,
            disagree_text: this.text.disagree,
            text: this.text.confirm
        } );
        var scr = this.window.showTextWindow( this.id );
        return false;
    },
    
    getPrintableVersion : function(scale) {
        var value = this.editor.save();
        if(value!=='[]')
        {
            $('options_'+this.optionId).setValue(value);
        }    
        scale = parseFloat(scale);
        if (scale == 0) {
            scale = 1;
        }
        var divForPrint = new Element('div');
        var printable = new Aitcg.Editor(divForPrint , this.areaSizeX*scale, this.areaSizeY*scale, this.textImgAspectRatio, this.id, 2);
        printable.getMaskUrl = this.getMaskUrl;
        printable.load($('options_' + this.optionId).getValue(),0,scale);
        return divForPrint.innerHTML;  
        
    },
    
    fireEvertFromMask : function(item)
    {
        item.style.zIndex = 10;
        /*alert('item.innerHTML');
            alert(findPosX(data)+' ? '+mX);
            alert(findPosY(data)+' ? '+mY);
        alert(item.innerHTML);*/
       // editor = this.editor;
        this.editor.e.unselect();
        list = item.next().childElements().first();
        if (document.documentMode <= 8)
        {
            list = list.childElements();
            thisobj = this;//magik
            list.each(function(data){
                thisobj.selectIfClickOnImage(data,item)
            });
        }
        else
        {
            list = list.getElementsByTagName('image');
            for (var i = 0; i < list.length; i++) {  
                this.selectIfClickOnImage(list.item(i), item)
            }  
        }

    },
    
    
    selectIfClickOnImage : function(data,item)
    {
        //alert('ffffff');
        data_container = item.next();
        mX = event.x+findPosX(item);
        mY = event.y+findPosY(item); 
        if (typeof data.raphael == 'undefined')
        {
            x1=findPosX(data);
            y1=findPosY(data);
            x2 = x1*1 + data.getWidth()*1;
            y2 = y1*1 + data.getHeight()*1;
        }
        else
        {
            
            data_container = item.next();
            x1=data.x.baseVal.value+findPosX(data_container);
            y1=data.y.baseVal.value+findPosY(data_container);
            x2 = x1*1 + data.width.baseVal.value*1;
            y2 = y1*1 + data.height.baseVal.value*1;
        }
        if(x1 < mX && x2 > mX  && y1 < mY && y2 > mY)
        {

            /*var evt = document.createEventObject();
            data.fireEvent('onclick', evt);*/
            // Event.simulate(data, 'click');
            /*editor.e.select(data);
            alert(typeof editor.e);*/
            item.style.zIndex = 3;
            
            if (typeof data.raphael != 'undefined')
            {
                this.editor.e.select(data.shape_object);
                //data.shape_object.select();
            }
            //data.click();
            //alert(findPosX(data)+' ? '+mX);
            //alert(findPosY(data)+' ? '+mY);
        }
        /*alert(findPosX(data)+' ? '+mX);
        alert(findPosY(data)+' ? '+mY);*/


    }
    
};



function findPosX(obj) {
    var curleft = 0;
    if (obj.offsetParent) {
        while (1) {
            curleft+=obj.offsetLeft;
            if (!obj.offsetParent) {
                break;
            }
            obj=obj.offsetParent;
        }
    } else if (obj.x) {
        curleft+=obj.x;
    }
    return curleft;
}
 
function findPosY(obj) {
    var curtop = 0;
    if (obj.offsetParent) {
        while (1) {
            curtop+=obj.offsetTop;
            if (!obj.offsetParent) {
                break;
            }
            obj=obj.offsetParent;
        }
    } else if (obj.y) {
        curtop+=obj.y;
    }
    return curtop;
}

function DisableNextLine(item)
{
    itemNext = item.up().up().next();
    if(item.getValue() == 'on')
    {
        itemNext.show();
    }
    else
    {
        itemNext.hide();
    }
}