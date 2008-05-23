<h2><span>Envoi de la clé de ta licence</span></h2>
{if $already_has}
  <p class="warning">Tu as déjà une clé pour ce logiciel. Tu peux visualiser la liste de tes licences sur <a href="profil/licences">cette page</a>.</p>
{elseif $already_asked}
  <p class="warning">Tu as déjà fait une demande pour ce logiciel. Le BR va t'envoyer ta clé de licence prochainement.</p>
{else}
  <p class="note">Ta demande a bien été prise en compte. Le BR va bientôt t'envoyer ta nouvelle clé pour {$logiciel_nom}.</p>
{/if}
<span><a href="profil/licences">Retour à la liste des licences</a></span>
{print_r($mail)}
