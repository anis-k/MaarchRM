# CHANGELOG

## Version 2.7.4
 - `Fixed` Mauvaise ventilation par profil d'archive et service producteur dans l'onglet statistique
 - `Fixed` Impossibilité de sauvegarder des métadonnées de type datetime via l'écran
 - `Fixed` Champs obligatoires n'apparaissent plus lors de l'ajout/modification des métadonnées
 - `Fixed` Mauvais retour lors de la recherche sur les champs description
 - `Changed` Modification de l'utilisateur attribué lors de la création du schéma medona

## Version 2.7.3
- `Fixed` Actualisation lors du changement d'organisation après connexion.
- `Added` Prise en compte de l'ajout d'une référence à une archive parente lors du versement d'un bordereau MADES.
- `Fixed` Erreur lors de la consultation des statistiques.
- `Fixed` Amélioration des performances du chargement des dossiers virtuels sur l'écran principal.

## Version 2.7.2
- `Added` Tri des dossiers virtuels par ordre alphabétique.
- `Added` Affichage des champs de référence externe dans la liste des données descriptives.
- `Fixed` Tri des résultats de recherche par date quand le nombre de résultats dépasse le nombre de résultats maximal.
- `Fixed` Mauvaise interprétation HTML quand un symbole '&' était saisi dans un champ de type textarea.
- `Fixed` Possibilité d'envoi de fichier profil rng sans extension.
- `Fixed` Possibilité de création des relations récursives parents/enfants dans les profils d'archive.
- `Fixed` Traductions.
- `Fixed` Problème chargement plugin datePicker sur l'écran principal lors de l'affichage avec de nombreux dossiers virtuels.
- `Fixed` Affichage d'erreur sur la page de création d'un nouveau compte de service.
- `Fixed` Résultats du service précédent affiché lors de la sélection d'un nouveau service dans la barre de menu.
- `Fixed` Amélioration de la vue mobile de la barre de navigation.
- `Fixed` Amélioration des performances lors de l'affichage/masquage des dossiers virtuels.

## Version 2.7.1
- `Fixed` Activation et désactivation des tâches dans le planificateur de tâches.
- `Fixed` Calcul de la prochaine exécution mêmeen cas d'erreur dans le planificateur de tâches.
- `Fixed` Affichage du compte de service en charge d'exécuter la tâche dans le planificateur de tâches.
- `Fixed` Restauration du fichier permettant la génération de la doc OPENAPI.
- `Fixed` Correction du bug de déconnexion intempestive lors du changement d'organisation d'appartenance.
- `Fixed` Amélioration de la détection de l'algorithme de hash sur les bordereaux externes.
- `Fixed` Correction du bug lors de la modification des paramètres d'un cluster de stockage.
- `Fixed` Correction de l'affichage de la prévisualisation de certains PDF.
- `Fixed` Correction de la fonction de réception des imports d'archive via batch.

## Version 2.7

- `Added` Gestion du comportement des demandes de communications multiples.
- `Added` Ajout d'un système de cotation automatique des unités d'archive.
- `Added` Choix de format d'échange lors des transaction de ressortie.
- `Added` Contrôle d'empreinte lors des versements.
- `Added` Statistiques de volume et nombre de Versement, Elimination, Communication, Conservation
- `Added` Ajout d'un champ 'historique du service' pour chaque service de l'organigramme.
- `Added` Conservation et journalisation de la taille des données lors des ressorties.
- `Added` Ajout de l'évènement "Contrôle d'intégrité du support de stockage" dans le journal du cycle de vie.
- `Added` Bouton d'export des métadonnées et des pièces sur l'écran principal du référent métier.
- `Added` Possibilité pour l'archiviste de traiter manuellement les communications.
- `Added` Possibilité pour le service producteur de rechercher les journaux depuis l'écran principal.
- `Changed` Suppression du choix de format de sortie lors d'une demande de modificaton.
- `Changed` Refonte du formulaire de recherche de l'écran de gestion
- `Changed` Une erreur de traitement dans le planificateur de tâche n'entraine plus une désactivation automatique de la tâche.
- `Changed` Nettoyage du stockage local des données du navigateur lors de la déconnexion.
- `Changed` Amélioration du chiffrement du mot de passe utilisateur.
- `Changed` Amélioration des performances dans le cas d'une recherche dans les journaux volumineux.
- `Fixed` Suppression des fichiers temporaires extraits lors du versement d'un zip.
- `Fixed` Suppression des fichiers temporaires extraits lors de la détection de format.
- `Fixed` Correction de la recherche des journaux du cycle de vie.
- `Fixed` Gestion des erreurs lors d'un import de zip.
- `Fixed` Gestion des erreurs lors d'un import sans archive.
- `Fixed` Filtrage des comptes de service disponibles dans le planificateur de tâche.
- `Fixed` Gestion d'erreur lors des ressorties medona.
- `Fixed` Gestion des erreurs lors des accès à la page Echanges.
- `Fixed` Formulaire de modification persistant lors d'une modification de service dans l'organigramme.
- `Fixed` Gestion du caractère spécial * dans le nom de dossier virtuel.
- `Fixed` Correction decodage du jeton utilisateur lors des requêtes AJAX.
- `Fixed` Gestion des formats inconnus du système lors d'un versement.
- `Fixed` Les exceptions liées à la base de données renvoient désormais une erreur 500.

## Version 2.6.7

- `Fixed` Gestion de l'affichage des ressources et pièces dans l'écran principal.

## Version 2.6.6

- `Fixed` Gestion d'erreur lors du contrôle d'intégrité d'une archive.
- `Fixed` Gestion de l'algorithme de chiffrement par défaut.
- `Added` Les administrateurs fonctionnels peuvent désormais créer des comptes de service de type "utilisateur simple".
- `Added` Ajout du paramètre `timestampService` dans le fichier de configuration, permettant de renseigner le chemin d'un service d'horodatage de test à utiliser pour le chaînage des journaux.
- `Fixed` Gestion d'erreur lors de la détection de format.
- `Added` Recherche des logs et archives de journaux par Web Service.
- `Fixed` Gestion des droits de création des tâches planifiées.
- `Fixed` Gestion d'erreur lors d'une recherche sur l'écran principal concernant les archives versées en SEDA 1.0.
- `Added` Affichage des conversions de la plus récente à la plus ancienne lors de la consultation.
- `Added` Versement d'une archive distante avec transmission du contenu numérique via une URI.
- `Changed` Performance renforcée sur les contrôles d'intégrité périodiques.
- `Changed` Renforcement des contrôles lors de la création d'un utilisateur et d'un compte de service.
- `Added` Le paramètre `maxResults` du fichier de configuration limite également le nombre de résultats retournés par les fonctions de recherche d'archives, de logs et de journaux.
- `Fixed` Nettoyage de la mémoire temporaire lors d'une erreur au versement de documents.
- `Fixed` Rétablissement de l'action de déverrouillage d'un utilisateur verrouillé.

## Version 2.6.5

- `Fixed` Rétablissement du changement de mot de passe pour les nouveaux utilisateurs.
- `Added` Ajout de la directive LAABS_SESSION_START dans le fichier vhost par défaut empêchant la création d'une session sur le serveur.
- `Added` Ajout de la directive LAABS_SECURE_COOKIE dans le fichier vhost par défaut permettant d'ajouter l'attribut secure aux cookies dans le cas d'une instance en HTTPS.
- `Fixed` Retrait de certaines données sensibles dans les données retournées par l'application.
- `Fixed` Désactivation de l'auto-complétion sur les formulaires contenant des mots de passe.

## Version 2.6.4

- `Fixed` Correction faille de sécurité concernant le vol de compte via l'interface de login

## Version 2.6.3

- `Fixed` Possiblité de verser en mode transactionnel via bordereau, avec des pièces de plus de 2Mo
- `Added` Possibilité de récupérer un contenu binaire d'une pièce d'archive directement via un appel en Web Service (voir nouvelle route pour les comptes de service dans le fichier configuration.ini.default)

## Version 2.6.2

- `Added` Possibilité de récupérer un lien de téléchargement à la place du contenu binaire dans les réponses à l'appel web service de consultation

## Version 2.6.1

- `Fixed` Suppression du fichier temporaire lors de la validation de la ressource

## Version 2.6

### Externalisation des fonctions Coffre-Fort Numérique
- `Changed` Le bundle `digitalSafe` a été déplacé vers la nouvelle extension éponyme, disponible dans le ![projet GitLab](https://labs.maarch.org/maarch/digitalSafe)

### Sécurité
- `Fixed` Erreur courante `Attempt to access without a valid token` du module de protection CSRF, et traduction du message en français
- `Fixed` Impossibilité de rattacher un compte de service de niveau Administrateur Fonctionnel à une organisation (au lieu d'un service)
- `Fixed` Faille XSS en faux positif sur la modale 404 "La page demandée n'existe pas"
- `Added` Gestion renforcée de la sécurité (option de configuration) : gestion de niveaux de sécurité sur les rôles et privilèges, en lien avec les niveaux d'utilisateur
- `Changed` Filtrage des comptes d'utilisateurs et comptes de service en fonction du niveau de sécurité, notamment pour le compte utilisé dans le planificateur de tâches (si activé)

### Import et export de référentiels
- `Added` Fonctions d'import de référentiels par téléversement de CSV, avec option de remise à blanc ou de modification/fusion
- `Added` Fonctions d'export de référentiels par téléchargement de CSV

### Echanges transactionnels, versement, ressortie d'archive
- `Fixed` Traitements concurrents sur les mêmes bordereaux (hors versement, déjà corrigé en V2.5)
- `Fixed` Impossibilité de transmettre des chemins de fichiers ou URI pour les données transmises
- `Added` Possibilité de verser des archives compressées d'arborescence de dossiers sans nommage particulier (lien avec profil) pour le vrac numérique

### Traitement des binaires
- `Changed` Réécriture complète des mécanismes de traitement des contenus binaires reçus et transmis, afin de permettre la gestion de documents numériques de grande taille (sup. à 1Go) sans dépassament de la mémoire
- `Changed` Dans l'adaptateur `FileSystem`, création des répertoires de stockage après résolution des parties variables et non plus récursivement pour améliorer les performances lors du stockage CEPH sur interface POSIX
- `Changed` Suppression de fichiers temporaires utilisés lors de versements et ressorties, tous modes (à l'exlusion de la consultation)

### Description
- `Added` Option pour activer la valeur par défaut à la date du jour pour les métadonnées de type date et date+heure

### Classement
- `Fixed` Impossibilité de déplacer les services à la racine de l'organisation
- `Fixed` Erreur lorsqu'un service/une activité n'a pas de rôle
- `Added` Fonction pour activer/désactiver des services/activités et ainsi empêcher les versements

### Traçabilité
- `Fixed` Recherche par terme impossible dans le journal de l'application
- `Fixed` Absence des données de règle de conservation avant modification dans le détail de l'événement
- `Fixed` Absence des noms d'organisation et services acteurs dans le détail des événements d'échange transactionnel
- `Added` Ajout d'un champ organisation dans la recherche d'événement de journal, il assistera l'utilisateur avec une auto-complétion regroupant l'ensemble des services et des organisations auxquels l'utilisateur est rattaché.
- `Added` Ajout de la possibilité de rechercher selon l'identifiant métier (renseigné par le client) en plus de l'identifiant technique dans la recherche d'événement de journal.
- `Added` Information de résultat d'opération dans le tableau des événements des objets (archive et messages d'échange)
- `Changed` Téléchargement des attestations depuis le détail des événements et non plus depuis la liste

### IHM
- `Fixed` Bouton de validation du formulaire de modification de règle de conservation qui se déplace au survol du curseur
- `Fixed` Effacement de la date déjà renseignée dans la zone de saisie, lorsque la même date est sélectionnée dans le sélecteur de date
- `Fixed` Fichier sans extension ni nom lors du téléchargement de fichier de profil d'archivage (SEDA par exemple)
- `Fixed` Affichage du point de menu "Échanges transactionnels" même lorsque l'utilisateur n'a accès à aucune transaction
- `Fixed` Erreur lors du classement de plusieurs archives dans un dossier virtuel, après versement ou modification des métadonnées de l'une des archives de la liste de résultat
- `Fixed` Mauvaise gestion de l'activation/désactivation du bouton de validation de la modale de confirmation avant envoi d'un message transactionnel (modification, communication...)
- `Added` Fonction pour déplier/replier toute une branche de l'organigramme des services
- `Added` Fonction de téléchargement des données de liste de résultat dans l'écran de gestion de l'Archive
- `Added` Mode de vue en liste pour la gestion de l'organisation (option de configuration) pour gérer les grands tableaux de gestion (milliers d'entrées)


### Pérennisation
- `Fixed` Erreur non interceptée lors de la détection de format lorsqu'un outil tiers appelé en ligne de commande (7z en l'occurence) se termine en erreur
- `Changed` Mise à jour des fichiers de signature DROID pour la détection de format (V96 - 2020-01-21)

___

## Version 2.5.3

- `Fixed` Contenu binaire corrompu lors du dépôt direct d'objet numérique dans les archives
- `Fixed` Erreur à l'initialisation du plugin `dataTable`
- `Fixed` Actions impossibles sur les éléments de liste de résultat au-delà de la première page

___

## Version 2.5.2

- `Fixed` Rétablissement des demandes de restitution d'archives.
- `Fixed` Les valeurs par défaut des paramètres de fonction dans JavaScript ont été retirées pour compatibilité à Internet Explorer 11.
- `Fixed` Les tableaux s'initialisent correctement lorsqu'un nombre important d'entrées doit être affiché.
- `Fixed` Rétablissement de la navigation entre archives liées.

### Thesaurus

- `Added` Ajout de la possiblité d'utiliser des fichiers CSV ou SKOS personnalisés comme référentiels de thesaurus (fonctionnalité de l'extension *archives publiques*).

___

## Version 2.5.1

- `Fixed` Correctif sur la vérification de validité du token de 'Mot de passe oublié'

___

## Version 2.5

### Sécurité

- `Fixed` Correction de la fonction permettant d'intégrer un fichier css personnalisé pour forcer à faire référence au fichier CSS et empêcher la remontée de répertoire grâce au chemin de celui-ci.  (Merci à *Vladimir TOUTAIN* et *Sammy FORGIT* pour le signalement et l'analyse).
- `Fixed` Déplacement de la route permettant la modification des utilisateurs pour éviter l'élévation de privilèges lorsqu'un utlisateur n'a pas les droits adéquats. (Merci à *Vladimir TOUTAIN* et *Sammy FORGIT*  pour le signalement et l'analyse).
- `Added` Trois niveaux d'utilisateurs basés sur les exigences de la NF Z 42-020 : Administrateur Général, Administrateur Fonctionnel et Utilisateur Simple.
- `Fixed` Attribution d'un "service par défaut à l'affichage" lors de la suppression du service par défaut d'un utilisateur.

### Open API

- `Added` Ajout d'un script de génération de documentation OpenAPI 2.0 exportable dans Swagger ou en documention HTML.

### Journalisation

- `Added` Génération et téléchargement manuel des attestations de dépôt, de validation d'intégrité et de destruction des archives à partir du journal de cycle de vie.
- `Fixed` Le nombre d'évènements sur l'archive n'est plus multiplié par le nombre de ressources qu'elle contient.
- `Fixed` L'accès aux évènements d'un journal chaîné est rétabli pour les utilisateurs non producteurs de cette archive de journal.

### Administration Technique

- `Added` Un fichier d'horodatage peut être généré par défaut lors du chaînage du journal, et associé à l'archive générée.
- `Added` Le nombre de résultats affichés dans les écrans est désormais paramétrable.
- `Changed` Si le niveau de service n'est pas déclaré par l'accord de versement ou par référence directe dans les données versées, c'est le niveau de service par défaut qui est désormais appliqué.

### Administration Fonctionnelle

- `Added` Le droit "Télécharger les attestations" est ajouté dans les privilèges de rôle.
- `Added` L'option "Autoriser les archives sans profil" est activable sur des profils de type dossier sans sous-profil déclaré.
- `Added` Une demande de modification d'une archive peut être envoyée, afin de notifier le service d'archives d'une correction d'erreur nécessaire ou d'un besoin d'ajout d'informations complémentaires.


### Métadonnées

- `Added` Des schémas de description d'archive peuvent être ajoutés dans l'application avec des métadonnées de type tableau ou objet.
- `Added` Les champs de métadonnées complexes (tableaux, objets) peuvent être ajoutés à un profil, complétés lors d'un versement, modifiés pendant la conservation et consultés via les écrans.
- `Added` Possibilité d'ajout de champ de description "clé valeur" de type énumération dans les données descriptives.
- `Added` Possibilité d'intégration de référentiels externes (exemple : CSV, base de données...) pour les données descriptives.
- `Added` Possibilité d'ajout d'un fichier permettant d'étendre le modèle de description d'un format d'échange (exemple : format d'échange SEDA 2.1 avec des champs complémentaires pour la modification).
- `Added` La métadonnée descriptive de type date peut comprendre une date complétée d'une heure (format livré par défaut : JJ-MM-AAAA HH:mm:ss).
- `Added` Les libellés des métadonnées descriptives qui sont référencées dans le dictionnaire sont affichés.
- `Added` Ajout d'un contrôle lors de la suppression d'un champ de données descriptives : si le champ est engagé dans un profil d'archive, la suppression sera bloquée.

### Gestion de l'archive

- `Added` Une ressource peut être ajoutée ou supprimée au sein d'une archive conservée, et la modification est tracée dans le journal du cycle de vie.
- `Added` Possibilité de versement par bordereaux XML/Medona.
- `Added` Les bordereaux entrants comportent le type d'empreinte poussé dans le système.
- `Changed` L'intégralité des échanges transactionnels MEDONA (extension thirdPartyArchiving) est désormais disponible dans le socle d'archivage. **L'extension thirdPartyArchiving est abandonnée à partir de cette version 2.5**.
- `Fixed` Correction de la fonction permettant de traiter plusieurs bordereaux de transfert par batch pour éviter le versement multiple d'une même archive.

### IHM

- `Fixed` Correction et/ou clarification de messages d'erreurs.
- `Fixed` Tri des résultats de recherche sur les champs date.

### Stockage / Conservation

- `Added` Possibilité de convertir unitairement des documents dans la modale de détail d'une archive.
- `Changed` Le nombre de sites de stockage minimum est ramené à 1.

### Modèle conceptuel

- `Added` Ajout de plusieurs index afin d'améliorer les performances de l'application.

___

## Version 2.4.4

### IHM

- `Fixed` Retrait de l’affichage des ressources si l'archive est en cours de suppression

### Métadonnées

- `Fixed` Ajout d'un bouton dans la modale de détail de l'archive permettant la conversion unitaire d'un document
- `Fixed` Rétablissement de l'affichage des fichiers convertis et des relations dans la modale de détails de l'archive
- `Changed` Différenciation entre le fichier d'origine et ses conversions dans l'arborescence du plan de classement

### Sécurité

- `Changed` Ajout du paramètre `lifetime` pour le jeton CSRF qui définit la durée de validité des jetons en secondes. Si omis, durée de 1 heure par défaut.
- `Fixed` Jeton CRSF consommé lorsqu'utilisé.

## Version 2.4.3

### Métadonnées

- `Changed` Le format interne _Name_ utilisé pour les codes de règles de conservation
et de communicabilité accepte désormais les caractères numériques en première position, ainsi que les tirets
- `Changed` Les appels au service de modification de la règle de conservation des archives
peuvent désormais n'envoyer que le code de la règle, le système utilisant le référentiel pour récupérer les durées et le sort final. Idem pour la règle de communicabilité et la durée qui y est associée.

### Exploitation

- `Changed` Nouveau paramètre pour les notifications, `mailSMTPAutoTLS` pour activer/désactiver le mode TLS
automatique, notamment pour les connexions sans authentification

## Version 2.4.2

### Script d'import

- `Fixed` Correction d'un bug lors de l'utilisation de cli

## Version 2.4.1

### IHM

- `Fixed` Le bouton 'Import' restait parfois grisé en cas d'erreur lors d'un versement

### Métadonnées

- `Fixed` Rétablissement de la fonctionnalité de création, diffusion et modification d'un fichier de profil d'archive de type
- `Changed` Suppression du bouton de modification des métadonnées descriptives SEDA dans la modale de détail du socle : le bouton est propre à l'extension archivesPubliques

## Version 2.4

### IHM

- `Changed` Nouveau design de la modale de détails des informations de l'archive
- `Changed` Modification du moteur de recherche : à présent insensible aux caractères spéciaux, ajout du symbole * qui permet de rechercher une archive débutant / finissant / contenant un terme spécifique (exemple* cherchera une archive débutant par le terme exemple, *exemple2* cherchera une archive contenant le terme exemple2)
- `Fixed` Blocage des imports multiples lors de clics répétés sur le bouton Importer
- `Fixed` Le bouton Annuler est également non cliquable durant l'execution d'un versement
- `Fixed` Le choix "sans profil" au versement d'une archive ne doit apparaître que si le dossier qui la receptionne l'autorise

### Métadonnées

- `Added` Une variable "actionWithoutRetentionRule" a été ajoutée dans le fichier de configuration pour permettre ou non l'élimination d'une archive n'ayant pas de règle (preserve : L'archive ne peut pas être supprimée si aucune règle n'a été définie OU dispose : L'archive peut-être éliminée si aucune règle n'a été définie)
- `Added` Mise à jour automatique de la date de dernière modification d'une archive

### Administration

- `Changed` Mise à jour dans la configuration des fichiers de signature Droid (v91 => v94)

### Accès

- `Changed` Les libellés des formats dans le tableau de documents indique désormais le nom du type de document au lieu de son identifiant
- `Fixed` Les routes d'évènements du journal de l'application ont été intégralement traduites en Anglais

## Extension

- `Added` L'extension Workflow permettant l'utilisation des flux de travail est désormais disponible

## Version 2.3

### Exploitation

- `Added` Compatibilité navigateur Microsoft (IE 11 / Edge)
- `Added` Compatibilité PHP 7.2
- `Fixed` Optimisation de l'utilisation de la mémoire pour la recherche dans les journaux volumineux

### IHM

- `Changed` Nouvau design de l'écran principal pour la navigation, la recherche et la consultation
- `Added` Personnalisation de la page de connexion avec une image de fonds et un style configurables
- `Fixed` Blocage des double-clics sur les boutons

### Administration

- `Fixed` Ajout d'un contact et/ou d'une adresse et/ou d'un moyen de communication aux organisations et services

## Version 2.2

### Exploitation

- `Added` Modification des règles de plusieurs archives sans modifier la date de départ
- `Changed` Gestion des jetons de compte de service
- `Changed` Ajout des routes disponibles pour les privilèges des comptes de service dans la configuration
- `Fixed` Uniformisation des formats de date

### Métadonnées

- `Added` Possibilité de mettre les métadonnées en lecture seule

### Classement

- `Added` Possibilité de définir un répertoire pour les archives des journaux

### IHM

- `Added` Ajout d'un message de confirmation avant la suppression
- `Added` Configuration d'un CSS spécifique
- `Added` Ajout d'un "À propos"
- `Changed` Amélioration de l'affichage des archives des journaux
- `Changed` Amélioration de l'affichage des événements de l'application

### Administration

- `Added` Modification rétroactive des règles de conservation

### Sécurité

- `Security` Amélioration de la protection contre le CSRF (Cross-Site Request Forgery)

## Version 2.1

### Exploitation
- `Added` Restriction des comptes de service disponibles pour la planification à ceux qui possèdent le privilège pour le service à exécuter
- `Changed` Liste des services disponibles pour la planification déplacée dans la configuration. Table `batchProcessing.task` supprimée
- `Fixed` Correction d'erreurs dans le calcul de la prochaine vacation après une en erreur

### Échéancier d'élimination
- `Added` Sort final non défini au versement ou à la modification désormais interprété comme *A définir ultérieurement*
- `Changed` Modification du statut final des unités d'archive en fonction de l'opération qui mène à leur ressortie : *Détruite*, *Restituée* et *Transférée*
- `Fixed` Interdiction de toute demande sur les unités d'archive gelées : élimination, restitution, modification, transfert

### Élimination
- `Changed` La demande n'est valide que si l'unité d'archive et toutes ses unités contenues sont éliminables (à terme de la DUA ou sans règle de conservation, sort final *Détuire* ou sans sort final

### Versement
- `Added` Ajout du choix d'un niveau de service dans la plan de classement, comme complément au couple producteur-profil, et pris en compte lors du versement

### Sécurité
- `Added` Nouvelle fonction de verrouillage des rôles fonctionnels
- `Added` Liste noire des cas d'usage dans la configuration, afin de ne plus autoriser leur utilisation dans les rôles
- `Added` Demande de confirmation du nouveau mot de passe lors du changement par l'utilisateur
- `Changed` Les rôles fonctionnels peuvent désormais être supprimés
- `Changed` Modification du format autorisé pour les noms d'utilisateurs, autorisant les caractères spéciaux pour permettre l'emploi des adresses électroniques
- `Changed` Désactivation de l'auto-complétion pour les champs de saisie des us de passe, à la connexion et à la modification par l'utilisateur
- `Changed` Chaque utilisateur doit être rattaché à un service organisationnel dès sa création
- `Changed` Liste blanche des cas d'usage déplacée dans la configuration. Table `auth.publicUserStory` supprimée
- `Security` Amélioration de la protection contre les injections SQL, le XSS (Cross-Site Scripting), le CSRF (Cross-Site Request Forgery) et le ClickJacking

### IHM
- `Changed` Amélioration des champs de choix de service avec saisie intuitive et auto-complétion en deux temps, l'organisme d'abord et le service ensuite
- `Changed` Harmonisation des titres de menu, de services et d'écrans. Harmonisation des icônes de menu et d'écran
- `Fixed` Correction d'une erreur lorsque aucun service d'organisation n'était choisi par défaut pour l'utilisateur

### Classement
- `Changed` Les unités organisationnelles ne peuvent désormais plus être déplacées vers un autre organisme

### Accès
- `Added` Filtre par défaut sur la date de dépôt dans les écrans et fonctions de recherche

### Stockage
- `Changed` Type de données pour la taille des ressources numériques modifié en `bigint` pour autoriser les objets de taille supérieure à 2Go

### Traçabilité
- `Fixed` Correction d'un effet indésirable lorsqu'un journal était chaîné plusieurs fois dans la même seconde. La règle de détermination du dernier journal en date utilise la date du dernier événement contenu et non plus la date du premier

### Pérennisation
- `Fixed` Erreur lors de la détection du format de certains fichiers conteneurs, notamment OpenXML et Microsoft OOXML. Correction de l'appel au plugin de décompression pour extraire avec l’arborescence interne des dossiers

## Version 2.0

### IHM
- `Added` Écran principal pour l'accès par navigation et recherche, le classement en dossiers virtuels, le détail des métadonnées et la prévisualisation des données
- `Added` Fonction d'aperçu des documents, avec outils tiers d'échantillonnage (PDF: 2 pages, image si inférieur à 2Mo, etc)
- `Added` Formulaires de saisie dynamique pour les métadonnées des archives lors du versement et pour la modification

### Métadonnées
- `Added` Administration d'un dictionnaire des métadonnées
- `Added` Stockage de métadonnées en NoSQL (Json avec PostGre)
- `Added` Moteur de recherche dynamique sur les métadonnées

### Classement
- `Added` Profils d'archive décrits par l'administrateur
- `Added` Rattachement des profils autorisés aux services producteurs dans l'organigramme
- `Changed` Modification du format autorisé pour les identifiant d'organisation et de services, autorisant les caractères spéciaux pour permettre l'emploi des barres obliques, points, tirets et soulignements notamment

### Intégrité
- `Added` Stratégies de contrôle d'intégrité périodique définies dans les niveaux de service, avec fréquence de contrôle et taux d'échantillonnage

### Sécurité
- `Changed` L'administrateur de la sécurité ne peut désormais plus choisir le mot de passe temporaire. En remplacement, l'intégration d'un client de messagerie électronique permet d'envoyer un courriel à l'utilisateur comportant un mot de passe temporaire généré par l'application
