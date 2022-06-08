<?php

namespace App\Controller;

use App\Model\TrainingManager;
use App\Model\StackManager;

class HomeController extends AbstractController
{
    /**
     * Display home page
     */
    public function index(): string
    {
        $stackManager = new StackManager();
        $stacks = $stackManager->selectAll('name');
        $trainingManager = new TrainingManager();
        //var_dump($stacks);
        $trainings = [];
        foreach ($stacks as $key => $stack) {
            $trainings[$key] = $trainingManager->groupTrainings($stack['id']);
        }
        //var_dump($trainings); die;

        // For the logout result
        $logout = false;
        if (isset($_GET['logout'])) {
            $logout = true;
            return $this->twig->render('Home/index.html.twig', [
                'trainings'     => $trainings,
                'logoutMessage' => $logout,
            ]);
        } else {
            return $this->twig->render('Home/index.html.twig', [
                'trainings'     => $trainings,
                'loginErrors'   => $this->loginErrors,
            ]);
        }
    }

    public function show(int $id): string
    {
        $trainingManager = new TrainingManager();
        $training = $trainingManager->selectOneById($id);
        return $this->twig->render('Training/show.html.twig', [
            'training'      => $training,
            'loginErrors'   => $this->loginErrors,
        ]);
    }
}
