
/**
 * Custom Product Preview
 *
 * @category:    Aitoc
 * @package:     Aitoc_Aitcg
 * @version      11.2.2
 * @license:     n/a
 * @copyright:   Copyright (c) 2013 AITOC, Inc. (http://www.aitoc.com)
 */
//required jscolor
//required script.aculo.us Builder

var aitcgColorSetEditor = Class.create
({
    initialize: function(params)
    {
        this.initVarName = params.initVarName;
        //form field with color set values
        this.source = params.source;
        //container for color set render
        this.container = params.container;
        this._colorContainer = null;
        this.lastColorId = null;
        this.colorAddText = params.colorAddText;
    },

    renderSet: function()
    {
        var colors = this.getColorsArray();
        var table = this.getColorContainer();

        var obj = this;
        if(colors !== null )
        {
            colors.each(function(colorCode){
                var colorElement = obj._getNewColorElement({
                    colorCode: colorCode 
                });
                table.appendChild(colorElement);
            });    
        }

        var addElement = this._getColorAddElement({
            id: 'aitcg_color_add'
        });

        table.appendChild(addElement);
        this.container.appendChild(table);
    },

    _generateColorIdNumber: function()
    {
        if(this.lastColorId == null)
        {
            this.lastColorId = 0;
        }
        else
        {
            this.lastColorId = this.lastColorId +1;
        }
        return this.lastColorId;
    },

    _getNewColorInputElement: function(params)
    {
        var settings = { 
            id: params.id,
            maxlength: 7,
            style: 'background-color:'+ params.colorCode,
            className: 'aitcg_colorset_color_input'
        };
        if(params.inputText !== undefined)
        {
            settings['value'] = params.inputText; 
        }
        return Builder.node('input', settings);
    },

    _getNewColorDeleteElement: function(params)
    {
        var colorDelElement = Builder.node('div',
        { 
            id: params.id,
            className: 'aitcg_colorset_color_delete'
        });

        colorDelElement.observe('click',function(){
            params.deletableEl.remove();
        });

        return colorDelElement;
    },

    _getColorAddElement: function(params)
    {
        var obj = this;

        var textEl = Builder.node('div', 
        { 
            className: 'aitcg_colorset_color_add_text'
        }, this.colorAddText);

        var colorAddElement =  Builder.node('div', 
        { 
            id: params.id,
            style: 'background-color: #FFFFFF',
            className: 'aitcg_colorset_color_add'
        }, [textEl]);

        var defaultColorcode = '#C0C0C0';
        colorAddElement.observe('click',function(){
            colorAddElement.insert({
                before: obj._getNewColorElement({colorCode:defaultColorcode})
            });
        });

        return colorAddElement;
    },

    _getNewColorElement: function(params)
    {
        var newNumber = this._generateColorIdNumber();
        var colorElement = Builder.node('div', 
        { 
            id:'aitcg_color_'+newNumber,
            style: 'background-color:'+ params.colorCode,
            className: 'aitcg_colorset_color'
        });

        var colorDeleteElement = this._getNewColorDeleteElement({
            id:'aitcg_color_delete'+newNumber,
            deletableEl: colorElement
        });

        var colorInputElement = this._getNewColorInputElement({
            id:'aitcg_color_input_'+newNumber,
            colorCode: params.colorCode 
        });
        //change color in ColorElement
        colorInputElement.observe('change',function(){
            colorElement.setStyle({backgroundColor: colorInputElement.getStyle('backgroundColor')});
        });

        colorElement.appendChild(colorDeleteElement);
        colorElement.appendChild(colorInputElement);

        //init ColorPicker
        var colorPicker = new jscolor.color(colorInputElement, {});
        colorPicker.fromString(params.colorCode);

        return colorElement;
    },

    getColorContainer: function()
    {
        this._initColorContainer();
        return this._colorContainer;
    },

    _initColorContainer: function()
    {
        if(this._colorContainer == null)
        {
            this._colorContainer = $(Builder.node('div', { id: 'aitcg_colorset' }));
        }    
    },

    getColorsSourceString: function()
    {
        return this.source.value;
    },

    getColorsArray: function()
    {
        return this.getColorsSourceString().match(/(#[A-F,0-9]{6})/ig);
    },

    updateSourceValue: function()
    {
        this.source.value = this.getColorsetString();
    },

    getColorsetString: function()
    {
        var colorset ='';
        var obj = this;
        this.getColorContainer().childElements().each(function(child){
            if(child.id !== 'aitcg_color_add')
            {
                colorset += obj._rgbToHex(child.getStyle('backgroundColor'));
            }
        });
        return colorset;
    },

    _rgbToHex: function(color) 
    {
        if (color.substr(0, 1) === '#') {
            return color;
        }
        var digits = [].slice.call(arguments).join(",").match(/\d+/g);
        var hex = '';

        digits.each(function(N){
            if (N==null)
            {
                hex += "00";
            }
            else
            {
                N=parseInt(N); 
                if(N==0 || isNaN(N))
                {
                    hex += "00";
                }
                else
                {
                    N=Math.max(0,N); N=Math.min(N,255); N=Math.round(N);
                    hex += "0123456789ABCDEF".charAt((N-N%16)/16)+ "0123456789ABCDEF".charAt(N%16);
                }
            }
        });
        return '#'+hex;
    }

});