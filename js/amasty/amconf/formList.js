var optionsPrice = [];
var confData = [];

function createForm(url, product, keys)
{
    var form = document.createElement("form");
    form.action = url;
    form.id = 'product_addtocart_form' + '-' + product;
    form.enctype="multipart/form-data";
    form.method="post";
    
    var input = document.createElement("input");
    input.type = "hidden";
    input.name = 'product';
    input.value = product;
    form.appendChild(input);
    
    var input = document.createElement("input");
    input.type = "hidden";
    input.name = 'qty';
    input.value = 1;
    form.appendChild(input);
    
    var valid = true;
    var selectId = '';
    var isValid = 1;
    for ( keyVar in keys ) {
         if ($('attribute' + keys[keyVar] + '-' + product) && isValid){
            var input = document.createElement("input");
            input.type = "hidden";
            $('attribute' + keys[keyVar] + '-' + product).childElements().each(function(element) {
                if(element.selected){
                    if (parseInt(element.value) > 0){
                         input.value = element.value;
                    }
                    else{
                         valid = false;
                         selectId = 'attribute' + keys[keyVar] + '-' + product;
                         isValid = 0;
                    }   
                }
            });
            input.name = $('attribute' + keys[keyVar] + '-' + product).name;
            form.appendChild(input);
         }
    }
    var submit = document.createElement("input");
    submit.type = "submit";
    form.appendChild(submit);
    $('insert').appendChild(form);
    if(valid){
        form.addClassName('isValid');
    }
    else{
        var attr = document.createAttribute('selectId')
        attr.nodeValue =  selectId; 
        form.attributes.setNamedItem(attr);    
    }
    return form;    
}

function formValidation(form)
{
    selectId = form.getAttribute('selectId');
        if ('' != selectId){
            $(selectId).parentNode.style.border = '1px dashed red';
            $(selectId).parentNode.style.margin = '0';
            $$('.dashed-red').each(function(elem){
               elem.removeClassName('dashed-red');
               elem.style.border = '0';
            });
            $(selectId).parentNode.addClassName('dashed-red');
             if(amRequaredField)
                $('requared-' + selectId).innerHTML = amRequaredField;
            $$('.required-field').each(function(elem){
               elem.removeClassName('required-field');
               elem.innerHTML = '';
            });
            $('requared-' + selectId).addClassName('required-field');
        }    
}

function formSubmit(button,url,product,keys)
{
    var form = createForm(url, product, keys); 
    if (form.hasClassName('isValid')) {
            var e = null;
            try {
                form.submit();
                form.remove();
            } catch (e) {
            }
            if (e) {
                throw e;
            }
            if (button && button != undefined) {
                button.disabled = true;
            }
    }
    else{
        formValidation(form);    
    }
}