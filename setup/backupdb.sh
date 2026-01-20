cd /var/www/cgi-bin/setup/
NOW=`date "+%Y%m%d%H%M%S"`
DUMP="./kendo_"$NOW".dump"
echo $DUMP
mysqldump --no-tablespaces --single-transaction --quote-names -h localhost -u keioffice_kendo -phprzjntc keioffice_kendo > $DUMP
