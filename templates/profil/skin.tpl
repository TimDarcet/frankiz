<form enctype="multipart/form-data" method="post" accept-charset="UTF-8" id="form_choix_skin" action="profil/skin/change_skin">
  <h2>
    <span>Choix de la skin</span>
  </h2>
  <div class="formulaire">
    <div>
      <span class="gauche">Skin :</span>
      <span class="droite">
      {foreach from=$liste_skins item=skin_desc key=skin_id}
	<input type="radio" id="{$skin_id}" name="newskin" value="{$skin_id}" {if $skin->id == $skin_id}checked="1"{/if} />
	{$skin_id} : {$skin_desc}<br />
      {/foreach}
      </span>
    </div>
    <div>
      <span class="boutons">
        <input type="submit" name="OK_skin" value="Appliquer" />
      </span>
    </div>
  </div>
</form>

<form enctype="multipart/form-data" method="post" accept-charset="UTF-8" id="form_param_skin" action="profil/skin/change_params">
  <h2>
    <span>Paramètres de la skin</span>
  </h2>
  <div class="formulaire">
    <div>
      <span class="droite">
        <span class="note">
	  Tu peux aussi ne pas faire apparaître tous les éléments de la skin. Tu gagneras ainsi de la
	  place. Choisis donc les éléments que tu veux afficher.
	</span>
      </span>
    </div>
    <div>
      <span class="gauche">
        Eléments :
      </span>
      <span class="droite">
      {foreach from=$liste_minimodules item=minimodule}
	<input type="checkbox" name="vis_{$minimodule.id}" {if $minimodule.est_visible}checked="1"{/if} />{$minimodule.desc}<br/>
      {/foreach}
      </span>
    </div>
    <div>
      <span class="boutons"><input type="submit" name="OK_param" value="Appliquer" /></span>
    </div>
  </div>
</form>
