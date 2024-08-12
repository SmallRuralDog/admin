<?php

use admin\controller\HomeController;
use Webman\Route;

Route::group("", function () {
    Route::get('/home', [HomeController::class, 'index']);
});