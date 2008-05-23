{config_load file="mails.conf" section="licence_cle"}
{if $mail_part eq "head"}
  {from full=#from#}
  {subject text="[Frankiz] Licence pour $logiciel_nom"}
{/if}
{if $mail_part eq "html"}
  <b>Bonjour,</b>
  <p>La clé qui t'a été attribuée pour {$logiciel_nom} est :</p>
  {$cle}<br />
  {if $pub_domaine}
    <p>Avec {$logiciel_nom}, tu disposes maintenant d'une machine qui peut se connecter au domaine. <br />
      Tu trouveras dans l'infoBR les informations te permettant de mener à bien cette opération.<br />
      Grâce au domaine, le réseau de l'X est plus sûr et tes demandes d'assistance seront simplifiées et donc accélérées.</p>
  {/if}
  Très cordialement,<br />
  Le BR.
{/if}
{if $mail_part eq "text"}
  Bonjour,
  La clé qui t'a été attribuée pour {$logiciel_nom} est :
  {$cle}
  {if $pub_domaine}
    
    Avec {$logiciel_nom}, tu disposes maintenant d'une machine qui peut se connecter au domaine Windows.
    Tu trouveras dans l'infoBR les informations te permettant de mener à bien cette opération.
    Grâce au domaine, le réseau de l'X est plus sûr et tes demandes d'assistance seront simplifiées et donc accélérées.
  {/if}

  Très cordialement,
  Le BR
{/if}
