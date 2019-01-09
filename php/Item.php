<?php

/*************************************************************************
*
* clsItem
*
*   A data object.
*
*   Get a single spare part as a json object.
*
*/

interface iItemRequest
{
  public function __construct($cn, $images_dir);
  public function get($id);
  public function getByMpn($mpn);
}



/**
* Usage;
*   $item = new Item($cn, $images_dir);
*   $json = $item->get(10);                             // fetch item id:10
*
* Returns a json object, eg.;
*  {
*   "id": 3,
*   "mpn": "C00201014",
*   "title": "Door Latch",
*   "description": "67mm door latch",
*   "price": 8.99,
*   "partNos": 1603063,
*   "location_id": 2,
*   "stock": 4,
*   "postage_id": 2,
*   "lastChange": "2014-08-14 14:32:52",
*   "images": [
*       "3.jpg",
*       "3.1.jpg"
*   ],
*   "fits": {
*       "Cooker/Oven": {
*           "Ariston": [
*               "I/LS12UK"
*           ],
*           "New World": [
*               "CKG21074BR",
*               "CKG21075WH"
*           ],
*       "Twin Tub": {
*           "Hotpoint": [
*               "1460",
*               "1469"
*           ]
*        }
*      }
*    }
*  }
*************************************************************************/


/**
* Database Tables
* ===============
* item
*   [id, mpn, title, description, price, partNos, location_id, stock, postage_id, lastChange]
* model
*   [id, title, appliance_id, brand_id]
* modelitem
*   [id, model_id, item_id]
* appliance
*   [id, title]
* brand
*   [id, title]
* location
*   [id, title]
* postage
*   [id, cost]
*/





class Item implements iItemRequest {

  /**
  * sql statements
  */
  const SQL_ITEM_BY_ID = "
  
          SELECT item.id          AS id,
                 mpn,
                 item.title       AS title,
                 description,
                 price,
                 partNos,
                 location_id,
                 location.title   AS location,
                 stock,
                 postage_id,
                 postage.cost     AS postage,
                 lastChange
          FROM item
          LEFT JOIN location 
            ON location_id = location.id
          LEFT JOIN postage
            ON item.postage_id = postage.id
          WHERE item.id = :id";

      
  const SQL_ITEM_BY_MPM = "
  
          SELECT item.id          AS id,
                 mpn,
                 item.title       AS title,
                 description,
                 price,
                 partNos,
                 location_id,
                 location.title   AS location,
                 stock,
                 postage_id,
                 postage.cost     AS postage,
                 lastChange
          FROM item
          LEFT JOIN location 
            ON location_id = location.id
          LEFT JOIN postage
            ON item.postage_id = postage.id
          WHERE item.mpn = :mpn";


  const MODELS_SQL = "
  
          SELECT appliance.title AS appliance,
                 brand.title AS brand,
                 model.title AS model
          FROM modelitem
          JOIN model ON model.id = modelitem.model_id
          JOIN appliance ON appliance.id = model.appliance_id
          JOIN brand ON brand.id = model.brand_id
          WHERE modelitem.item_id = :id
            AND appliance != ''
            AND brand != ''
            AND model != ''
          ORDER BY appliance.title, brand.title, model.title
          COLLATE NOCASE";
          
          
  /**
  * PDO connection
  *
  * @var  PDO object    PDO database connection object
  */
  private $cn;


  /**
  * images directory
  *
  * @var  string        the directory containing the item images eg. '../sites/becs/www/res/images/'
  */
  private $images_dir;

  
  

  /***************************************************************
  *
  * Public
  *
  ***************************************************************/

  
  /**
  * constructor
  *
  * @param  PDO object  database connection object
  * @param  string      directory containing the item images eg. '../sites/becs/www/res/images/'
  *
  * @return void
  */
  public function __construct($cn, $images_dir) {
    $this->cn = $cn;
    $this->images_dir = $images_dir;
  }


  
  /**
  * Get an item by id
  *
  * @param  string      item id
  *
  * @return json        a json data object
  */
  public function get($id) {

    return $this->getItem($this->cn, $this->images_dir, self::MODELS_SQL, self::SQL_ITEM_BY_ID, [':id'=>$id]);
    
  }


  
  /**
  * Get an item by mpn
  *
  * @param  string      item mpn
  *
  * @return json        a json data object
  */
  public function getByMpn($mpn) {

    return $this->getItem($this->cn, $this->images_dir, self::MODELS_SQL, self::SQL_ITEM_BY_MPM, [':mpn'=>$mpn]);
    
  }
  
  
  
  
  
  /***************************************************************
  *
  * Private
  *
  ***************************************************************/

  
  /**
  * Get an item
  *
  * @param  PDO object  database connection object
  * @param  string      directory containing the item images eg. '../sites/becs/www/res/images/'
  * @param  string      sql statement to get the item fits data
  * @param  string      sql statement to get the item properties
  * @param  array       query params eg. [':id'=>'21'] or [':mpn'=>'C00213002']
  *
  * @return json        a json data object
  */
  private function getItem($cn, $images_dir, $models_sql, $props_sql, $params)
  {
    $item =  $this->getProps($cn, $props_sql, $params);
    $images = $this->getImages($images_dir, $item['id']);
    $fits = $this->getFits($cn, $models_sql, $item['id']);
    
    if (!empty($images))
      $item['images'] = $images;
    
    if (!empty($fits))
      $item['fits'] = $fits;
    
    return $item;
  }

  
  /**************************
  * get item fits
  */
  private function getFits($cn, $models_sql, $id)
  {
    $stmt = $cn->prepare($models_sql);
    $stmt->execute([':id' => $id]);
    $arr_models = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $ret = [];
    
    foreach($arr_models as $row) {
      $appliance = $row['appliance'];
      $brand = $row['brand'];
      $ret[$appliance][$brand][] = $row['model'];
    }
    
    return $ret;
  }
  
  
  /**
  * get item properties
  *
  * Note: PDO does not respect data types - all values are returned as strings by
  * default (yes really). So casting to the correct types must be done manually.
  */
  private function getProps($cn, $sql, $params)
  {
    // get item properties
    $stmt = $cn->prepare($sql);
    $stmt->execute($params);
    $item = $stmt->fetchAll(PDO::FETCH_ASSOC)[0];
    
    // set the correct property types
    settype($item['id'], 'int');
    settype($item['price'], 'float');
    settype($item['location_id'], 'int');
    settype($item['stock'], 'int');
    settype($item['postage_id'], 'int');
    settype($item['postage'], 'float');
    
    return $item;
  }
  
  
  /**
  * Get image filenames, with the primary image (ie. '*.jpg') first
  *
  * @param  string      directory containing the item images eg. '../sites/becs/www/res/images/'
  * @param  string      item id
  *
  * @return array       an array of zero or more image filesnames eg. ['5.jpg', '5.1.jpg']
  */
  private function getImages($images_dir, $id)
  {
    // secondary images eg. '12.1.jpg', '12.2.jpg'
    $images = array_map('basename', glob("{$images_dir}/{$id}.*.jpg"));    // Match "{$id}.*.jpg"
    natsort($images);
    
    // primary image eg. '12.jpg'
    $image = glob("{$images_dir}/{$id}.jpg");                              // Match "{$id}.jpg"
    if (count($image))
      array_unshift($images, basename($image[0]));      // Prepend "{$id}.jpg" to the start of the array of images.
    
    return $images;
  }
  
}

?>