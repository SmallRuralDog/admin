<?php

namespace admin\controller;

use smallruraldog\admin\controller\AdminController;
use smallruraldog\admin\renderer\Page;
use support\Request;
use support\Response;

class HomeController extends AdminController
{

    public function index(Request $request): Response
    {
        $page = Page::make()->title('首页');

        return jsonData($page);
    }
}