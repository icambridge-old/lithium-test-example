<?php

namespace app\tests\cases\controllers;

use app\controllers\DatabaseController;

class DatabaseControllerTest extends \lithium\test\Unit {
    public function testIndexList()
    {
        $databaseController = new DatabaseController();
        $viewData = $databaseController->view();
        $this->assertTrue(isset($viewData['posts']),"The index 'posts' isn't set");
        $this->assertEqual(1, sizeof($viewData['posts']));
    }
}