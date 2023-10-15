<?php
declare(strict_types=1);
namespace Leinc\MinichanRouter\Router\Interfaces;


interface IRoutes {
    public static function get(string $path, callable|array $callback): IRoutes;
    public static function post(string $path, callable|array $callback): IRoutes;
    public static function put(string $path, callable|array $callback): IRoutes;
    public static function patch(string $path, callable|array $callback): IRoutes;
    public static function delete(string $path, callable|array $callback): IRoutes;
    public static function option(string $path, callable|array $callback): IRoutes;
    public function middleware(array $middleware): IRoutes;
    public function where(string $pattern): IRoutes;
}