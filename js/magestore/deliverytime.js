var Deliverytime = Class.create();
Deliverytime.prototype = {
    initialize: function(changeDeliveryTimeUrl){
        
        this.changeDeliveryTimeUrl = changeDeliveryTimeUrl;
       
    },

	changeDeliveryTime: function(){						
		var deliverytime;
		
		deliverytime = escape($('deliverytime').value);
		
	    url = this.changeDeliveryTimeUrl;
	   		
		var request = new Ajax.Request(
                url,
                {method: 'get', onFailure: "", parameters: {value:deliverytime}}
            );
		    
	}		
}