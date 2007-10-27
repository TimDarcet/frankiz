<h1><span>Modification de son profil</span></h1>
<form enctype="multipart/form-data" method="post" accept-charset="UTF-8" id="mod_frankiz" action="profil/profil.php">
  <h2><span>Modification du compte Frankiz</span></h2>
  <div class="formulaire">
    <div>
      <span class="droite">
        <span class="note">Ne pas toucher ou laisser vide pour conserver l'ancien mot de passe</span>
      </span>
    </div>
    <div>
      <span class="gauche">Mot de passe :</span>
      <span class="droite">
        <input type="password" id="mod_frankizpasswd" name="passwd" value="12345678"/>
      </span>
    </div>
    <div>
      <span class="gauche">Retaper le mot de passe :</span>
      <span class="droite">
        <input type="password" id="mod_frankizpasswd2" name="passwd2" value="87654321"/>
      </span>
    </div>
    <div>
      <span class="droite">
        <span class="note">
	  L'authentification par cookie permet de se connecter automatiquement lorsque tu accèdes à frankiz. N'active pas cette authentification si tu te connectes sur un ordinateur qui n'est pas le tien.
        </span>
      </span>
    </div>
    <div>
      <span class="gauche">Utiliser l'authentification par cookie :</span>
      <span class="droite">
        <select id="mod_frankizcookie" name="cookie">
          <option value="oui" selected="selected">Activé</option>
          <option value="non">Désactivé</option>
        </select>
      </span>
    </div>
    <div>
      <span class="boutons">
        <input type="submit" name="changer_frankiz" value="Enregistrer" />
      </span>
    </div>
  </div>
</form>
    
<form enctype="multipart/form-data" method="post" accept-charset="UTF-8" id="mod_trombino" action="profil/profil.php">
  <h2><span>Changement de la fiche trombino</span></h2>
  <div class="formulaire">
    <div>
      <span class="gauche">Nom :</span>
      <span class="droite">
        <span id="mod_trombinonom">{$profil_fkz_nom} {$profil_fkz_prenom}</span>
      </span>
    </div>
    <div> 
      <span class="gauche">Login poly :</span>
      <span class="droite">
        <span id="mod_trombinologinpoly">{$profil_fkz_loginpoly}</span>
      </span>
    </div>
    <div>
      <span class="gauche">Promo :</span>
      <span class="droite">
        <span id="mod_trombinopromo">{$profil_fkz_promo}</span>
      </span> 
    </div>
    <div>
      <span class="gauche">Section :</span>
      <span class="droite">
        <span id="mod_trombinosection">{$profil_fkz_section} (compagnie {$profil_fkz_compagnie})</span>
      </span>
    </div>
    <div>
      <span class="gauche">Kazert :</span>
      <span class="droite">
        <span id="mod_trombinocasert">{profil_fkz_casert}</span>
      </span>
    </div>
    <div>
      <span class="gauche">Surnom :</span>
      <span class="droite">
        <input type="text" id="mod_trombinosurnom" name="surnom" value="{$profil_fkz_surnom}" />
      </span>
    </div>
    <div>
      <span class="gauche">Email :</span>
      <span class="droite">
        <input type="text" id="mod_trombinoemail" name="email" value="{$profil_fkz_email}" />
      </span>
    </div>
    <div>
      <span class="droite">
        <span class="image" style="display:block;text-align:center">
          <img src="trombino.php?image=true&amp;login={$profil_fkz_loginpoly}&amp;promo={$profil_fkz_promo}" 
               alt="photo" height="95" width="80" />
        </span>
       </span>
    </div>
    <div>
      <span class="droite">
        <span class="note">
	  Tu peux personnaliser le trombino en changeant ta photo. Attention, elle ne doit pas dépasser 200Ko ou 300x400 pixels. Les TOLmestres te rappellent que cette photo doit permettre de te reconnaître facilement. Propose donc plutôt une photo sur laquelle tu es seul, et où on voit bien ton visage.
        </span>
      </span>
    </div>
    <div>
      <span class="gauche">Nouvelle photo :</span>
      <span class="droite">
        <input type="hidden" id="MAX_FILE_SIZE" value="200000" />
        <input type="file" id="mod_trombinofile" name="file" />
      </span>
    </div>
    <div>
      <span class="boutons">
        <input type="submit" name="changer_trombino" value="Changer" />
      </span>
    </div>
  </div>
</form>
  
<form method="post" accept-charset="UTF-8" action="profil/profil.php" id="liste_binet">
  <span class="note">
    Si tu viens d'adhérer à un binet, n'hésite pas à le montrer et inscris le sur le TOL</span>
  <h2><span>Mes Binets</span></h2>
  <table class="liste">
    <tr>
      <td class="entete"> </td>
      <td class="entete" valign="top">Binet</td>
      <td class="entete" valign="top">Commentaire</td>
    </tr>
    {foreach from=$profil_fkz_binets item=binet}
    <tr>
      <td class="element" valign="top">
	<input type="checkbox" name="elements[{$binet.id}]"/>
      </td>
      <td class="element" valign="topi">
        {$binet.nom} :
      </td>
      <td class="element" valign="top">
	<input type="text" id="commentairecommentaire[{$binet.id}]" name="commentaire[{$binet.id}]" 
	       value="{$binet.commentaire}"/>
      </td>
    </tr>
    {/foreach}
    <tr>
      <td class="element" valign="top" />
      <td class="element" valign="top">Rajouter un binet</td>
      <td class="element" valign="top">
        <select id="commentaireliste_binet" name="liste_binet">
          <option value="default"></option>
	  {foreach from=$profil_fkz_binets_tous item=binet}
	  <option value="{$binet.id}">{$binet.nom}</option>
	  {/foreach}
	</select>
      </td>
    </tr>
    <tr>
      <td class="element" valign="top"/>
      <td class="element" valign="top">Autres commentaires</td>
      <td class="element" valign="top">
        <textarea id="commentaireperso" name="perso" rows="7" cols="50">{$profil_fkz_comment}</textarea>
      </td>
      <td class="boutons">
        <input type="submit" name="add_binet" value="Ajouter" />
      </td>
    </tr>
    <tr>
      <td class="boutons" colspan="3">
        <input type="submit" name="suppr_binet" value="Supprimer" 
	       onclick="return window.confirm('Es-tu sûr de vouloir supprimer ce binet ?')" />
        <input type="submit" name="mod_binet" value="Enregistrer les commentaires" /> 
      </td>
    </tr>
  </table>
</form>
