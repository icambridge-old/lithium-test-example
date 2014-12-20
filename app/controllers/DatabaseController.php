<?php

namespace app\controllers;

use app\models\Record;

class DatabaseController extends \lithium\action\Controller {

    public function view() {
        $posts = Record::find('all');
        return compact('posts');
    }
}
