<?php

namespace App\Controller;

use App\Model\LanguageManager;

class LanguageController extends AbstractController
{
    /**
     * List items
     */
    public function index(): string
    {
        $languageManager = new LanguageManager();
        $languages = $languageManager->selectAll('name');

        return $this->twig->render('Language/index.html.twig', ['languages' => $languages]);
    }

    /**
     * Show informations for a specific item
     */
    public function show(int $id): string
    {
        $languageManager = new LanguageManager();
        $language = $languageManager->selectOneById($id);

        return $this->twig->render('Language/show.html.twig', ['language' => $language]);
    }

    /**
     * Edit a specific item
     */
    public function edit(int $id): ?string
    {
        $errors = [] ;
        $languageManager = new LanguageManager();
        $language = $languageManager->selectOneById($id);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // clean $_POST data
            $languageUpdated = array_map('trim', $_POST);

            // TODO validations (length, format...)
            $errors = $this->validateLanguage($languageUpdated);
            // if validation is ok, update and redirection
            if (empty($errors)) {
                $languageManager->update($languageUpdated);
                header('Location: /languages/show?id=' . $id);
            }
        }
        return $this->twig->render('Language/edit.html.twig', [
        'language' => $language, 'errors' => $errors
        ]);
    }

    /**
     * Add a new language
     */
    public function add(): ?string
    {
        $errors = [] ;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // clean $_POST data
            $language = array_map('trim', $_POST);
             // TODO validations (length, format...)
            $errors = $this->validateLanguage($language);

            if (empty($errors)) {
                $languageManager = new LanguageManager();
                $id = $languageManager->insert($language);
                header('Location:/languages/show?id=' . $id);
                return null;
            }
        }
        return $this->twig->render('Language/add.html.twig', ['errors' => $errors]);
    }

    public function validateLanguage(array $language): array
    {
        $errors = [];
        $languageManager = new LanguageManager();
        $existingLanguages = $languageManager->selectAll();
        if (empty($language['name'])) {
                $errors[] = 'Tous les champs sont requis';
        }
        if (!empty($language['name']) && strlen($language['name']) > 80) {
                $errors[] = 'Le nom du language ne doit pas dépasser 80 caractères';
        }
        foreach ($existingLanguages as $existingLanguage) {
            if (strcasecmp($language['name'], $existingLanguage['name']) === 0) {
                $errors [] = 'Le language existe déjà';
            }
        }
        return $errors;
    }

    public function delete(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $id = trim($_POST['id']);
                $languageManager = new LanguageManager();
                $languageManager->delete((int)$id);
                header('Location:/languages');
        }
    }
}
