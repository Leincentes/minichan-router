<?php
declare(strict_types=1);
namespace Leinc\MinichanRouter\Router;

use Leinc\MinichanRouter\Router\Interfaces\IMiddleware;
use Leinc\MinichanRouter\Router\Interfaces\IRoutes;
use Exception;
use BadMethodCallException;

class Router implements IRoutes {
    private const VALID_METHODS = [
        'GET',
        'POST',
        'PUT',
        'PATCH',
        'DELETE',
        'OPTION',
    ];
    private const GET = 'GET';
    private const POST = 'POST';
    private const PUT = 'PUT';
    private const PATCH = 'PATCH';
    private const DELETE = 'DELETE';
    private const OPTION = 'OPTION';
    private const HOSTNAME = 'localhost';

    private ?string $serverMode;
    private ?Request $request = null;
    private static ?Router $instance = null;
    private ?array $routes = [];
    private ?string $prefix = null;
    private ?string $path = '/';
    private array $middlewares = [];
    private ?string $host = null;
    private ?string $uri = '/';
    private array $args = [];

    /**
     *
     * @throws Exception
     */
    public function __construct()
    {
        $this->serverMode = php_sapi_name();
        $this->uri = $this->serverMode === 'cli-server' ? parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) : null;
        $this->host = $_ENV['HOSTNAME'] ?? static::HOSTNAME;
    }

    /**
     * @return void
     */
    private function __clone()
    {

    }

    /**
     * create a singleton instance pattern to instantiate only one object from this class
     * @return static|null
     */
    public function getAllRoutes() {
        return $this->routes;
    } 
    private static function getInstance(): ?static 
    {
        if (static::$instance === null || is_null(static::$instance)) {
            static::$instance = new static();
        }
        return static::$instance;
    }
    public static function get(string $path, callable|array $callable): IRoutes {
        return static::getInstance()->addRoute(self::GET, $path, $callable);
    }
    
    /**
     * @param string $path
     * @param callable|array $callback
     * @return IRoutes
     * @throws Exception
     */
    public static function post(string $path, callable|array $callback): IRoutes
    {
        return static::getInstance()->addRoute(self::POST, $path, $callback);
    }

    /**
     * @param string $path
     * @param callable|array $callback
     * @return IRoutes
     * @throws Exception
     */
    public static function put(string $path, callable|array $callback): IRoutes
    {
        return static::getInstance()->addRoute(self::PUT, $path, $callback);
    }

    /**
     * @param string $path
     * @param callable|array $callback
     * @return IRoutes
     * @throws Exception
     */
    public static function patch(string $path, callable|array $callback): IRoutes
    {
        return static::getInstance()->addRoute(self::PATCH, $path, $callback);
    }

    /**
     * @param string $path
     * @param callable|array $callback
     * @return IRoutes
     * @throws Exception
     */
    public static function delete(string $path, callable|array $callback): IRoutes
    {
        return static::getInstance()->addRoute(self::DELETE, $path, $callback);
    }
    public static function option(string $path, callable|array $callback): IRoutes
    {
        return static::getInstance()->addRoute(self::OPTION, $path, $callback);
    }

    /** 
     * @param array $middlewares
     * @return IRoutes
     */
    public function middleware(array $middlewares): IRoutes
    {
        foreach ($this->routes as $index => $route) {
            if ($route['domain'] . $route['path'] === $this->host . $this->path) {
                $this->routes[$index]['middlewares'] = [...$middlewares, ...$this->middlewares];
            }
        }

        return $this;
    }

    /**
     * @param string $pattern
     * @return IRoutes
     */
    public function where(string $pattern): IRoutes
    {
        preg_match($pattern, $this->uri, $matches);

        foreach ($this->routes as $index => $route) {
            if ($route['domain'] . $route['path'] === $this->host . $this->path) {
                $this->routes[$index]['valid'] = count($matches) > 0;
            }
        }

        return $this;
    }

    /**
     * Adding the route to the routes
     * 
     * @param string $method
     * @param mixed $path
     * @param callable|array $callback
     * @return IRoutes
     * @throws Exception
     */
    private function addRoute(string $method, mixed $path, callable|array $callback)
    {
        // Validate the HTTP method
        if (!in_array($method, self::VALID_METHODS)) {
            $this->respondWithNotAllowedResponse($method);
        }
    
        // Build the complete route path
        $this->path = $this->buildCompletePath($path);
    
        // Check for duplicate routes
        $this->checkForDuplicateRoutes($method);
    
        // Add the route to the list
        $this->routes[] = [
            'method' => $method,
            'path' => $this->path,
            'callback' => $callback,
            'middlewares' => [...$this->middlewares],
            'valid' => true,
            'domain' => $this->host
        ];
    
        return $this;
    }

    /**
     * @param string $method
     */
    private function respondWithNotAllowedResponse(string $method) : void
    {
        http_response_code(405);
        throw new BadMethodCallException("$method not allowed.");
    }

    /**
     * @param string $path
     */
    private function buildCompletePath(string $path) : string
    {
        return $this->prefix !== null ? $this->prefix . $path : $path;
    }

    /**
     * @param string $method
     */
    private function checkForDuplicateRoutes(string $method) : void
    {
        $currentRoute = $this->host . $this->path;
    
        foreach ($this->routes as $route) {
            if ($route['domain'] . $route['path'] === $currentRoute && $route['method'] === $method) {
                throw new Exception("Route {$this->path} added before.");
            }
        }
    }

    /**
     * @return void
     * @throws Exception
     */
    public static function executeRoutes(): void
    {
        static::getInstance()->serve();
    }

    /**
     * @return void
     * @throws Exception
     */
    private function serve(): void
    {
        foreach ($this->routes as $route) {
            if ($this->isMatchingRoute($route)) {
                $this->executeRoute($route);
                return;
            }
        }

        $this->redirectToNotFoundRoute();
    }

    /**
     * Check if the given route matches the request.
     *
     * @param array $route
     * @return bool
     */
    private function isMatchingRoute(array $route): bool
    {
        return (
            $this->isMatchingPath($route['path'], $this->uri) &&
            $route['method'] === $_SERVER['REQUEST_METHOD'] &&
            $route['valid'] &&
            $this->checkDomain($route['domain'])
        );
    }

    /**
     * Execute the matched route.
     *
     * @param array $route
     * @return void
     * @throws Exception
     */
    private function executeRoute(array $route): void
    {
        $middlewares = $route['middlewares'];
        $callback = $route['callback'];

        $this->request = new Request($this->args);

        foreach ($middlewares as $middleware) {
            $this->executeMiddleware($middleware, $callback);
        }

        $this->callCallback($callback, $this->request);
    }

    /**
     * Execute a middleware.
     *
     * @param string $middleware
     * @param callable|array $callback
     * @return void
     * @throws Exception
     */
    private function executeMiddleware(string $middleware, callable|array $callback): void
    {
        $instance = new $middleware();

        if ($instance instanceof IMiddleware) {
            $instance->handle($this->request, fn (Request $request) => $this->callCallback($callback, $request));
        } else {
            throw new Exception("$middleware must be of type IMiddleware interface.");
        }
    }

    /**
     * Call the route callback.
     *
     * @param callable|array $callback
     * @param Request $request
     * @return void
     */
    private function callCallback(callable|array $callback, Request $request): void
    {
        if (is_array($callback)) {
            call_user_func_array([new $callback[0], $callback[1]], [$request]);
        } else {
            call_user_func($callback, $request);
        }
    }

    /**
     * Check if the domain matches the request.
     *
     * @param string $domain
     * @return bool
     */
    private function checkDomain(string $domain): bool
    {
        return $domain === parse_url($_SERVER['HTTP_HOST'], PHP_URL_HOST);
    }

    /**
     * Check if the given route path matches the URI.
     *
     * @param string $route
     * @param string $uri
     * @return bool
     */
    private function isMatchingPath(string $route, string $uri): bool
    {
        // Extract route parameters and compare with the URI
        $pattern = "/{(.*?)}/";
        preg_match_all($pattern, $route, $matches);

        $uriArray = explode('/', $uri);
        $pathArray = explode('/', $route);
        $uriDiff = array_diff($uriArray, $pathArray);
        $path = "";

        if (count($matches[1]) === count($uriDiff)) {
            $this->args = [...array_combine($matches[1], $uriDiff)];
            $path = sprintf(preg_replace("$pattern", "%s", $route), ...array_values($this->args));
        }

        return $path === $uri;
    }

    /**
     * Redirect to the "not found" route.
     *
     * @return void
     */
    private function redirectToNotFoundRoute(): void
    {
        header('Location: /route-not-found');
        exit();
    }
}