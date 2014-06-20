
/**
 * Custom Product Preview
 *
 * @category:    Aitoc
 * @package:     Aitoc_Aitcg
 * @version      11.2.2
 * @license:     n/a
 * @copyright:   Copyright (c) 2013 AITOC, Inc. (http://www.aitoc.com)
 */
Aitcg.ImageSelectorClosed = new Class.create();
Aitcg.ImageSelectorClosed.prototype =
{
    id : 'aitcg_option_select',
    optionId : null,

    initialize : function() {},

    setOptionId : function ( option_id, image_template_id ) {
        this.optionId = option_id;
    }
};

Aitcg.ImageSelectorOpened = new Class.create();
Aitcg.ImageSelectorOpened.prototype = Object.extend(new Aitcg.ImageSelectorClosed(), 
{
    // redefine the methods
    optionContainer : {},
    templateSyntax : /(^|.|\r|\n)({{(\w+)}})/,
    customImages : '',
    imageText  : '',
       
    setOptionId : function ( option_id, image_template_id ) {
        this.optionId = option_id;
        var optionParam = null;
        if(typeof ( this.optionContainer [ option_id ] ) == 'undefined')
        {
            this.optionContainer [ option_id ] = {};
        }

        //selecting option & loading image
        if(image_template_id != 0 && typeof( this.customImages[ image_template_id ] ) != 'undefined' ) 
        {
            $$('#'+this.id + '_' + option_id + ' option[value='+image_template_id+']')[0].selected = true;
            this.loadImage(option_id, image_template_id, {target: $(this.id + '_' + option_id ) } );
        }
        //selecting options
        if(option_id!= 0 && typeof( $('aitcg_option_allow_text_distortion_'+option_id+'_tmp')) != 'undefined' && $('aitcg_option_allow_text_distortion_'+option_id+'_tmp').getValue()!=undefined) 
        {
            optionParam = $('aitcg_option_allow_text_distortion_'+option_id+'_tmp').getValue();
            $$('#'+'aitcg_option_allow_text_distortion_'+option_id + ' option[value='+optionParam+']')[0].selected = true;
        }
        
        if(option_id!= 0 && typeof( $('aitcg_option_allow_predefined_colors_'+option_id+'_tmp')) != 'undefined' && $('aitcg_option_allow_predefined_colors_'+option_id+'_tmp').getValue()!=undefined) 
        {
            optionParam = $('aitcg_option_allow_predefined_colors_'+option_id+'_tmp').getValue();
            $$('#'+'aitcg_option_allow_predefined_colors_'+option_id + ' option[value='+optionParam+']')[0].selected = true;
        }
        
        if(option_id!= 0 && typeof( $('aitcg_option_color_set_id_'+option_id+'_tmp')) != 'undefined' && $('aitcg_option_color_set_id_'+option_id+'_tmp').getValue()!=undefined) 
        {
            optionParam = $('aitcg_option_color_set_id_'+option_id+'_tmp').getValue();
            $$('#'+'aitcg_option_color_set_id_'+option_id + ' option[value='+optionParam+']')[0].selected = true;
        }
        
        //checking checkboxs
        if(option_id!= 0 && typeof( $('aitcg_option_use_user_image_'+option_id+'_tmp')) != 'undefined' && $('aitcg_option_use_user_image_'+option_id+'_tmp').getValue()!='0' && $('aitcg_option_use_user_image_'+option_id+'_tmp').getValue()!='')
        {
            $('aitcg_option_use_user_image_'+option_id).checked="true";
        }
        
        if(option_id!= 0 && typeof( $('aitcg_option_use_text_'+option_id+'_tmp')) != 'undefined' && $('aitcg_option_use_text_'+option_id+'_tmp').getValue()!='0' && $('aitcg_option_use_text_'+option_id+'_tmp').getValue()!='')
        {
            $('aitcg_option_use_text_'+option_id).checked="true";
        }

        if(option_id!= 0 && typeof( $('aitcg_option_use_predefined_image_'+option_id+'_tmp')) != 'undefined' && $('aitcg_option_use_predefined_image_'+option_id+'_tmp').getValue()!='0' && $('aitcg_option_use_predefined_image_'+option_id+'_tmp').getValue()!='')
        {
            $('aitcg_option_use_predefined_image_'+option_id).checked="true";
        }     

        if(option_id!= 0 && typeof( $('aitcg_option_use_masks_'+option_id+'_tmp')) != 'undefined' && $('aitcg_option_use_masks_'+option_id+'_tmp').getValue()!='0' && $('aitcg_option_use_masks_'+option_id+'_tmp').getValue()!='')
        {
            $('aitcg_option_use_masks_'+option_id).checked="true";
        }     
        
        if(option_id!= 0 && typeof( $('aitcg_option_allow_colorpick_'+option_id+'_tmp')) != 'undefined' && $('aitcg_option_allow_colorpick_'+option_id+'_tmp').getValue()!='0' && $('aitcg_option_allow_colorpick_'+option_id+'_tmp').getValue()!='')
        {
            $('aitcg_option_allow_colorpick_'+option_id).checked="true";
        }           
        
        if(option_id!= 0 && typeof( $('aitcg_option_mask_location_'+option_id+'_tmp')) != 'undefined' )
        {
            $('aitcg_option_mask_location_'+option_id+'_tmp').setValue($('aitcg_option_mask_location_'+option_id).getValue());
        }           
        //selecting predefined cats
        if(option_id!= 0 && typeof( $('aitcg_option_predefined_cats_'+option_id+'_tmp')) != 'undefined')
        {       
            $('aitcg_option_predefined_cats_'+option_id+'_tmp').setValue($('aitcg_option_predefined_cats_'+option_id).getValue().split(','));        
        }

        if(option_id!= 0 && typeof( $('aitcg_option_masks_cat_id_'+option_id+'_tmp')) != 'undefined')
        {       
            $('aitcg_option_masks_cat_id_'+option_id+'_tmp').setValue($('aitcg_option_masks_cat_id_'+option_id).getValue().split(','));        
        }
        },
    
    reloadImage: function( option_id ) {        
        this.loadImage( option_id, $('aitcg_option_select_'+option_id).value, {target: $(this.id + '_' + option_id ) });
    },
    
    loadImage : function ( option_id, value, event ) {
        var option = this.customImages[ value ],
            html;
        if( value!=0 )
        {
            option.option_id = option_id;
            if(typeof(option.error) == 'string' ) {
                html = option.error;
            } else {
                option.left = Math.round($('product_option_'+option_id+'_coords_offset_x').value * option.thumbnail_size.mult);
                if(option.left > option.thumbnail_size[0]) {
                    $('product_option_'+option_id+'_coords_offset_x').value = 0;
                    option.left = 0;
                }
                option.top = Math.round($('product_option_'+option_id+'_coords_offset_y').value * option.thumbnail_size.mult);
                if(option.top > option.thumbnail_size[1]) {
                    $('product_option_'+option_id+'_coords_offset_y').value = 0;
                    option.top = 0;
                }
                option.width = this.min($('product_option_'+option_id+'_coords_size_x').value, option.img_size[0], $('product_option_'+option_id+'_coords_offset_x').value, option.thumbnail_size.mult );
                if(option.width==0) {
                    $('product_option_'+option_id+'_coords_offset_x').value = 0;
                    $('product_option_'+option_id+'_coords_size_x').value = option.img_size[0];
                    option.width = Math.round(option.img_size[0] * option.thumbnail_size.mult);
                }
                option.height = this.min($('product_option_'+option_id+'_coords_size_y').value, option.img_size[1], $('product_option_'+option_id+'_coords_offset_y').value, option.thumbnail_size.mult );
                if(option.height==0) {
                    $('product_option_'+option_id+'_coords_offset_y').value = 0;
                    $('product_option_'+option_id+'_coords_size_y').value = option.img_size[1];
                    option.height = Math.round(option.img_size[1] * option.thumbnail_size.mult);
                }
                option.left = Math.max(0,option.left - 1);
                option.top = Math.max(0,option.top - 1);

                this.template = new Template(this.imageText, this.templateSyntax);
                html = this.template.evaluate(option);
            }
        }
        Element.update($( this.id + "_" + option_id + "_image"), html );        
    },
    
    min: function(value_now, value_max, value_offset, mult) {
        return Math.round( Math.min( value_now, value_max - value_offset ) * mult );
    }
});