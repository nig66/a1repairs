<?php

/***************************
* clsBasket
* =========
* The contents of the basket is maintained by the client. The server receives the basket in a cookie called 'basket'. The basket
* HTML is rendered on the server and returned to the client via an Ajax request.
*
* Data Structures
* ---------------
* Example basket cookie;
*   {"x13":{"qty":2,"item":{"uri":"/2015-07-29-clsBasket/www/","img":"res/images/13.jpg"}}}
*
* Equivalent array;
*   [x13] => Array
*     (
*       [qty] => 2
*       [item] => Array
*         (
*           [uri] => /2015-07-29-clsBasket/www/
*           [img] => res/images/13.jpg
*         )
*     )
*
* Equivalent XML;
*  <basket subtotal='13' postage='2.99' total='15.99'>
*    <item id='x13' qty='2', uri='/2015-07-29-clsBasket/www/', img='res/images/13.jpg' total='13'>
*      <title>Belt</title>
*      <description>Drum belt - 5 tooth</description>
*      <price>6.5</price>
*      <postage>2.99</postage>
*    </item>
*  </basket>
*
* Postage calc
* ------------
* Each item has a postage price eg. '1.99'. The price does NOT increase as the qty per item increases. The basket postage equals
* the highest item postage, eg. if the basket has two items with postage of 1.99 and 2.99 respectively, the basket postage is 2.99.
*/

class clsBasket {

  // Private const.
  private $cookie_name = 'basket';
  
  // Private vars.
  private $json;
  private $arr;
  private $xml;
  private $subtotal;
  private $html;
  
  
  // Constructor.
  public function __construct($cn) {
    $this->m_init($cn, $this->cookie_name);
  }

  // Get json.
  public function get_json() {
    return $this->json;
  }

  // Get json.
  public function get_arr() {
    return $this->arr;
  }
  
  // Get XML.
  public function get_xml() {
    return $this->xml;
  }

  // Get subtotal.
  public function get_subtotal() {
    return $this->subtotal;
  }

  // Get HTML.
  public function get_html($file_skin) {
    $html = mini_transform($this->xml, $file_skin);
    return $this->m_cleanup_basket($html);
  }

  // Get Paypal Checkout URL.
  public function get_paypal_checkout_url($file_paypal, $params) {
    $query_xml = mini_transform($this->xml, $file_paypal, $params);
    $sxe = new simpleXMLElement($query_xml);
    return 'https://www.paypal.com/cgi-bin/webscr?'.(string)$sxe;
  }
  
  
  
  /*********************************************************************************************
  *
  * Private.
  *
  *********************************************************************************************/
  
  // Cleanup the basket HTML by removing the XHTML doctype declaration.
  private function m_cleanup_basket($str_html) {
    $str = '<!DOCTYPE table PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">';
    return str_replace($str, '', $str_html);
  }
  
  
  // Constructor.
  private function m_init($cn, $cookie_name) {
  
    // The basket as a single line JSON string.
    $json = isset($_COOKIE[$cookie_name])
      ? $_COOKIE[$cookie_name]
      : '{}';
      
    // The basket as an array.
    $arr = json_decode($json, true);
    
    // The basket as XML.
    $xml = $this->basket_xml($cn, $arr);
    
    // Basket subtotal.
    $subtotal = call_user_func(function() use($xml) {
      $sxe = new SimpleXMLElement($xml);
      return (float)$sxe->attributes()->{'subtotal'};
    });
    
    // Set private vars.
    $this->json = $json;
    $this->arr = $arr;
    $this->xml = $xml;
    $this->subtotal = $subtotal;
  }

  
  // Get basket XML.
  private function basket_xml($cn, $arr) {

    // Items XML.
    $fn = function($id, $arr) use($cn){
      return $this->get_item_xml($cn, $id, $arr['qty'], $arr['item']);
    };
    $xml = array_map($fn, array_keys($arr), $arr);
    
    // Postage.
    $fn_postage = function($item){
      $sxe = new SimpleXMLElement($item);
      return (float)$sxe->{'postage'};
    };
    $postages = array_map($fn_postage, $xml);
    $postage = count($postages) ? max($postages) : 0;
    
    // Total.
    $fn_total = function($item){
      $sxe = new SimpleXMLElement($item);
      return (float)$sxe->attributes()->{'total'};
    };
    $totals = array_map($fn_total, $xml);
    $subtotal = count($totals) ? array_sum($totals) : 0;
    $total = $subtotal + $postage;
    
    // Basket XML.
    $str = $this->m_indent2(implode("\n", $xml));
    return count($arr)
      ? "<basket subtotal='{$subtotal}' postage='{$postage}' total='{$total}'>\n{$str}\n</basket>"
      : "<basket subtotal='0' postage='0' total='0'/>";
  }


  // Get item XML.
  private function get_item_xml($cn, $id, $qty, array $item) {
    $sql = "SELECT mpn, title, description, price, postage.cost AS postage
            FROM item
            JOIN postage ON item.postage_id = postage.id
            WHERE item.id = :id";
    $stmt = $cn->prepare($sql);
    $stmt->bindValue(":id", $id);
    $stmt->execute();
    $props = $stmt->fetchAll(PDO::FETCH_ASSOC)[0]; // FETCH_FUNC
    //echo '<xmp>'.print_r($props, true).'</xmp>';
    $xml = $this->get_props_xml($props);
    $img = isset($item['img']) ? " img='{$item['img']}'" : '';
    $price = (float)$props['price'];
    $total = $price * $qty;
    return "<item id='{$id}' qty='{$qty}' uri='{$item['uri']}'{$img} total='{$total}'>\n".$this->m_indent2($xml)."\n</item>";
  }


  // Get item props as XML.
  private function get_props_xml($props) {
    $title = htmlentities($props['title']);
    $description = htmlentities($props['description']);
    $arr = [
      "<mpn>{$props['mpn']}</mpn>",
      "<title>{$title}</title>",
      "<description>{$description}</description>",
      "<price>{$props['price']}</price>",
      "<postage>{$props['postage']}</postage>",
    ];
    return implode("\n", $arr);
  }


  // Indent each line of the supplied string by two spaces.
  private function m_indent2($str) {
     return '  '.implode("\n  ", explode("\n", $str));
  }

}

?>