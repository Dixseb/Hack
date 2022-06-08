<?php

namespace App\Controller;

use App\Model\WishlistManager;

class WishlistController extends AbstractController
{
    public function index(): string
    {
        $wishlistManager = new WishlistManager();
        $wishlists = $wishlistManager->selectAll('w.id');

        return $this->twig->render('Wishlist/index.html.twig', ['wishlists' => $wishlists]);
    }

    public function show(int $id): string
    {
        $wishlistManager = new WishlistManager();
        $wishlist = $wishlistManager->selectOneById($id);

        return $this->twig->render('Wishlist/show.html.twig', ['wishlist' => $wishlist]);
    }

    public function add(): ?string
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $wishlist = array_map('trim', $_POST);
            $errors = $this->wishlistValidation($_POST);
            if (empty($errors)) {
                $wishlistManager = new WishlistManager();
                $wishlistManager->insert($wishlist);
                header('Location:/wishlists');
                return null;
            }
        }
        return $this->twig->render('Wishlist/add.html.twig');
    }

    public function wishlistValidation($post): array
    {
        $errors = [];
        if (isset($post['title']) && empty($post['title'])) {
            $errors[] = 'Veuillez choisir la formation que vous souhaitez enregistrer';
        }
        return $errors;
    }

    public function delete(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = trim($_POST['id']);
            $wishlistanager = new WishlistManager();
            $wishlistanager->delete((int)$id);

            header('Location:/wishlists');
        }
    }
}
