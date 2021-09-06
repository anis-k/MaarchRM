<?php

namespace presentation\maarchRM\UserStory\import;

interface importDescriptionFieldInterface
{
    /**
     * Import a csv file with description field informations
     *
     * @param resource  $data      Data base64 encoded or not in proper format
     * @param boolean   $isReset   Reset tables or not
     *
     * @uses recordsManagement/descriptionField/createImport
     *
     */
    public function createImportDescriptionfields($data, $isReset = false);
}
