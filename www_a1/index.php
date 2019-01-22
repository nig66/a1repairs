<?php

/**
* A1 Repairs - root pages.
*/


/***************************************************************
*
* Root webpage entry point
*
***************************************************************/

require '../php/Router.php';
require '../php/functions.php';


/**
* global constants
*/
$query = explode_uri($_SERVER['QUERY_STRING']);


/**
* assemble the needed parts of the HTTP request
*/
$request = [
  'method'  => $_SERVER['REQUEST_METHOD'],
  'path'    => (isset($query[0]) ? $query[0] : ''),
];


/**
* create the route handlers
*/
$routes = [];


# Home
$routes['GET']['/'] = function($request) {
  return render_view('../views_a1/home.php', [
    'title'  => 'A1 Repairs - Repairs of Cookers - Washers - Dryers - Ovens - Dishwashers',
  ]);
};

# Appliance Repair Service
$routes['GET']['/appliance-repair-service'] = function($request) {
  return render_view('../views_a1/repairs.php', [
    'title'  => 'Domestic Appliance Repairs in Stockport | A1 Repairs',
  ]);
};

# Contact Us
$routes['GET']['/contact-us'] = function($request) {
  return render_view('../views_a1/contact_us.php', [
    'title'  => 'Contact Us | A1 Repairs',
  ]);
};


/**
* bodge active tab 
*/
$active = [
  'home' => $request['path'] == '' ? 'active' : '',
  'repair' => $request['path'] == 'appliance-repair-service' ? 'active' : '',
  'contact' => $request['path'] == 'contact-us' ? 'active' : '',
];


/**
* execute the appropriate handler
*/

$router = new Router($routes);                          # create a router
$response = $router->getResponse($request);             # execute the matching handler and get the response


?>
<!DOCTYPE html>
<html lang="en" data-currency="EUR" data-countrycode="en_US" data-rHash="6920626369b1f05844f5e3d6f93b5f6e" dir="auto">
  <head>
    <meta charset="UTF-8"/>
    <title>A1 Repairs - Repairs of Cookers - Washers - Dryers - Ovens - Dishwashers</title>
    <meta name="keywords" content="Cooker Repairs,electric oven repairs,dishwasher repairs,Tumble Dryer repairs,washing machine repairs,washer dryer repairs,electrical domestic appliance repairs,appliance repairs,Manchester">
    <meta name="description" content="Domestic Appliance repairs in Greater Manchester. Call today to find out more. Repairs of Cookers Ovens Washing Machines Tumble Dryers Dishwashers">
    <link rel='canonical' href='http://www.a1repairs-stockport.co.uk/'/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel='icon' type='image/x-icon' href='/assets/css/img/favicon.ico'/>
    <link rel="stylesheet" href="../assets/css/bootstrap_maybe.css"/>
    <link rel="stylesheet" href="../assets/css/pagination.css"/>
    <!--
    <link rel="stylesheet" href="/plugins/bootstrap/3.2.0/css/bootstrap.min.css" />
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css"/>
    -->
    <!--[if IE 8]>
    <link rel="stylesheet" href="/assets/css/grids.css?#RELEASETS#">
    <![endif]-->
    <link rel="stylesheet" href="../assets/css/style_site.css"/>
    <!--[if lt IE 9]><script src="/assets/js/html5shiv.min.js"></script><script src="/assets/js/respond.min.js"></script><![endif]-->
    <style id='globalCSS'> /* Mono Cookie Bar - Fix to Bottom*/ #infobar.infobar-wrapper{position:fixed;bottom:0;top:inherit;padding-top:5px;padding-bottom:5px}.infobar-wrapper a#infobar-donottrack{margin-top:0;color:#fff;padding:5px 15px;font-size:12px;background:#666;-webkit-border-radius:3px;-moz-border-radius:3px;border-radius:3px;transition:opacity ease .3s}.infobar-wrapper a#infobar-donottrack:hover{opacity:.8}.infobar-wrapper .infobar-close{display:none}div#infobar p{display:inline-block;line-height:1em;text-align:center;vertical-align:middle}div#infobar p:nth-child(1){width:75%}div#infobar p:nth-child(2){width:25%}@media only screen and (max-width:767px){#infobar.infobar-wrapper{font-size:10px}} /*END Mono Cookie Bar - Fix to Bottom*/ </style>
    <link rel="stylesheet" href="../assets/css/spares.css" />
  </head>
  <body id="p3754" data-dateformat='d/m/Y'>
    <div id="r5001" class="row designRow">
      <div class="container container-fixed">
        <div class="col col-sm-12">
          <header>
            <div class="row row hidden-sm">
              <div class="container container-fixed">
                <div id="c3127" class="col col-sm-6" data-req=""><a class="imageModuleWrap" id="w_m2174"> <img id="m2174" class="module image" src="/uploads/384x0_600x0/header-object.png" alt="" data-req="" data-settings="enablehover=false,showelement=none,hovertransition=slowFadeIn"/> </a> </div>
                <div class="col col-sm-6">
                  <div class="module text">
                    <p class="companyname" style="font-weight: bold;"><a href="mailto:raybryce@hotmail.co.uk" data-track-event="click" data-track-action="email_link">raybryce@hotmail.co.uk</a> <br><a data-global="phone"  href="tel:01614395712" data-track-event="click" data-track-action="phone_link">0161 439 5712</a><br></p>
                  </div>
                </div>
              </div>
            </div>
            <div id="r2930" class="row row hidden-lg hidden-md" data-req="">
              <div class="container container-fixed">
                <div class="col col-sm-12"><a data-global="phone"  id="m1727" class="module button2" href="tel:01614395712"  title="" data-track-event="click" data-track-action="phone_link"><i class="buttonIcon fa fa-phone"></i> Tap to Call</a> </div>
              </div>
            </div>
            <div id="r5000" class="row " data-req="">
              <div class="container container-fixed">
                <div id="c4354" class="col col-lg-12 col-md-11 col-sm-12" data-req="">
                  <nav class="module nav" data-settings="vertical=false,direction=right,push=true,activeParent=false">
                    <ul>
                      <li class="<?=$active['home']?>"> <a href="/" data-track-event="click" data-track-action="internal_link_clicked">Home</a> </li>
                      <li class="<?=$active['repair']?>"> <a href="/?appliance-repair-service" data-track-event="click" data-track-action="internal_link_clicked">Appliance Repair Service</a> </li>
                      <li class=""> <a href="/spares" data-track-event="click" data-track-action="internal_link_clicked">Spare Parts</a> </li>
                      <li class="<?=$active['contact']?>"> <a href="/?contact-us" data-track-event="click" data-track-action="internal_link_clicked">Contact Us</a> </li>
                      <li class=""> <a href="/basket" data-track-event="click" data-track-action="internal_link_clicked">Basket</a> </li>
                    </ul>
                  </nav>
                </div>
              </div>
            </div>
          </header>
          <div id="r5002" class="row designRow">
            <div class="container container-fluid">
              <div class="col col-sm-12">
                <div id="r2618" class="row row hidden-lg hidden-md" data-req="">
                  <div class="container container-fixed">
                    <div class="col col-sm-12"><a class="imageModuleWrap" id="w_m2273"> <img id="m2273" class="module image" src="/uploads/767x0_1199x0/header-object.png" alt="A1 Repairs" data-req="" data-settings="enablehover=false,showelement=none,hovertransition=slowFadeIn"/> </a> </div>
                  </div>
                </div>
                <div id="r3040" class="row " data-req="">

                
<?=$response?>

                  
                </div>
              </div>
            </div>
          </div>
          <footer>
            <div id="r1074" class="row " data-req="">
              <div class="container container-fixed">
                <div id="c3869" class="col col-sm-12 col-lg-12 col-md-12" data-req="">
                  <div id="m1028" class="module text">
                    <p class="footertext"><span class="redactor-invisible-space"></span></p>
                    <p class="footertext">Copyright © 2018 A1 Repairs&nbsp;<br>Website powered by <a href="https://business.bt.com/products/computing-apps/" target="_blank" rel="noopener" data-track-event="click" data-track-action="link_clicked">BT</a></p>
                  </div>
                </div>
              </div>
            </div>
          </footer>
          <div class="row brandfooter">
            <div class="container container-fixed">
              <div class="col col-sm-12">
                <p>Website powered by <a href="https://business.bt.com/products/computing-apps/" target="_blank" rel="noopener" data-track-event="click" data-track-action="link_clicked"> BT </a></p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <script async defer src="/assets/js/loader.js"></script>
    <!--[if lt IE 8]><script src="/assets/js/boxsizing.js"></script><![endif]-->
    <script type='application/ld+json'>{"@context":"http://schema.org","@type":"LocalBusiness","name":"A1 Repairs","address":{"@type":"PostalAddress","streetAddress":"23 Westminster Drive","addressLocality":"Cheadle","addressRegion":"Lancashire","postalCode":"SK8 7QX","addressCountry":"UK"},"email":"raybryce@hotmail.co.uk","faxNumber":"","telephone":"01614395712","description":"","logo":"http://www.a1repairs-stockport.co.uk/uploads/header-object.png","image":"http://www.a1repairs-stockport.co.uk/uploads/header-object.png","url":"http://www.a1repairs-stockport.co.uk"}</script><script type="text/javascript"> (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){ (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o), m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m) })(window,document,'script','//www.google-analytics.com/analytics.js','_mga'); if(document.cookie.indexOf('mono_donottrack=true') !== -1) { window['ga-disable-UA-60603089-39'] = true; } _mga('create', 'UA-60603089-39', 'auto'); _mga('set', 'anonymizeIp', true); _mga('set', 'dimension1', '968849'); _mga('set', 'dimension2', 'website'); _mga('send', 'pageview'); var _mtr = _mtr || []; _mga(function() { _mtr.push(['addTracker', function (action) { _mga('send', 'event', 'monoAction', action); }]); _mtr.push(['addRawTracker', function() { _mga.apply(_mga,arguments); }]); }); </script> <script>var cb=function(){var l=document.createElement('link'); l.rel='stylesheet'; var h=document.getElementById('style_site'); h.parentNode.insertBefore(l, h); l.href='/assets/user-style.css';};var raf=window.requestAnimationFrame || window.mozRequestAnimationFrame || window.webkitRequestAnimationFrame || window.msRequestAnimationFrame;if (typeof raf !=='undefined'){raf(cb);}else{if(window.addEventListener){window.addEventListener('load', cb);}else{window.attachEvent('onload', cb);}}</script>
    <script src="/assets/js/jquery.min.js"></script>
    <script src="/assets/js/client.js"></script>
  </body>
</html>