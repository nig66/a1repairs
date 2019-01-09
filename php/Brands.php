<?php

/*************************************************************************
*
* clsBrands
*
*   A dataset object.
*
*   Get a set of brands as a json array.
*
*/


interface iBrandSetRequest
{
  public function __construct($cn);
  public function get($appliance);
}



/*************************************************************************
*
* Usage;
*   $objBrands = new clsBrands($cn);
*   $json = $objBrands->get($appliance);
*
* Returns a set of brands as a json array, eg.;
*  ['Creda', 'Hotpoint']
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



class Brands implements iBrandSetRequest {


  /**
  * sql statements
  *     
  *   , count(*) OVER() AS full_count
  */
  const SQL_BRANDS = "
    SELECT DISTINCT brand.title
    FROM brand
    JOIN model
      ON model.brand_id = brand.id
    JOIN modelitem
      ON modelitem.model_id = model.id
    WHERE model.appliance_id =
      (SELECT id FROM appliance WHERE title = :appliance)
    ORDER by brand.title";

  
  
  /**
  * PDO connection
  *
  * @var  PDO object    PDO database connection object
  */
  private $cn;


  

  /***************************************************************
  *
  * Public
  *
  ***************************************************************/

  
  /**
  * constructor
  *
  * @param  PDO object  database connection object
  *
  * @return void
  */
  public function __construct($cn) {
    $this->cn = $cn;
  }


  
  /**
  * Get a set of brands
  *
  * @param  string      appliance name eg. 'Microwave'
  *
  * @return string      a set of brands as a json string
  */
  public function get($appliance) {

    // brands
    return $this->getSqlColumn($this->cn, self::SQL_BRANDS, [':appliance'=>$appliance]);

    // convert json object to a json string
    //return json_encode($brands, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES);
  }


  
  
  /***************************************************************
  *
  * Private
  *
  ***************************************************************/

  
  /**
  * Get a single column sql query
  *
  * @param  PDO object  database connection object
  * @param  string      sql statement
  * @param  array       optional query params eg. [':appliance'=>'Microwave', ':brand'=>'Creda']
  
  * @return array       result of the sql query as a simple numerically indexed array eg. [0=>'Creda', 1=>'Hotpoint'];
  */
  private function getSqlColumn($cn, $sql, $query_params = []) {
    
    $stmt = $cn->prepare($sql);
    $stmt->execute($query_params);
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
  }
  
}

?>