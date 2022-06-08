<?php

return [
    '' => ['HomeController', 'index',],

    'wishlists' => ['WishlistController', 'index',],
    'wishlists/edit' => ['WishlistController', 'edit', ['id']],
    'wishlists/show' => ['WishlistController', 'show', ['id']],
    'wishlists/add' => ['WishlistController', 'add',],
    'wishlists/delete' => ['WishlistController', 'delete',],

    'invoices' => ['InvoiceController', 'index',],
    'invoices/edit' => ['InvoiceController', 'edit', ['id']],
    'invoices/show' => ['InvoiceController', 'show', ['id']],
    'invoices/add' => ['InvoiceController', 'add',],
    'invoices/delete' => ['InvoiceController', 'delete',],

    'comments' => ['CommentController', 'index',],
    'comments/show' => ['CommentController', 'show', ['id']],
    'comments/add' => ['CommentController', 'add',],
    'comments/edit' => ['CommentController', 'edit', ['id']],
    'comments/delete' => ['CommentController', 'delete',],

    'users' => ['UserController', 'index',],
    'signup' => ['UserController', 'add',],
    'profile' => ['UserController', 'show', ['id']],
    'profile/invoices' => ['UserController', 'showInvoicesByUser', ['id']],
    'update' => ['UserController', 'edit', ['id']],
    'users/delete' => ['UserController', 'delete',],

    'stacks' => ['StackController', 'index',],
    'stacks/edit' => ['StackController', 'edit', ['id']],
    'stacks/show' => ['StackController', 'show', ['id']],
    'stacks/add' => ['StackController', 'add',],
    'stacks/delete' => ['StackController', 'delete',],

    'languages' => ['LanguageController', 'index',],
    'languages/edit' => ['LanguageController', 'edit', ['id']],
    'languages/show' => ['LanguageController', 'show', ['id']],
    'languages/add' => ['LanguageController', 'add',],
    'languages/delete' => ['LanguageController', 'delete',],

    'trainings' => ['TrainingController', 'index',],
    'trainings/edit' => ['TrainingController', 'edit', ['id']],
    'trainings/show' => ['TrainingController', 'show', ['id']],
    'trainings/add' => ['TrainingController', 'add',],
    'trainings/delete' => ['TrainingController', 'delete',],
    'trainings/search' => ['TrainingController', 'search',],
    'filter' => ['TrainingController', 'filter',],

    'images' => ['ImageController', 'index',],
    'images/edit' => ['ImageController', 'edit', ['id']],
    'images/show' => ['ImageController', 'show', ['id']],
    'images/add' => ['ImageController', 'add',],
    'images/delete' => ['ImageController', 'delete',],

    'cart' => ['CartController', 'showCart',],
    'recap' => ['CartController', 'showRecap',],

    'logout' => ['HomeController', 'logout',],
];
