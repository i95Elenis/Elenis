if (typeof (ob) == 'undefined') var ob = {};

ob.minqty = {
  init: function(e, qty){
    jQuery("#qty-"+e).change(function(){
      if(parseInt(this.value) < parseInt(qty)){
        jQuery("#btnadd-"+e).attr("disabled", "disabled");
        jQuery("#btnadd-"+e).addClass('disabled');
        jQuery("#qty-"+e).addClass('error');
        alert('You have to buy at least '+qty);
      } else {
        jQuery("#btnadd-"+e).removeAttr("disabled");
        jQuery("#btnadd-"+e).removeClass('disabled');
        jQuery("#qty-"+e).removeClass('error');
      }
    });
  },
  
  initCartupdate: function(e, qty, productName){
    jQuery("#updqty-"+e).change(function(){
      if(parseInt(this.value) < parseInt(qty)){
        jQuery('button.button.btn-update').attr('disabled', 'disabled');
        jQuery('button.button.btn-update').addClass('disabled');
        jQuery("#updqty-"+e).addClass('error');
        alert('You have to buy at least '+qty+' of '+productName);
      } else {
        jQuery('button.button.btn-update').removeAttr("disabled");
        jQuery('button.button.btn-update').removeClass('disabled');
        jQuery("#updqty-"+e).removeClass('error');
      }
    });
  }
}