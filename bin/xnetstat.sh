#/bin/bash
# met � jour le nombre de connect�s � xnet dans le graphique
# ~br/public_html/xnetstat.jpg
# ce script est lanc� par un cron de l'utilisateur br accessible par 
# crontab -e si l'utilisateur est bien br (et non root ou un autre)
# script �crit par Castan Eric en 1996
# modifi� et comment� par Le Loarer Lo�c le 29/09/1999

cd /home/web/public/xnet-stats

# on recup�re le nombre de connect�s
echo -n "`date \"+%d/%m/%Y %H:%M\"` " >>stats
/usr/local/bin/totcon 2>/dev/null >>stats
tail -n 192 <stats >stats.new
mv stats.new stats >& /dev/null

# on met en place le fichier cmd qui permet de tracer le graphique avec gnuplot
echo set output \"image.png\" >cmd
echo set terminal png small color >>cmd
echo set xdata time >>cmd
echo set format x \"%H:%M\">>cmd
echo set timefmt \"%d/%m/%Y %H:%M\" >>cmd
echo set xlabel \"Heure\" >>cmd
echo set ylabel \"Nbre personnes\" >>cmd
echo set title \"Connexions XNet\">>cmd
echo plot \'stats\' using 1:3 with lines>> cmd
# trac� du graphique
gnuplot <cmd
# conversion en jpg
#/usr/bin/convert image.pbm xnetstat.jpg
cp -f image.png ../accueil/xnetstatquick.png

rm -r image.png cmd
exit 0
