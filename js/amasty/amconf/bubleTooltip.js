function findTopLeft(obj)
{
    var curleft = curtop = 0;
    if (obj.offsetParent) {
        curleft = obj.offsetLeft
        curtop = obj.offsetTop
        while (obj = obj.offsetParent) {
            curleft += obj.offsetLeft
            curtop += obj.offsetTop
        }
    }
    return [curleft,curtop];
}

//class for onmouseover showing option name
Buble = Class.create();
Buble.prototype = 
{    
    isCreated : false,
    
    bubleTooltip : null,
    
    text : null, 
    
     initialize : function()
    {
        var me = this;    
    },  
        
    showToolTip : function(event)
    {
         if( !this.isCreated ){
            var element = Event.element(event);
            var bubleTooltip = $('bubble');
            var bubleMiddle = $('buble_middle');
            var parent  =  element.parentNode;
            parent.appendChild(bubleTooltip);
            this.text = element.alt; 
                   
			bubleTooltip.style.opacity = 0;
            new Effect.Opacity('bubble', { from: 0, to: 1, duration: 0.2 });
			
            bubleMiddle.innerHTML = this.text;
            bubleTooltip.style.display = 'block'; 
            var offset = findTopLeft(element);
            bubleTooltip.style.left =  10 - bubleTooltip.getWidth() + "px";
            bubleTooltip.style.top =   - bubleTooltip.getHeight() + 5 + 'px';

            this.isCreated = true;
            this.bubleTooltip = bubleTooltip;
            if(!this.text){
				$('bubble').hide();
                this.isCreated = false;    
            }
        }
    },
    
    hideToolTip : function()
    {
        if(this.isCreated){
	    $('bubble').hide();
	    $$('body')[0].appendChild($('bubble'));
            this.isCreated = false;   
        }
    }
}
 var buble = new Buble();
 