<?php

/*************************************************************************
*
* Router
*
*   Basic route handler for HTTP requests
*
*   Created:    27 September 2018
*   Copyright:  Nigel Alderton
*
*
*
* Hello world example
* -------------------
* require 'Router.php';
*
* $routes = [];                               # the routes array maps each HTTP request method/path pair to a handler
* 
* $routes['GET']['/'] = function($request) {  # create a root route
*   return "Hello world!";
* };
* 
* $request = [                                # the HTTP request. must have 'method' and 'path' keys. add more info if needed by the handlers. (eg. cookies etc.)
*   'method'  => 'GET',                       # - the method (eg. from $_SERVER['REQUEST_METHOD'])
*   'path'    => '/'                          # - the path (eg. from $_SERVER['QUERY_STRING'] or $_SERVER['REQUEST_URI'])
* ];
* 
* $router = new Router($routes);              # create a router
* echo $router->getResponse($request);        # execute the handler. outputs "Hello world!"
*
*
*
* Using params
* ------------
* $routes = [];
* 
* $routes['GET']['/foo/{name}/'] = function($request, $name) {
*   return "Hi there {$name}.";
* };
* $routes['GET']['/foo/{name}/{id}/'] = function($request, $name, $id) {
*   return "Hello {$name}. Your id is {$id}.";
* };
*
* $router = new Router($routes);
*
* $request = ['method'=>'GET', 'path'=>'/foo/nig'];
* echo $router->getResponse($request);        # outputs "Hi there nig."
*
* $request = ['method'=>'GET', 'path'=>'/foo/fred/4'];
* echo $router->getResponse($request);        # outputs "Hello fred. Your id is 4."
*
*
*
* Handler notes
* -------------
* 1. Route paths have forward slashes removed from the start and end, so all these paths are equivalent; '/foo/bar', '/foo/bar/', 'foo/bar/', 'foo/bar'.
* 2. Each handler must accept $request as the first parameter plus one parameter for each possible param in the path.
*
*
*
* Revision history
* ----------------
* 2018-09-02  Working. Alpha testing done. Documentation done.
* 2018-09-05  Params now returned after urldecode()ing.
*/


interface iRouter
{
  public function __construct(array $routes);
  public function getHandler(array $request);
  public function getResponse(array $request);
}



class Router implements iRouter {
  
    
  /**
  * route tree
  *
  *   the routes array restructured into a tree. see merge_routes() for more info
  *
  * @var  array         
  */
  private $tree;

  
  
  
  /***************************************************************
  *
  * Public
  *
  ***************************************************************/

  
  /**
  * constructor
  *
  * @param  array       array of arrays of the form [$method][$path] = function(){}, eg. ['GET']['/foo/{name}/{id}'] = function(){return 'hi';}
  *
  * @return void
  */
  public function __construct(array $routes) {
    $this->tree = $this->merge_routes($routes);;
  }


  /**
  * get the handler for the specified request
  *
  * @param  array       array containing information about the HTTP request. must have at least 'method' and 'path' keys
  *
  * @return closure     closure containing the handler
  */
  public function getHandler(array $request) {
    
    // create an array containing the HTTP request method and path, eg. ['GET', 'part', '{id}']
    $path = array_merge([$request['method']], explode('/', trim($request['path'], '/')));      # array_merge() doesn't merge, it appends.

    // get the the handler for the specified http request
    return $this->get_handler($request, $path, $this->tree);
  }
  


  /**
  * execute the handler for the specified request
  *
  * @param  array       array containing information about the HTTP request. must have at least 'method' and 'path' keys
  *
  * @return mixed       the return value of the handler
  */
  public function getResponse(array $request) {
    
    // create an array containing the HTTP request method and path, eg. ['GET', 'part', '{id}']
    $path = array_merge([$request['method']], explode('/', trim($request['path'], '/')));      # array_merge() doesn't merge, it appends.

    // get the the handler for the specified http request
    $fn = $this->get_handler($request, $path, $this->tree);
    
    // execute the handler
    return $fn();
  }
  
  
  
  /***************************************************************
  *
  * Private
  *
  ***************************************************************/

  
  /**
  * merge all routes into a single tree
  *
  * @param  array       list of route mappings. each route mapping is structured [HTTP_METHOD][ROUTE_PATH] = handler(), eg.
  *   [
  *     'GET' => ['/foo'      => closure1],
  *     'GET' => ['/bar'      => closure2],
  *     'GET' => ['/bar/baz'  => closure3],
  *   ]
  *
  * @return array       the route mappings merged with each other to form a tree, eg.
  *   [
  *     'GET' => [
  *       'foo' => [
  *         '_handler' => closure1
  *       ],
  *       'bar' => [
  *         '_handler' => closure2,
  *         'baz' => [
  *           '_handler' => closure3
  *         ]
  *       ]
  *     ]
  *   ]
  */
  private function merge_routes($routes)
  {
    $tree = [];
    
    foreach ($routes as $method => $map) {
      
      foreach ($map as $path => $handler) {
        
        $keys = explode('/', trim($path, '/'));                           # turn path eg. '/foo/bar' into array eg. ['foo', 'bar']
        
        $keys = array_map(function($key){                                 # replace params eg. '{id}' with '_param'
          return $this->is_param($key) ? '_param' : $key;
        }, $keys);
        
        array_unshift($keys, $method);                                    # prepend the HTTP method to the $keys array
        
        $branch = $this->list_to_tree($keys, ['_handler' => $handler]);   # turn (['a', 'b', 'c'], $leaf) into (['a'=>['b'=>['c'=>$leaf]]])
        $tree = array_merge_recursive($tree, $branch);
      }
    }
    
    return $tree;
  }


  /**
  * make a tree from a list and leaf
  *
  * eg. for a list of ['foo', 'bar', 'baz'] and leaf of 'hi', return the tree;
  *   [
  *     'foo' => [
  *       'bar' => [
  *         'baz' => 'hi'
  *       ]
  *     ]
  *   ]
  *
  * @param  array       list of path tokens eg. ['foo', 'bar']
  * @param  mixed       the inner-most array value ie. leaf
  *
  * @return array       tree (see above)
  */
  private function list_to_tree($list, $leaf)
  {
    $list = array_reverse($list);
    
    foreach ($list as $key) {
      $leaf = [$key => $leaf];
    }
    return $leaf;
  }


  /**
  * get the matching handler (recursive)
  *
  * @param  array       HTTP request. must have at least 'method' and 'path' keys, eg. ['method'=>'GET', 'path'=>'/test']
  * @param  array       route path as an array of tokens eg. for a route path '/foo/bar', this param would be ['foo', 'bar']
  * @param  array       route tree
  * @param  array       accumulator for param values, eg. for route '/foo/{name}/{id}' and request path '/foo/nig/3', this param would be ['nig', '3']
  *
  * @return closure     a closure containing the matching handler
  */
  private function get_handler($request, $steps, $tree, $params = [])
  {
    if (empty($steps)) {

      // there are no more steps to consume, so there should be a '_handler' key on this branch of the tree. If not, throw an error
      if (!array_key_exists('_handler', $tree))
        throw new Exception("No matching route found for path '{$request['path']}'");
      
      // return a closure containing the handler
      return function() use($request, $tree, $params){
        return call_user_func_array($tree['_handler'], array_merge([$request], $params));
      };
      
    } else {
      
      // consume the next step in the path
      $step = $steps[0];
      
      // at this point there are three possibilites: 1) the token is in the tree branch, 2) the tree branch has a param, 3) neither 1 nor 2
      if ((!array_key_exists($step, $tree)) && (!array_key_exists('_param', $tree)))
        throw new Exception("No matching route found for path '{$request['path']}' at step '{$step}'");
      
      // now there are only two possibilites, either 1) the path token is in the tree branch, or 2) the tree branch has a param
      if (!array_key_exists($step, $tree))
        $params[] = urldecode($step);
        
      $child_name = (array_key_exists($step, $tree))
        ? $step
        : '_param';
        
      $found = $this->get_handler($request, array_slice($steps, 1), $tree[$child_name], $params);
      
      if (is_null($found)) {
        return null;
      } else {
        return $found;
      }
    }
  }


  /**
  * is a string a param?
  *   i.e. does it start with '{' and end with '}'
  *
  * @param  string      a token from an HTTP request path eg. 'foo', '{id}', '{name}' etc
  *
  * @return TRUE|FALSE  return TRUE if the token starts with '{' and ends with '}', otherwise return FALSE
  */
  private function is_param($str)
  {
    return ('{' === substr($str, 0, 1)) && ('}' === substr($str, -1));
  }

}

?>