{config_load file="mails.conf" section="mdp_perdu"}
{if $mail_part eq "head"}
  {from full=#from#}
  {subject text="[Frankiz] Création de compte / Perte de mot de passe"}
{/if}
{if $mail_part eq "html"}
<b>Bonjour,</b><br />
<br />
Pour te connecter sur Frankiz, il te suffit de cliquer sur le lien ci-dessous: <br />
<a href='{$globals->baseurl}/profil/fkz?uid={$uid}&hash={$hash}'>{$globals->baseurl}/profil/fkz?uid={$uid}&hash={$hash}</a><br />
<br />
N'oublie pas ensuite de modifier ton mot de passe.<br />
<br />
Très cordialement,<br />
Le BR.
{/if}
{if $mail_part eq "text"}
Bonjour,

Pour te connecter sur Frankiz, il te suffit de te rendre à l'adresse suivante :
{$globals->baseurl}/profil/fkz?uid={$uid}&hash={$hash}

N'oublie pas ensuite de modifier ton mot de passe.

Très cordialement,
Le BR.
{/if}
