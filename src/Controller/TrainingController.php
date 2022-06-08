<?php

namespace App\Controller;

use App\Model\TrainingManager;
use App\Model\LanguageManager;
use App\Model\LanguageTrainingManager;
use App\Model\StackManager;
use App\Model\ImageManager;

class TrainingController extends AbstractController
{
    public function index(): string
    {
        $trainingManager = new TrainingManager();
        $trainings = $trainingManager->selectAll('title');
        return $this->twig->render('Training/index.html.twig', [
            'trainings' => $trainings,
            'loginErrors'   => $this->loginErrors,
        ]);
    }

    public function show(int $id): string
    {
        $trainingManager = new TrainingManager();
        $training = $trainingManager->selectOneById($id);
        return $this->twig->render('Training/show.html.twig', [
            'training' => $training,
            'loginErrors'   => $this->loginErrors,
        ]);
    }

    public function edit(int $id): ?string
    {
        $errors = [];
        $trainingManager = new TrainingManager();
        $training = $trainingManager->selectOneById($id);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $trainingUpdated = array_map('trim', $_POST);
            $errors = $this->validateTrainings($trainingUpdated);
            if (empty($errors)) {
                $trainingManager->update($trainingUpdated);
                header('Location: /trainings/show?id=' . $id);
            }
        }
        return $this->twig->render('Training/edit.html.twig', [
            'training' => $training,
            'errors' => $errors,
            'loginErrors'   => $this->loginErrors,
        ]);
    }

    private function validateTrainings(array $training): array
    {
        $errors = [];
        $trainingManager = new TrainingManager();
        $existingTrainings = $trainingManager->selectAllTrainings();

        if ($this->fieldsAreFilled($training) === false) {
            $errors[] = 'Tous les champs sont requis';
        }

        if (!empty($training['title']) && strlen($training['title']) > 255) {
            $errors[] = 'Le titre ne doit pas dépasser 255 caractères';
        }

        foreach ($existingTrainings as $existingTraining) {
            if (
                strcasecmp($training['title'], $existingTraining['title']) === 0
            ) {
                $errors[] = 'Le cursu existe déjà';
            }
        }
        return $errors;
    }

    private function fieldsAreFilled(array $training): bool
    {
        if (
            !empty($training['title']) ||
            !empty($training['description']) ||
            !empty($training['price']) ||
            !empty($training['duration']) ||
            !empty($training['date_start']) ||
            !empty($training['date_end']) ||
            !empty($training['max_students'])
        ) {
            return true;
        } else {
            return false;
        }
    }

    public function add(): ?string
    {
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $training = array_map('trim', $_POST);
            $errors = $this->validateTrainings($training);

            if (empty($errors)) {
                $trainingManager = new TrainingManager();
                $trainingId = $trainingManager->insert($training);
                // fill Automatically the intermediary language_training table
                // For the first language selected
                $languageTraining = [
                    'language_id' => $training['first_language_id'],
                    'training_id' => $trainingId
                ];
                $languageTrainingMgr = new LanguageTrainingManager();
                $languageTrainingMgr->insertLanguageTraining($languageTraining);

                // For 2nd language is selected
                if (!empty($_POST['second_language_id'])) {
                    $languageTraining = [
                        'language_id' => $training['second_language_id'],
                        'training_id' => $trainingId
                    ];
                    // Fill again the intermediary language_training table
                    $languageTrainingMgr->insertLanguageTraining($languageTraining);
                }

                $this->uploadFile($trainingId);
                header('Location:/trainings/show?id=' . $trainingId);
                return null;
            }
        }

        // -> Display of the availables stacks in the form
        $stackManager = new StackManager();
        $stacks = $stackManager->selectAll('name');

        // -> Display of the availables languages in the form
        $languageManager = new LanguageManager();
        $languages = $languageManager->selectAll('name');

        return $this->twig->render('Training/add.html.twig', [
            'errors' => $errors,
            'languages' => $languages,
            'stacks' => $stacks,

        ]);
    }

    private function uploadFile(int $trainingId): void
    {
        $uploadDir = '../public/assets/images/';
        $extension = pathinfo($_FILES['url']['name'], PATHINFO_EXTENSION);
        $uploadFile = $uploadDir . uniqid('') . "." . $extension ;
        move_uploaded_file($_FILES['url']['tmp_name'], $uploadFile);
        $pathToNewFile = pathinfo($uploadFile);
        $uploadFile = '/assets/images/' . $pathToNewFile['basename'];
        $_FILES['url'] = $uploadFile;
        $image = [
            'url' => $_FILES['url'],
            'trainingId' => $trainingId
        ];

        $imageManager = new ImageManager();

        $imageManager->insertImageWithTrainingId($image);
    }

    public function delete(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = trim($_POST['id']);
            $trainingManager = new TrainingManager();
            $trainingManager->delete((int) $id);

            header('Location:/trainings');
        }
    }

    public function search()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['wordsearch'])) {
            $search = $_POST['wordsearch'];
            $words = explode(' ', $search);
            $regex = implode('|', $words);
            $trainingManager = new TrainingManager();
            $filters = $trainingManager->searchByTitle($regex);
            $countResults = $trainingManager->countResults($regex);
            if ($countResults > 0) {
                return $this->twig->render('Training/filter.html.twig', [
                    'filters' => $filters,]);
            } else {
                return $this->twig->render('Training/search.html.twig', [
                    'errorSearch' => 'Votre recherche ne correspond à aucun résultat',]);
            }
        }
    }

    /**
     * Insert a new participant
     * only when adding a new training
     */
    public function insertParticipant(array $trainingParticipant): ?int
    {
        $trainingUser = new TrainingManager();
        return $trainingUser->insertParticipant($trainingParticipant);
    }

    public function filter(): string
    {
        $filters = false;
        if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET["result"])) {
            $filters = array_map('trim', $_GET);
            if ($_GET["result"] !== "backend" && $_GET["result"] !== "frontend") {
                $languageManager = new LanguageManager();
                $languages = $languageManager->selectAll();
                foreach ($languages as $key => $language) {
                    if (strtolower($language["name"]) === $_GET["result"]) {
                        $filterManager = new TrainingManager();
                        $filters = $filterManager->trainingsByFilter($language["id"]);
                    }
                }
            } else {
                $stackManager = new StackManager();
                $stacks = $stackManager->selectAll();
                foreach ($stacks as $key => $stack) {
                    if (strtolower($stack["name"]) === $_GET["result"]) {
                        $filterManager = new TrainingManager();
                        $filters = $filterManager->trainingsByStack($stack["id"]);
                    }
                }
            }
        }
        return $this->twig->render('Training/filter.html.twig', [
            'filters' => $filters,
        ]);
    }
}
