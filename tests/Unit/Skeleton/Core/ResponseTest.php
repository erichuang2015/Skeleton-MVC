<?php

use PHPUnit\Framework\TestCase;
use Skeleton\Core\Response;
use Skeleton\Core\View;

class ResponseTest extends TestCase
{
    /** @var Response */
    private $response;

    protected function setUp()
    {
        $this->response = new Response('', 200, new View(VIEW_PATH));
    }

    public function test_check_header_add_headers_to_headers_array()
    {
        $this->response->header("X-header-one", "one")
                       ->header("X-header-two", "two");

        $expected = [
            "X-header-one" => "one",
            "X-header-two" => "two"
        ];
        $this->assertSame($expected, $this->response->getHeaders());
    }

    public function test_view_sets_content_with_view_passed_and_correct_headers()
    {
        $this->response->view('demo.testview', [], 200, ["X-header-one" => "one"])
                        ->header("X-header-two", "two");

        $expectedContent = "Demo Content";
        $expectedHeaders = [
            "Content-Type" => "text/html",
            "X-header-one" => "one",
            "X-header-two" => "two"
        ];
        $this->assertSame($expectedContent, $this->response->getContent());
        $this->assertSame($expectedHeaders, $this->response->getHeaders());
        $this->assertSame(200, $this->response->getStatus());
    }


    public function test_json_sets_contents_as_passed_and_correct_headers()
    {
        $data = [
            "hello" => "world"
        ];
        $this->response->json($data, 200, ["X-header-one" => "one"])
                        ->header("X-header-two", "two");

        $expectedContent = \json_encode($data);
        $expectedHeaders = [
            "Content-Type" => "application/json",
            "X-header-one" => "one",
            "X-header-two" => "two"
        ];
        $this->assertSame($expectedContent, $this->response->getContent());
        $this->assertSame($expectedHeaders, $this->response->getHeaders());
        $this->assertSame(200, $this->response->getStatus());
    }
}
