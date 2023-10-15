<?php
declare(strict_types=1);
namespace Leinc\MinichanRouter\Router\Interfaces;

interface IRequest {
    public function get(string $key);
    public function post(string $key);
    public function params(): object;
}