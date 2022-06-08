<?php

namespace App\Controller;

use App\Model\TrainingManager;
use App\Model\UserManager;
use App\Controller\TrainingController;
use App\Controller\InvoiceController;
use App\Controller\UserController;

class CartController extends AbstractController
{
    public function showCart()
    {
        $cartInfos = $this->getCartInfos();
        $user = '';
        if (isset($_SESSION['user'])) {
            $userManager = new UserManager();
            $user = $userManager->selectOneById($_SESSION['user']['id']);
        }
        return $this->twig->render('Cart/checkout.html.twig', [
            'cart' => $cartInfos,
            'user' => $user,
        ]);
    }

    public function showRecap()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $_SESSION['total'] = intval($_POST['total-price']);
        }
        $userId = $_SESSION['user']['id'];
        $invoiceController = new InvoiceController();
        $newInvoice = $invoiceController->generateInvoice();
        $invoiceDetails = $invoiceController->showInvoiceInRecap($newInvoice);
        $userController = new UserController();
        $user = $userController->showUserInRecap($userId);

        // Increase number of participants when someone enrols in a training
        $trainingController = new TrainingController();
        foreach (array_keys($_SESSION['cart']) as $trainingRegistration) {
            $trainingParticipant = array(
                'training_id' => $trainingRegistration,
                'user_id' => $userId
            );
            $trainingController->insertParticipant($trainingParticipant);

            // Associate the invoice and the trainings
            // Fill the invoice_training table
            $invoiceTraining = array(
                'training_id' => $trainingRegistration,
                'invoice_id' => $newInvoice
            );
            $invoiceController->insertTrainingInInvoice($invoiceTraining);
        }
        $totalCart = $_SESSION['total'];
        $this->deleteCart();
        return $this->twig->render('Cart/recap.html.twig', [
            'total' => $totalCart,
            'invoice' => $invoiceDetails,
            'user' => $user
        ]);
    }

    public function addToCart()
    {
        $trainingManager = new TrainingManager();
        $training = $trainingManager->selectOneById($_GET['add_to_cart']);
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
        if (!empty($_SESSION['cart'][$training['id']])) {
            $_SESSION['cart'][$training['id']]++;
        } else {
            $_SESSION['cart'][$training['id']] = 1;
        }
    }

    public function getCartInfos()
    {
        if (isset($_GET['add_to_cart'])) {
            $this->addToCart();
        }
        $trainingManager = new TrainingManager();
        if (isset($_SESSION['cart'])) {
            $cart = $_SESSION['cart'];
            $cartInfos = [];
            foreach ($cart as $trainingId => $qty) {
                $training = $trainingManager->selectOneById($trainingId);
                $training['qty'] = $qty;
                $cartInfos[] = $training;
            }
            return $cartInfos;
        }
    }

    public function deleteCart()
    {
        // We destroy the session variable
        unset($_SESSION["cart"]);
    }
}
