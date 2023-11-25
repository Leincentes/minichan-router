<?php
declare(strict_types=1);
namespace MinichanRouter\Router\Interfaces;

interface IMiddleware {
    public function handle(IRequest $request, callable $next);
}