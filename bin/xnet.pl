#!/usr/bin/perl

#Script pour les stats du nb de connect�s xnet
# Garde 144 r�sultats, soit 6* 24: Une journ�e avec un enregistrement toutes les 10minutes.

$file="/home/frankiz2/cache/stats-xnet";
use DBI();

my $dbh = DBI->connect("DBI:mysql:database=xnet:host=heol","web","kokouije?.",{'RaiseError'=>1});

$reqt="select sum(isconnected) as 'Connect�s' from clients";
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