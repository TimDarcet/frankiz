{if $nouvelle_demande}
<span class='note'>
  Nous avons bien pris en compte ta demande d'enregistrement de machine. Nous allons la traiter dans les plus brefs délais.
</span>
{else}
{if $demande_en_cours}
<span class='warning'>
  Tu as déja fait une demande d'enregistrement d'une nouvelle machine. Attends que le BR te valide la première pour en faire une seconde si cela est justifié.
</span>
{/if}
{/if}
<form enctype='multipart/form-data' method='post' id='demandeip' action='profil/reseau/demande_ip'>
  <span class='note'>
    Si tu as juste changé d'ordinateur, tu peux garder la même IP et la même configuration réseau. Tu n'as donc pas à demander une nouvelle IP !
  </span>
  <div class='formulaire'>
    <div>
      <span class='gauche'>
        Je fais cette demande parce que:
      </span>
      <span class='droite'>
        <input type='radio' name='type' value='1' checked='checked' /> J'ai installé un 2ème ordinateur dans mon casert et je souhaite avoir une nouvelle adresse IP pour cette machine.<br />
        <input type='radio' name='type' value='2' /> Autre raison (précise ci-dessous) :<br />
      </span>
    </div>
    <div>
      <span class='gauche'>
        Raison:
      </span>
      <span class='droite'>
        <textarea name='raison' rows='7' cols='50'>
        </textarea>
      </span>
    </div>
    <div>
      <span class='boutons'>
        <input type='submit' name='demander' value='Demander une nouvelle IP' />
      </span>
    </div>
  </div>
</form>
