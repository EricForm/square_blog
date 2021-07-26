<?php

namespace App\Controllers;

use JetBrains\PhpStorm\NoReturn;
use SquareMvc\Foundation\AbstractController;
use SquareMvc\Foundation\Authentication as Auth;
use SquareMvc\Foundation\Session;
use SquareMvc\Foundation\Validator;
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

    #[NoReturn]
    public function updateName(): void
    {
        if (!Auth::check()) {
            $this->redirect('login.form');
        }

        $validator = Validator::get($_POST);
        $validator->mapFieldsRules([
            'name' => ['required', ['lengthMin', 5]],
        ]);

        if (!$validator->validate()) {
            Session::addFlash(Session::ERRORS, $validator->errors());
            Session::addFlash(Session::OLD, $_POST);
            $this->redirect('home');
        }

        $user = Auth::get();
        $user->name = $_POST['name'];
        $user->save();

        Session::addFlash(Session::STATUS, 'Votre nom a été mis à jour !');
        $this->redirect('home');
    }
}