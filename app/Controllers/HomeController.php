<?php

namespace App\Controllers;

use SquareMvc\Foundation\AbstractController;
use SquareMvc\Foundation\Authentication as Auth;
use SquareMvc\Foundation\View;

class HomeController extends AbstractController
{
    public function index(): void
    {
        if (!Auth::check()) {
            $this->redirect('login.form');
        }

        $user = Auth::get();

        View::render('home', [
            'user' => $user,
        ]);
    }
}