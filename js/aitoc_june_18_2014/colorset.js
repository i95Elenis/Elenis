
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

var aitcgColorSet = Class.create
({
    initialize: function(params)
    {
        //colorset id
        this.id = params.id;
        //input DOM object for colorcode(reed about 'jscolor' colorpicker)
        this.colorInputId = params.colorInputId;
        //concrete object name  of this class 
        this.initVarName = params.initVarName;
        // color set values string
        this.source = params.source;
        //container for color set render
        this.containerId = params.containerId;
        this._colorContainer = null;
        this.lastColorId = null;
    },

    renderSet: function()
    {
        var colors = this.getColorsArray();
        this._colorContainer = null;
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
        $(this.containerId).insert({top:table});
        this._initColorInput();
    },
    
    _initColorInput: function()
    {
        //add first color from set
        $(this.colorInputId).color.fromString(this.getColorsArray().first());
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
        return this.id +'_'+ this.lastColorId;
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
        var colorsetObj = this;
        //change color in ColorElement
        colorElement.observe('click',function(){
            $(colorsetObj.colorInputId).color.fromString(params.colorCode);
        });
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
            this._colorContainer = $(Builder.node('div', {
                id: 'aitcg_colorset_'+this.id,
                className: 'aitcg_colorset'
            }));
        }    
    },

    getColorsArray: function()
    {
        return this.source.match(/(#[A-F,0-9]{6})/ig);
    }

});