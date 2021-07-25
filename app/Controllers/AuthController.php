<?php

namespace App\Controllers;

use App\Models\User;
use SquareMvc\Foundation\AbstractController;
use SquareMvc\Foundation\Authentication as Auth;
use SquareMvc\Foundation\Session;
use SquareMvc\Foundation\Validator;
use SquareMvc\Foundation\View;

class AuthController extends AbstractController
{
    public function registerForm(): void
    {
        if (Auth::check()) {
            $this->redirect('home');
        }

        View::render('auth.register');
    }

    public function register(): void
    {
        if (Auth::check()) {
            $this->redirect('home');
        }

        $validator = Validator::get($_POST);
        $validator->mapFieldsRules([
            'name' => ['required', ['lengthMin', 5]],
            'email' => ['required', 'email', ['unique', 'email', 'users']],
            'password' => ['required', ['lengthMin', 8], ['equals', 'password_confirmation']],
        ]);

        if (!$validator->validate()) {
            //var_dump($validator->errors());die();

            Session::addFlash(Session::ERRORS, array_column($validator->errors(), 0));
            Session::addFlash(Session::OLD, $_POST);
            $this->redirect('register.form');
        }

        $user = User::create([
            'name' => $_POST['name'],
            'email' => $_POST['email'],
            'password' => password_hash($_POST['password'], PASSWORD_DEFAULT),
        ]);

        Auth::authenticate($user->id);
        $this->redirect('home');

    }
}