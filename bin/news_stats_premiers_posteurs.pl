#!/usr/bin/perl -I/var/spool/news/scripts/librairies/

#Script destiné a faire les stats pour les afficher

use DBI();
#use Net::NNTP;
use HTTP::Date;
use Unicode::String qw(latin1);

my $dbh = DBI->connect("DBI:mysql:database=news:host=localhost","news",
        "ug0yc1jo",{'RaiseError'=>1});



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
	print("Traitement de ".$ref->{'pseudo_news'}."\n");
           if ( !filtrage($ref) )
	   {
		if (!$count){			
			$count++;
		 	print DATA "<strong>".$ref->{'pseudo_news'}."</strong> est soit un couche-très-tard, soit un lève-très-tôt! Il a posté ce matin à ".
 			substr($ref->{'date'},-8,2)."h".substr($ref->{'date'},-5,2)." sur le ".$ref->{'forum'}." !<br/>\n";
			print ("** C\'est le numéro 1! :) **\n");
			$posteur=$ref->{'pseudo_news'};
		}
		if ($count eq 1){
			if ($ref->{'pseudo_news'} ne $posteur){
		 	print DATA "<strong>".$ref->{'pseudo_news'}."</strong> n'est pas mauvais non plus, avec un post à ".
 			substr($ref->{'date'},-8,2)."h".substr($ref->{'date'},-5,2)." sur le ".$ref->{'forum'}." !<br/>\n";
			print ("** Ce n\'est que le numéro 2 **\n");
			$posteur2=$ref->{'pseudo_news'};
			$count++;
		}       }
		if ($count eq 2){
		 if (  ($ref->{'pseudo_news'} ne $posteur) && ($ref->{'pseudo_news'} ne $posteur2) ){	
		 	print DATA "<strong>".$ref->{'pseudo_news'}."</strong>: peut mieux faire! Un post à ".
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
	print("Traitement de ".$ref->{'pseudo_news'}."\n");
           if ( !filtrage($ref) )
	   {
		if (!$count){			
			$count++;
		 	print DATA "<strong>".$ref->{'pseudo_news'}."</strong> doit être insomniaque! Il a posté cette nuit à ".
 			substr($ref->{'date'},-8,2)."h".substr($ref->{'date'},-5,2)." sur le ".$ref->{'forum'}." !<br/>\n";
			print ("** Autiste numéro 1! :) **\n");
			$posteur=$ref->{'pseudo_news'};
		}
		  if ($count eq 1){
			if ($ref->{'pseudo_news'} ne $posteur){
		 	print DATA "<strong>".$ref->{'pseudo_news'}."</strong> a un bon potentiel, avec un post à ".
 			substr($ref->{'date'},-8,2)."h".substr($ref->{'date'},-5,2)." sur le ".$ref->{'forum'}." !<br/>\n";
			print ("** Autiste numéro 2 **\n");
			$posteur2=$ref->{'pseudo_news'};
			$count++;
		   }    }
		if ($count eq 2){
		 if (  ($ref->{'pseudo_news'} ne $posteur) && ($ref->{'pseudo_news'} ne $posteur2) ){	
		 	print DATA "<strong>".$ref->{'pseudo_news'}."</strong> a encore un effort à faire... il a posté à ".
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



open (DATA ,">/home/frankiz2/cache/news_data_premiers_posteurs.test");

$result=premier_posteur();

print DATA "<br/>";
#exec("iconv -t utf8 /home/frankiz2/cache/news_data_premiers_posteurs.tmp > /home/frankiz2/cache/news_data_premiers_posteurs");

$result=dernier_posteur($result);


