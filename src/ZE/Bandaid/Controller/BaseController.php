<?php
namespace ZE\Bandaid\Controller;
use Slim\Slim;

abstract class BaseController
{
    protected $app;

    public function __construct()
    {
        $this->app = Slim::getInstance();
    }
}