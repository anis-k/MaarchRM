#!/bin/bash

cd $SCRIPT_PATH

# Set environment/server variables
export LAABS_APP="maarchRM"
export LAABS_INSTANCE_NAME="maarchRM"
export LAABS_CONFIGURATION="../data/maarchRM/conf/configuration.ini"
export LAABS_BUNDLES="audit;auth;batchProcessing;contact;digitalResource;lifeCycle;organization;recordsManagement;filePlan;medona;mades"
export LAABS_DEPENDENCIES="repository;xml;html;localisation;datasource;sdo;json;fileSystem;notification;PDF"
export LAABS_BUFFER_MODE=1
export LAABS_PHP_INI="$SCRIPT_PATH/../conf/php_batch.ini"
export LAABS_CONTENT_TYPES="url:application/x-www-form-urlencoded;html:text/html,application/xhtml+xml;xml:application/xml;json:application/json,application/javascript;soap:application/soap+xml"
# export LAABS_CONTENT_LANGUAGES="fr:fr,fr-fr,fr-ca"
export LAABS_TMP_DIR="$SCRIPT_PATH/../tmp"
# export LAABS_LOG="$SCRIPT_PATH/../log.txt"
export LAABS_CRYPT_KEY="mySecretKey"
export LAABS_CRYPT_CIPHER=MCRYPT_BLOWFISH

# Change working directory to web root
cd /var/www/laabs/web/
