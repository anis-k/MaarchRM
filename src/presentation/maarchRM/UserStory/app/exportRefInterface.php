<?php

namespace presentation\maarchRM\UserStory\app;

interface exportRefInterface
{
    /**
     * @requires [export/archivalProfile, export/descriptionField, export/organization, export/retentionRule, export/role, export/serviceAccount, export/userAccount]
     *
     * @return importExport/Export/home
     */
    public function readExport();
}
