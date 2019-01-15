/*--------------------
* Bigtooth Client
* (c) Nigel Alderton
*/


/* =========================================================================
*
* Client-side Shopping Basket
*
* ==========================================================================
*
* Usage;
*
*   <button class="add-to-basket" data-item="x123">Add item x123</button>
*   <button class="remove-from-basket" data-item="x123">Remove item x123</button>
*
*   <p class="basket" data-basket-name="total">Fetching...</p>
*   <p class="basket">Fetching...</p>
*
*   <script src="/plugins/jquery/1.11.2/jquery.min.js"></script>
*   <script src="this_script.js"></script>
*
* JSON structure;
*  {
*    "x13": {
*      "qty": 5,
*      "item": {
*        "uri": "/2015-07-29-clsBasket/www/",
*        "img": "res/images/13.jpg"
*      }
*    }
*  }
*/

(function() {

  // Constants.
  var basketUrl = 'basket/';          // URL of the server-side component of the basket.
  var bubbletTimeout = 2000;          // How long a speech bubble lasts (milliseconds).
  
  
  /* -------------------------------------
  * Document ready.
  */  
  jQuery(document).ready(function(){
    
    // Bind basket buttons.
    jQuery('.add-to-basket').click(addToBasket);
    jQuery(document).on('click', '.remove-from-basket', removeFromBasket);    // This method also binds to ajaxed content.
    
    // Refresh all the baskets.
    //refreshBaskets();
    
  });
  
  
  /* -------------------------------------
  * Refresh all the baskets.
  *   NOT USED
  */  
  function refreshBaskets() {
    jQuery('.basket').each(function() {
      var basketName = jQuery(this).attr('data-basket-name') || false;     // Eg. false, 'total', 'test'.
      var query = (basketName) ? basketName : '';
      jQuery.ajax({url:basketUrl, data:query, context:this}).done(function(data){
        jQuery(this).html(data);
      });
    });
  }
  
  
  /* -------------------------------------
  * Add an item to the basket.
  */  
  function addToBasket() {
    var id = jQuery(this).attr('data-item');                        // Eg. 'x47'.
    var uri = window.location.pathname + window.location.search;    // Eg. '/2015-04-16-basket/?Hotpoint'.
    var img = jQuery(this).siblings('img').attr('src');             // Eg. 'res/images/47.jpg'.
    var item = {uri:uri};
    if (typeof img !== 'undefined')
      item['img'] = img;
    basket.addItem(id, item);
    bubble(this);
    //refreshBaskets();
  }
  
  
  /* -------------------------------------
  * Remove an item from the basket.
  */  
  function removeFromBasket() {
    var id = jQuery(this).attr('data-item');               // Eg. 'x47'.
    basket.removeItem(id);
    //refreshBaskets();
    window.location.reload(true);     // reload page (from server, not cache)
  }

  
  /* -------------------------------------
  * Bubble.
  */  
  function bubble(button) {
    var bubble = jQuery(button).attr('data-bubble');
    if (bubble) {
      jQuery(button).prepend('<div class="bubble">' + bubble + '</div>');
      var fn_remove = function() {
        jQuery(button).children().eq(0).remove();
      }
      setTimeout(fn_remove, bubbletTimeout);
    }
  }
  
  
  /* ======================================================
  *
  * Client-side Shopping Basket Object.
  *
  * ====================================================== */
  
  var basket = new function(){
  
    var cookieName = 'basket';      // Constant.
  
  
    /* -------------------------------------
    * Public methods.
    */  
    
    // Get the basket as an indented JSON string. Used for testing.
    this.get = function(){
      var obj = getBasket();
      return getCookie(cookieName)
        ? JSON.stringify(obj, null, 2)      // Prettyprint the JSON.
        : '';
    };
    
    // Kill the basket, ie. delete the basket cookie.
    this.kill = function(){
      unsetCookie(cookieName);
    };
    
    // Add an item to the basket.
    this.addItem = function(id, item){
      var items = getBasket();
      var qty = items.hasOwnProperty(id)
        ? 1 + items[id]['qty']
        : 1;
      items[id] = {'qty':qty, 'item':item};
      saveBasket(items);
    };
    
    // Remove an item from the basket.
    this.removeItem = function(id){
      var items = getBasket();
      if (items.hasOwnProperty(id)) {
        delete items[id];
        saveBasket(items);
      }
    }
    
    
    /* -------------------------------------
    * Basket functions.
    */  
    
    // Save the basket object to a cookie.
    function saveBasket(objBasket) {
      var str = JSON.stringify(objBasket);
      setCookie(cookieName, str);
    }
    
    // Get the basket object from a cookie.
    function getBasket() {
      var str = getCookie(cookieName);
      return str ? JSON.parse(str) : {};
    }
  
  
    /* -------------------------------------
    * Cookie functions.
    */  

    // Set a cookie.
    function setCookie(key, val) {
      document.cookie = key + '=' + encodeURIComponent(val) + ';path=/';
    }

    // Get a cookie.
    function getCookie(key) {
      var keyval = document.cookie.match('(^|;) ?' + key + '=([^;]*)(;|$)');
      return keyval ? decodeURIComponent(keyval[2]) : null;
    }

    // Unset a cookie.
    function unsetCookie(key) {
      document.cookie = key + '=;expires=Thu, 01 Jan 1970 00:00:00 UTC';
    }
    
  };

})();