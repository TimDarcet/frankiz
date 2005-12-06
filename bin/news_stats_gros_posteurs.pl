#!/usr/bin/perl -I/var/spool/news/scripts/librairies/

#Script destiné à faire les stats pour les afficher

$script_dir = `/bin/dirname $0`;
$script_dir =~ s/\n//gs;
require "$script_dir/../etc/config.pl";

use DBI();
use Net::NNTP;
use HTTP::Date;
use Unicode::String ; 
Unicode::String->stringify_as('utf8');

my $dbh = DBI->connect("DBI:mysql:database=$mysql_news{database}:host=$mysql_news{host}",
        "$mysql_news{user}","$mysql_news{password}",{'RaiseError'=>1});

sub selection_fiche {
	open (DATA ,">/home/frankiz2/cache/news_data");
	print DATA "<strong>Liste des 200 personnes postant le plus pendant les 10 derniers jours : </strong><br/>\n<br/>" ;
    $reqt =("select pseudo_news,sum(1) from news where TO_DAYS(NOW()) - TO_DAYS(date) <= 10 and forum not like 'private.%' group by pseudo_news order by 'sum(1)' DESC limit 0,200");
    my $rep = $dbh->prepare($reqt);
    $rep->execute;
    while (my $ref = $rep->fetchrow_hashref()) {
    	if (($ref->{'pseudo_news'}) ne 'arpwatch@gwennoz.polytechnique.fr'){ 
		print DATA "<strong>" . Unicode::String::latin1($ref->{'pseudo_news'})."   :  </strong>".$ref->{'sum(1)'} . "<br/>" ;
	}
	}
	close(DATA);
}

selection_fiche();


