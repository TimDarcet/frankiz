#!/usr/bin/perl

#Script destiné a envoyer sur les news les rappels de convocation de tour kawa

use DBI();
use Net::NNTP;
use Net::Cmd;
use Time::localtime;

my $dbh = DBI->connect("DBI:mysql:database=frankiz2:host=localhost","web","kokouije?.",{'RaiseError'=>1});

sub post {
        local ($serveur) = "129.104.201.51";
	local ($ng,$groupe,$type,$name,$subject,$body)= @_;
	local $nntp = Net::NNTP->new("$serveur") or die "Ne peut pas se connecter au serveur";
	open (POST, "post.file");
	@post = <POST>;
	close POST;
	$nntp->post() or die "Could not post article: $!";
	
	if ($type==1) {
		$nntp->datasend("From: ".$name." <news\@frankiz.eleves.polytechnique.fr>\n");
		$nntp->datasend("Newsgroups: ". $ng ."\n");
		$nntp->datasend("Subject: [Tour Kawa ".$groupe."] ".$subject."\n");
		$nntp->datasend("X-Newsreader:Tour Kawa Reminder v1.0\n");
		$nntp->datasend("\n\n");
		$nntp->datasend($body."\n");
		}
	elsif ($type==2) {
                $nntp->datasend("From: Tour kawa <news\@frankiz.eleves.polytechnique.fr>\n");
		$nntp->datasend("Newsgroups: ". $ng ."\n");
                $nntp->datasend("Subject: [Tour Kawa] Bouh il n'y a personne\n");
		$nntp->datasend("X-Newsreader:Tour Kawa Reminder v1.0\n");
		$nntp->datasend("\n\n");
		$nntp->datasend($text . "\n" . "Bon alors pas de kawa!\n");
		}								
	else {	
		$nntp->datasend("From: Tour Kawa <news\@frankiz.eleves.polytechnique.fr>\n");
		$nntp->datasend("Newsgroups: ". $ng ."\n");
		$nntp->datasend("Subject: [Tour Kawa] Il y a un bug\n");
	      	$nntp->datasend("X-Newsreader:Tour Kawa Reminder v1.0\n");
		$nntp->datasend("\n\n");
		$nntp->datasend($text . "\n" . "Et merde bordel!\n");
               }
	for (@post)     {
	    $nntp->datasend($_);
	}
	close POST;
	$nntp->quit;
}
 
   
sub traiter_jour {
    my ($date,$name,$subject,$body) =@_;
    print "Date : " . $date . "\n";                                     #DEBUG
    $reqt="SELECT sections.nom,sections.newsgroup FROM kawa INNER JOIN trombino.sections ON kawa.section_id=sections.section_id WHERE date='$date'";
    my $rep = $dbh->prepare($reqt);
    $rep->execute;
    $non_vide=(($groupe,$newsgroup)=$rep->fetchrow_array());
    print "Groupe : " . $groupe . "\n";                                 #DEBUG
    if (!$non_vide) 
    	{
#	$groupe="personne";
	print "Le groupe est vide.\n";
    }
    if ($non_vide)
    	{
	if ($groupe ne "personne")
	    {
	    post($newsgroup,$groupe,1,$name,$subject,$body);
	    print "Envoi d'un post sur le ng : ".$newsgroup . "\n";
	    }
	else 
	    {
	    print "Pas de post envoyé\n";
	    }
	}
    else {
    	post($newsgroup,$groupe,2,$name,$subject,$body);
        print "Envoi d'un post de type 4\n";    	
	}
}

sub selection {
# definir la date
	my $name = "Tour kawa";
	my @subject = ("Au Bôb à 12h15","Un petit café demain?","Dans deux jours tour kawa");
	my $body = "";
	my $date;
	for ($i=0; $i<3; $i++) {
		$tm=localtime(time);
		$year = ($tm->year+ 1900);
		$month = ($tm->mon + 1);
		if($month < 10){
		   $month = "0" . $month; 
		}
		$day = ($tm->mday + $i);
		if($day < 10){
		   $day = "0" . $day; 
		}
		$date=$year . "-" . $month . "-" . $day;
		traiter_jour($date,$name,$subject[$i],$body);
	}
}

selection();








