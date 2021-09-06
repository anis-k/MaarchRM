<?php

namespace presentation\maarchRM\UserStory\app;

interface importRefInterface
{
    /**
     * @requires [import/archivalProfile, import/descriptionField, import/organization, import/retentionRule, import/role, import/serviceAccount, import/userAccount]
     *
     * @return importExport/Import/home
     */
    public function readImport();
}
