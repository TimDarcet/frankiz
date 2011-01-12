#!/bin/bash
./convert.2to3.php
./update.promos.php
cd cron
./update.scores.php
cd ..
for ((i = 2009; i >= 1998; i -= 1))
do
./import.tol.php '{"type":"user","condition":{"type":"promo","comparison":"=","promo":'$i'}}' /home/2008/riton/dev/tol/$i poly original
./import.tol.php '{"type":"user","condition":{"type":"promo","comparison":"=","promo":'$i'}}' /home/2008/riton/dev/tol/$i poly photo
done 

