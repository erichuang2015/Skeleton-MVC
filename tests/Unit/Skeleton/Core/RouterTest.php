<?php

use PHPUnit\Framework\TestCase;
use Skeleton\Core\Router;

class RouterTest extends TestCase
{
    protected $router;

    protected function setUp()
    {
        $this->router = new Router;
    }

    public function aP($obj, $prop)
    {
        $reflection = new ReflectionClass($obj);
        $property = $reflection->getProperty($prop);
        $property->setAccessible(true);
        return $property->getValue($obj);
    }
    
    public function test_match_adds_allowed_method()
    {
        // $this->router = new Router();
        $this->router->match('GET', 'test/demo', 'DemoController@demo');

        $this->assertEquals(
            ['GET'=>[['pattern' => 'test/demo', 'fn' => 'DemoController@demo']]],
            $this->aP($this->router, 'routes')
        );
    }

    public function test_match_doesnt_accpet_not_allowed_method()
    {
        // As router class should not allow any ohter than get, post, etc..
        $this->router->match('DEMO', 'test/demo', 'DemoController@demo');
        $this->assertArrayNotHasKey('DEMO', $this->aP($this->router, 'routes'));
    }

    public function test_if_any_adds_all_allowed_methods()
    {
        $this->router->any('/test', 'DemoController@demo');
        $this->assertArrayHasKey('GET', $this->aP($this->router, 'routes'));
        $this->assertArrayHasKey('POST', $this->aP($this->router, 'routes'));
        $this->assertArrayHasKey('PATCH', $this->aP($this->router, 'routes'));
        $this->assertArrayHasKey('OPTIONS', $this->aP($this->router, 'routes'));
        $this->assertArrayHasKey('DELETE', $this->aP($this->router, 'routes'));
        $this->assertArrayHasKey('PUT', $this->aP($this->router, 'routes'));
    }
}
