<form method="post" action="profil/licences/final" id="licences_raison" accept-charset="UTF-8" enctype="multipart/form-data">
  <input type="hidden" name="logiciel" value="{$logiciel}">
  <h2><span>Motivation de la demande</span></h2>
  <div class="formulaire">
    <div>
    {if $logiciel_rare}
      <span class="droite">
        <span class="warning">Vu le faible nombre de licences que nous possédons pour ce logiciel, il nous faut une raison valable pour te l'attribuer.</span>
      </span>
    {else}
      <span class="droite">
        <span class="warning">Tu ne figures pas dans la liste des personnes ayant droit à une licence dans le cadre du programme MSDNAA</span>
        <p>Seuls les étudiants sur le platâl peuvent faire une demande pour une license Microsoft dans le cadre MSDNAA. s'il s'agit d'une erreur, tu peux le signaler aux admin@windows.</p>
        <p>Si c'est le cas, indique la raison de ta demande :</p>
      </span>
    {/if}
    </div>
    <div>
      <span class="gauche">Raison :</span>
      <span class="droite">
        <textarea name="raison" id="licence_raison_text" rows='7' cols='50'></textarea>
      </span>
    </div>
    <div>
      <span class="boutons">
        <input type="submit" name="valid" value="Valider">
        <input type="submit" name="refus" value="Ne rien faire">
      </span>
    </div>
  </div>
</form>
