<?php

namespace App\Controller;

use App\Model\InvoiceManager;
use App\Model\UserManager;

class InvoiceController extends AbstractController
{
    /**
     * List invoices
     */
    public function index(): string
    {
        $invoiceManager = new InvoiceManager();
        // i stand for 'invoice", an alias for the invoice table
        $invoices = $invoiceManager->selectAll('i.created_at');

        return $this->twig->render('Invoice/index.html.twig', ['invoices' => $invoices]);
    }

    /**
     * Show informations for a specific invoice
     */
    public function show(int $id): string
    {
        $invoiceManager = new InvoiceManager();
        $invoice = $invoiceManager->selectOneById($id);
        $userManager = new UserManager();
        $user = $userManager->selectOneById($_SESSION['user']['id']);
        $trainings = $invoiceManager->selectTrainingsInInvoice($id);

        return $this->twig->render('Invoice/show.html.twig', [
            'invoice'   => $invoice,
            'user'      => $user,
            'trainings' => $trainings,
        ]);
    }

    public function showInvoiceInRecap(int $id): array
    {
        $invoiceManager = new InvoiceManager();
        return $invoiceManager->selectOneById($id);
    }

    /**
     * Edit a specific invoice
     */
    public function edit(int $id): ?string
    {
        $errors = [];
        $invoiceManager = new InvoiceManager();
        $invoice = $invoiceManager->selectOneById($id);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // clean $_POST data
            $invoiceUpdate = array_map('trim', $_POST);

            $errors = $this->validateInvoice($_POST);
            if (empty($errors)) {
                // if validation is ok, update and redirection
                $invoiceManager->update($invoiceUpdate);

                header('Location: /invoices/show?id=' . $id);

                // we are redirecting so we don't want any content rendered
                return null;
            }
        }

        return $this->twig->render('Invoice/edit.html.twig', [
            'invoice' => $invoice,
            'errors' => $errors,
        ]);
    }

    /**
     * Add a new invoice
     */
    // public function add(): ?string
    // {
    //     $errors = [];
    //     if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    //         // clean $_POST data
    //         $invoice = array_map('trim', $_POST);
    //         $errors = $this->validateInvoice($_POST);
    //         if (empty($errors)) {
    //             // if validation is ok, insert and redirection
    //             $invoiceManager = new InvoiceManager();
    //             $id = $invoiceManager->insert($invoice);

    //             header('Location:/invoices/show?id=' . $id);
    //             return null;
    //         }
    //     }
    //     return $this->twig->render('Invoice/add.html.twig', [
    //         'errors' => $errors,
    //     ]);
    // }

    public function generateInvoice()
    {
        $invoiceManager = new InvoiceManager();
        return $invoiceManager->generateInvoice($_SESSION);
    }

    // function called to automatically fill invoice_training table
    // when an invoice is generated
    public function insertTrainingInInvoice(array $invoiceTraining): ?int
    {
        $invoiceManager = new InvoiceManager();
        return $invoiceManager->insertTrainingInInvoice($invoiceTraining);
    }

    /**
     * Delete a specific invoice
     */
    public function delete(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = trim($_POST['id']);
            $invoiceManager = new InvoiceManager();
            $invoiceManager->delete((int)$id);

            header('Location:/invoices');
        }
    }

    // function called to automatically delete invoice_training rows
    // when an invoice or a booked training is deleted
    public function deleteTrainingInInvoice(int $invoiceId, int $trainingId): void
    {
        $invoiceManager = new InvoiceManager();
        $invoiceManager->deleteTrainingInInvoice((int) $invoiceId, (int) $trainingId);
    }

    /**
     * Checks for invoice validation
     */
    public function validateInvoice($post): array
    {
        $errors = [];
        // user id and total should be numeric
        if (empty($post['userid']) || !is_numeric($post['userid'])) {
            $errors[] = 'Veuillez indiquer un étudiant par son ID';
        }
        if (empty($post['total']) || !is_numeric($post['total'])) {
            $errors[] = 'Veuillez indiquer un montant total pour cette facture';
        }
        return $errors;
    }
}
