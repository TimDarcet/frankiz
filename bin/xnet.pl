#!/usr/bin/perl

#Script pour les stats du nb de connectés xnet
# Garde 144 résultats, soit 6* 24: Une journée avec un enregistrement toutes les 10minutes.

$file="/home/frankiz2/cache/stats-xnet";
use DBI();

my $dbh = DBI->connect("DBI:mysql:database=xnet:host=heol","web","kokouije?.",{'RaiseError'=>1});

$reqt="select sum(isconnected) as 'Connectés' from clients";
my $rep = $dbh->prepare($reqt);
$rep->execute;
$non_vide=(($nombre)=$rep->fetchrow_array());
    

my $i=1;

if ($non_vide) 
{
	my $string.= time." ".$nombre."\n";
	open(FILE,"$file");
	while (defined(my $l=<FILE>)&&$i<144) {$string.= $l;$i++;}
	close(FILE);
	
	open(FILE,">$file");
	print FILE $string;
	close(FILE);
}
