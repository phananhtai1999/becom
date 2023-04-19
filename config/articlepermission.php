<?php

use App\Models\Article;

return [
    'public' => [Article::PUBLIC_CONTENT_FOR_USER],
    'login' => [
        Article::PUBLIC_CONTENT_FOR_USER,
        Article::LOGIN_CONTENT_FOR_USER,
    ],
    'editor' => [
        Article::PUBLIC_CONTENT_FOR_USER,
        Article::LOGIN_CONTENT_FOR_USER,
        Article::EDITOR_CONTENT_FOR_USER,
    ],
    'payment' => [
        Article::PUBLIC_CONTENT_FOR_USER,
        Article::LOGIN_CONTENT_FOR_USER,
        Article::EDITOR_CONTENT_FOR_USER,
        Article::PAYMENT_CONTENT_FOR_USER,
    ],
    'admin' => [
        Article::PUBLIC_CONTENT_FOR_USER,
        Article::LOGIN_CONTENT_FOR_USER,
        Article::EDITOR_CONTENT_FOR_USER,
        Article::PAYMENT_CONTENT_FOR_USER,
        Article::ADMIN_CONTENT_FOR_USER,
    ],
];
