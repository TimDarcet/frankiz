#!/bin/sh

source `/bin/echo $0 | /bin/sed -e "s/bin\/cron\/backup_/configs\//;s/\.\/cron\/backup_/..\/configs\//;s/\.\/backup_/..\/..\/configs\//"`

if [ $1 ] && [ $1 = all ]
then
  IGNORE=
else
  IGNORE="/images_sizes/d"
fi;

TBLIST=`/usr/bin/mysql -u $MYSQL_USER -p$MYSQL_PASSWORD frankiz <<< "show tables;" | /bin/sed -e "1d;$IGNORE" | /bin/tr "\n" " "`

[ -d /tmp/fkz3mysql/ ] || /bin/mkdir /tmp/fkz3mysql/

/usr/bin/mysqldump -u $MYSQL_USER -p$MYSQL_PASSWORD -r /tmp/fkz3mysql/dump-`date +%F-%R`.mysql frankiz $TBLIST

for i in /tmp/fkz3mysql/dump-*.mysql
do
 [ -f $i ] && /usr/bin/rsync -zu $i $RSYNC_REP > /dev/null && rm $i
done

/bin/rmdir --ignore-fail-on-non-empty /tmp/fkz3mysql/
