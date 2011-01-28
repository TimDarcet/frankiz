#!/bin/bash

./convert.php $1

cd ../../bin
./update.promos.php
./update.studies.php

for ((i = 2009; i >= $1; i -= 1))
do
./import.tol.php '{"type":"user","condition":{"type":"promo","comparison":"=","promo":'$i'}}' /home/2008/riton/dev/tol/$i poly original
./import.tol.php '{"type":"user","condition":{"type":"promo","comparison":"=","promo":'$i'}}' /home/2008/riton/dev/tol/$i poly photo
done

cd ../upgrade/2.0.0_to_3.0.0/unversionned
./add.php

cd ../../../bin/cron
./update.scores.php
./update.birthday.php
