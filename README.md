# minichan-router

A minimal router.

<!-- # installation

```bash
$ composer require leinc/minichan-router
``` -->

# Usage

You can use this router like below

   ```php
    <?php
   
    require __DIR__ . "/vendor/autoload.php";

    Router::get('/',function (Request $request){
        echo "Hello World";
    });

    Router::get('/foo',function (Request $request){
        echo "foo route";
    });

    Router::executeRoutes();
   ```

Use Controller instead of callback functions

  ```php
   <?php

    require __DIR__ . "/vendor/autoload.php";
    
    Router::get('/foo/create',[FooController::class,'create']);
    
    Router::post('/foo',[FooController::class,'store']);

    Router::executeRoutes();
  ```
  
However you would be able to use dynamic route parameters

   ```php
    <?php

    require __DIR__ . "/vendor/autoload.php";

    Router::get('/bar/{id}',function (Request $request){
       echo $request->params()->id;
    });
    
    Router::get('/foo/{file}',function (Request $request){
       echo $request->params()->file;
    })->where('/foo\/[a-z]+/');

    Router::executeRoutes();
   ```

# Request methods

You can use only this request methods to handle you're api

 ```bash 
    GET,POST,PUT,PATCH,DELETE,OPTION
 ``` 
 # Middleware

Create a class for example AuthMiddleware that implements IMiddleware contract

```php
<?php

 class AuthMiddleware implements IMiddleware
 {
   public function handle(IRequest $request,Callable $next)
   {
     if(!isset($_SESSION['admin']) && $_SESSION['admin'] !== 'zanko'){
           header("Location:/");
           exit();
     }
     $next($request);
   }
 }
```
After middleware has been created you should register it on you're router

```php
<?php

  require __DIR__ . "/vendor/autoload.php";
  
  Router::get('/foo',function (Request $request){
     // your code
  })->middleware([AuthMiddleware::class]); 

  Router::executeRoutes();
```
