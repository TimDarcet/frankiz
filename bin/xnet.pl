#!/usr/bin/perl

# Détermination du nombre de connectés
$script_dir = `/bin/dirname $0`;
$script_dir =~ s/\n//gs;
require "$script_dir/../etc/config.pl";

use DBI();
my $dbh = DBI->connect("DBI:mysql:database=$mysql_xnet{database}:host=$mysql_xnet{host}",
	"$mysql_xnet{user}","$mysql_xnet{password}",{'RaiseError'=>1});

$reqt="select sum(isconnected) as 'clients' from clients";
my $rep = $dbh->prepare($reqt);
$rep->execute;
$non_vide=(($nombre)=$rep->fetchrow_array());

# Création, si besoin est, de la base rrd
$file="/home/frankiz2/data/xnet.rrd";

if (!-e $file)
{
	system "rrdtool create $file ".
		"DS:clients:GAUGE:600:0:65536 ".
		"RRA:AVERAGE:0.5:1:600 ".
		"RRA:AVERAGE:0.5:6:700 ".
		"RRA:AVERAGE:0.5:24:775 ".
		"RRA:AVERAGE:0.5:288:797 ".
		"RRA:MAX:0.5:1:600 ".
		"RRA:MAX:0.5:6:700 ".
		"RRA:MAX:0.5:24:775 ".
		"RRA:MAX:0.5:288:797";
	print "$0: creating $file file ...\n";
}

# Mise à jour des stats
if (-w $file)
{
	system "rrdtool update $file ".time.":".$nombre;
}
else
{
	print "Error: unwriteable $file ...\n";
}

