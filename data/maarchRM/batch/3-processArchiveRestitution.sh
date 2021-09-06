#!/bin/bash

SCRIPT_PATH=`dirname $(readlink -f "$0")`

cd $SCRIPT_PATH

source 0-config.sh

# Run test job
php cli.php UPDATE medona/archiveRestitution/process/Batch -tokenfile:"$SCRIPT_PATH/0-token.txt" -accept:application/json