<?php

namespace App\Controller;

use App\Model\LanguageTrainingManager;

class LanguageTrainingController extends AbstractController
{
    /**
     * Only when adding a new language
     */
    public function add(array $language): ?int
    {
            $languageTraining = new LanguageTrainingManager();
            return $languageTraining->insertLanguageTraining($language);
    }

    /**
     * Only when deleting a specific language
     */
    public function delete(int $languageId, int $trainingId): void
    {
            $languageTraining = new LanguageTrainingManager();
            $languageTraining->deleteLanguageTraining((int)$languageId, (int)$trainingId);
    }

    // Since it is an intermediate table, there is no need to create
    // a read or update function to this point.
}
