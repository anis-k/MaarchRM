<?php

namespace presentation\maarchRM\UserStory\import;

interface importServiceAccountInterface
{
    /**
     * Import a csv file with service account informations
     *
     * @param resource  $data      Data base64 encoded or not in proper format
     * @param boolean   $isReset   Reset tables or not
     *
     * @uses auth/serviceAccount/createImport
     *
     */
    public function createImportServiceaccounts($data, $isReset = false);
}
