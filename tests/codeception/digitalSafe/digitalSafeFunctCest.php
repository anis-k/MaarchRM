<?php

namespace tests\digitalSafe;

class DigitalSafeCest
{
    private $token = "RJpzB36bmR%2Biuz%2FaHN9Zl9PDn8tZEs4mzsz9ORUXZpbMim%2FilUMpE9FzYG3TW0Eii0Oy1PaFyJ35aBqcMU3gvAq4v0ZY0Z%2Fr0cPVzbAaymd1UEnsAe3MjqGLt7BxvxiHJQ%3D%3D";
    private $archiveId;

    public function _before(\ApiTester $I)
    {
        $I->haveHttpHeader('Accept', 'application/json');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('User-Agent', 'service');
        $I->haveHttpHeader('Cookie', "LAABS-AUTH=".$this->token);
    }

    public function createReceive(\ApiTester $I)
    {
        $I->sendPOST(
            '/digitalSafe/digitalSafe/ACME/RH',
            [
                "originatorArchiveId" => "DOC_109283.pdf",
                "descriptionObject" => [
                    "nb pages" => "3"
                ],
                "digitalResources" => [
                    [
                        "handler" => "iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAMAAABEpIrGAAAAAXNSR0IArs4c6QAAAJxQTFRFAAAA/0BA/z
                        w8/84A/9EA/zY2/zU1/zQ0/84A/zQ0/80A/8wA/zMz/zMz/8wA/zQ0/80A/zQ0/zQ0/zQ0/8wA/zMz/8wA/80A/zQ0/8wA/8
                        0A/zMz/zYz/zox/z0w/z8v/0Iv/0Qu/1Ep/1Mp/1Qo/1Uo/1co/1om/1on/18l/5AU/5AV/5IU/5MT/70F/8EE/8kB/8oB/8
                        wA/8wBQncGQAAAABt0Uk5TAAQRGhwmP0BOWWtuc4ybpra/wNnZ7vLz+/z9y8MvZgAAAPdJREFUOMu1k9cawiAMhdE6aquIWr
                        tw7z3S9383BfwoBsUrz104P5CEQMgHDTqNCnGJcz7stqpO4Km01647AaE/Av30B0Cqre7QAmoBi5IkYoEyKs3OwLQ9msFLnH
                        v2sX4MUAKxj/0wBxOAPET7TV8AkL+d4cWAAYjNPKgyiuNqPNsfiokAgBr1qfwvW9WdzVwCWU0Dgdq/LVssFwINMBkfOQKYBi
                        IZrzAQaSCR8bj0R3Ih+Q5MEGBdsUZXWEmeUZK4zOUNlYkatbgCapTZ6unudFcR/f5YUm+PhZ5bKPcdAyP80DVyz/N9eyiNoc
                        2o9/E76LE36iMP4IhOCszyPTIAAAAASUVORK5CYII="
                    ]
                ]
            ]
        );

        $I->seeResponseIsJson();
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson(array('operationResult' => true));
        $this->archiveId = $I->grabDataFromResponseByJsonPath('$.archiveId')[0];
    }

    public function readConsultation(\ApiTester $I)
    {
        $I->sendGET('/digitalSafe/digitalSafe/ACME/RH/' . $this->archiveId);
        $I->seeResponseIsJson();
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson(array('operationResult' => true));
    }

    public function readRetrieve(\ApiTester $I)
    {
        $I->sendGET('/digitalSafe/digitalSafe/ACME/RH/' . $this->archiveId . '/Metadata');
        $I->seeResponseIsJson();
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson(array('operationResult' => true));
    }

    public function readIntegrity(\ApiTester $I)
    {
        $I->sendGET('/digitalSafe/digitalSafe/ACME/RH/' . $this->archiveId . '/Integritycheck');
        $I->seeResponseIsJson();
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson(array('operationResult' => true));
    }

    public function readCounting(\ApiTester $I)
    {
        $I->sendGET('/digitalSafe/digitalSafe/ACME/RH/count');
        $I->seeResponseIsJson();
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson(array('operationResult' => true));
    }

    public function readListing(\ApiTester $I)
    {
        $I->sendGET('/digitalSafe/digitalSafe/ACME/RH');
        $I->seeResponseIsJson();
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson(array('operationResult' => true));
    }

    public function deleteDestruct(\ApiTester $I)
    {
        $I->sendDELETE('/digitalSafe/digitalSafe/ACME/RH/' . $this->archiveId);
        $I->seeResponseIsJson();
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson(array('operationResult' => true));
    }

    public function readJournal(\ApiTester $I)
    {
        $I->sendGET('/digitalSafe/digitalSafe/ACME/events');
        $I->seeResponseIsJson();
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson(array('operationResult' => true));
    }
}