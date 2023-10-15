<?php
declare(strict_types=1);
namespace Leinc\MinichanRouter\Router;

use Leinc\MinichanRouter\Router\Interfaces\IFormRequest;
use Leinc\MinichanRouter\Router\Interfaces\IRequest;
use Leinc\MinichanRouter\Router\Interfaces\IServices;

class Request implements IServices, IRequest {
    private ?object $server = null;
    private array $args = [];
    
    /**
     * @param array $args
     */
    public function __construct(array $args = [])
    {
        $this->server = new \stdClass();
        foreach ($_SERVER as $key => $value) {
            $this->server->{strtolower($key)} = $value;
        }

        $this->args = $args;
    }
    
    /**
     * @param IFormRequest $request
     * @return bool
     */
    public function validator(IFormRequest $request): mixed
    {
        return $request->validate($this);
    }

    /**
     * @param string $name
     * @return mixed
     * @throws \Exception
     */
    public function __get(string $name)
    {
        if (!in_array($name, (array)$this->server)) {
            throw new \Exception("property $name doesn't exists on collection instance request.");
        }
        return $this->server->{$name};
    }

    /**
     * @return object
     */
    public function query(): object
    {
        return (object)$_GET ?? (object)$this->server->query_string;
    }

    /**
     * @return object
     */
    public function params(): object
    {
        return (object)$this->args;
    }

    /**
     * @param string $key
     * @return mixed|null
     */
    public function get(string $key)
    {
        return array_key_exists($key, $_GET) ? $_GET[$key] : null;
    }
    /**
     * @param string $key
     * @return mixed|null
     */
    public function post(string $key)
    {
        return array_key_exists($key, $_POST) ? $_POST[$key] : null;
    }

    public function getHttpHost(): string {
        return $this->server()->http_host;
    }
    public function getServerPort(): string
    {
        return $this->server()->server_port;
    }
    public function getRequestMethod(): string
    {
        return $this->server()->request_method;
    }
    public function server(): object {
        return $this->server;
    }

        /**
     * @param string|null $key
     * @return array|mixed
     */
    public function session(string $key = null): mixed
    {
        if (!isset($_SESSION[$key])) {
            return null;
        }
        return $_SESSION[$key];
    }

    /**
     * @param string|null $key
     * @return array|mixed
     */
    public function cookie(string $key = null): mixed
    {
        if (!isset($_COOKIE[$key])) {
            return null;
        }
        return $_COOKIE[$key];
    }

}