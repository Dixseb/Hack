<?php

namespace App\Controller;

use App\Model\ImageManager;

class ImageController extends AbstractController
{
    /**
     * List images
     */
    public function index(): string
    {
        $imageManager = new ImageManager();
        $images = $imageManager->selectAllImages('url');

        return $this->twig->render('Image/index.html.twig', ['images' => $images]);
    }

    /**
     * Show informations for a specific image
     */
    public function show(int $id): string
    {
        $imageManager = new ImageManager();
        $image = $imageManager->selectImageById($id);

        return $this->twig->render('Image/show.html.twig', ['image' => $image]);
    }

    /**
     * Edit a specific image
     */
    public function edit(int $id): ?string
    {
        $errors = [];
        $imageManager = new ImageManager();
        $image = $imageManager->selectImageById($id);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $imageUpdate = array_map('trim', $_POST);

            $errors = $this->validateImage($imageUpdate);

            if (empty($errors)) {
                $imageManager->updateImage($imageUpdate);
                header('Location: /images/show?id=' . $id);
                return null;
            }
        }

        return $this->twig->render('Image/edit.html.twig', [ 'image' => $image, 'errors' => $errors ]);
    }

    /**
     * Add a new image
     */
    public function add(): ?string
    {
        // $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $image = array_map('trim', $_POST);

            // $errors = $this->validateImage($image);

            // if (empty($errors)) {
                $imageManager = new ImageManager();
                $id = $imageManager->insertImageWithTrainingId($image);
                header('Location:/images/show?id=' . $id);
                return null;
            // }
        }

        return $this->twig->render('Image/add.html.twig');
    }

    /**
     * Generate errors
     */
    public function validateImage(array $image): array
    {
        $errors = [];

        if (empty($image['url'])) {
            $errors[] = 'Tous les champs sont requis';
        }

        if (!empty($image['url']) && strlen($image['url']) > 100) {
            $errors[] = 'Le nom ne doit pas dépasser 100 caractères';
        }

        return $errors;
    }

    /**
     * Delete a specific image
     */
    public function delete(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = trim($_POST['id']);
            $imageManager = new ImageManager();
            $imageManager->delete((int)$id);

            header('Location:/images');
        }
    }
}
