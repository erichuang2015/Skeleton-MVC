<?php

use PHPUnit\Framework\TestCase;
use Skeleton\Core\Input;

class InputTest extends TestCase
{
    public function aP($obj, $method)
    {
        $reflection = new ReflectionClass($obj);
        $met = $reflection->getMethod($method);
        $met->setAccessible(true);
        return $met;
    }
    public function test_fetchFromArray_returns_requested_index()
    {
        $post = array("demo" => "correct");

        $inp = new Input;

        $fetchFromArray = $this->aP($inp, 'fetchFromArray');
        $this->assertSame('correct', $fetchFromArray->invokeArgs($inp, array(
            &$post, "demo"
        )));
    }

    public function test_fetchFromArray_returns_entire_array()
    {
        $post = array("demo" => "correct");
        $clone = $post;
        $inp = new Input;

        $fetchFromArray = $this->aP($inp, 'fetchFromArray');
        $this->assertSame($clone, $fetchFromArray->invokeArgs($inp, array(&$post)));
    }
}
