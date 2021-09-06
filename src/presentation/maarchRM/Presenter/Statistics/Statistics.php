<?php

/*
 * Copyright (C) 2020 Maarch
 *
 * This file is part of bundle Statistics.
 *
 * Bundle Statistics is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Bundle Statistics is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with bundle Statistics.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace presentation\maarchRM\Presenter\Statistics;

/**
 * Serializer html for access code
 *
 * @package Statistics
 * @author  Jérôme Boucher <jerome.boucher@maarch.com>
 */
class Statistics
{
    public $view;

    protected $json;

    protected $translator;

    /**
     * Constuctor
     * @param \dependency\html\Document                    $view       The view
     * @param \dependency\json\JsonObject                  $json       The json base object
     * @param \dependency\localisation\TranslatorInterface $translator The translator object
     */
    public function __construct(
        \dependency\html\Document $view,
        \dependency\json\JsonObject $json,
        \dependency\localisation\TranslatorInterface $translator
    ) {

        $this->view = $view;

        $this->json = $json;
        $this->json->status = true;

        $this->translator = $translator;
        $this->translator->setCatalog('Statistics/Statistics');
    }

    /**
     * Set default options to datatable
     * @param  datatable $dataTable       the view dataTable
     * @param  array     $columnsToExport List of columns to export
     *
     * @return datatable                  the view dataTable
     */
    protected function setDatatableOptions($dataTable, $columnsToExport)
    {
        $dataTable->setPaginationType("full_numbers");
        $dataTable->setColumnsToExport($columnsToExport);
        $titleExport = $this->view->translator->getText(
            "Export to ",
            false,
            "recordsManagement/messages"
        );

        $dataTable->setExport(
            [
                [
                    "exportType" => "csv",
                    "text" => "<i class='fa fa-download'></i> CSV",
                    "titleAttr" => $titleExport . "CSV"
                ],
                [
                    "exportType" => "pdf",
                    "text" => "<i class='fa fa-download'></i> PDF",
                    "titleAttr" => $titleExport . "PDF"
                ]
            ],
            false
        );
        return $dataTable;
    }

    /**
     * Get default statistics view
     * @param  array $statistics associative array of statistics and their values
     *
     * @return view              The view
     */
    public function index($statistics)
    {
        $isTransactionnal = false;

        $this->view->addContentFile("Statistics/index.html");

        $descriptionFragment = $this->view->createDocumentFragment();
        $description = $this->view->getElementById("statsResults");
        if (\laabs::configuration('medona')['transaction']) {
            $isTransactionnal = true;
        }
        
        $descriptionFragment->appendHtmlFile("Statistics/defaultResults.html");

        $description = $this->view->getElementById("statsResults");
        $description->appendChild($descriptionFragment);

        $dataTable = $this->view->getElementsByClass("dataTable")->item(0)->plugin['dataTable'];
        $dataTable = $this->setDatatableOptions($dataTable, [0, 1, 2]);

        $this->view->setSource('isTransactionnal', $isTransactionnal);
        $this->view->setSource('statistics', $statistics);
        $this->view->merge();
        $this->view->translate();

        return $this->view->saveHtml();
    }

    /**
     * Rearrange the array
     * @param  array  $array    array to rearrange
     * @param  string $statType Filter
     *
     * @return array            rearranged array
     */
    protected function rearrangeArray($array, $statType)
    {
        $results = [];
        foreach ($array as $key => $statistic) {
            if (array_key_exists('originatingorg', $statistic)) {
                $results[$statistic['originatingorg']] = $statistic[$statType];
            } else {
                $results[$statistic['archivalprofile']] = $statistic[$statType];
            }
        }

        return $results;
    }

    /**
     * Retrieve statistics
     * @param  array  $statistics array of statistics
     * @param  string $operation
     * @param  string $filter
     *
     * @return array              rearranged array
     */
    public function retrieveStats($statistics, $operation, $filter)
    {
        if (!$operation && !$filter) {
            $isTransactionnal = false;
            if (\laabs::configuration('medona')['transaction']) {
                $isTransactionnal = true;
            }

            $this->view->addContentFile("Statistics/defaultResults.html");

            $dataTable = $this->view->getElementsByClass("dataTable")->item(0)->plugin['dataTable'];
            $dataTable = $this->setDatatableOptions($dataTable, [0, 1, 2]);

            $this->view->setSource('isTransactionnal', $isTransactionnal);
            $this->view->setSource('statistics', $statistics);
            $this->view->merge();
            $this->view->translate();

            return $this->view->saveHtml();
        }
        $loopArray = [];

        $statisticsNames = [
            'deposit' => 'groupedDepositMemory',
            'deleted' => 'deletedGroupedMemory',
            'conserved' => 'groupedArchive',
            'restituted' => 'restitutedGroupedMemory',
            'transfered' => 'transferedGroupedMemory',
            'communicated' => 'communicatedGroupedMemory'
        ];

        $sizes = $this->rearrangeArray($statistics[$statisticsNames[$operation] . 'Size'], 'sum');
        $counts = $this->rearrangeArray($statistics[$statisticsNames[$operation] . 'Count'], 'count');

        $condensedStats = [];

        foreach ($sizes as $type => $value) {
            $condensedStats[$type]['sum'] = isset($value) ? $value : null;
            $condensedStats[$type]['count'] = isset($counts[$type]) ? $counts[$type]: null;
        }

        $this->view->addContentFile("Statistics/orderedResults.html");

        $dataTable = $this->view->getElementsByClass("dataTable")->item(0)->plugin['dataTable'];
        $dataTable = $this->setDatatableOptions($dataTable, [0, 1, 2]);
        $dataTable->setSorting(array(array(0, 'asc')));

        $this->view->setSource('unit', $statistics['unit']);
        $this->view->setSource('filter', $filter);
        $this->view->setSource('condensedStats', $condensedStats);
        $this->view->merge();
        $this->view->translate();

        return $this->view->saveHtml();
    }
}
