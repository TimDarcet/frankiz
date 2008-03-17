{config_load file="mails.conf" section="demande_ip"}
{if $mail_part eq "head"}
{from full=#from#}
{to full=#to#}
{subject text="[Frankiz] Demande d'enregistrement d'une nouvelle machine"}
{/if}
{if $mail_part eq "html"}
<p>
{$prenom} {$nom} a demandé l'enregistrement d'une nouvelle machine pour la raison suivante:
{$raison}
</p>
<p>
Pour valider ou non cette demande, va sur la page:<br /><br />
<div align='center'>
  <a href='{$globals->baseurl}/admin/valid_ip.php'>{$globals->baseurl}/admin/valid_ip.php</a>
</div>
</p>
Cordialement,<br />
Le BR<br />
{/if}
