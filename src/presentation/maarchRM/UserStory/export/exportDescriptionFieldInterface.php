<?php

namespace presentation\maarchRM\UserStory\export;

interface exportDescriptionFieldInterface
{
    /**
     * Get description fields infos
     *
     * @uses recordsManagement/descriptionField/readExport
     *
     * @return importExport/Export/listCsv
     */
    public function readExportDescriptionfields($limit = null, $ref = null);
}
