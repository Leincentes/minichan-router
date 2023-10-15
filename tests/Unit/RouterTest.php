<?php

use Leinc\MinichanRouter\Router\Interfaces\IRoutes;
use Leinc\MinichanRouter\Router\Router;
use PHPUnit\Framework\TestCase;

class RouterTest extends TestCase{
    protected $router;
    protected function setUp(): void 
    {
        $this->router = new Router();
    }
    public function test_it_register_get_if_instance_of_iroutes() {
        $result = $this->router::get('/', function () {
            return true;
        });
        $this->assertInstanceOf(IRoutes::class, $result);
    }
    public function test_it_register_post_if_instance_of_iroutes() {
        $result = $this->router::post('/', function () {
            return true;
        });
        $this->assertInstanceOf(IRoutes::class, $result);
    }
    public function test_it_register_put_if_instance_of_iroutes() {
        $result = $this->router::put('/', function () {
            return true;
        });
        $this->assertInstanceOf(IRoutes::class, $result);
    }
    public function test_it_register_patch_if_instance_of_iroutes() {
        $result = $this->router::patch('/', function () {
            return true;
        });
        $this->assertInstanceOf(IRoutes::class, $result);
    }
    public function test_it_register_delete_if_instance_of_iroutes() {
        $result = $this->router::delete('/', function () {
            return true;
        });
        $this->assertInstanceOf(IRoutes::class, $result);
    }
    public function test_it_register_option_if_instance_of_iroutes() {
        $result = $this->router::option('/', function () {
            return true;
        });
        $this->assertInstanceOf(IRoutes::class, $result);
    }
}