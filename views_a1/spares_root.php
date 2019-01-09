<?php

/**
* example scope
* -------------
* $images_dir = '../../sites/becs/www/res/images/'
* $appliances = ['Cooker Hood', 'Dishwasher']
* $items = [
*   'pageCount'   => 3,
*   'pageNumber'  => 2,
*   'dataset'     => [{}, {}, {}]
* ]
*/

?>

                  <div class="container container-fixed">
                  
                    <div id="c3389" class="col col col-sm-3 hidden-sm" data-req="">
                      <div class="module text">
                        <p class="smalltext"><b>Filter by appliance</b></p>
                      </div>
                      <ul class="appliances list-unstyled">
<?php foreach($appliances as $appliance): ?>
                        <li>
                          <a href="?<?= urlencode($appliance) ?>"><?= $appliance ?></a>
                        </li>
<?php endforeach ?>
                      </ul>
                    </div>
                    
                      

                    <div id="c3743" class="col col-md-9 col-lg-9 col-sm-12">
                      <a class="imageModuleWrap" id="w_m4339"> <img id="m4339" class="module image" src="/uploads/659x0_899x0/Captura.PNG" alt="White Washing Machine" data-req="" data-settings="enablehover=false,showelement=none,hovertransition=slowFadeIn"/> </a> 
                      <div class="module text">
                        <h1 class="headline">Online Spare Parts</h1>
                      </div>
                      <div class="module text">
                        <p class="bodytext">We sell spare parts for Dishwashers, Fridges, Freezers, Microwaves, Tumble Dryers, Vacuum Cleaners and many other appliances. Order spares online and pay by credit card or Paypal.</p>
                      </div>
                      <div class="module text">
                        <h2 class="subtitle">Order Parts by Phone</h2>
                      </div>
                      <div class="module text">
                        <p class="bodytext">If you can't see the item on our website, please phone us. We can source and supply thousands of parts. This website only lists a small selection of our catalog.</p>
                      </div>
                      <div id="m2240" class="module text hidden-md hidden-lg">
                        <p class="smalltext">Please call us on<br>Tel: <span style="font-weight: bold;"><a data-global="phone"  href="tel:01614395712" data-track-event="click" data-track-action="phone_link">0161 439 5712</a></span></p>
                      </div>
                    </div>


                    
                  </div>