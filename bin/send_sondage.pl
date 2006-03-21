#!/usr/bin/perl
#
# Ce script permet l'envoie des résultats des sondages à leur demandeurs
#

$script_dir = `/bin/dirname $0`;
$script_dir =~ s/\n//gs;
require "$script_dir/../etc/config.pl";

use MIME::Lite;
use Encode qw/encode decode/;
use DBI();

my       $dbh = DBI->connect("DBI:mysql:database=$mysql_frankiz2{database}:host=$mysql_frankiz2{host}",
                             "$mysql_frankiz2{user}","$mysql_frankiz2{password}",{'RaiseError'=>1});
my $separator = "|";
my   $my_mail = 'web@frankiz.polytechnique.fr';

# Retourne l'adresse mail du propriétaire du sondage
# @param eleve_id INTEGER
# @return mail STRING
sub get_mail($) {
    my ($eleve_id) = @_;
    my $eleve = $dbh->prepare("SELECT login, mail FROM trombino.eleves WHERE eleve_id='$eleve_id'");
    $eleve->execute();
    my ($login, $mail) = $eleve->fetchrow_array();

    $mail = $login.'@poly.polytechnique.fr' if(length($mail) == 0);
    return $mail;
}

# Retourne un CSV des résultats du sondage indiqué
# @param sondage_id INTEGER
# @return cvs STRING
sub make_CSV($) {
    my ($sondage_id) = @_;
    my          $csv = "";
    my      $answers = $dbh->prepare("SELECT answer_id, question_num, reponse FROM sondage_reponse WHERE sondage_id='$sondage_id' ORDER BY answer_id, question_num");
    $answers->execute();

    my $last_aid = 0;
    my $last_qid = 0;
    while(($answer_id, $question_num, $reponse) = $answers->fetchrow_array()) {
        if ($last_aid != $answer_id) {
            $last_qid = 0;
            $last_aid = $answer_id;
            $csv .= "\n";
        }
        while ($last_qid < $question_num) {
            $csv .= $separator;
            $last_qid++;
        }
        $reponse =~ s/\n/<br\/>/g;
        $reponse =~ s/\|/!/g;
        $csv .= $reponse;
    }

    return $csv."\n";
}

# Envoie un mail au propriétaire du sondage
# @param sondage_id INTEGER
# @param eleve_id INTEGER id de la personne ayant proposé le sondage
# @param nom STRING nom du sondage
sub send_result($$$) {
    my ($sondage_id, $eleve_id, $nom) = @_;

    my $msg = new MIME::Lite
            From    => $my_mail,
            To      => get_mail($eleve_id),
            CC      => $my_mail,
            Type    => 'multipart/mixed',
            Subject => encode('MIME-Q', "Résultat de ton sondage Frankiz : $nom");
    attach $msg
            Type    => 'text/plain; charset=utf-8',
            Data    => "Bonjour,\n\n".
                       "Tu trouveras ci-joint les résultats de ton sondage Frankiz. Ils sont organisés \n".
                       "de la manière suivante :\n".
                       "question1|question2|question3|....|...\n\n".
                       "Chaque ligne étant les réponses d'une personne différente.\n\n".
                       "Ce format est facilement importable dans n'importe quel tableur.\n".
                       "-- \n".
                       "Les Webmestres de Frankiz";
    attach $msg
            Type    => 'text/plain; charset=utf-8',
            Filename => 'resultats.csv',
            Data    => make_CSV($sondage_id);
    $msg->send();
}

# Recherche les sondages pour lesquels il faut envoyer un mail et réalise cet envoie
sub send_all() {
    my $sondages = $dbh->prepare("SELECT sondage_id, eleve_id, titre FROM sondage_question WHERE perime < NOW() AND sent = 0");
    $sondages->execute();

    while(($sondage_id, $eleve_id, $titre) = $sondages->fetchrow_array()) {
        send_result($sondage_id, $eleve_id, $titre);
        $dbh->do("UPDATE sondage_question SET sent = '1' WHERE sondage_id = '$sondage_id'");
    }
}

send_all();
