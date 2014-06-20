// extension Code
function applyProductZoom()
{
    if ($('image') && $('zoom_out') && $('zoom_in'))
    {
        new Product.Zoom('image', 'track', 'handle', 'zoom_in', 'zoom_out', 'track_hint');
    }
}

AmConfigurableData = Class.create();
AmConfigurableData.prototype = 
{
    textNotAvailable : "",
    
    mediaUrlMain : "",
    
    currentIsMain : "",
    
    optionProducts : null,
    
    optionDefault : new Array(),
    
    oneAttributeReload : false,

    isResetButton : false,
    
    amlboxInstalled : false,
    
    initialize : function(optionProducts)
    {
        this.optionProducts = optionProducts;
    },
    //special for simple price
    reloadOptions : function()
    {
        if ('undefined' != typeof(spConfig) && spConfig.settings)
        {
            spConfig.settings.each(function(select){
                if (select.enable) {
                    spConfig.reloadOptionLabels(select);
                }    
            });    
        }
    },
     
    hasKey : function(key)
    {
        return ('undefined' != typeof(this.optionProducts[key]));
    },
    
    getData : function(key, param)
    {
        if (this.hasKey(key) && 'undefined' != typeof(this.optionProducts[key][param]))
        {
            return this.optionProducts[key][param];
        }
        return false;
    },
    
    saveDefault : function(param, data)
    {
        this.optionDefault['set'] = true;
        this.optionDefault[param] = data;
    },
    
    getDefault : function(param)
    {
        if ('undefined' != typeof(this.optionDefault[param]))
        {
            return this.optionDefault[param];
        }
        return false;
    }
}
// extension Code End

Product.Config.prototype.resetChildren = function(element){
    if(element.childSettings) {
        for(var i=0;i<element.childSettings.length;i++){
            element.childSettings[i].selectedIndex = 0;
            element.childSettings[i].disabled = true;
            if(element.config){
                this.state[element.config.id] = false;
            }
        }
    }
    
    // extension Code Begin
    this.processEmpty();
    // extension Code End
}

Product.Config.prototype.fillSelect = function(element){
    var attributeId = element.id.replace(/[a-z]*/, '');
    var options = this.getAttributeOptions(attributeId);
	this.clearSelect(element);
    element.options[0] = new Option(this.config.chooseText, '');

    var prevConfig = false;
    if(element.prevSetting){
        prevConfig = element.prevSetting.options[element.prevSetting.selectedIndex];
    }

    if(options) {
        if ($('amconf-images-' + attributeId))
            {
                $('amconf-images-' + attributeId).parentNode.removeChild($('amconf-images-' + attributeId));
            }
            
       if (this.config.attributes[attributeId].use_image)
        {
            holder = element.parentNode;
            holderDiv = document.createElement('div');
            holderDiv = $(holderDiv); // fix for IE
            holderDiv.addClassName('amconf-images-container');
            holderDiv.id = 'amconf-images-' + attributeId;
            holder.insertBefore(holderDiv, element);
        }
        // extension Code End
        
        var index = 1;		
        for(var i=0;i<options.length;i++){
            var allowedProducts = [];
            if(prevConfig) {
                for(var j=0;j<options[i].products.length;j++){
                    if(prevConfig.config.allowedProducts
                        && prevConfig.config.allowedProducts.indexOf(options[i].products[j])>-1){
                        allowedProducts.push(options[i].products[j]);
                    }
                }
            } else {
                allowedProducts = options[i].products.clone();
            }

            if(allowedProducts.size()>0)
            {
                // extension Code
                if (this.config.attributes[attributeId].use_image)
                {
                    var imgContainer = document.createElement('div');
                    imgContainer = $(imgContainer); // fix for IE
                    imgContainer.addClassName('amconf-image-container');
                    imgContainer.id = 'amconf-images-container-' + attributeId;
                    imgContainer.style.float = 'left';
                    holderDiv.appendChild(imgContainer);
            
                    var image = document.createElement('img');
                    image = $(image); // fix for IE
                    image.id = 'amconf-image-' + options[i].id;
			        image.src = options[i].image;
			        image.addClassName('amconf-image');
			        image.alt = options[i].label;
			        image.title = options[i].label;
					
			if(showAttributeTitle != 0) image.style.marginBottom = '0px';
			else image.style.marginBottom = '7px';
					
                     image.observe('click', this.configureImage.bind(this));
					
		            if('undefined' != typeof(buble)){
                         image.observe('mouseover', buble.showToolTip);
                         image.observe('mouseout', buble.hideToolTip); 				 
                    }
					
					imgContainer.appendChild(image);
                    
                    if(showAttributeTitle && showAttributeTitle != 0){ 
                        var amImgTitle = document.createElement('div');
                        amImgTitle = $(amImgTitle); // fix for IE
                        amImgTitle.addClassName('amconf-image-title');
                        amImgTitle.id = 'amconf-images-title-' + options[i].id;
                        amImgTitle.setStyle({
                            fontWeight : 600,
                            textAlign : 'center'
                        });
                        amImgTitle.innerHTML = options[i].label;  
                        imgContainer.appendChild(amImgTitle);     
                    }
                    image.onload = function(){
                        var optId = this.id.replace(/[a-z-]*/, '');
                        var maxW = this.getWidth();
                        if(optId) {
                            var title = $('amconf-images-title-' + optId);
                            if(title && title.getWidth() && title.getWidth() > maxW) {
                                maxW = title.getWidth();
                            }
                                
                        }
                        if(this.parentNode){
                            this.parentNode.style.width =   maxW + 'px'; 
                        }
                        if(this.parentNode.childElements()[1]){
                            this.parentNode.childElements()[1].style.width =   maxW + 'px'; 
                        }
                    };  
                }
                // extension Code End
                
                options[i].allowedProducts = allowedProducts;
                element.options[index] = new Option(this.getOptionLabel(options[i], options[i].price), options[i].id);    
                element.options[index].config = options[i];
                index++;
            }
        }
        if(this.config.attributes[attributeId].use_image) {
            var lastContainer = document.createElement('div');
            lastContainer = $(lastContainer); // fix for IE
            lastContainer.setStyle({clear : 'both'});
            holderDiv.appendChild(lastContainer);    
        }        
    }
}

Product.Config.prototype.configureElement = function(element) 
{
    // extension Code
    optionId = element.value;
    if ($('amconf-image-' + optionId))
    {
        this.selectImage($('amconf-image-' + optionId));
    } else 
    {
        attributeId = element.id.replace(/[a-z-]*/, '');
        if ($('amconf-images-' + attributeId))
        {
        $('amconf-images-' + attributeId).childElements().each(function(child){
             if(child.childElements()[0])
                child.childElements()[0].removeClassName('amconf-image-selected');
        });
        }
    }
    // extension Code End
    
    this.reloadOptionLabels(element);
    if(element.value){
        this.state[element.config.id] = element.value;
        if(element.nextSetting){
            element.nextSetting.disabled = false;
            this.fillSelect(element.nextSetting);
            this.resetChildren(element.nextSetting);
        }
    }
    else {
        // extension Code
        if(element.childSettings) {
            for(var i=0;i<element.childSettings.length;i++){
                attributeId = element.childSettings[i].id.replace(/[a-z-]*/, '');
                if ($('amconf-images-' + attributeId))
                {
                    $('amconf-images-' + attributeId).parentNode.removeChild($('amconf-images-' + attributeId));
                }
            }
        }
        // extension Code End
        
        this.resetChildren(element);
        
        // extension Code
        if (this.settings[0].hasClassName('no-display'))
        {
            this.processEmpty();
        }
        // extension Code End
    }
    
    // extension Code
    var key = '';
    this.settings.each(function(select){
        // will check if we need to reload product information when the first attribute selected
        if (!parseInt(select.value) && 'undefined' != typeof(confData) && confData.oneAttributeReload && "undefined" != select.options[1] && !confData.isResetButton)
	{
            // if option is not selected, and setting is set to "Yes", will consider it as if the first attribute was selected (0 - is "Choose ...")
	    if(select.options[1])
	            key += select.options[1].value + ',';
        } else 
        {
             if('undefined' != typeof(confData) && (!confData.oneAttributeReload || (confData.oneAttributeReload && key == ''))){
                 key += select.value + ',';   
             }
             else{
                 if('undefined' != typeof(confData)){
                    confData.currentIsMain = true;    
                 }
             }
        }
    });
    if (typeof confData != 'undefined') {
        confData.isResetButton = false;    
    }
    key = key.substr(0, key.length - 1);
    this.updateData(key);
    
    if (confData.useSimplePrice == "1")
    {
        // replace price values with the selected simple product price
        this.reloadSimplePrice(key);
    }
    else
    {
        // default behaviour
        this.reloadPrice();
    }
    
    // for compatibility with custom stock status extension:
    if ('undefined' != typeof(stStatus) && 'function' == typeof(stStatus.onConfigure))
    {
        stStatus.onConfigure(key, this.settings);
    }
	//Amasty code for Automatically select attributes that have one single value
    if(('undefined' != typeof(amConfAutoSelectAttribute) && amConfAutoSelectAttribute) ||('undefined' != typeof(amStAutoSelectAttribute) && amStAutoSelectAttribute)){
        var nextSet = element.nextSetting;
        if(nextSet && nextSet.options.length == 2 && !nextSet.options[1].selected && element && !element.options[0].selected){
            nextSet.options[1].selected = true;
            this.configureElement(nextSet);
        } 
    }
    // extension Code End
}

Product.Config.prototype.configureForValues =  function () {
        if (this.values) {
            this.settings.each(function(element){
                var attributeId = element.attributeId;
                element.value = (typeof(this.values[attributeId]) == 'undefined')? '' : this.values[attributeId];
                this.configureElement(element);
            }.bind(this));
        }
        //Amasty code for Automatically select attributes that have one single value
        if(('undefined' != typeof(amConfAutoSelectAttribute) && amConfAutoSelectAttribute) ||('undefined' != typeof(amStAutoSelectAttribute) && amStAutoSelectAttribute)){
            var select  = this.settings[0];
            if(select && select.options.length == 2 && !select.options[1].selected){
                select.options[1].selected = true;
                this.configureElement(select);
            }
        }
}
    
// these are new methods introduced by the extension
// extension Code
Product.Config.prototype.configureImage = function(event){
    var element = Event.element(event);
    attributeId = element.parentNode.id.replace(/[a-z-]*/, '');
    optionId = element.id.replace(/[a-z-]*/, '');
    
    this.selectImage(element);
    
    $('attribute' + attributeId).value = optionId;
    this.configureElement($('attribute' + attributeId));
}

Product.Config.prototype.selectImage = function(element)
{
    attributeId = element.parentNode.id.replace(/[a-z-]*/, '');
    $('amconf-images-' + attributeId).childElements().each(function(child){
        if(child.childElements()[0])
            child.childElements()[0].removeClassName('amconf-image-selected');
    });
    element.addClassName('amconf-image-selected');
}

Product.Config.prototype.processEmpty = function()
{
    $$('.super-attribute-select').each(function(select) {
        var attributeId = select.id.replace(/[a-z]*/, '');
        if (select.disabled)
        {
            if ($('amconf-images-' + attributeId))
            {
                $('amconf-images-' + attributeId).parentNode.removeChild($('amconf-images-' + attributeId));
            }
            holder = select.parentNode;
            holderDiv = document.createElement('div');
            holderDiv.addClassName('amconf-images-container');
            holderDiv.id = 'amconf-images-' + attributeId;
            if ('undefined' != typeof(confData))
            {
            	holderDiv.innerHTML = confData.textNotAvailable;
            } else 
            {
            	holderDiv.innerHTML = "";
            }
            holder.insertBefore(holderDiv, select);
        } else if (!select.disabled && !$(select).hasClassName("no-display")) {
            var element = $(select.parentNode).select('#amconf-images-' + attributeId)[0];
            if (typeof confData != 'undefined' && typeof element != 'undefined' && element.innerHTML == confData.textNotAvailable){
                element.parentNode.removeChild(element);
            }
        }
    }.bind(this));
}

Product.Config.prototype.clearConfig = function()
{
    this.settings[0].value = "";
    if (typeof confData != 'undefined')
    	confData.isResetButton = true;
    this.configureElement(this.settings[0]);
    return false;
}

Product.Config.prototype.updateData = function(key)
{
    if ('undefined' == typeof(confData))
    {
        return false;
    }
    if (confData.hasKey(key))
    {
        // getting values of selected configuration
        if (confData.getData(key, 'name'))
        {
            $$('.product-name h1').each(function(container){
                if (!confData.getDefault('name'))
                {
                    confData.saveDefault('name', container.innerHTML);
                }
                container.innerHTML = confData.getData(key, 'name');
            }.bind(this));
        }
        if (confData.getData(key, 'short_description'))
        {
            $$('.short-description div').each(function(container){
                if (!confData.getDefault('short_description'))
                {
                    confData.saveDefault('short_description', container.innerHTML);
                }
                container.innerHTML = confData.getData(key, 'short_description');
            }.bind(this));
        }
        if (confData.getData(key, 'description'))
        {
            $$('.box-description div').each(function(container){
                if (!confData.getDefault('description'))
                {
                    confData.saveDefault('description', container.innerHTML);
                }
                container.innerHTML = confData.getData(key, 'description');
            }.bind(this));
        }
        if (confData.getData(key, 'media_url'))
        {
            // should reload images
            $$('.product-img-box').each(function(container){
                tmpContainer = container;
            }.bind(this));
            new Ajax.Updater(tmpContainer, confData.getData(key, 'media_url'), {
                evalScripts: true,
                onSuccess: function(transport)
                {
                    if(Boolean(parseInt(AmZoomerOptions.zoomEnable)))
                    {
                       confData.saveDefault('media', tmpContainer.innerHTML);
                       var tm = setTimeout("applyProductZoom()",2500);
                       confData.currentIsMain = false;
                    }
                },
                onCreate: function()
                {
                    if($('preloader') && AmZoomerOptions && AmZoomerOptions.mainImgHeight){
                        var globalSmallHeight = parseInt(AmZoomerOptions.mainImgHeight);
                        if(globalSmallHeight == 0) globalSmallHeight = 265;
                        var topPading = globalSmallHeight/2 - $('preloader').getHeight()/2;
                        var leftPading = (globalSmallHeight - $('preloader').getWidth())/2;
                        $('preloader').style.left = leftPading + 'px';
                        $('preloader').style.top = topPading + 'px';
                        Element.show("preloader");
                    }
                },
                onComplete: function()
                {
                    if($('preloader')){
                        Element.hide("preloader");
						tmpContainer.innerHTML.evalScripts();
                        loadAllImages();
                    }
                    
                }
            });
        } else if (confData.getData(key, 'noimg_url'))
        {
            noImgInserted = false;
            $$('.product-img-box img').each(function(img){
                if (!noImgInserted)
                {
                    img.src = confData.getData(key, 'noimg_url');
                    $(img).stopObserving('click');
                    $(img).stopObserving('mouseover');
                    $(img).stopObserving('mousemove');
                    $(img).stopObserving('mouseout');
                    noImgInserted = true;
                }
            });
        }
        else if (confData.getDefault('media') && !confData.currentIsMain)
        {
            $$('.product-img-box').each(function(container){
                tmpContainer = container;
            }.bind(this));
            new Ajax.Updater(tmpContainer, confData.mediaUrlMain, {
                evalScripts: true,
                onSuccess: function(transport) {
                    confData.saveDefault('media', tmpContainer.innerHTML);
                    var tm = setTimeout("applyProductZoom()", 2000);
                    confData.currentIsMain = true;
                },
				onComplete: function()
				{
					if($('preloader')){
						Element.hide("preloader");
						tmpContainer.innerHTML.evalScripts();
						loadAllImages();
					}
					
				}
            });
        }
    } else 
    {
        // setting values of default product
        if (true == confData.getDefault('set'))
        {
            if (confData.getDefault('name'))
            {
                $$('.product-name h1').each(function(container){
                    container.innerHTML = confData.getDefault('name');
                }.bind(this));
            }
            if (confData.getDefault('short_description'))
            {
                $$('.short-description div').each(function(container){
                    container.innerHTML = confData.getDefault('short_description');
                }.bind(this));
            }
            if (confData.getDefault('description'))
            {
                $$('.box-description div').each(function(container){
                    container.innerHTML = confData.getDefault('description');
                }.bind(this));
            }
            if (confData.getDefault('media') && !confData.currentIsMain)
            {
                $$('.product-img-box').each(function(container){
                    tmpContainer = container;
                }.bind(this));
                new Ajax.Updater(tmpContainer, confData.mediaUrlMain, {
                    evalScripts: true,
                    onSuccess: function(transport) {
                        confData.saveDefault('media', tmpContainer.innerHTML);
                        var tm = setTimeout("applyProductZoom()",2000);
                        confData.currentIsMain = true;
                    },
					onComplete: function()
					{
						if($('preloader')){
							Element.hide("preloader");
							tmpContainer.innerHTML.evalScripts();
							loadAllImages();
						}
						
					}
				});
            }
        }
    }
}

//start code for reload simple price

Product.Config.prototype.reloadSimplePrice = function(key)
{
    if ('undefined' == typeof(confData))
    {
        return false;
    }
    
    var container;
    var result = false;
    if (confData.hasKey(key))
    {
        // convert div.price-box into price info container
        
        // top price box
        if (confData.getData(key, 'price_html'))
        {
	    $$('.product-shop .price-box').each(function(container)
            {
                if (!confData.getDefault('price_html'))
                {
                    confData.saveDefault('price_html', container.innerHTML);
                }
                container.addClassName('amconf_price_container');
            }.bind(this));
            
           $$('.amconf_price_container').each(function(container)
            {
		container.outerHTML = confData.getData(key, 'price_clone_html');	
	    }.bind(this));        
	   

        }
        
        // bottom price box
        if (confData.getData(key, 'price_clone_html'))
        {
            $$('.product-options-bottom .price-box').each(function(container)
            {
                if (!confData.getDefault('price_clone_html'))
                {
                    confData.saveDefault('price_clone_html', container.innerHTML);
                }
                container.addClassName('amconf_price_container');
            }.bind(this));
            
            $$('.amconf_price_clone_container').each(function(container)
            {
		container.outerHTML = confData.getData(key, 'price_clone_html');	
	    }.bind(this));

        }
        
        // function return value
        if (confData.getData(key, 'price'))
        {
            result = confData.getData(key, 'price');
        }
    } 
    else 
    {
        // setting values of default product
        if (true == confData.getDefault('set'))
        {
            // restore price info containers into default price-boxes
            
            if (confData.getDefault('price_html'))
            {
                $$('.amconf_price_container').each(function(container)
            	{
			container.outerHTML  = confData.getDefault('price_html');
                	container.removeClassName('amconf_price_container');	
	    	}.bind(this));
               
            }
            
            if (confData.getDefault('price_clone_html'))
            {
                $$('.amconf_price_clone_container').each(function(container)
            	{
			container.outerHTML = confData.getDefault('price_clone_html');
                	container.removeClassName('amconf_price_clone_container');	
	    	}.bind(this));
                
            }
            
            // function return value
            if (confData.getDefault('price'))
            {
                result = confData.getDefault('price');
            }
        }
    }
    
    return result; // actually the return value is never used
}

Product.Config.prototype.getOptionLabel = function(option, price){
    var price = parseFloat(price);
    if (this.taxConfig.includeTax) {
        var tax = price / (100 + this.taxConfig.defaultTax) * this.taxConfig.defaultTax;
        var excl = price - tax;
        var incl = excl*(1+(this.taxConfig.currentTax/100));
    } else {
        var tax = price * (this.taxConfig.currentTax / 100);
         var excl = price;
         var incl = excl + tax;
    }
    if (this.taxConfig.showIncludeTax || this.taxConfig.showBothPrices) {
        price = incl;
    } else {
        price = excl;
    }
    var str = option.label;
    if(price){
        if('undefined' != typeof(confData) && confData.useSimplePrice == "1" && confData['optionProducts'] && confData['optionProducts'][option.id] && confData['optionProducts'][option.id]['price']) {
            str+= ' ' + this.formatPrice(confData['optionProducts'][option.id]['price'], true);
            pos = str.indexOf("+");
            str = str.substr(0, pos) + str.substr(pos + 1, str.length);
        }
        else {
            if (this.taxConfig.showBothPrices) {
                str+= ' ' + this.formatPrice(excl, true) + ' (' + this.formatPrice(price, true) + ' ' + this.taxConfig.inclTaxTitle + ')';
            } else {
                str+= ' ' + this.formatPrice(price, true);
            }
        }
    }
    else {
        if('undefined' != typeof(confData) && confData.useSimplePrice == "1" && confData['optionProducts'] && confData['optionProducts'][option.id] && confData['optionProducts'][option.id]['price']) {
            str+= ' ' + this.formatPrice(confData['optionProducts'][option.id]['price'], true);
            pos = str.indexOf("+");
            str = str.substr(0, pos) + str.substr(pos + 1, str.length);
        }    
    }
    return str;
}


Product.Config.prototype.reloadOptionLabels = function(element){
    var selectedPrice;
    if(element.options[element.selectedIndex].config && !this.config.stablePrices){
        selectedPrice = parseFloat(element.options[element.selectedIndex].config.price)
    }
    else{
        selectedPrice = 0;
    }
    for(var i=0;i<element.options.length;i++){
        if(element.options[i].config){
            //extension code start for compatibility with Custom Stock Sratus
            var text = element.options[i].text;
            var posFirst = text.indexOf('(');
            var posSecond = text.indexOf(')');
            if(posFirst && posSecond){
                text = text.substring(posFirst-1, posSecond+1);    
            }
            else {
                text = '';
            }
            //extension code end
            element.options[i].text = this.getOptionLabel(element.options[i].config, element.options[i].config.price-selectedPrice) + text;
        }
    }
} 

Event.observe(window, 'load', function(){
    if ('undefined' != typeof(confData) && confData.useSimplePrice == "1")
    {
        confData.reloadOptions();
    }
});

// extension Code End
