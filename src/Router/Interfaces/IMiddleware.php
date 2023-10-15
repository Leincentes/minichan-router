<?php
declare(strict_types=1);
namespace Leinc\MinichanRouter\Router\Interfaces;

interface IMiddleware {
    public function handle(IRequest $request, callable $next);
}