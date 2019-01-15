<?php

/**
* example scope
* -------------
* $images_dir = '../../sites/becs/www/res/images/';
* $basket = [];
* $total = 15;
* $postage = 5;
* $basket_total = 20;
*/


?>
                  <div class="container container-fixed">
                  

                    <div id="c3743" class="col col-md-12 col-lg-12 col-sm-12">

<div class="basket">
  <table class="table table-striped">
    <tr>
      <th></th>
      <th>Part number</th>
      <th>Details</th>
      <th class="col_price">Each</th>
      <th class="col_qty">Qty</th>
      <th class="col_subtotal">Subtotal</th>
      <th class="col_cmd"></th>
    </tr>
    
    
  <?php foreach($basket as $item): ?>
      <tr>
        <td>
          <a href="<?=$item['uri']?>" class="img_wrap"><img style="max-width:4em" class="img-rounded  img-responsive" src="<?=$item['image']?>"/></a>
          <!--
          <xsl:if test="@img">
          </xsl:if>
          -->
        </td>
        <td><a href="<?=$item['uri']?>"><?=$item['mpn']?></a></td>
        <td><b><?=$item['title']?></b><br/><?=$item['description']?></td>
        <td class="col_price">£<?=number_format($item['each'], 2)?></td>
        <td class="col_qty"><?=$item['qty']?></td>
        <td class="col_subtotal">£<?=number_format($item['price'], 2)?></td>
        <td class="col_cmd">
          <button class="remove-from-basket basket_action btn btn-warning btn-sm" data-item="<?=$item['id']?>" title="Remove from basket">x</button>
        </td>
      </tr>
  <?php endforeach ?>
    
    
    <tr>
      <td colspan="5" class="col_price total">Total</td>
      <td class="col_price total">£<?=number_format($total, 2)?></td>
      <td></td>
    </tr>
    <tr>
      <td colspan="5" class="col_price">Postage (UK mainland only)</td>
      <td class="col_price">£<?=number_format($postage, 2)?></td>
      <td></td>
    </tr>
    <tr>
      <td colspan="5" class="col_price total">Order total</td>
      <td class="col_price total">£<?=number_format($basket_total, 2)?></td>
      <td></td>
    </tr>
  </table>
  <div>
    <!--
    <img class="basket_action" data-cmd="paypal_checkout" src="https://www.paypal.com/en_US/i/btn/btn_xpressCheckout.gif"/>
    -->
    <form class='easybasket_paypal_button' method="POST" action="/basket/">
      <input type="hidden" name="cmd" value="paypal_checkout"/>
      <input
        type="image" name="submit" alt="PayPal Checkout"
        src="https://www.paypal.com/en_US/i/btn/btn_xpressCheckout.gif"
        title="MasterCard / Eurocard, Visa / Delta / Electron, American Express, Switch / Maestro, Solo."
      />
    </form>
  </div>
</div>
                    </div>

                    
                  </div>

<?php
                  
#echo "<xmp>".print_r($basket, true)."</xmp>";

?>                  
