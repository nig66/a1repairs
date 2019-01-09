<?php

/**
* example scope
* -------------
* $images_dir = '../../sites/becs/www/res/images/'
* $appliance   = 'Dishwasher'
* item_id     = 10,
* $item = [
* ]
*/

?>

                  <br/>
                  
                  <div class="single_item">
                    <div class="container container-fixed">


          <div class="col-md-3">
            <div class="images">

<?php foreach($item['images'] as $image): ?>
              <div class="image_wrapper">
                <a href="/assets/images/<?=$image?>">
                  <img class="img-thumbnail img-responsive" src="/assets/images/<?=$image?>"/>
                </a>
              </div>
<?php endforeach ?>
              
            </div>
          </div>
          <div class="col-md-6">
            <div class="well title_description">
              <div class="toggleMe">
                <h2><?=$item['title']?></h2>
                <p><?=$item['description']?></p>
              </div>
            </div>
            <div class="well mpn_partNos">
              <div class="toggleMe">
                <p>Part number <b><?=$item['mpn']?></b></p>
                <p>Other part number(s) <b><?=$item['partNos']?></b></p>
              </div>
            </div>
            <h3>This item fits the following models</h3>
            <nav>
<?php foreach($item['fits'] as $appliance => $brands): ?>
<?php foreach($brands as $brand => $models): ?>
              <div class="model_group panel panel-info">
                <div class="panel-heading">
                  <h2 class="panel-title">
                    <span><?=$brand?> <?=$appliance?> models</span>
                  </h2>
                </div>
                <div class="panel-body">
                  <div class="models">
<?php foreach($models as $model): ?>
                    <span title="<?=$model?>"><?=$model?></span>
<?php endforeach ?>
                  </div>
                </div>
              </div>
<?php endforeach ?>
<?php endforeach ?>
            </nav>
          </div>
          <div class="col-md-3">
            <div class="well price_panel">
              <h4>
                <b>Price including VAT</b>
              </h4>
              <p class="price toggleMe">
                <meta itemprop="priceCurrency" content="GBP" />
                <span itemprop="price" content="<?=$item['price']?>">£<?=number_format($item['price'], 2)?></span>
              </p>
              <link itemprop="availability" content="http://schema.org/InStock" />
              <button type="button" class="add-to-basket btn btn-warning" data-item="134" data-bubble="Added">Add to Basket</button>
            </div>
          </div>
                  
                    
                    </div>
                  </div>
<!--
<xmp><?php # json_encode($item, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) ?></xmp>
-->