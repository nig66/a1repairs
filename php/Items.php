<?php

/*************************************************************************
*
* clsItems
*
*   A dataset object.
*
*   Get a set of spare parts (items) as json objects.
*
*/

interface iItemSetRequest
{
  public function __construct($cn, $images_dir);    # SQLite database connection object, location of item images, optional perPage
  public function get();                            # optional params: $appliance = null, $brand = null
  public function setPerPage($perPage);             # set the number of items per page. defaults to 60 if not set
  public function setPageNumber($pageNumber);       # set the pagination page number. defaults to 1 if not set
  public function find($search);                    # string of tokens eg. 'hotpoint belt'
}



/**
* Usage;
*   $objItems = new clsItems($cn, $images_dir);
*   $json = $objItems->get();                               // fetch all
*   $json = $objItems->get('microwave');                    // filtered by appliance
*   $json = $objItems->get('microwave', 'creda');           // filtered by appliance, brand
*   $json = $objItems->find('hotpoint belt');               // search by one or more keywords
*
* Returns a set of item objects as a json string. Example item object;
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
*   "image": "3.jpg"
*  }
*
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





class Items implements iItemSetRequest {

  /**
  * sql statements
  */
  const SQL_FIND = "
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
    WHERE item.id IN
      (SELECT docid AS id FROM data WHERE data MATCH :tokens)
    ORDER BY
      item.lastChange DESC,
      item.id         ASC";            // ':tokens*'


  const SQL_ITEMS = "
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
    ORDER BY
      item.lastChange DESC,
      item.id         ASC";


  const SQL_ITEMS_APPLIANCE = "
    SELECT DISTINCT item.id         AS id,
                    mpn,
                    item.title      AS title,
                    description,
                    price,
                    partNos,
                    location_id,
                    location.title  AS location,
                    stock,
                    postage_id,
                    postage.cost    AS postage,
                    lastChange
    FROM item
    JOIN modelitem ON modelitem.item_id = item.id
    JOIN model ON model.id = modelitem.model_id
    JOIN location
      ON location_id = location.id
    JOIN postage
      ON item.postage_id = postage.id
    WHERE model.appliance_id =
      (SELECT appliance.id FROM appliance WHERE appliance.title = :appliance)
    ORDER BY
      item.lastChange DESC,
      item.id         ASC";

                                   
  const SQL_ITEMS_APPLIANCE_BRAND = "
    SELECT DISTINCT item.id         AS id,
                    mpn,
                    item.title      AS title,
                    description,
                    price,
                    partNos,
                    location_id,
                    location.title  AS location,
                    stock,
                    postage_id,
                    postage.cost    AS postage,
                    lastChange
    FROM item
    JOIN modelitem
      ON modelitem.item_id = item.id
    JOIN model
      ON model.id = modelitem.model_id
    JOIN location
      ON location_id = location.id
    JOIN postage
      ON item.postage_id = postage.id
    WHERE model.appliance_id =
      (SELECT appliance.id FROM appliance WHERE appliance.title = :appliance)
    AND model.brand_id =
      (SELECT brand.id FROM brand WHERE brand.title = :brand)
    ORDER BY
      item.lastChange DESC,
      item.id         ASC";

      
  
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

  
  /**
  * items per page
  *
  * @var  int           number of items per page for pagination
  */
  private $perPage;
  
  
  /**
  * current page number
  *
  * @var  int           pagination page number starting at 1
  */
  private $pageNumber = 1;

  
  
  
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
  public function __construct($cn, $images_dir, $perPage = 60)
  {
    $this->cn = $cn;
    $this->images_dir = $images_dir;
    $this->perPage = $perPage;
  }



  /**
  * set the pagination page number
  *
  * @param  int         pagination page number starting at 1
  *
  * @return void
  */
  public function setPageNumber($pageNumber)
  {
    $this->pageNumber = $pageNumber;
  }
  
  
  
  /**
  * set the number of items per page (for pagination)
  *
  * @param  int         number of items per page
  *
  * @return void
  */
  public function setPerPage($perPage)
  {
    $this->perPage = $perPage;
  }
  
  
  
  /**
  * Get a set of items
  *
  * @param  string      optional appliance eg. 'Microwave'
  * @param  string      optional brand eg. 'Creda'
  *
  * @return string      a set of data objects as a json string
  */
  public function get($appliance = null, $brand = null)
  {
    // items
    return (is_null($appliance))
      ? $this->getItemSet($this->cn, $this->images_dir, $this->perPage, $this->pageNumber, self::SQL_ITEMS)
      : ((is_null($brand))
        ? $this->getItemSet($this->cn, $this->images_dir, $this->perPage, $this->pageNumber, self::SQL_ITEMS_APPLIANCE,
                            [':appliance'=>$appliance])
        : $this->getItemSet($this->cn, $this->images_dir, $this->perPage, $this->pageNumber, self::SQL_ITEMS_APPLIANCE_BRAND,
                            [':appliance'=>$appliance, ':brand'=>$brand]));
  }


  
  /**
  * Get a set of items matching one or more tokens
  *
  * @param  string      one or more tokens eg. 'hotpoint belt'
  
  * @return string      a set of data objects as a json string
  */
  public function find($tokens)
  {
    // items
    return $this->getItemSet($this->cn, $this->images_dir, $this->perPage, $this->pageNumber, self::SQL_FIND, [':tokens'=>$tokens]);
  }
  
  
  
  
  /***************************************************************
  *
  * Private
  *
  ***************************************************************/
  
  
  /**
  * create a pagination structure eg.
  *   {
  *     "currentPage" : 2,
  *     "pageCount"   : 5,
  *     "pages"       : [1, 2, 3, 4, 5]
  *   }
  */
  private function createPagination()
  {
    
  }

  
  /**
  * Get a set of items
  *
  * @param  PDO object  database connection object
  * @param  string      directory containing the item images eg. '../sites/becs/www/res/images/'
  * @param  int         number of items per page for pagination
  * @param  int         page number (starting at 1) for pagination
  * @param  string      sql statement specifying the wanted items
  * @param  array       optional query params eg. [':tokens'=>'creda belt'] or [':appliance'=>'Microwave']
  *
  * @return string      a set of data objects as a json string
  */
  private function getItemSet($cn, $images_dir, $perPage, $pageNumber, $sql, $params = [])
  {
    // get the primary image filenames as an array, i.e. the first image for each item
    $primary_image_filenames = $this->getPrimaryImageFilenames($images_dir);

    // get items without image filename
    $items = $this->getItems($cn, $sql, $params);

    // calculate pageCount
    $pageCount = ceil(count($items) / $perPage);
      
    // paginate items
    $items = array_slice($items, $perPage * ($pageNumber - 1), $perPage);
    
    // add image filenames to items
    $items = $this->addFilenameToItems($items, $primary_image_filenames);

    return [
      'pageNumber'  => $pageNumber,
      'pageCount'   => $pageCount,
      'dataset'     => $items,
    ];
  }


  
  /**
  * Scan the images directory to get the filenames of the one primary image for each part (if present) eg;
  *   [10=>'10.jpg', 12=>'12.jpg', 13=>'13.jpg']
  */
  private function getPrimaryImageFilenames($images_dir)
  {
    
    $all_image_filenames = array_diff(scandir($images_dir, SCANDIR_SORT_NONE), ['.', '..']);   // scandir is much faster than glob.

    $primary_image_filenames = [];
    
    foreach($all_image_filenames as $filename) {
      $id = strstr($filename, '.', true);                 // id eg. 10.
      $ext = pathinfo($filename, PATHINFO_EXTENSION);     // extension eg. '.jpg'.
      $primary_image_filenames[$id] = "{$id}.{$ext}";     // primary image id=>filename eg. 10=>'10.jpg'.
    }
    
    ksort($primary_image_filenames);
    return $primary_image_filenames;
  }



  /**
  * Get items as an array.
  *
  * NB. PDO does not respect data types - all values are returned as strings by
  * default (yes really). So casting to the correct types must be done manually.
  */
  private function getItems($cn, $sql, $filter = [])
  {
    $stmt = $cn->prepare($sql);
    $stmt->execute($filter);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Correct type casting.
    foreach($items as &$item) {
      settype($item['id'], 'int');
      settype($item['price'], 'float');
      settype($item['location_id'], 'int');
      settype($item['stock'], 'int');
      settype($item['postage_id'], 'int');
      settype($item['postage'], 'float');
    }
    
    return $items;
  }



  /**
  * Add primary image filename (if found) to each item.
  */
  private function addFilenameToItems(array $items, array $primary_image_filenames)
  {
    
    foreach($items as &$item) {
      
      $id = $item['id'];
      
      if (array_key_exists($id, $primary_image_filenames))
        $item['image'] = $primary_image_filenames[$id];
    }
    
    return $items;
  }
  
}

?>