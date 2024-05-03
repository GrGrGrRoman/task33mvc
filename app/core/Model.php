<?php 

namespace App\core;

use App\lib\Db;

abstract class Model
{
    public $db;

    public function __construct()
    {
        $this->db = new Db;
    }
}