{config_load file=mails.conf section=siteweb_ext}
{if $mail_part eq "head"}
{from full=#from#}
{to full=#to#}
{subject text="[Frankiz] Demande de page perso de `$prenom` `$nom`"}
{elseif $mail_part eq "html"}
{$prenom} {$nom} a demandé que sa page perso apparaisse sur la liste des sites personnels. <br/>
<br />
Pour valider ou non cette demande, va sur la page suivante :<br />

<div align='center'><a href='{$globals->baseurl}/admin/valid_pageperso.php'>{$globals->baseurl}/admin/valid_pageperso.php</a></div>
<br />
<br />
Cordialement,<br />
Les Webmestres de Frankiz<br />
{/if}
