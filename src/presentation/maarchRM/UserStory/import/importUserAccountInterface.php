<?php

namespace presentation\maarchRM\UserStory\import;

interface importUserAccountInterface
{
    /**
     * Import a csv file with user account informations
     *
     * @param resource  $data      Data base64 encoded or not in proper format
     * @param boolean   $isReset   Reset tables or not
     *
     * @uses auth/userAccount/createImport
     *
     */
    public function createImportUseraccounts($data, $isReset = false);
}
