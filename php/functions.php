<?php

/***************************************************************
*
* global functions
*
*   get_database_connection($database_file)   # create a connection to an SQLite database file and specify some default settings
*   render_view($file, $scope = [])           # open a php view file and return the output
*   explode_uri($uri, $delimiter = ';')       # parse a uri string and return an array of the url params
*   populate_basket($cn, $images_dir, $items) # populate a basket containing item ids with the item properties
*
***************************************************************/


/**
* create a connection to an SQLite database file and specify some default settings
*
* @param  string      filepath of the database file

* @return PDO object  PDO database connection object
*/
function get_database_connection($database_file)
{
  $cn = new PDO("sqlite:{$database_file}");
  $cn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Set error handling to exception mode so that a PDO error will raise a PHP exception.
  $cn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);         // Don't emulate prepared statements - use the driver's native functionality.
  $cn->exec('PRAGMA foreign_keys = ON;');                       // Enable foreign keys.
  
  return $cn;
}




/**
* open a php view file and return the output
*/
function render_view($file, $scope = [])
{
  extract($scope);                      # import variables from an array into the current symbol table
  ob_start();
  require $file;
  return ob_get_clean();
}




/**
* parse a uri string and return an array of the url params
*
*   if the uri is an empty string "", then return an empty array [].
*   if a param contains an equals sign eg. 'x=y', then the corresponding array key => value will be 'x' => 'y'.
*   if a param does not contain an equals sign eg. 'foo', then the corresponding array key will be numeric eg. [0] => 'foo'.
*
* @param  string      the uri
* @param  string      optional delimiter used to explode the uri. defaults to ';'
*
* @return array       the uri exploded into an array of zero or more params
*/
function explode_uri($uri, $delimiter = ';')
{
  $arr = [];
  
  $exploded = ('' === $uri)
    ? []
    : explode($delimiter, $uri);

  foreach($exploded as $part) {
    
    # does the param contain an equals sign?
    if (FALSE === strpos($part, '=')) {     
      $arr[] = $part;                       # autonumbered array key
    } else {
      $t = explode('=', $part, 2);
      $arr[$t[0]] = $t[1];                  # specify the array key
    }
  }
  
  return $arr;
}



/**
* populate a basket containing item ids with the item properties
*/
function populate_basket($cn, $images_dir, $items)
{
    $database_item = new Item($cn, $images_dir);
    $basket = [];
    $basket['items'] = [];
    $total = 0;
    
    foreach ($items as $id => $basket_item)
    {
      $json = $database_item->get($id);
      $basket['items'][] = [
        'id'          => $id,
        'mpn'         => $json['mpn'],
        'qty'         => $basket_item['qty'],
        'uri'         => $basket_item['item']['uri'],
        'each'        => $json['price'],
        'price'       => $json['price'] * $basket_item['qty'],
        'title'       => $json['title'],
        'description' => $json['description'],
        'image'       => file_exists("{$images_dir}{$id}.jpg") ? "{$images_dir}{$id}.jpg" : "/assets/no_picture.jpg",
      ];
      $total = $total + ($json['price'] * $basket_item['qty']);
    }
    
    $basket['total'] = $total;
    
    return $basket;
}

?>