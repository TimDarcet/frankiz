#!/usr/bin/perl -I/var/spool/news/scripts/librairies/

#Script destiné à faire les stats pour les afficher

use DBI();
use Net::NNTP;
use HTTP::Date;

my $dbh = DBI->connect("DBI:mysql:database=news:host=localhost","news",
        "ug0yc1jo",{'RaiseError'=>1});

sub selection_fiche {
	open (DATA ,">/home/frankiz2/cache/news_data");
	print DATA "n<strong>Liste des 200 personnes postant le plus pendant les 10 derniers jours : </strong><br/>\n<br/>" ;
    $reqt =("select pseudo_news,sum(1) from news where TO_DAYS(NOW()) - TO_DAYS(date) <= 10 and forum not like 'private.%' group by pseudo_news order by 'sum(1)' DESC limit 0,200");
    my $rep = $dbh->prepare($reqt);
    $rep->execute;
    while (my $ref = $rep->fetchrow_hashref()) {
    	if (($ref->{'pseudo_news'}) ne 'arpwatch@gwennoz.polytechnique.fr'){ 
		print DATA "<strong>" . $ref->{'pseudo_news'}."   :  </strong>".$ref->{'sum(1)'} . "<br/>" ;
	}
	}
	close(DATA);
}

selection_fiche();


