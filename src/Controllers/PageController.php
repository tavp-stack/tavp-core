<?php

declare(strict_types=1);

namespace Tavp\Core\Controllers;

/**
 * Example controller showing the BaseController helpers in action.
 * Used by the default routes in routes/web.php.
 */
class PageController extends BaseController
{
    public function home(): string
    {
        return $this->view('home', ['title' => 'TAVP Core']);
    }

    public function about(): string
    {
        return $this->view('about');
    }

    public function contact(): string
    {
        return $this->view('contact');
    }

    public function dashboard(): string
    {
        return $this->view('dashboard', [
            'title' => 'Dashboard',
            'user' => [
                'name' => 'User',
                'email' => 'user@example.com',
            ],
        ]);
    }
}
