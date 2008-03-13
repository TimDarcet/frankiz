{config_load file="mails.conf" section="mdp_perdu"}
{if $mail_part eq "head"}
{from full=#from#}
{to full=#to#}
{subject text="[Frankiz] Création de compte / Perte de mot de passe"}
{/if}
{if $mail_part eq "html"}
<b>Bonjour,</b><br />
<br />
Pour te connecter sur Frankiz, il te suffit de cliquer sur le lien ci-dessous: <br />
<a href='{$base}/profil/fkz?uid={$uid}&hash={$hash}'>{$base}/profil/fkz?uid={$uid}&hash={$hash}</a><br />
<br />
N'oublie pas ensuite de modifier ton mot de passe.<br />
<br />
Très cordialement,<br />
Le BR.
{/if}
