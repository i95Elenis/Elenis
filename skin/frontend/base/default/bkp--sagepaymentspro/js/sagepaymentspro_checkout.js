toggleNewCard = function (action) {

    var adminFrms = $$("#order-billing_method_form [name='payment[method]']");
    var frontFrms = $$("#" + payment.form + " [name='payment[method]']");
    var msFrms = $$("#multishipping-billing-form [name='payment[method]']");

    if (adminFrms.length) {
        var frms = adminFrms;
    } else if (msFrms.length) {
        var frms = msFrms;
    } else {
        var frms = frontFrms;
    }

    var method = null;
    if (frms.length) {
        frms.each(function (el) {
            if (el.checked) {
                method = el.value;
            }
        });
    }
    var frmSelector = 'div#payment_form_' + method;
    if (parseInt(action) == 2) {

        $$(frmSelector + ' li', frmSelector + ' ul').invoke('show');
        $$(frmSelector + ' ul li.tokencard-radio input').each(function (radiob) {
            //radiob.disabled = 'disabled';
            radiob.disabled = true;
        });

        if (adminFrms.length) {
            $$(frmSelector + ' ul.paymentsage select').each(function (sl) {
                sl.disabled = false;
            });
            $$(frmSelector + ' ul.paymentsage input').each(function (sl) {
                sl.disabled = false;
            });
        }

        $$(frmSelector + ' ul li.tokencard-radio', frmSelector + ' a.addnew').invoke('hide');

    } else {

        var tokenInputs = $$(frmSelector + ' ul li.tokencard-radio input');
        if (parseInt(tokenInputs.length) === 0) {
            return;
        }
        if (adminFrms.length) {
            $$(frmSelector + ' ul.paymentsage select').each(function (sl) {
                sl.disabled = true;
            });
            $$(frmSelector + ' ul.paymentsage input').each(function (sl) {
                sl.disabled = true;
            });
        }

        $$(frmSelector + ' ul.paymentsage li', frmSelector + ' ul.paymentsage').invoke('hide');
        tokenInputs.each(function (radiob) {
            radiob.disabled = false;
        });
        $$(frmSelector + ' ul li.tokencard-radio', frmSelector + ' a.addnew').invoke('show');

    }

}
tokenRadioCheck = function(radioID, cvv){
    try{
        $(radioID).checked = true;
    }catch(noex){}

    var adminFrms = $$("#order-billing_method_form [name='payment[method]']");
    if(adminFrms.length){
        var frmSelector = 'div#payment_form_sagepaymentspro';
        $$(frmSelector + ' ul.paymentsage select').each(function(sl){
            sl.disabled = true;
        });
        $$(frmSelector + ' ul.paymentsage input').each(function(sl){
            sl.disabled = true;
        });
        $$('input.tokencvv').each(function(sl){
            if(sl.id != cvv.id){
                sl.disabled = true;
            }
        });
    }
}

switchToken = function (radio) {
    $$('div.tokencvv').invoke('hide');
    $$('input.tokencvv').each(function (inp) {
        inp.disabled = 'disabled';
    })

    if ($('serversecure')) {
        $('serversecure').hide();
    }

    var divcont = radio.next('div');
    if ((typeof divcont) != 'undefined') {
        divcont.down().next('input').removeAttribute('disabled');
        divcont.show();
    }
}
removeCard = function(elem){

    var oncheckout = elem.hasClassName('oncheckout');

    new Ajax.Request(elem.href, {
        method: 'get',
        onSuccess: function(transport) {
            try{
                var rsp = transport.responseText.evalJSON();

                if(rsp.st != 'ok'){
                    //var ng = new k.Growler({location:"tc"});
                    //			ng.warn(rsp.text, {life:10});
                    alert(rsp.text);
                }else{
                    if(false === oncheckout){
                        elem.up().up().fade({
                            afterFinish:function(){
                                elem.up().up().remove();
                                updateEvenOdd();
                            }
                        });
                    }else{
                        elem.up().fade({
                            afterFinish:function(){
                                elem.up().remove();
                            }
                        });
                    }
                }

                if(!oncheckout){
                    $('sagepaymentsproTokenCardLoading').hide();
                }
            }catch(er){
                alert(er);
            }
        },
        onLoading: function(){
            if(!oncheckout){
                if($('iframeRegCard')){
                    $('iframeRegCard').remove();
                }else if($('frmRegCard')){
                    $('frmRegCard').remove();
                }
                $('sagepaymentsproTokenCardLoading').show();
            }

        }
    })

}
try{
    Event.observe(window,"load",function(){

        if(parseInt(SageConfig.getConfig('global','valid')) === 0){
                new PeriodicalExecuter(function(){
                    alert(SageConfig.getConfig('global','not_valid_message'));
                }, 10);
        }
    })
}catch(er){
    sageLogError(er);
}
