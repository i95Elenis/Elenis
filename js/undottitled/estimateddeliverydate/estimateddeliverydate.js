var host = window.location.protocol+"//"+window.location.host+"/";
document.observe('dom:loaded',function() {
	$$('.product-options dd.last').each(function(el) {
		var finalAttribute = $(el).children[0].children[0];
		$(finalAttribute.id).observe('change',function(event) {
			var ele = Event.element(event);
			updateDeliveryDate(ele);
		});
	});   
	
	$('qty').observe('keyup',function() {
		if($('product-options-wrapper')) {
			$$('.product-options dd.last').each(function(el) {
				var finalAttribute = $(el).children[0].children[0];
	
				var ele = $(finalAttribute.id);
				updateDeliveryDate(ele);
			});
		} else {
			updateDeliveryDate_Simple(document.getElementsByName('product')[0].value);q	
		}	
		   
	});
});


function updateDeliveryDate(ele) {
	var eleTrimmedId = $(ele).id.replace('attribute','');
	
	if(!isNaN(parseInt(eleTrimmedId))) { //Check for anything other attribute dropdowns causing errors
		var jsonConfig = spConfig.config.attributes[eleTrimmedId].options;
		jsonConfig.each(function(obj) {
			if(obj.id == ele.value) {
				new Ajax.Request(host+'estimateddeliverydate/index/getdeliveries/pid/'+obj.allowedProducts[0]+'/qty/'+$('qty').value, {
					onSuccess: function(response) {
					 $('estimatedDeliveryText').update(response.responseText);
					}
				});	
			} else {
				$('estimatedDeliveryText').update('Please select options to check delivery');
			}
		});
	}
}

function updateDeliveryDate_Simple(pid) {
	new Ajax.Request(host+'estimateddeliverydate/index/getdeliveries/pid/'+pid+'/qty/'+$('qty').value, {
		onSuccess: function(response) {
		 $('estimatedDeliveryText').update(response.responseText);
		}
	});	
}