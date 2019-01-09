<?php

/*************************************************************************
*
* clsAppliances
*
*   A dataset object.
*
*   Get a set of appliances as a json array.
*
*/


interface iApplianceSetRequest
{
  public function __construct($cn);
  public function get();
}



/*************************************************************************
*
* Usage;
*   $objItems = new clsAppliances($cn);
*   $json = $objItems->get();
*
* Returns a set of item objects as a json array, eg.;
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



class Appliances implements iApplianceSetRequest {


  /**
  * sql statements
  */
  const SQL_APPLIANCES = "
    SELECT DISTINCT appliance.title
    FROM appliance
    JOIN model
      ON model.appliance_id = appliance.id
    JOIN modelitem
      ON modelitem.model_id = model.id
    JOIN item
      ON item.id = modelitem.item_id
    WHERE item.stock > 0
    ORDER by appliance.title";

 

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
  * Get a set of appliances
  *
  * @return string      a set of data objects as a json string
  */
  public function get() {

    // appliances
    return $this->getSqlColumn($this->cn, self::SQL_APPLIANCES);

    // convert json object to a json string
    //return json_encode($appliances, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES);
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