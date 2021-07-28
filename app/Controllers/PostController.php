<?php

namespace App\Controllers;

use App\Models\Post;
use Cocur\Slugify\Slugify;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use JetBrains\PhpStorm\NoReturn;
use SquareMvc\Foundation\AbstractController;
use SquareMvc\Foundation\Authentication as Auth;
use SquareMvc\Foundation\Exceptions\HttpException;
use SquareMvc\Foundation\Session;
use SquareMvc\Foundation\Validator;
use SquareMvc\Foundation\View;

class PostController extends AbstractController
{
    public function index(): void
    {
        $posts = Post::withCount('comments')->orderBy('id', 'desc')->get();
        View::render('index', [
            'posts' => $posts,
        ]);
    }

    public function create(): void
    {
        if (!Auth::checkIsAdmin()) {
            $this->redirect('login.form');
        }

        View::render('posts.create');
    }

    public function store(): void
    {
        if (!Auth::checkIsAdmin()) {
            $this->redirect('login.form');
        }

        $validator = Validator::get($_POST + $_FILES);
        $validator->mapFieldsRules([
            'title' => ['required', ['lengthMin', 3]],
            'post' => ['required', ['lengthMin', 3]],
            'file' => ['required_file', 'image', 'square'],
        ]);

        if (!$validator->validate()) {
            Session::addFlash(Session::ERRORS, $validator->errors());
            Session::addFlash(Session::OLD, $_POST);
            $this->redirect('posts.create');
        }

        $slug = $this->slugify($_POST['title']);

        $ext = pathinfo(
            $_FILES['file']['name'],
            PATHINFO_EXTENSION
        );
        $filename = sprintf('%s.%s', $slug, $ext);

        if (!move_uploaded_file(
            $_FILES['file']['tmp_name'],
            sprintf('%s' . DIRECTORY_SEPARATOR . 'public'
                . DIRECTORY_SEPARATOR . 'img'
                . DIRECTORY_SEPARATOR . '%s', ROOT, $filename)
        )) {
            Session::addFlash(Session::ERRORS, ['file' => [
                'Il y a eu un problème lors de l\'envoi. Retentez votre chance !'
            ]]);
            Session::addFlash(Session::OLD, $_POST);
            $this->redirect('posts.create');
        }

        $post = Post::create([
            'user_id' => Auth::id(),
            'title' => $_POST['title'],
            'slug' => $slug,
            'body' => $_POST['post'],
            'reading_time' => ceil(str_word_count($_POST['post']) / 238),
            'img' => $filename,
        ]);

        Session::addFlash(Session::STATUS, 'Votre post a été publié !');

        // Redirection vers posts.show lorsque ce sera en place!
    }

    /**
     * @param string $slug
     */
    public function edit(string $slug): void
    {
        if (!Auth::checkIsAdmin()) {
            $this->redirect('login.form');
        }

        try {
            $post = Post::where('slug', $slug)->firstOrFail();
        } catch (ModelNotFoundException) {
            HttpException::render();
        }

        View::render('posts.edit', [
            'post' => $post,
        ]);
    }

    /**
     * @param string $slug
     */
    #[NoReturn]
    public function update(string $slug): void
    {
        if (!Auth::checkIsAdmin()) {
            $this->redirect('login.form');
        }

        $post = Post::where('slug', $slug)->firstOrFail();

        $validator = Validator::get($_POST);
        $validator->mapFieldsRules([
            'title' => ['required', ['lengthMin', 3]],
            'post' => ['required', ['lengthMin', 3]],
        ]);

        if (!$validator->validate()) {
            Session::addFlash(Session::ERRORS, $validator->errors());
            Session::addFlash(Session::OLD, $_POST);
            $this->redirect('posts.edit', ['slug' => $post->slug]);
        }

        $post->fill([
            'title' => $_POST['title'],
            'body' => $_POST['post'],
            'reading_time' => ceil(str_word_count($_POST['post']) / 238),
        ]);
        $post->save();

        Session::addFlash(Session::STATUS, 'Votre post a été mis à jour !');
        // Redirection vers posts.show lorsque ce sera en place!
    }

    /**
     * @param string $title
     * @return string
     */
    protected function slugify(string $title): string
    {
        $slugify = new Slugify();
        $slug = $slugify->slugify($title);
        $i = 1;
        $unique_slug = $slug;
        while (Post::where('slug', $unique_slug)->exists()) {
            $unique_slug = sprintf('%s-%s', $slug, $i++);
        }
        return $unique_slug;
    }
}