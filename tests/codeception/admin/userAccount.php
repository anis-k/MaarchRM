<?php

class userAccountCest
{
    private $token = "RJpzB36bmR%2Biuz%2FaHN9Zl9PDn8tZEvA%2B3Mr7PAM%2FSYnOlW%2B4i0w1AsElIXeUVxYp2QT6l7HTxd7pYwCdYkm16gi77Rhf0cy8hcfQzbFNnCdqGQrqTKXGmqTN7P1isQDSIL6Kr82aL4pKFuAjmQ%3D%3D";

    public function _before(\ApiTester $I)
    {
        $I->haveHttpHeader('Accept', 'application/json');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('User-Agent', 'service');
        $I->haveHttpHeader('Cookie', "LAABS-AUTH=".$this->token);
    }

    public function getUserList(ApiTester $I)
    {
        $I->sendGET('/auth/userAccount/userList');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }

    public function getUserListByQuery(ApiTester $I)
    {
        $I->sendGET('/auth/userAccount/query/bern');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();

        $I->seeResponseContainsJson([
            "accountId" =>  "bblier",
            "accountName" =>  "bblier",
            "displayName" =>  "Bernard BLIER",
            "emailAddress" =>  "info@maarch.org",
            "accountType" =>  "user",
            "enabled" =>  true,
            "password" =>  "fffd2272074225feae229658e248b81529639e6199051abdeb49b6ed60adf13d",
            "passwordLastChange" =>  null,
            "passwordChangeRequired" =>  false,
            "locked" =>  false,
            "lockDate" =>  null,
            "badPasswordCount" =>  0,
            "lastLogin" =>  "2019-03-15T13:26:01,022593Z",
            "lastIp" =>  "127.0.0.1",
            "replacingUserAccountId" =>  null,
            "firstName" =>  "Bernard",
            "lastName" =>  "BLIER",
            "title" =>  "M.",
            "salt" =>  null,
            "tokenDate" =>  null,
            "authentication" =>  null,
            "preferences" =>  null
        ]);
    }

    public function getNewUser(ApiTester $I)
    {
        $I->sendGET('/auth/userAccount/new');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();

        $I->seeResponseContainsJson([
            "accountId" =>  null,
            "accountName" =>  null,
            "displayName" =>  null,
            "emailAddress" =>  null,
            "accountType" =>  null,
            "enabled" =>  true,
            "password" =>  null,
            "passwordLastChange" =>  null,
            "passwordChangeRequired" =>  false,
            "locked" =>  false,
            "lockDate" =>  null,
            "badPasswordCount" =>  0,
            "lastLogin" =>  null,
            "lastIp" =>  null,
            "replacingUserAccountId" =>  null,
            "firstName" =>  null,
            "lastName" =>  null,
            "title" =>  null,
            "salt" =>  null,
            "tokenDate" =>  null,
            "authentication" =>  null,
            "preferences" =>  null
        ]);
    }

    public function getUserByAccountId(ApiTester $I)
    {
        $I->sendGET('/auth/userAccount/bblier');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();

        $I->seeResponseContainsJson([
            "accountId" =>  "bblier",
            "accountName" =>  "bblier",
            "accountType" =>  "user",
            "emailAddress" =>  "info@maarch.org",
            "passwordLastChange" =>  null,
            "passwordChangeRequired" =>  false,
            "locked" =>  false,
            "enabled" =>  true,
            "badPasswordCount" =>  0,
            "lastLogin" =>  "2019-03-15T13:26:01,022593Z",
            "lastIp" =>  "127.0.0.1",
            "replacingUserAccountId" =>  null,
            "roles" =>  [
                "roleId" =>  "CORRESPONDANT_ARCHIVES",
                "roleName" =>  "Correspondant d'archives"
            ],
            "organizations" =>  null,
            "firstName" =>  "Bernard",
            "lastName" =>  "BLIER",
            "displayName" =>  "Bernard BLIER",
            "title" =>  "M."
        ]);
    }

    public function getListPrivilege(ApiTester $I)
    {
        $I->sendGET('/auth/userAccount/bblier/Privileges?userAccountId=bblier');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();

        $I->seeResponseContainsJson([
            "app/*",
            "adminArchive/*",
            "adminFunc/AdminArchivalProfileAccess",
            "adminFunc/adminAuthorization",
            "adminFunc/adminOrganization",
            "adminFunc/adminOrgContact",
            "adminFunc/adminOrgUser",
            "adminFunc/adminUseraccount",
            "archiveDeposit/*",
            "archiveManagement/*",
            "destruction/*",
            "journal/lifeCycleJournal",
            "journal/searchLogArchive"
        ]);
    }

    public function createUser(ApiTester $I)
    {
        $I->sendPOST('/auth/userAccount',
            [
                'userAccount' => [
                    'accountName' => 'toto',
                    'password' => 'maarch',
                    'emailAddress' =>  'info@maarch.org',
                    'passwordLastChange' => null,
                    'passwordChangeRequired' =>  false,
                    'locked' =>  false,
                    'enabled' =>  true,
                    'badPasswordCount' => 0,
                    'lastLogin' => null,
                    'lastIp' => null,
                    'replacingUserAccountId' => null,
                    'roles' =>
                        [
                            'id' =>  'UTILISATEUR'
                        ],
                    'organizations' =>
                        [
                            'id' =>  'CTBLE'
                        ],
                    'firstName' =>  'tata',
                    'lastName' =>  'toto',
                    'displayName' =>  'toto',
                    'title' =>  'M.'
                ]
            ]);

        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }

    public function updateDisableUser(ApiTester $I)
    {
        $I->sendPUT('/auth/userAccount/disable/bblier');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseContains('true');
    }

    public function updateEnableUser(ApiTester $I)
    {
        $I->sendPUT('/auth/userAccount/enable/bblier');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseContains('true');
    }

    public function updateLockUser(ApiTester $I)
    {
        $I->sendPUT('/auth/userAccount/lock/bblier');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseContains('true');
    }

    public function updateUnlockUser(ApiTester $I)
    {
        $I->sendPUT('/auth/userAccount/unlock/bblier');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseContains('true');
    }

    public function updatePasswordUser(ApiTester $I)
    {
        $I->sendPUT('/auth/userAccount/password/bblier', [
            'newPassword' => '$maarchToto2019',
            'oldPassword' => 'maarch'
        ]);

        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseContains('true');
    }
}