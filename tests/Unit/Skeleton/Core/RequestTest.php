<?php

use PHPUnit\Framework\TestCase;
use Skeleton\Core\Request;

class RequestTest extends TestCase
{
    private $request;
    private $request2;

    protected function setUp()
    {
        $server = [
            "REQUEST_URI" => "/mvc/public/",
            "SCRIPT_NAME" => "/mvc/public/index.php",
            "REQUEST_METHOD" => "GET"
        ];

        $server2 = [
            "REQUEST_URI" => "/mvc/public/users/demo/3?param=345&demo=users",
            "SCRIPT_NAME" => "/mvc/public/index.php",
            "REQUEST_METHOD" => "GET"
        ];

        $this->request = new Request($server);
        $this->request2 = new Request($server2);
    }

    public function test_path_returns_uri_without_base_path()
    {
        $this->assertSame('/', $this->request->path());
        $this->assertSame('/users/demo/3', $this->request2->path());
    }

    public function test_calling_undefined_method_throws_bad_call_exception()
    {
        $this->expectException(\BadMethodCallException::class);
        $this->request->undefinedMethod();
    }
}
