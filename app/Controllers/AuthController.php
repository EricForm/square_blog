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

    public function loginForm(): void
    {
        if (Auth::check()) {
            $this->redirect('home');
        }

        View::render('auth.login');
    }

    public function login(): void
    {
        if (Auth::check()) {
            $this->redirect('home');
        }

        $validator = Validator::get($_POST);
        $validator->mapFieldsRules([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if ($validator->validate() && Auth::verify($_POST['email'], $_POST['password'])) {
            $user = User::where('email', $_POST['email'])->first();
            Auth::authenticate($user->id);
            $this->redirect('home');
        }

        Session::addFlash(Session::ERRORS, ['Identifiants erronés']);
        Session::addFlash(Session::OLD, $_POST);
        $this->redirect('login.form');
    }

    public function logout(): void
    {
        if (Auth::check()) {
            Auth::logout();
        }

        $this->redirect('login.form');
    }
}