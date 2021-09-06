<?php
/*
 * Copyright (C) 2015 Maarch
 *
 * This file is part of bundle organization.
 *
 * Bundle organization is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Bundle organization is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with bundle organization.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace presentation\maarchRM\Presenter\medona;

/**
 * Bundle organization type presenter
 *
 * @package Organization
 */
class controlAuthority
{
    use \presentation\maarchRM\Presenter\exceptions\exceptionTrait;
    public $view;
    public $sdoFactory;

    /**
     * __construct
     *
     * @param \dependency\html\Document   $view       A new ready-to-use empty view
     * @param \dependency\json\JsonObject $jsonObject The json base object
     * @param \dependency\sdo\Factory     $sdoFactory The Sdo Factory for data access
     */
    public function __construct(
        \dependency\html\Document $view,
        \dependency\json\JsonObject $jsonObject,
        \dependency\sdo\Factory $sdoFactory)
    {
        $this->view = $view;

        $this->json = $jsonObject;
        $this->json->status = true;

        $this->sdoFactory = $sdoFactory;

        $this->translator = $this->view->translator;
        $this->translator->setCatalog('medona/messages');

    }

    /**
     * index
     * @param object $controlAuthorities a relation control authority / originator
     *
     * @return view
     */
    public function index($controlAuthorities)
    {
        $originatorsOrg = \laabs::callService('organization/organization/readByrole_role_', 'originator');
        $controlAuthoritiesOrg = \laabs::callService('organization/organization/readByrole_role_', 'controlAuthority');

        $ownerOriginatorOrgs = [];
        $ownerControlAuthority = [];

        foreach ($originatorsOrg as $originator) {
            if (!isset($ownerOriginatorOrgs[(string) $originator->ownerOrgId])) {
                $orgObject = \laabs::callService(
                    'organization/organization/read_orgId_',
                    (string) $originator->ownerOrgId
                );

                $ownerOriginatorOrgs[(string) $orgObject->orgId] = new \stdClass();
                $ownerOriginatorOrgs[(string) $orgObject->orgId]->displayName = $orgObject->displayName;
                $ownerOriginatorOrgs[(string) $orgObject->orgId]->originators = [];
            }

            $ownerOriginatorOrgs[(string) $orgObject->orgId]->originators[] = $originator;
        }
        foreach ($controlAuthoritiesOrg as $controlAuthority) {
            if (!isset($ownerControlAuthority[(string) $controlAuthority->ownerOrgId])) {
                $orgObject = \laabs::callService(
                    'organization/organization/read_orgId_',
                    (string) $controlAuthority->ownerOrgId
                );
                $ownerControlAuthority[(string) $orgObject->orgId] = new \stdClass();
                $ownerControlAuthority[(string) $orgObject->orgId]->displayName = $orgObject->displayName;
                $ownerControlAuthority[(string) $orgObject->orgId]->depositors = [];
            }

            $ownerControlAuthority[(string) $orgObject->orgId]->depositors[] = $controlAuthority ;
        }
        $controlAuthorityList = [];

        if ($controlAuthorities) {
            foreach ($controlAuthorities as $controlAuthority) {
                $controlAuthorityList[$controlAuthority->originatorOrgUnitId] = new \stdClass();
                $controlAuthorityList[$controlAuthority->originatorOrgUnitId]->controlAuthorityOrgUnitId =
                    \laabs::callService(
                        'organization/organization/read_orgId_',
                        (string)$controlAuthority->controlAuthorityOrgUnitId
                    );
                if ($controlAuthority->originatorOrgUnitId != '*') {
                    $controlAuthorityList[$controlAuthority->originatorOrgUnitId]->originatorOrgUnitId =
                        \laabs::callService(
                            'organization/organization/read_orgId_',
                            (string)$controlAuthority->originatorOrgUnitId
                        );
                    if (isset(
                        $controlAuthorityList[$controlAuthority->originatorOrgUnitId]->originatorOrgUnitId->ownerOrgId
                    )) {
                        $controlAuthorityList[$controlAuthority->originatorOrgUnitId]->originatorOrgUnitId->owner =
                            \laabs::callService(
                                'organization/organization/read_orgId_',
                                (string)$controlAuthorityList[$controlAuthority->originatorOrgUnitId]->originatorOrgUnitId->ownerOrgId
                            );
                    }
                } else {
                    $controlAuthorityList[$controlAuthority->originatorOrgUnitId]->originatorOrgUnitId = '*';
                }
            }
        }

        $this->view->addContentFile("medona/controlAuthority/index.html");

        $dataTable = $this->view->getElementsByClass("dataTable")->item(0)->plugin['dataTable'];
        $dataTable->setPaginationType("full_numbers");
        $dataTable->setUnsortableColumns(2);

        $this->view->translate();
        $organizationsOriginator = \laabs::callService('organization/organization/readTodisplay', true, true, "");

        foreach ($organizationsOriginator as $orgOrignator) {
            if (!$orgOrignator->isOrgUnit) {
                foreach ($organizationsOriginator as $originator) {
                    if ($orgOrignator->orgId == $originator->ownerOrgId) {
                        $originator->ownerOrgName = $orgOrignator->displayName;
                    }
                }
            }
        }

        $this->view->setSource("organizationsOriginator", $organizationsOriginator);
        $this->view->setSource("controlAuthorityList", $controlAuthorityList);
        $this->view->setSource("ownerOriginatorOrgs", $ownerOriginatorOrgs);
        $this->view->setSource("ownerControlAuthorityOrgs", $ownerControlAuthority);

        $this->view->merge();

        return $this->view->saveHtml();
    }

    // JSON
    /*
     * Serializer JSON for seting create method
     *
     * @return object JSON object with a status and message parameters
     */
    public function create()
    {
        $this->json->message = "Relation created";
        $this->json->message = $this->translator->getText($this->json->message);

        return $this->json->save();
    }

    /*
     * Serializer JSON for seting update method
     *
     * @return object JSON object with a status and message parameters
     */
    public function update()
    {
        $this->json->message = "Relation updated";
        $this->json->message = $this->translator->getText($this->json->message);

        return $this->json->save();
    }

    /*
     * Serializer JSON for seting update method
     *
     * @return object JSON object with a status and message parameters
     */
    public function delete()
    {
        $this->json->message = "Relation deleted";
        $this->json->message = $this->translator->getText($this->json->message);

        return $this->json->save();
    }
}
