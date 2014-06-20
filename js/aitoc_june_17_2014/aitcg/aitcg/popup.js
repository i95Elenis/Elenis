
/**
 * Custom Product Preview
 *
 * @category:    Aitoc
 * @package:     Aitoc_Aitcg
 * @version      11.2.2
 * @license:     n/a
 * @copyright:   Copyright (c) 2013 AITOC, Inc. (http://www.aitoc.com)
 */
Aitcg.Popup = new Class.create();
Aitcg.Popup.prototype =
{
    template : null,
    options : {},
    templateSyntax : /(^|.|\r|\n)({{(\w+)}})/,
    
    initialize: function(template, options) {
        this.template = template;
        this.options = options;
    },
    
    render: function() {
        var popup = new Template(this.template, this.templateSyntax);
        var html = popup.evaluate(this.options);
        return html;
    },
    
    renderWindow: function ( imgId, fullUrl, callback ) {
        this.addLoadingMask();
        $('loading-mask').show();
        Aitcg.cacheImage(imgId, fullUrl, callback);
    },

    addLoadingMask: function()
    {
        if( $('loading-mask') == null ) 
        {
            if(typeof(AitPopupHtml)!= 'undefined') {
                $$('body')[0].insert( {bottom:AitPopupHtml} );
            } else {
                document.body.appendChild( '<div id="loading-mask">Please wait...</div>' );
            }
        }
    },
    
    showTextWindow: function ( ) {
        // window rendering
        var scr = Aitcg.getDefault();
        //var window = new Aitcg.Popup(PrintableAreaEditor, this.imgInfo);
        var body = $$('body')[0];        
        Element.insert(body, {bottom: this.render()});
        var height = body.getHeight();
        $('message-popup-window-mask').setStyle({'height':height+'px'});
        $('message-popup-window').setStyle({
            'width'      : '' + parseInt(scr.dim.width*3/4) +'px',
            'top'        : '' + parseInt(50 + scr.off.top) + 'px',
            'marginLeft'       : '-' + parseInt(scr.dim.width*3/8) + 'px'
        });
        Element.show('message-popup-window-mask');
        $('message-popup-window').addClassName('show');         
    }, 
    
    showWindow: function ( imgId )
    {
        var scr = Aitcg.countMult( imgId, arguments[1], arguments[2], arguments[3] );

        // some window rendering variables for templates
        this.options['img_width'] = scr.curr.width;
        this.options['img_height'] = scr.curr.height;

        //if(typeof(arguments[1])!='undefined' && arguments[1]==true) {
            this.options['width']   = Math.floor(this.options.areaSizeX * scr.mult)+'px';
            this.options['height']  = Math.floor(this.options.areaSizeY * scr.mult)+'px';
            this.options['left']    = Math.max(0, Math.round(this.options.areaOffsetX * scr.mult - 1))+'px';
            this.options['top']     = Math.max(0, Math.round(this.options.areaOffsetY * scr.mult - 1))+'px';
        //}
        
        // window rendering
        //var window = new Aitcg.Popup(PrintableAreaEditor, this.imgInfo);
        var body = document.body;        
        Element.insert(body, {bottom: this.render()});
        var height = body.getHeight();
        var windowWidth = (scr.curr.width < 380)? 380 : scr.curr.width ;
        $('message-popup-window-mask').setStyle({'height':height+'px'});
        var topTmp = parseInt((scr.dim.height-scr.curr.height-10)/2 + scr.off.top);
        $('message-popup-window').setStyle({
            'width'      : windowWidth + 'px',
            'top'        : '' + (((topTmp + scr.off[1])>0)?topTmp:0) + 'px',
            'marginLeft' : '-'+(parseInt(windowWidth/2+4))+'px'
        });
        if(scr.curr.width < 380)
        {
            $('aitcg_popup_image_container').setStyle({
                'marginLeft' : (parseInt((windowWidth-scr.curr.width)/2))+'px'
            });
        }
        Element.show('message-popup-window-mask');
        $('message-popup-window').addClassName('show');         
        return scr;
    },

    closeEditor : function() {
        $('message-popup-window').remove();
        $('message-popup-window-mask').remove();
    }
    
};