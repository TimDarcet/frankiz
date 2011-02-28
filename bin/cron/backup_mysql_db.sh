#!/bin/sh

source `echo $0 | sed -e "s/bin\/cron\/backup_/configs\//;s/\.\/cron\/backup_/..\/configs\//;s/\.\/backup_/..\/..\/configs\//"`

if [ $1 ] && [ $1 = all ]
then
  IGNORE=
else
  IGNORE="/images_sizes/d"
fi;

DBLIST=`mysql -u $USER -p$PASSWORD frankiz <<< "show tables;" | sed -e "1d;$IGNORE" | tr "\n" " "`

[ -d /tmp/fkz3mysql/ ] || mkdir /tmp/fkz3mysql/

mysqldump -u $USER -p$PASSWORD -r /tmp/fkz3mysql/dump-`date +%F-%R`.mysql frankiz $TBLIST

for i in /tmp/fkz3mysql/dump-*.mysql
do
 [ -f $i ] && rsync -zu $i $RSYNC_REP > /dev/null && rm $i
done

rmdir --ignore-fail-on-non-empty /tmp/fkz3mysql/
