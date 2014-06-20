
/**
 * Custom Product Preview
 *
 * @category:    Aitoc
 * @package:     Aitoc_Aitcg
 * @version      11.2.2
 * @license:     n/a
 * @copyright:   Copyright (c) 2013 AITOC, Inc. (http://www.aitoc.com)
 */
//this class is used to add social networks sharing functionality to the module
Aitcg.Main.SocialWidgets = new Class.create();
// inherit from Aitcg.Main class:
Aitcg.Main.SocialWidgets.prototype = Object.extend(new Aitcg.Main(), 
{
    // redefine the methods
    socialWidgetsReservedImgId: 0,

    showHideWidgetsMain: function(response)
    {
        if (200 == response.status)
        {
            if(response.responseText == 'success')
            {
                $('socialWidgetsTip'+this.optionId).setStyle({display:'none'});
                this.showSocialWidgets();
            }
            else if(response.responseText == 'imgSizeError')
            {
                $('imgSizeError'+this.optionId).setStyle({display:'block'});
                $('socialWidgetsTip'+this.optionId).setStyle({display:'block'});
                this.hideSocialWidgets();
            }
            else
            {
                $('socialWidgetsTip'+this.optionId).setStyle({display:'block'});
                this.hideSocialWidgets();
            }
        }
        else
        {
            $('socialWidgetsTip'+this.optionId).setStyle({display:'block'});
            this.hideSocialWidgets();
        }

        this.showHideSocialWidgetsAjaxLoader('hide');
    },

    showHideWidgets: function()
    {
        //this.showHideSocialWidgetsAjaxLoader('show');

        new Ajax.Request(this.sharedImgWasCreatedUrl, 
            {
                method: 'post',
                parameters: {sharedImgId : this.socialWidgetsReservedImgId},
                onComplete: function(response) {
                    this.showHideWidgetsMain(response);                    
                }.bind(this)
            });
    },

    hideSocialWidgets : function()
    {
        $('fbaitcg'+this.optionId).setStyle({
            visibility:'hidden'
        });

        document.getElementById('gaitcgWrapper'+this.optionId).innerHTML = '';

        if($('emailToFriend'+this.optionId)){
            $('emailToFriend'+this.optionId).setStyle({
                display:'none'
            });
        }
    },

    showSocialWidgets : function()
    {
            this.showHideSocialWidgetsAjaxLoader('hide');

            if($('imgSizeError'+this.optionId)){
                $('imgSizeError'+this.optionId).setStyle({display:'none'});
            }

            $('fbaitcg'+this.optionId).setStyle({
                    visibility:'visible'
            });

            // to not create more than 1 google share button on multiple click 'submit' button
            // we add checking if google share button was already created
            if(document.getElementById('gaitcgWrapper'+this.optionId).innerHTML.indexOf('plusone.google.com') == -1)
            {
                document.getElementById('gaitcgWrapper'+this.optionId).innerHTML = '<div style="display:inline;" class="g-plus" id="gaitcg'+
                    this.optionId+'" data-annotation="none" data-action="share" data-href="'+this.socialWidgetsImgViewUrl+'" ></div>';

                var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
                po.src = 'https://apis.google.com/js/plusone.js';
                var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
            }

            if($('emailToFriend'+this.optionId)){
                $('emailToFriend'+this.optionId).setStyle({
                    display:'inline'
                });
            }
    },

    showHideSocialWidgetsAjaxLoader : function(aitcgAjaxLoaderAction)
    {
        if(aitcgAjaxLoaderAction == 'show')
            aitcgAjaxLoaderStyle = 'inline-block';
        else
            aitcgAjaxLoaderStyle = 'none';
        if($('socialButtonsLoader'+this.optionId)){
            $('socialButtonsLoader'+this.optionId).setStyle({
                   display:aitcgAjaxLoaderStyle
                });
        }
    },

    //overwrite parent
    apply : function()
    {
        var value = this.editor.save();

        if(value != '[]'){
            this.hideSocialWidgets();
            this.editor.socialWidgetsReservedImgId = this.socialWidgetsReservedImgId;//send reservedImgId to editor object
            $('socialWidgetsTip'+this.optionId).setStyle({display:'block'});
        }
        else{
            $('socialWidgetsTip'+this.optionId).setStyle({display:'none'});
        }
        if($('imgSizeError'+this.optionId)){
            $('imgSizeError'+this.optionId).setStyle({
                   display:'none'
                });
        }

        value = this.editor.save();
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
        this.preview.e.deleteAll();
        this.preview.load($('options_'+this.optionId).getValue(),1,this.previewScale);
        opConfig.reloadPrice();
        this.closeEditor();
    },

    

    createImage : function()
    {   
        this.showHideSocialWidgetsAjaxLoader('show');

        new Ajax.Request(this.socialWidgetsImgCreatePath, 
        {
            method: 'post',
            parameters: {prodId : this.product_id, templateImgPath : this.templateImgPath,
                optionValue: $('options_'+this.optionId).getValue(), 
                imgFullUrl : this.productImageFullUrl, areaSizeX : this.areaSizeX, 
                areaSizeY : this.areaSizeY, areaOffsetX : this.areaOffsetX, 
                areaOffsetY : this.areaOffsetY},
            onComplete: function(response) {
                this.showHideWidgetsMain(response);
            }.bind(this)
        });        
    },
});