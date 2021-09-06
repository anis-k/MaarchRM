# Migration 2.6 vers 2.7
## Configuration

## Ajout dans la configuration

Dans la section [recordsManagement], ajout dee la directive `archiveIdGenerator` qui permet de configurer la cotation automatique lors d'un versement dans l'application.

Dans la section [medona], ajout de la directive `packageConnectors` qui permet la configuration de connecteurs pour faciliter le versement de paquets externes au format incomplet.

Dans la section nouvellement créee [dependency.timestamp], la directive `pathToOpenSSL` a été ajoutée pour faciliter la prise en charge sur Windows :

```
pathToOpenSSL="C:\Program Files\OpenSSL-Win64\bin\openssl"
```

### Ajout d'un droit utilisateur

Si le mode transactionnel est activé, ajout du droit de traiter manuellement les communications.
Pour l'ajouter :

```
        {
            'serviceURI' : 'medona/ArchiveDelivery/updateProcessBatch',
            'description' : 'Traiter les communications'
        },
```

dans la directive `servicePrivileges` présente dans la section [auth] de votre fichier de configuration.

De fait, un point de menu a été ajouté sur l'écran d'Echange pour traiter manuellement les communication, il faut ajouter dans la directive `menu` :

```
        {
            'label' : 'Communications à finaliser',
            'href'  : '/delivery/Process'
        },
```

c'est un `submenu` présent sous le label `Communication` de la section [medona] de votre fichier de configuration.

### Ajout du bundle Statistiques et configuration de session dans virtual host

Afin d'accéder aux fonctionnalités relatives aux statistiques, le bundle `Statistics` doit être ajoutée à l'instance dans le fichier vhost.conf :

```
SetEnv LAABS_BUNDLES audit;auth;batchProcessing;contact;digitalResource;lifeCycle;organization;recordsManagement;filePlan;medona;mades;digitalSafe;Statistics
```

Modifications liées à la configuration de la session :
```
SetEnv LAABS_SESSION_START Off
#SetEnv LAABS_SECURE_COOKIE On
```

## Ajout d'un droit de compte de Service

Ajout d'une fonctionnalité permettant de récupérer directement le contenu d'une ressource d'archive, il faut ajouter :

```
    {
        'serviceURI' : 'recordsManagement/archive/read_archiveId_Digitalresource_resId_Contents',
        'description' : 'Récupérer directement le contenu d\'une ressource d\'archive'
    }
```

dans la directive `servicePrivileges` présente dans la section [auth] de votre fichier de configuration.

### Modification de configuration

Dans la section [presentation.maarchRM], la directive `maxResults` livrée par défaut est désormais à 500.
Dans la section [auth], modification de la configuration du CSRF :

```
csrfConfig = '{
    "cookieName" : "Csrf",
    "tokenLength" : 32
}'
```

Dans la section [recordsManagement], modification de la configuration des schémas de description :

```
descriptionSchemes = "{
    'extension' : {
        'label' : 'extension',
        'type' : 'json',
        'uri' : '%laabsDirectory%/data/maarchRM/samples/sample.json'
    },
    'log' : {
        'label' : 'log',
        'type' : 'php',
        'uri' : 'recordsManagement/log',
        'search': 'recordsManagement/log'
    }
}"
```

## Service horodatage tiers de test

Dans la section [lifeCycle] du fichier de configuration, si la directive `chainWithTimestamp` est activé, vous pouvez choisir votre service d'horodatage tiers dans la section nouvellement crée [dependency.timestamp] parmis les 3 suivants :

```
; The URL of the TSA provider
; Somme open and free TSA test services :
; tsaUrl=http://zeitstempel.dfn.de
; tsaUrl=http://timestamp.entrust.net/TSS/RFC3161sha2TS
; tsaUrl=http://time.certum.pl
```

### Mise à jour des fichiers de signature DROID

Mise à jour des fichiers signature et container permettant la détection du format des fichiers

```
signatureFile = "%laabsDirectory%/data/maarchRM/droidSignatureFiles/DROID_SignatureFile_V97.xml"
containerSignatureFile = "%laabsDirectory%/data/maarchRM/droidSignatureFiles/container-signature-20201001.xml"
```

## Schéma SQL

Voir le fichier spécifique

    laabs/data/maarchRM/sql/pgsql/migrationV2.6_V2.7.sql


# Migration 2.5 vers 2.6
## Configuration
### Lien de téléchargement d'une ressource
Cette configuration facultative permet au moment de la consultation, de recevoir une uri vers une ressource au lieu du contenu binaire. 

À renseigner dans [recordsManagement] : 
```
exportPath = "%laabsDirectory%/web/tmp"
```

## Configuration des instances publiées (hôte(s) virtuel(s) http et scripts en ligne de commande)

### Externalisation du bundle `digitalSafe`
Le bundle `digitalSafe` qui présente les fonctions relatives à l'usage du produit 
comme composant de coffre-fort numérique, ajouté en V2.5, a été déplacé dans une nouvelle 
extension du même nom.

Les instances ne doivent plus faire référence à ce bundle si l'extension `digitalSafe` n'est pas installée.

Socle SAE seul :
```
SetEnv LAABS_BUNDLES audit;auth;batchProcessing;contact;digitalResource;lifeCycle;organization;recordsManagement;filePlan;medona;mades
```

Avec l'usage CCFN :
```
SetEnv LAABS_BUNDLES audit;auth;batchProcessing;contact;digitalResource;lifeCycle;organization;recordsManagement;filePlan;medona;mades;digitalSafe
SetEnv LAABS_EXTENSIONS digitalSafe
```

### Nouvelle dépendance technique CSV
Les fonctions d'import et d'export de référentiel utilisent une nouvelle dépendance qui 
gère les conversions en CSV. Ladépendance `csv` doit être ajoutée à l'instance :

```
SetEnv LAABS_DEPENDENCIES repository;xml;html;localisation;datasource;sdo;json;fileSystem;notification;PDF;csrf;timestamp;csv
```

## Configuration 
### Protection CSRF
Il faut ajouter des routes en liste blanche pour la protection contre les requêtes en Cross-Site Forgery:

```
csrfWhiteList = "['user/login', 'user/password', 'user/prompt', 'user/logout', 'user/generateResetToken']"
```

### Mise à jour des fichiers de signature DROID
Mise à jour des fichiers signature et container permettant la détection du format des fichiers
```
signatureFile = "%laabsDirectory%/data/maarchRM/droidSignatureFiles/DROID_SignatureFile_V96.xml"
containerSignatureFile = "%laabsDirectory%/data/maarchRM/droidSignatureFiles/container-signature-20200121.xml"
```

## Schéma SQL

Voir le fichier spécifique

    laabs/data/maarchRM/sql/pgsql/migrationV2.5_V2.6.sql

___

# Migration 2.4 vers 2.5

## Présentation et fonctionnalités orientées "archives publiques"

### Situation dans les versions antérieures

Dans la section `[presentation.maarchRM]`, la directive `publicArchives` définissait les comportements suivants :
  * dans la gestion des utilisateurs, un seul rôle autorisé par utilisateur
  * dans la gestion des rôles, pas de gestion des utilisateurs rattachés
  * dans l'organigramme fonctionnel, pas de gestion des accès aux profils d'archive (géré par accords de versement uniquement)
  * dans la gestion des règles de communicabilité, pas de suppression ni de modification (règles issues du référentiel contrôlé par les Archives de France)
  * dans la gestion des règles de conservation, pas de gestion du sort final

La directive `menu` n'intégrait pas le point de menu vers la gestion du dictionnaire de données (champs de description);

Dans la section `[auth]`, la directive `blackListedUserStories` inhibait les droits sur les fonctions suivantes :
  * versement direct dans l'Archive
  * gestion du dictionnaire de données

### Nouvelles configurations

Ces directives sont utilisables à la place de la directive existante `publicArchives` pour gérer plus finement les fonctionnalités correspondantes :

Dans la section `[presentation.maarchRM]`, ajout du paramètre maxResults de type nombre (valeur par défaut à 200), qui permet de définir le nombre maximum d'archives retournées lors d'une recherche dans l'application.

Dans la section `[auth]`, ajout de la directive `restrictUserRoles`, de type booléen.
Si activée, chaque utilisateur ne peut avoir qu'un rôle et la gestion des rôles ne permet plus d'ajouter ou retirer des utilisateurs.

Dans la section `[recordsManagement]`, la valeur de directive `archivalProfileType` définit désormais le comportement suivant :
  * `1` indique des profils de versement de type MEDONA et inhibe la gestion des accès aux profils dans l'organigramme
  * `2` indique des profils d'archive avec description des métadonnées et règles de gestion, puis active la gestion des accès aux profils dans l'organigramme
  * `3` indique des profils mixtes (MEDONA et description interne) et active la gestion des accès aux profils dans l'organigramme

## Branchement des schémas de description

Dans la section `[recordsManagement]`, la directive `descriptionSchemes` permet de définir
les schémas de description en lieu et place des entrées de la table `recordsManagement.descriptionClass`
qui doit être supprimée.

A chaque identifiant de schéma de description (éventuellement précédemment inscrit dans la table) correspond un élément
de configuration qui fournit :

  * le libellé affiché
  * le format de description : classe php, schema json, schéma XML
  * le nom du schéma
  * les URIs des différents services utilisés par l'application pour la gestion des données,
    la recherche, la transformation, la présentation, etc.

```
descriptionSchemes = "{
  'seda2' : {
    'name' : 'SEDA 2',
    'type' : 'php',
    'uri' : 'seda2/Content',
    'controller' : '',
    'presenter' : ''
  }
}"
```

## Regroupement Socle + ThirdPartyArchiving

Rapatriement de la configuration de l'extension thirdPartyArchiving dans le socle.

Création d'une nouvelle section `[medona]`

```
[medona]
; Enable or disable the transaction mode
; true = Enable
; false = Disable
transaction = false

messageDirectory = "%laabsDirectory%/data/maarchRM/medona"
autoValidateSize = 2147000

;packageSchemas = "{
;    'seda' : {
;         'label' : 'Seda 1',
;         'xmlNamespace' : 'fr:gouv:culture:archivesdefrance:seda:v1.0',
;         'phpNamespace' : 'seda',
;         'presenter' : 'seda/message'
;    },
;    'seda2' : {
;         'label' : 'Seda 2',
;         'xmlNamespace' : 'fr:gouv:culture:archivesdefrance:seda:v2.0',
;         'phpNamespace' : 'seda2',
;         'presenter' : 'seda2/message'
;    }
;}"

; Array of task to remove medona message directories
; 'type' is an array of medona message type
; 'status' is an array of medona message status
; 'delay' is the difference with the current date
;removeMessageTask = "{
;    'task1' : {
;        'type' : ['ArchiveTransfer', 'ArchiveTransferReply'],
;        'status' : ['processed'],
;        'delay' : '-P1Y'
;    },
;    'task2' : {
;        'type' : ['ArchiveModificationNotification'],
;        'status' : ['sent'],
;        'delay' : '-P6M'
;    }
;}"

menu = "[
   {
       'label' : 'Transferts entrants',
       'href'  : '#',
       'class' : 'fa fa-sign-in fa-fw',
       'submenu' : [
           {
               'label' : 'Importer un bordereau',
               'href'  : '/transfer'
           },
           {
               'label' : 'Transferts en attente de traitement',
               'href'  : '/transfer/sent'
           },
           {
               'label' : 'Transferts à traiter',
               'href'  : '/transfer/received'
           },
           {
               'label' : 'Historique de transfert',
               'href'  : '/transfer/history'
           }
       ]
   },
   {
       'label' : 'Communication',
       'href'  : '#',
       'class' : 'fa fa-share fa-fw',
       'submenu' : [
           {
               'label' : 'Demandes d\'autorisation',
               'href'  : '/delivery/Authorizationrequest'
           },
           {
               'label' : 'Communications à valider',
               'href'  : '/delivery/request'
           },
           {
               'label' : 'Communications à récupérer',
               'href'  : '/delivery/list'
           },
           {
               'label' : 'Historique de communication',
               'href'  : '/delivery/history'
           }
       ]
   },
   {
       'label' : 'Restitution',
       'href'  : '#',
       'class' : 'fa fa-reply fa-fw',
       'submenu' : [
           {
               'label' : 'Demandes à valider',
               'href'  : '/restitution/Requestvalidation'
           },
           {
               'label' : 'Restitutions à récupérer',
               'href'  : '/restitution/validation'
           },
           {
               'label' : 'Restitutions à finaliser',
               'href'  : '/restitution/process'
           },
           {
               'label' : 'Historique de restitution',
               'href'  : '/restitution/history'
           }
       ]
   },
   {
       'label' : 'Élimination',
       'href'  : '#',
       'class' : 'fa fa-remove fa-fw',
       'submenu' : [
           {
               'label' : 'Demandes d\'autorisation',
               'href'  : '/destruction/Authorizationrequests'
           },
           {
               'label' : 'Demandes à valider',
               'href'  : '/destruction/processlist'
           },
           {
               'label' : 'Historique d\'élimination',
               'href'  : '/destruction/history'
           }
       ]
   },
   {
       'label' : 'Transferts sortants',
       'href'  : '#',
       'class' : 'fa fa-sign-out fa-fw',
       'submenu' : [
           {
               'label' : 'Transferts à acquitter',
               'href'  : '/outgoingTransfer/received'
           },
           {
               'label' : 'Transferts à finaliser',
               'href'  : '/outgoingTransfer/Process'
           },
           {
               'label' : 'Historique de transfert',
               'href'  : '/outgoingTransfer/history'
           }
       ]
   },
   {
       'label' : 'Notifications',
       'href'  : '/notifications',
       'class' : 'fa fa-bell-o fa-fw'
   }
]"
```
## SQL

Voir le fichier spécifique

    laabs/data/maarchRM/sql/pgsql/migrationV2.4_V2.5.sql

## Branchement de listes externes

Une nouvelle fonctionnalité permet de brancher des référentiels externes afin d'utiliser des valeurs ou des paires de clé et valeur dans les métadonnées descriptives des archives.

Pour le moment, uniquement les csv sur deux colonnes sont gérés. Il est nécessaire de créer un dossier avec l'ensemble des référentiels externes à l'intérieur. Le chemin vers ce fichier est à renseigner dans la valeur de configuration [recordsManagement] refDirectoy, par exemple :

[recordsManagement]
refDirectory = "%laabsDirectory%/data/maarchRM/ref"

Les csv sont considérés comme étant séparés par des virgules et les données présentes entre des guillemets ("").
Lors de l'ajout d'un mot clé, il est désormais donné la possibilité de choisir un référentiel externe. Le nom du reférentiel externe doit correspondre avec le nom du fichier csv à charger dans le dossier renseigné dans la configuration, sans son extension.
Lors de la saisie d'une archive, un typeahead viendra aider l'opérateur dans la saisie. Il est à noter que la première colonne du csv sert d'identification dans la base de données; Les données affichéees à l'écran sont celles de la deuxième colonne. Les colonnes surnuméraires sont chargées mais ne servent que d'aide à la recherche lors de la saisie.

## Plugins

Ajout du plugin dateTimePicker, permettant la saisie simultanée d'une date et d'un horaire au sein d'un même champ (au format DD-MM-YYYY HH:mm:ss par défaut) via une interface.

___

# Migration 2.3 vers 2.4

## Evenement

Ajout de l'évènement recordsManagement/resourceDestruction dans la table "lifeCycle.eventFormat" qui permet la suppression d'une ressource détenue dans une archive.

Ajout de l'évènement recordsManagement/updateRelationship dans la table "lifeCycle.eventFormat" qui permet de mettre à jour les relations d'archives.

Ajout de l'évènement recordsManagement/restitutionRequest dans la table "lifeCycle.eventFormat" qui permet de faire une demande de restitution de l'archive.

Ajout de l'évènement recordsManagement/restitutionRequestCanceling dans la table "lifeCycle.eventFormat" qui permet d'annuler une demande de restitution de l'archive.

## Configuration

Rajout des options dateTimeFormat, timestampFormat, timezone dans les paramètres dependency.localisation

```
[dependency.localisation]
@Adapter                        = Gettext
lang                            = fr
dateFormat                      = d-m-Y
dateTimeFormat                  = "Y-m-d H:i:s \(P\)"
timestampFormat                 = "Y-m-d H:i:s \(P\)"
timezone                        = Europe/Paris
```

Ces paramètres permettent de modifier le fuseau horaire et l'affichage des dates à l'écran.
Le paramètre `dateTimeFormat` définit le format d'affichage des valeurs date et heure en suivant le formalisme d'affichage php (se référer à http://php.net/manual/fr/function.date.php )
Le paramètre `timestampFormat` définit le format d'affichage des temps en suivant le formalisme d'affichage php.
Le paramètre `timeZone` définit le fuseau horaire utilisé pour l'affichage en heure locale.
Si ces paramètres sont ignorés, les valeurs par défaut sont chargées par le logiciel, correspondant à un format respectant le standard ISO8601.

Ajout d'un paramètre "actionWithoutRetentionRule" pour permettre ou non l'élimination d'une archive n'ayant pas de règle de conservation.
Ce paramètre peut prendre deux valeurs : "preserve" ou "dispose" (valeur "preserve" par défaut).
La valeur preserve bloque la suppression d'une archive si aucune règle de conservation ne lui a été attribuée.
La valeur dispose permet la suppression d'une archive si aucune règle de conservation ne lui a été attribuée.

Mise à jour des fichiers signature et container permettant la détection du format des fichiers
```
signatureFile = "%laabsDirectory%/data/maarchRM/droidSignatureFiles/DROID_SignatureFile_V94.xml"
containerSignatureFile = "%laabsDirectory%/data/maarchRM/droidSignatureFiles/container-signature-20180920.xml"
```

## SQL

Voir le fichier spécifique

    laabs/data/maarchRM/sql/pgsql/migrationV2.3_V2.4.sql

___

# Migration 2.2 vers 2.3

## Configuration

### Ecran de connexion

Un style peut être appliqué à l'écran de connexion utilisateur, via la directive
"loginBackground" de la section "presentation.maarchRM":

```
loginBackground = ".modal-backdrop {
    background-image: url('presentation/img/19093d7d-21f4-491b-bca1-5f57704c29d9.jpg');
    background-repeat: no-repeat;
    background-position: center top;
    background-color: #fff;
    background-size: cover;
    opacity: 1 !important;
}"
```

De même, le logo utilisé dans la mire de connexion peut être modifié séparément
de celui affiché dans la barre de navigation:

```
altLogo = "/presentation/img/RM.svg"

```

### CSRF

Modification de la configuration :

    csrfWhiteList = "['user/login', 'user/password']"
    csrfConfig = '{
        "cookieName" : "CSRF",
        "tokenLength" : 32
    }'
    # Migration 2.1 => 2.2

Pour toutes les modifications ci-dessous, merci de vous référer à la documentation **AVANT tout changement** pour plus de détails'.

## Exploitation

Les clés des comptes de service doivent être réinitialisées

## Virtual host

Suppression de la dépendance CSRF dans le fichier **vhost.conf**

## SQL

Voir le fichier spécifique

    laabs/data/maarchRM/sql/pgsql/migrationV2.1_V2.2.sql

## Configuration

Les modifications de configuration font référence au fichier **configuration.ini**

### CSRF

Ajout de la configuration suivante :

    csrfWhiteList = "['user/login']"
    csrfConfig = '{
        "cookieName" : "CSRF",
        "tokenLength" : 32
    }'

[Documentation](https://labs.maarch.org/maarch/maarchRM.doc/blob/b5ff8d2a3c3ad5669eeb01b0ec56f33184ee474e/conf/csrf.md)

### Customisation CSS

L'ajout de la customisation CSS permet d'utiliser une feuille de style personnalisable.

    css = "/presentation/css/style.css"

[Documentation](https://labs.maarch.org/maarch/maarchRM.doc/blob/b5ff8d2a3c3ad5669eeb01b0ec56f33184ee474e/conf/customisation.md)

### Gestion des répertoires de log

Les répertoires des logs sont personnalisables depuis la 2.2.

    ; The path of journals in the file plan
    ; To include the type of log you must use <type>
    ; To include a part of date, you must use <date(format)>.
    ;   - Y for a full numeric representation of a year, 4 digits
    ;   - m for the numeric representation of a month, with leading zeros
    ;   - d for day of the month, 2 digits with leading zeros
    logFilePlan = "<type>/<date(Y)>/<date(m)>"

    translationLogType = "
    {
         'lifeCycle' : 'Journal du cycle de vie',
         'application' : 'Journal de l\'application',
         'system' : 'Journal du système'
     }"

[Documentation](https://labs.maarch.org/maarch/maarchRM.doc/blob/b5ff8d2a3c3ad5669eeb01b0ec56f33184ee474e/conf/log_filePlan_path.md)

### Rôle d'organisation

Les rôles d'organisation ont été déplacés de la base de données vers la configuration.

    orgUnitRoles = "
    {
        'owner' : {
            'code' : 'owner',
            'description' : 'The system owner'
        }
    }"

[Documentation](https://labs.maarch.org/maarch/maarchRM.doc/blob/b5ff8d2a3c3ad5669eeb01b0ec56f33184ee474e/conf/organization_roles.md)

### Ajout d'une nouvelle tâche planifiée

Une tâche se prénommant "Mise à jour de la durée d'utilité administrative" a été ajoutée.

De ce fait, il faut ajouter le privilège :

    servicePrivileges = "[
        {
            'serviceURI': 'audit/event/createChainjournal',
            'description' : 'Chaîner le journal de l\'application'
        },
        {
            'serviceURI': 'batchProcessing/scheduling/updateProcess',
            'description' : 'Exécution automatique des tâches planifiées'
        },
        {
            'serviceURI': 'lifeCycle/journal/createChainjournal',
            'description' : 'Chaîner le journal du cycle de vie'
        },
        {
            'serviceURI': 'recordsmanagement/archivecompliance/readperiodic',
            'description' : 'Valider l\'intégrité des archives'
        },
        {
            'serviceURI': 'recordsManagement/archives/deleteDisposablearchives',
            'description' : 'Détruire les archives'
        },
        {
            'serviceURI': 'recordsManagement/archives/updateIndexfulltext',
            'description' : 'Extraction plein texte'
        },
        {
            'serviceURI': 'recordsManagement/archive/create',
            'description' : 'Création d\'une archive'
        },
        {
            'serviceURI': 'recordsManagement/archive/createArchiveBatch',
            'description' : 'Création par batch d\'archive(s)'
        },
        {
            'serviceURI' : 'recordsManagement/archives/updateArchivesretentionrule',
            'description' : 'Mise à jour de la durée d\'utilité administrative'
        },
        {
            'serviceURI': '*',
            'description' : 'Tous les droits'
        }
    ]"

et toutes les tâches :

    [batchProcessing]
    tasks = "[
                {
                    'taskId': '01',
                    'route' : 'audit/event/createChainjournal',
                    'description' : 'Chainer le journal de l\'application'
                },
                {
                    'taskId': '02',
                    'route' : 'lifeCycle/journal/createChainjournal',
                    'description' : 'Chainer le journal du cycle de vie'
                },
                {
                    'taskId': '03',
                    'route' : 'recordsManagement/archiveCompliance/readPeriodic',
                    'description' : 'Valider l\'intégrité des archives'
                },
                {
                    'taskId': '04',
                    'route' : 'recordsManagement/archives/deleteDisposablearchives',
                    'description' : 'Détruire les archives'
                },
                {
                    'taskId': '05',
                    'route' : 'batchProcessing/notification/updateProcess',
                    'description' : 'Envoyer notification'
                },
                {
                    'taskId': '06',
                    'route' : 'recordsManagement/archives/updateIndexfulltext',
                    'description' : 'Extraction plein texte'
                },
                {
                    'taskId': '07',
                    'route' : 'recordsManagement/archives/updateArchivesretentionrule',
                    'description' : 'Mise à jour de la durée d\'utilité administrative'
                }
            ]"

[Documentation](https://labs.maarch.org/maarch/maarchRM.doc/blob/b5ff8d2a3c3ad5669eeb01b0ec56f33184ee474e/conf/scheduling.md)
