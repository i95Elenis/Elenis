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
    
    amlboxInstalled : false,
    
    initialize : function(optionProducts)
    {
        this.optionProducts = optionProducts;
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

prevNextSetting = [];
// extension Code End
Product.Config.prototype.initialize = function(config){
        this.config     = config;
        this.taxConfig  = this.config.taxConfig;
        if (config.containerId) {
            this.settings   = $$('#' + config.containerId + ' ' + '.super-attribute-select' + '-' + config.productId);
        } else {
            this.settings   = $$('.super-attribute-select' + '-' + config.productId);
        }
     
        this.state      = new Hash();
        this.priceTemplate = new Template(this.config.template);
        this.prices     = config.prices;
        
        // Set default values from config
        if (config.defaultValues) {
            this.values = config.defaultValues;
        }
        //hide all labels
         this.settings.each(function(element){
            var attributeId = element.id.replace(/[a-z]*/, '');
             $('label-' + attributeId).hide();
         }.bind(this))
        
        // Overwrite defaults by url
        var separatorIndex = window.location.href.indexOf('#');
        if (separatorIndex != -1) {
            var paramsStr = window.location.href.substr(separatorIndex+1);
            var urlValues = paramsStr.toQueryParams();
            if (!this.values) {
                this.values = {};
            }
            for (var i in urlValues) {
                this.values[i] = urlValues[i];
            }
        }
        
        // Overwrite defaults by inputs values if needed
        if (config.inputsInitialized) {
            this.values = {};
            this.settings.each(function(element) {
                if (element.value) {
                    var attributeId = element.id.replace(/[a-z]*/, '');
                    this.values[attributeId] = element.value;
                }
            }.bind(this));
        }
            
        // Put events to check select reloads 
        this.settings.each(function(element){
            Event.observe(element, 'change', this.configure.bind(this))
        }.bind(this));

        // fill state
        this.settings.each(function(element){
            var attributeId = element.id.replace(/[a-z]*/, '');
            var pos = attributeId.indexOf('-');
            if ('-1' != pos)
                attributeId = attributeId.substring(0, pos);
            if(attributeId && this.config.attributes[attributeId]) {
                element.config = this.config.attributes[attributeId];
                element.attributeId = attributeId;
                this.state[attributeId] = false;
            }
        }.bind(this))
   //If Ajax Cart     
    if('undefined' != typeof(AmAjaxObj)) {
            var length = this.settings.length;
            for (var i = 0; i < length-1; i++) {
              var element = this.settings[i];
              if(element  && element.config){
                   for (var j = i+1; j < length; j++) {
                       var elementNext = this.settings[j];
                       if(elementNext  && elementNext.config && (elementNext.config['id'] == element.config['id'])){
                            this.settings.splice (i,1);
                            i--;
                            break;    
                       }    
                   }    
              }
            }    
         }  
            
        // Init settings dropdown
        var childSettings = [];
        for(var i=this.settings.length-1;i>=0;i--){
            var prevSetting = this.settings[i-1] ? this.settings[i-1] : false;
            var nextSetting = this.settings[i+1] ? this.settings[i+1] : false;
            if (i == 0){
                this.fillSelect(this.settings[i])
            } else {
                this.settings[i].disabled = true;
            }
            $(this.settings[i]).childSettings = childSettings.clone();
            prevNextSetting[this.settings[i].config.id] = [prevSetting, nextSetting];
            var optionId = this.settings[i].id;
            var pos = optionId.indexOf('-');
            if ('-1' != pos){
                optionId = optionId.substring(pos+1, optionId.lenght);
                id = parseInt(optionId);
                prevNextSetting[id] = [];
                prevNextSetting[id][this.settings[i].config.id] = [prevSetting, nextSetting];
            }
            $(this.settings[i]).prevSetting   = prevSetting;
            $(this.settings[i]).nextSetting   = nextSetting;
            childSettings.push(this.settings[i]);
        }
        // Set values to inputs
        this.configureForValues();
        document.observe("dom:loaded", this.configureForValues.bind(this));
}
 
    
resetChildren = function(element){
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
    var pos = attributeId.indexOf('-');
    if ('-1' != pos)
        attributeId = attributeId.substring(0, pos);
    var options = this.getAttributeOptions(attributeId);
    this.clearSelect(element);
    element.options[0] = new Option(this.config.chooseText, '');

    var prevConfig = false;
    if(element.prevSetting){
        prevConfig = element.prevSetting.options[element.prevSetting.selectedIndex];
    }
    if(options) {
        // extension Code
        if (this.config.attributes[attributeId].use_image)
        {
            if ($('amconf-images-' + attributeId + '-' + this.config.productId))
            {
                $('amconf-images-' + attributeId + '-' + this.config.productId).parentNode.removeChild($('amconf-images-' + attributeId + '-' + this.config.productId));
            }
            holder = element.parentNode;
            $('label-' + attributeId + '-' + this.config.productId).show();
            var holderDiv = document.createElement('div');
            holderDiv = $(holderDiv); // fix for IE
            holderDiv.addClassName('amconf-images-container');
            holderDiv.id = 'amconf-images-' + attributeId + '-' + this.config.productId;
            holder.insertBefore(holderDiv, element);
        }
        // extension Code End
        
        var index = 1;
        for(var i=0;i<options.length;i++){
            var allowedProducts = [];
            if(prevConfig) {
                for(var j=0;j<options[i].products.length;j++){
                    if(prevConfig.config && prevConfig.config.allowedProducts
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
                    imgContainer.id = 'amconf-images-container-' + options[i].id + '-' + this.config.productId;
                    holderDiv.appendChild(imgContainer);
            
                    image = document.createElement('img');
                    image = $(image); // fix for IE
                    image.id = 'amconf-image-' + options[i].id + '-' + this.config.productId;
		            image.src   = options[i].image;
                    image.width = this.config.size;
                    image.height = this.config.size;
                    image.addClassName('amconf-image');
		            image.alt = options[i].label;
		            image.title = options[i].label;
                    image.observe('click', this.configureImage.bind(this));
		    		if('undefined' != typeof(buble)){
                         image.observe('mouseover', buble.showToolTip)
                         image.observe('mouseout', buble.hideToolTip)       
                    }
                    imgContainer.appendChild(image);
                }
                // extension Code End
                
                options[i].allowedProducts = allowedProducts;
                element.options[index] = new Option(this.getOptionLabel(options[i], options[i].price), options[i].id);
                element.options[index].config = options[i];
                index++;
            }
        }
        if(index > 1 && this.config.attributes[attributeId].use_image) {
            var amcart  = document.createElement('div');
            amcart = $(amcart); // fix for IE
            amcart.id = 'amconf-amcart-' + this.config.productId;
            holderDiv.appendChild(amcart);
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
    var me = this;
    var optionId = element.value;
   
    this.reloadOptionLabels(element);
//Ajax cart
    if(element.value){
        if (element.config.id){
            this.state[element.config.id] = element.value;
        }
        var elId = element.id;
        var pos = elId.indexOf('-');
        if ('-1' != pos){
            elId = elId.substring(pos+1, elId.lenght);
            elId = 	parseInt(elId);
            if(prevNextSetting[elId] && prevNextSetting[elId][element.config.id] && prevNextSetting[elId][element.config.id][1] || element.nextSetting){
                if( prevNextSetting[elId][element.config.id][1]){
                    element.nextSetting = prevNextSetting[elId][element.config.id][1]
                }
                element.nextSetting.disabled = false;
                this.fillSelect(element.nextSetting);
                this.resetChildren(element.nextSetting);
            }
        }
    }
    else {
        // extension Code
        if(element.childSettings) {
            for(var i=0;i<element.childSettings.length;i++){
                attributeId = element.childSettings[i].id.replace(/[a-z-]*/, '');
                if ($('amconf-images-' + attributeId + '-' + this.config.productId))
                {
                    $('amconf-images-' + attributeId + '-' + this.config.productId).parentNode.removeChild($('amconf-images-' + attributeId + '-' + this.config.productId));
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
    
    if ($('amconf-image-' + optionId + '-' + this.config.productId))
    {
        this.selectImage($('amconf-image-' + optionId + '-' + this.config.productId));
    } 
    else {
        attributeId = element.id.replace(/[a-z-]*/, '');
        if ($('amconf-images-' + attributeId))
        {
            $('amconf-images-' + attributeId).childElements().each(function(child){
                child.removeClassName('amconf-image-selected');
            });
        }
    }
    
    // for compatibility with custom stock status extension:
    if ('undefined' != typeof(stStatus) && 'function' == typeof(stStatus.onConfigure))
    {
        stStatus.onConfigure(key, this.settings); // todo: key is undefined
    }
    // extension Code End
}
    
Product.Config.prototype.reloadSimplePrice = function(parentId, key)
{
    if ('undefined' == typeof(confData))
    {
        return false;
    }
    
    var result = false;
    var childConf = confData[parentId]['optionProducts'][key];
    
    result = childConf['price'];

    var elmExpr = '.price-box span#product-price-'+parentId+' span.price';
    $$(elmExpr).each(function(container)
    {
        container.innerHTML = this.formatPrice(result);
    }.bind(this));
    
    return result; // actually the return value is never used
}

// these are new methods introduced by the extension
// extension Code
Product.Config.prototype.configureImage = function(event){
    var element = Event.element(event);
    attributeId = element.parentNode.parentNode.id.replace(/[a-z-]*/, '');
    var optionId = element.id.replace(/[a-z-]*/, '');
    var pos = optionId.indexOf('-');
    if ('-1' != pos)
        optionId = optionId.substring(0, pos);
    //this.selectImage(element);
    $$('#attribute' + attributeId).each(function(select){
        select.value = optionId;    
    });
    this.configureElement($('attribute' + attributeId));
}

Product.Config.prototype.selectImage = function(element)
{
    attributeId = element.parentNode.parentNode.id.replace(/[a-z-]*/, '');
    $('amconf-images-' + attributeId).childElements().each(function(child){
        var childr = child.childElements();
        if(childr[0]) {
            $(childr[0]).removeClassName('amconf-image-selected');    
        }
    });
    element.addClassName('amconf-image-selected');
    
    var pos = attributeId.indexOf('-');
    if ('-1' != pos) {
        var optionId = attributeId.substring(0, pos);
        var parentId = attributeId.substring(pos+1, attributeId.length);
        var elId = element.id.replace(/[a-z-]*/, '');
        pos = elId.indexOf('-');
        elId = elId.substring(0, pos);
        var key = '';
        this.settings.each(function(select){
            // will check if we need to reload product information when the first attribute selected
            if (!parseInt(select.value) && 'undefined' != typeof(confData[parentId]) && confData[parentId].oneAttributeReload && "undefined" != select.options[1])
            {
                key += select.options[1].value + ',';
            } else 
            {
                if( parseInt(select.value) > 0)
                    key += select.value + ',';
            }
        });
        key = key.substr(0, key.length - 1);
        
        if(confData[parentId]['optionProducts'][key]){ 
             var parUrl = confData[parentId]['optionProducts'][key]['parent_image'];
             var possl = parUrl.lastIndexOf('/');
             $$('.product-image img').each(function(img){
		    var posslImg = img.src.lastIndexOf('/');
                  if(img.src.substr(posslImg, img.src.length) == parUrl.substr(possl, parUrl.length) || img.className == ('amconf-parent-'+parentId)){
                      img.src = confData[parentId]['optionProducts'][key]['small_image'];
                      img.addClassName('amconf-parent-'+parentId);
                  }
             });              
                        
          }

          
        if (confData[parentId].useSimplePrice == "1")
        {
            this.reloadSimplePrice(parentId, key);
        }
        else
        {
            // default behaviour
            this.reloadPrice();
        }
    }  
}

Product.Config.prototype.processEmpty = function()
{
    if ('undefined' == typeof(this.config)) return true;
    var me = this;
    $$('.super-attribute-select').each(function(select) {
        if (select.disabled)
        {
            var attributeId = select.id.replace(/[a-z]*/, '');
            if ($('amconf-images-' + attributeId + '-' + this.config.productId))
            {
                $('amconf-images-' + attributeId + '-' + this.config.productId).parentNode.removeChild($('amconf-images-' + attributeId + '-' + this.config.productId));
            }
            holder = select.parentNode;
            holderDiv = document.createElement('div');
            holderDiv.addClassName('amconf-images-container');
            holderDiv.id = 'amconf-images-' + attributeId + '-' + this.config.productId;
            if ('undefined' != typeof(confData[me.config.productId]))
            {
            	holderDiv.innerHTML = confData[me.config.productId].textNotAvailable;
            } else 
            {
            	holderDiv.innerHTML = "";
            }
            holder.insertBefore(holderDiv, select);
        }
    }.bind(this));
}

Product.Config.prototype.reloadPrice = function(){
        if (this.config.disablePriceReload) {
            return;
        }
        var price    = 0;
        var oldPrice = 0;
        for(var i=this.settings.length-1;i>=0;i--){
            var selected = this.settings[i].options[this.settings[i].selectedIndex];
            if(selected.config){
                price    += parseFloat(selected.config.price);
                oldPrice += parseFloat(selected.config.oldPrice);
            }
        }

        optionsPrice[this.config.productId].changePrice('config', {'price': price, 'oldPrice': oldPrice});
        optionsPrice[this.config.productId].reload();
        return price;
        if($('product-price-'+this.config.productId)){
            $('product-price-'+this.config.productId).innerHTML = price;
        }
        this.reloadOldPrice();
}


Event.observe(window, 'load', function(){
     imageObj = new Image();
     for ( keyVar in confData ) {
         if( parseInt(keyVar) > 0){
             for ( keyImg in confData[keyVar]['optionProducts'] ) {
                 imageObj.src = confData[keyVar]['optionProducts'][keyImg]['small_image'];
             }
         } 
     }
});