{config_load file="mails.conf" section="licence_admin"}
{if $mail_part eq "head"}
  {to full=#to#}
  {subject text="[Frankiz] Demande de clé de $prenom $nom (X$promo) pour $logiciel_nom"}
{/if}
{if $mail_part eq "html"}
  La clé attribuée à {$prenom} {$nom} (X{$promo}) pour {$logiciel_nom} est : {$cle}.
{/if}
{if $mail_part eq "text"}
  La clé attribuée à {$prenom} {$nom} (X {$promo}) pour {$logiciel_nom} est : {$cle}.
{/if}
