foreach ($_rates as $_rate):
                                    ?>
                                        <?php $shippingCodePrice[] = "'" . $_rate->getCode() . "':" . (float) $_rate->getPrice();
                                        $methodTitle=$this->escapeHtml($_rate->getMethodTitle());
                                        //$customShippingMethod[]=$methodTitle;
                                        //$hh=new AdjustWare_Deliverydate_Model_Holiday();
                                        //echo "ll".$hh->isHoliday($currentDate);
                                        // $holiday = Mage::getSingleton('adjdeliverydate/holiday');
                                        // $holiday->getConfigSettingAtProductLevel();
                                         //echo "hjgj".$holiday->getFirstAvailableDate();
                                         //echo "ll1=".$holiday->isDayoff($d,$m,$y);
                                         //echo "ll2=".$holiday->isHoliday($currentDate);
                                         //echo "<pre>";print_r($configSetting);
                                        // echo "<pre>123";print_r($configSetting);
                                         //if (strpos($methodTitle, $messengerConfig) !== false && $timeNow>=$timeFrom && $timeNow<=$timeTo && !$holiday->isDayoff($d,$m,$y) && !$holiday->isHoliday($currentDate) && in_array($configSetting,'Lead Time')){
                                         //if (strpos($methodTitle, $messengerConfig) !== false && $timeNow>=$timeFrom && $timeNow<=$timeTo && !$holiday->isDayoff($d,$m,$y) && !$holiday->isHoliday($currentDate) && in_array($configSetting,'Lead Time')){
                                         if (strpos($methodTitle, $messengerConfig) !== false &&  $timeNow>=$timeFrom && $timeNow<=$timeTo &&  !in_array('Lead Time',$configSetting,true) &&  !in_array('Next Available Date',$configSetting,true)){
                                             //echo "1".$methodTitle."=".$timeTo."-".$timeNow."=".$totalLeadTime;
                                             //echo "<pre>34";print_r($configSetting);
                                            $customShippingMethod[]=$methodTitle;
                                                 ?>
                                        <li>
                                            <?php if ($_rate->getErrorMessage()): ?>
                                                <ul class="messages"><li class="error-msg"><ul><li><?php echo $this->escapeHtml($_rate->getErrorMessage()) ?></li></ul></li></ul>
                                            <?php else: ?>
                                                <?php if ($_sole) : ?>
                                                    <span class="no-display"><input name="shipping_method" type="radio" value="<?php echo $_rate->getCode() ?>" id="s_method_<?php echo $_rate->getCode() ?>" checked="checked" /></span>
                                                <?php else:
                                                    

                                                    ?>
                                                    <?php 
                                                    

                                                   
                                                   //echo "<pre>";print_r($methodTitle);print_r($messengerConfig);
                                                   ?>
                                                    <input name="shipping_method" type="radio" value="<?php echo $_rate->getCode() ?>" id="s_method_<?php echo $_rate->getCode() ?>"<?php if ($_rate->getCode() === $this->getAddressShippingMethod()) echo 'checked="checked"' ?> class="radio" checked="checked"/>

                                                    <?php if ($_rate->getCode() === $this->getAddressShippingMethod()): ?>
                                                        <script type="text/javascript">
                                                            //<![CDATA[
                                                            lastPrice = <?php echo (float) $_rate->getPrice(); ?>;
                                                            //]]>
                                                        </script>


                                                    <?php endif; ?>

                                                <?php endif; ?>
                                                <label for="s_method_<?php echo $_rate->getCode() ?>"><?php echo $this->escapeHtml($_rate->getMethodTitle()) ?>
                                                    <?php $_excl = $this->getShippingPrice($_rate->getPrice(), $this->helper('tax')->displayShippingPriceIncludingTax()); ?>
                                                    <?php $_incl = $this->getShippingPrice($_rate->getPrice(), true); ?>
                                                    <?php echo $_excl; ?>
                                                    <?php if ($this->helper('tax')->displayShippingBothPrices() && $_incl != $_excl): ?>
                                                        (<?php echo $this->__('Incl. Tax'); ?> <?php echo $_incl; ?>)
                                                    <?php  endif; ?>
                                                </label>
                                            <?php   endif; ?>
                                            
                                        </li>
                                        <?php } 
                                        if(strpos($methodTitle, $messengerConfig) === false){
                                            $customShippingMethod[]=$methodTitle;
                                        
                                        //echo "2".$methodTitle."=".$timeTo."-".$timeNow."=".$totalLeadTime;?>
                                        <li>
                                            <?php if ($_rate->getErrorMessage()): ?>
                                                <ul class="messages"><li class="error-msg"><ul><li><?php echo $this->escapeHtml($_rate->getErrorMessage()) ?></li></ul></li></ul>
                                            <?php else: ?>
                                                <?php if ($_sole) : ?>
                                                    <span class="no-display"><input name="shipping_method" type="radio" value="<?php echo $_rate->getCode() ?>" id="s_method_<?php echo $_rate->getCode() ?>" checked="checked" /></span>
                                                <?php else:


                                                    ?>
                                                    <?php



                                                   //echo "<pre>";print_r($methodTitle);print_r($messengerConfig);
                                                   ?>
                                                    <input name="shipping_method" type="radio" value="<?php echo $_rate->getCode() ?>" id="s_method_<?php echo $_rate->getCode() ?>" class="radio"/>

                                                    <?php if ($_rate->getCode() === $this->getAddressShippingMethod()): ?>
                                                        <script type="text/javascript">
                                                            //<![CDATA[
                                                            lastPrice = <?php echo (float) $_rate->getPrice(); ?>;
                                                            //]]>
                                                        </script>


                                                    <?php endif; ?>

                                                <?php endif; ?>
                                                <label for="s_method_<?php echo $_rate->getCode() ?>"><?php echo $this->escapeHtml($_rate->getMethodTitle()) ?>
                                                    <?php $_excl = $this->getShippingPrice($_rate->getPrice(), $this->helper('tax')->displayShippingPriceIncludingTax()); ?>
                                                    <?php $_incl = $this->getShippingPrice($_rate->getPrice(), true); ?>
                                                    <?php echo $_excl; ?>
                                                    <?php if ($this->helper('tax')->displayShippingBothPrices() && $_incl != $_excl): ?>
                                                        (<?php echo $this->__('Incl. Tax'); ?> <?php echo $_incl; ?>)
                                                    <?php  endif; ?>
                                                </label>
                                            <?php   endif; ?>

                                        </li>
                                        <?php } ?>


                                    <?php  endforeach; ?>
                                </ul>
                               
                                                        <script type="text/javascript">
                                                            
                                                            
                            jQuery("div.deliverydateblock ul li:nth-child(2)" ).css('display','none');
                            
                           if(jQuery("div#checkout-shipping-method-load dl.sp-methods dd").length==2)
                               {
                                  //jQuery("div#checkout-shipping-method-load dl.sp-methods div#ship_options div#radio_choices ul li:nth-child(1) input[class='radio']").attr('checked','checked');
                                  jQuery("div.deliverydateblock ul li:nth-child(2)" ).css('display','none');
                               }
                           if(jQuery("div#checkout-shipping-method-load dl.sp-methods dd").length==1)
                               {
                                    jQuery("div#checkout-shipping-method-load dl.sp-methods dd ul li:nth-child(1) input[class='radio']").attr('checked','checked');
                                    jQuery("div.deliverydateblock ul li:nth-child(2)" ).css('display','block');
                               }

                            jQuery("li input[id^='s_method_ups_']").each(function() {
                                ////alert(this.id);
                               jQuery("#"+this.id).click(function() {
                                        jQuery("div.deliverydateblock ul li:nth-child(2)" ).css('display','none');
                                    });


                            });
                            totallength=jQuery("li input[id^='s_method_matrixrate_matrixrate_']").length;
                            jQuery("li input[id^='s_method_matrixrate_matrixrate_']").each(function(i) {
                                var count=i+1;
                                <?php /*$holiday = Mage::getSingleton('adjdeliverydate/holiday');  */?>
                                ////alert("<?php //echo $holiday->getFirstAvailableDate();?> ");
                               // //alert(this.id);
                                ////alert(jQuery("li label[for^="+this.id+"]").text());
                                //jQuery("div.deliverydateblock ul li:nth-child(2)" ).css('display','none');
                                if(jQuery("li label[for^="+this.id+"]").text().substring(0, 17) == "Same Day Delivery" && totallength>=3)
                                    {
                                        jQuery("div#checkout-shipping-method-load dl.sp-methods dd ul li:nth-child("+count+") input[class='radio']").attr('checked','checked');
                                        jQuery("div.deliverydateblock ul li:nth-child(2)" ).css('display','none');
                                    }
                                  if(jQuery("li label[for^="+this.id+"]").text().substring(0, 17) == "Next Day Delivery" && totallength==2)
                                      {
                                         jQuery("div#checkout-shipping-method-load dl.sp-methods dd ul li:nth-child(1) input[class='radio']").attr('checked','checked');
                                         jQuery("div.deliverydateblock ul li:nth-child(2)" ).css('display','block');
                                      }
                                ////alert("div#checkout-shipping-method-load dl.sp-methods dd ul li:nth-child("+count+") input[id='"+this.id+"']");
                                 ////alert("kk");
                                // //alert(jQuery("div#checkout-shipping-method-load dl.sp-methods dd ul li:nth-child("+count+") label").text());
                                /* if (jQuery("div#checkout-shipping-method-load dl.sp-methods dd ul li:nth-child("+count+") label").text().substring(0, 17) == "Same Day Delivery") {
                                  // $('#targetButton').hide();
                                  jQuery("div#checkout-shipping-method-load dl.sp-methods dd ul li:nth-child("+count+") input[class='radio']").attr('checked','checked');
                                }*/
                                if(count==1)
                                {
                                                                 //jQuery("div#checkout-shipping-method-load dl.sp-methods dd ul li:nth-child("+count+") input[class='radio']").attr('checked','checked');
                                     jQuery("div.deliverydateblock ul li:nth-child(2)" ).css('display','block');


                                    jQuery("div#checkout-shipping-method-load dl.sp-methods dd ul li:nth-child("+count+") input[id='"+this.id+"']").click(function() {
                                        jQuery("div.deliverydateblock ul li:nth-child(2)" ).css('display','block');
                                    });

                                } else
                                {
                                    jQuery("div#checkout-shipping-method-load dl.sp-methods dd ul li:nth-child("+count+") input[id='"+this.id+"']").click(function() {
                                        jQuery("div.deliverydateblock ul li:nth-child(2)" ).css('display','none');
                                    });
                                }

                            });
                            jQuery.noConflict();
    jQuery( "li input[id^='s_method_matrixrate_matrixrate_']").change(function() {


                                if(jQuery("li label[for^="+this.id+"]").text().substring(0, 17) == "Same Day Delivery")
                                    {
                                         jQuery("#deliverydate-please-wait").show();
                                         jQuery.ajax({
                        type: 'GET',
                       // dataType: 'json',
                        url: '<?php echo $this->getUrl("adjdeliverydate/ajax/datevalidate", array("_secure" => true)) ?>',
                        data: 'days='+"<?php echo Mage::getStoreConfig('elenissec/elenisgrp/messenger_sameday_delivery'); ?>",

                        complete: function(data) {
                        //console.log(data.responseText);
                          //$j.each( data.responseText.responseText, function(  val ) {
                       //  //alert(val);
                        // });
                        var date=data.responseText;
                         ////alert(date);

                         jQuery("#deliverydate-please-wait").hide();
                          jQuery("#delivery_date").val(date);
                        }
                    });
                                    }
                                  if(jQuery("li label[for^="+this.id+"]").text().substring(0, 17) == "Next Day Delivery")
                                      {
                                      jQuery("#deliverydate-please-wait").show();
                                          jQuery.ajax({
                        type: 'GET',
                        //dataType: 'json',
                        url: '<?php echo $this->getUrl("adjdeliverydate/ajax/datevalidate", array("_secure" => true)) ?>',
                        data: 'days='+"<?php echo Mage::getStoreConfig('elenissec/elenisgrp/messenger_nextday_delivery'); ?>",

                        complete: function(data) {

                            var date=data.responseText;
                           // //alert(date);
                      jQuery("#deliverydate-please-wait").hide();
                       jQuery("#delivery_date").val(date);


                        }
                    });
                                      }

});
                           
                    </script>
                                 <?php Mage::getSingleton('core/session')->setcustomShippingMethod($customShippingMethod); ?>