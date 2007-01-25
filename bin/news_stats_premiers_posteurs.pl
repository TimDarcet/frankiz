#!/usr/bin/perl -I/var/spool/news/scripts/librairies/

#Script destiné a faire les stats pour les afficher

$script_dir = `/bin/dirname $0`;
$script_dir =~ s/\n//gs;
require "$script_dir/../etc/config.pl";

use DBI();
#use Net::NNTP;
use HTTP::Date;
use Unicode::String qw(latin1);

my $dbh = DBI->connect("DBI:mysql:database=$mysql_news{database}:host=$mysql_news{host}",
        "$mysql_news{user}","$mysql_news{password}",{'RaiseError'=>1});

sub filtrage($){  # si c'est TRUE, on vire
return (   
		 (  (($_[0]->{'mail'}) eq "news\@frankiz.eleves.polytechnique.fr") &&  (  ($_[0]->{'pseudo_news'}) eq "Tour kawa" )   )
	        || !(      ( ($_[0]->{'forum'}) =~ /^br\./  ) 
		        || ( ($_[0]->{'forum'}) =~ /^public\./  )   )
	)  ;
}

sub date_ajd(){

$mday=(gmtime)[3];
$mon=(gmtime)[4]+1;
$year=(gmtime)[5]+1900;
if ($mday<10) {$mday="0".$mday;}
if ($mon<10) {$mon="0".$mon;}

$date =  $year."-".$mon."-".$mday ;
return $date;
}




sub premier_posteur(){


$date=date_ajd()." 06".$RANplus;

print ("\nSélection des posts après ".$date."\n");

$reqt =("SELECT `ip`,`nom_dns`,`pseudo_news`,`mail`,`forum`,`date`,`sujet` FROM `news` WHERE 1 AND `date` > '$date' ORDER BY `date` ASC");
$count=0;

print $reqt."\n\n";
my $rep = $dbh->prepare($reqt);
 $rep->execute;

       while (my $ref = $rep->fetchrow_hashref()) {
	$ref->{'pseudo_news'}= latin1($ref->{'pseudo_news'});
	print("Traitement de ".$ref->{'pseudo_news'}." (".$ref->{'mail'}.")\n");
           if ( !filtrage($ref) )
	   {
		if ($ref->{'pseudo_news'} =~ m/\w/) {
			$pseudo = $ref->{'pseudo_news'};
		} else {
			$pseudo = $ref->{'mail'};
		}

		if (!$count){			
			$count++;
		 	print DATA "<strong>".$pseudo."</strong> est soit un couche-très-tard, soit un lève-très-tôt! Il a posté ce matin à ".
 			substr($ref->{'date'},-8,2)."h".substr($ref->{'date'},-5,2)." sur le ".$ref->{'forum'}." !<br/>\n";
			print ("** C\'est le numéro 1! :) **\n");
			$posteur=$pseudo;
		}
		if ($count eq 1){
			if ($pseudo ne $posteur){
		 	print DATA "<strong>".$pseudo."</strong> n'est pas mauvais non plus, avec un post à ".
 			substr($ref->{'date'},-8,2)."h".substr($ref->{'date'},-5,2)." sur le ".$ref->{'forum'}." !<br/>\n";
			print ("** Ce n\'est que le numéro 2 **\n");
			$posteur2=$pseudo;
			$count++;
		}       }
		if ($count eq 2){
		 if (  ($pseudo ne $posteur) && ($pseudo ne $posteur2) ){	
		 	print DATA "<strong>".$pseudo."</strong>: peut mieux faire! Un post à ".
 			substr($ref->{'date'},-8,2)."h".substr($ref->{'date'},-5,2)." sur le ".$ref->{'forum'}." .<br/><br/>\n";
			print ("** Ce n\'est que le numéro 3 :( **\n");
			return 3;
		}}	
	   }	
	}


return $count;
   
}				    

sub dernier_posteur($){


$date=date_ajd()." 06".$RAN;

print ("\nSélection des derniers posts avant ".$date."\n");

$reqt =("SELECT `ip`,`nom_dns`,`pseudo_news`,`mail`,`forum`,`date`,`sujet` FROM `news` WHERE 1 AND `date` <  '$date' ORDER BY `date` DESC");
print $reqt."\n\n";
$count=0;

my $rep = $dbh->prepare($reqt);
 $rep->execute;

       while (my $ref = $rep->fetchrow_hashref()) {
        $ref->{'pseudo_news'} = latin1($ref->{'pseudo_news'});
	print("Traitement de ".$ref->{'pseudo_news'}." (".$ref->{'mail'}.")\n");
           if ( !filtrage($ref) )
	   {
		if ($ref->{'pseudo_news'} =~ m/\w/) {
			$pseudo = $ref->{'pseudo_news'};
		} else {
			$pseudo = $ref->{'mail'};
		}
		
		if (!$count){			
			$count++;
		 	print DATA "<strong>".$pseudo."</strong> doit être insomniaque! Il a posté cette nuit à ".
 			substr($ref->{'date'},-8,2)."h".substr($ref->{'date'},-5,2)." sur le ".$ref->{'forum'}." !<br/>\n";
			print ("** Autiste numéro 1! :) **\n");
			$posteur=$pseudo;
		}
		  if ($count eq 1){
			if ($pseudo ne $posteur){
		 	print DATA "<strong>".$pseudo."</strong> a un bon potentiel, avec un post à ".
 			substr($ref->{'date'},-8,2)."h".substr($ref->{'date'},-5,2)." sur le ".$ref->{'forum'}." !<br/>\n";
			print ("** Autiste numéro 2 **\n");
			$posteur2=$pseudo;
			$count++;
		   }    }
		if ($count eq 2){
		 if (  ($pseudo ne $posteur) && ($pseudo ne $posteur2) ){	
		 	print DATA "<strong>".$pseudo."</strong> a encore un effort à faire... il a posté à ".
 			substr($ref->{'date'},-8,2)."h".substr($ref->{'date'},-5,2)." sur le ".$ref->{'forum'}." .<br/>\n";
			print ("** Autiste numéro 3 :( **\n");
			return 3;
		}}
		    	
		

	   }	
	}


return $count;
   
}				    





srand;

$ranmin=int(rand 10)+17;
$ransec=int(rand 60);

if ($ransec<10) {$ransec="0".$ransec;}

$RANplus=":".($ranmin+20).":".$ransec;

#if ($ranmin<10) {$ranmin="0".$ranmin;}

$RAN=":".$ranmin.":".$ransec;

print ("Random choisi: 06".$RAN."\n\n");



open (DATA ,">/home/frankiz2/cache/news_data_premiers_posteurs");

$result=premier_posteur();

print DATA "<br/>";

$result=dernier_posteur($result);


