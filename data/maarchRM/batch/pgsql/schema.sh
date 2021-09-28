#!/bin/sh

SCRIPT_PATH=`dirname $(readlink -f "$0")`;
cd $SCRIPT_PATH;

# INIT PATH
LAABS_PATH="$SCRIPT_PATH/../../../../";
BUNDLE_PATH="$LAABS_PATH/src/bundle";
DATA_MAARCHRM_SQL_PATH="$LAABS_PATH/data/maarchRM/sql/";

# HELP TEXT
usage="$(basename "$0") [--options ...] -- This program initializes the database with schemas and basic data

where:
    -?  --help      show this help text
    -p  --port      set the port value (default: 5432)
    -h  --host      set the host value (default: 127.0.0.1)
    -d  --database  set the database value (default: maarchRM)
    -u  --username  set the user name value (default: maarch)

Exemple :
$(basename "$0") -p=5432 -h=127.0.0.1 -d=maarchRM -u=maarch
"


# INIT DEFAULT VALUES
input_pgsql_port=5432
input_pgsql_host=ec2-52-213-119-221.eu-west-1.compute.amazonaws.com
input_pgsql_database=d5a9aok1v5qgdt
input_pgsql_user=bzpbrscfprimfo
input_pgsql_password=506fcd2211b8bfdd6f774633f20befd6072a9f4c37704f6afbd380fb8c35419a



# INIT PARAMS
for i in "$@"
do
case $i in
    -?|--help)
    echo "$usage";
    exit
    ;;
    -p=*|--port=*)
    input_pgsql_port="${i#*=}"
    shift # past argument=value
    ;;
    -h=*|--host=*)
    input_pgsql_host="${i#*=}"
    shift # past argument=value
    ;;
    -d=*|--database=*)
    input_pgsql_database="${i#*=}"
    shift # past argument=value
    ;;
    -u=*|--username=*)
    input_pgsql_user="${i#*=}"
    shift # past argument=value
    ;;
    *)
        echo "illegal option: $i"
        echo "$usage";
        exit
    ;;
esac
done

cat \
$BUNDLE_PATH/audit/Resources/sql/schema.pgsql.sql \
$BUNDLE_PATH/auth/Resources/sql/schema.pgsql.sql \
$BUNDLE_PATH/batchProcessing/Resources/sql/schema.pgsql.sql \
$BUNDLE_PATH/contact/Resources/sql/schema.pgsql.sql \
$BUNDLE_PATH/medona/Resources/sql/schema.pgsql.sql \
$BUNDLE_PATH/recordsManagement/Resources/sql/schema.pgsql.sql \
$BUNDLE_PATH/digitalResource/Resources/sql/schema.pgsql.sql \
$BUNDLE_PATH/filePlan/Resources/sql/schema.pgsql.sql \
$BUNDLE_PATH/lifeCycle/Resources/sql/schema.pgsql.sql \
$BUNDLE_PATH/organization/Resources/sql/schema.pgsql.sql \
| psql --host=$input_pgsql_host --port=$input_pgsql_port --username="$input_pgsql_user" --dbname="$input_pgsql_database" -f -
