{config_load file="mails.conf" section="licence_admin"}
{if $mail_part eq "head"}
  {to full=#to#}
  {subject text="[Frankiz] Echec de la demande de clé de $prenom $nom (X$promo) pour $logiciel_nom"}
{/if}
{if $mail_part eq "html"}
  La demande de clé de {$prenom} {$nom} (X{$promo}) pour {$logiciel_nom} n'a pas pu aboutir faute de clé disponible.
{/if}
{if $mail_part eq "text"}
  La demande de clé de {$prenom} {$nom} (X{$promo}) pour {$logiciel_nom} n'a pas pu aboutir faute de clé disponible.
{/if}
