<?php

use PHPUnit\Framework\TestCase;
use Skeleton\Core\View;

class ViewTest extends TestCase
{
    private $view;
    protected function setUp()
    {
        $this->view = new View(VIEW_PATH);
    }
    
    public function test_view_should_return_contents_from_single_view_file()
    {
        $contents = $this->view->view('demo.testview');

        $this->assertSame('Demo Content', $contents);
    }
    
    public function test_view_should_return_contents_from_multiple_view_files()
    {
        $contents = $this->view->view(array(
            'templates.header',
            'demo.testview',
        ));

        $this->assertSame('Demo ContentDemo Content', $contents);
    }

    public function test_loading_non_existing_view_throws_runtime_exception()
    {
        $this->expectException(\RuntimeException::class);
        $contents = $this->view->view('demo.i_dont_exist');
    }
}
