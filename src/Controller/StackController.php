<?php

namespace App\Controller;

use App\Model\StackManager;

class StackController extends AbstractController
{
    /**
     * List stacks
     */
    public function index(): string
    {
        $stackManager = new StackManager();
        $stacks = $stackManager->selectAll('name');

        return $this->twig->render('Stack/index.html.twig', ['stacks' => $stacks]);
    }

    /**
     * Show informations for a specific stack
     */
    public function show(int $id): string
    {
        $stackManager = new StackManager();
        $stack = $stackManager->selectOneById($id);

        return $this->twig->render('Stack/show.html.twig', ['stack' => $stack]);
    }

    /**
     * Edit a specific stack
     */
    public function edit(int $id): ?string
    {
        $errors = [];
        $stackManager = new StackManager();
        $stack = $stackManager->selectOneById($id);

        // Result check
        // if (!isset($stack['name'])) {
        //     header("Location: /stacks");
        //     exit("Stack not found");
        // }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // clean $_POST data
            $stackUpdate = array_map('trim', $_POST);

            // validations (length, format...)
            $errors = $this->validateStack($stackUpdate);
            // if validation is ok, update and redirection
            if (empty($errors)) {
                $stackManager->updateStack($stackUpdate);
                header('Location: /stacks/show?id=' . $id);
                return null;
            }
        }

        return $this->twig->render('Stack/edit.html.twig', [ 'stack' => $stack, 'errors' => $errors ]);
    }

    /**
     * Add a new stack
     */
    public function add(): ?string
    {
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // clean $_POST data
            $stack = array_map('trim', $_POST);

            // TODO validations (length, format...)
            $errors = $this->validateStack($stack);
            // if validation is ok, insert and redirection
            if (empty($errors)) {
                $stackManager = new StackManager();
                $id = $stackManager->insertStack($stack);
                header('Location:/stacks/show?id=' . $id);
                return null;
            }
        }

        return $this->twig->render('Stack/add.html.twig', ['errors' => $errors]);
    }

    /**
     * Generate errors
     */
    public function validateStack(array $stack): array
    {
        $stackManager = new StackManager();
        $existingStacks = $stackManager->selectAll();
        $errors = [];

        if (empty($stack['name'])) {
            $errors[] = 'Tous les champs sont requis';
        }
        if (!empty($stack['name']) && strlen($stack['name']) > 100) {
            $errors[] = 'Le nom ne doit pas dépasser 100 caractères';
        }
        foreach ($existingStacks as $existingStack) {
            if (strcasecmp($stack['name'], $existingStack['name']) === 0) {
                $errors[] = 'Le nom de ce stack existe déjà';
            }
        }
        return $errors;
    }

    /**
     * Delete a specific stack
     */
    public function delete(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = trim($_POST['id']);
            $stackManager = new StackManager();
            $stackManager->delete((int)$id);

            header('Location:/stacks');
        }
    }
}
