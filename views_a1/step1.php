<?php

/**
* example scope
* -------------
* $images_dir = '../../sites/becs/www/res/images/'
* $appliances = ['Cooker Hood', 'Dishwasher']
* $apliance   = 'Dishwasher'
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
                        <p class="smalltext"><b>Choose appliance</b></p>
                      </div>
                    
                      <ul class="appliances list-unstyled">
<?php foreach($appliances as $title): ?>
                        <li>
                          <a href="?<?= urlencode($title) ?>"><?= $title ?></a>
                        </li>
<?php endforeach ?>
                      </ul>
                    </div>
                    
                    <div id="c3743" class="col col-md-9 col-lg-9 col-sm-12">
                      <div class="module text">
                        <h1 class="headline"><?=$appliance?> spare parts</h1>
                      </div>
                      <ul class="items">
<?php foreach($items['dataset'] as $item): ?>
                        <li>
                          <a class="img_wrap" href="?<?=urlencode($appliance)?>/<?=$item['id']?>">
                            <img src="<?=isset($item['image'])?"/assets/images/{$item['image']}":'../assets/no_picture.jpg'?>"/>
                          </a>
                          <h4>
                            <a href="?<?=urlencode($appliance)?>/<?=$item['id']?>"><?=$item['title']?></a>
                          </h4>
                          <div>
                            <b>£<?=number_format($item['price'], 2)?></b>
                          </div>
                        </li>
<?php endforeach ?>
                      </ul>
                      <ul class="pagination">
<?php foreach($items['pageCount'] == 1 ? [] : range(1, $items['pageCount']) as $pageNumber): ?>
                        <li<?php if ($items['pageNumber'] == $pageNumber) echo ' class="active"';?>>
                          <a href='./?<?= urlencode($appliance).($pageNumber > 1 ? ";p={$pageNumber}" : '') ?>'><?=$pageNumber?></a>
                        </li>
<?php endforeach ?>
                      </ul>
                      
                      <div id="m2240" class="module text hidden-md hidden-lg">
                        <p class="smalltext">Please call us on<br>Tel: <span style="font-weight: bold;"><a data-global="phone"  href="tel:01614395712" data-track-event="click" data-track-action="phone_link">0161 439 5712</a></span></p>
                      </div>
                      
                    </div>
                    
                  </div>