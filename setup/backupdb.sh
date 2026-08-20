#!/bin/bash
set -x

cd /var/www/kendo-bin/setup/backup/
NOW=`date "+%Y%m%d%H%M%S"`
DUMP="./kendo_"$NOW".dump"
echo $DUMP
mysqldump --no-tablespaces --single-transaction --quote-names -h localhost -u keioffice_kendo -phprzjntc keioffice_zenchu > $DUMP
