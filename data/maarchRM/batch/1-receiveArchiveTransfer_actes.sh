#!/bin/bash

SCRIPT_PATH=`dirname $(readlink -f "$0")`
LAABS_PATH="$SCRIPT_PATH/../../../../..";
EXT_SAMPLE="$LAABS_PATH/src/ext/archivesPubliques/data/samples";
cd $SCRIPT_PATH;

source 0-config.sh

# Run test job
# -d xdebug.profiler_enable=1 -d xdebug.collect_params=0 


php cli.php CREATE medona/Archivetransfer messageFile="$EXT_SAMPLE/ArchiveTransfer_Actes_04/ArchiveTransfer_Actes_04.xml" attachments="$EXT_SAMPLE/ArchiveTransfer_Actes_04" -tokenfile:"$SCRIPT_PATH/0-token.txt" -accept:"application/json"
php cli.php UPDATE medona/ArchiveTransfer/Validate/Batch -tokenfile:"$SCRIPT_PATH/0-token.txt" -accept:"application/json"
php cli.php UPDATE medona/ArchiveTransfer/Process/Batch -tokenfile:"$SCRIPT_PATH/0-token.txt" -accept:"application/json"
