{if $demande}
<span class='note'>
  Le mail a été envoyé avec succès à l'adresse {$mail}. Il te permettra de te connecter une fois au site
  web Frankiz pour changer ton mot de passe ou choisir ton mot de passe si tu n'en as pas encore défini un.
</span>
{/if}

<form enctype='multipart/form-data' method='post' action='profil/mdp_perdu'>
  <span class='note'>
    Si tu souhaites créer ton compte Frankiz, ou si tu as perdu ton mot de passe, entre ton loginpoly.promo
    (par exemple dupont.2002) dans le champ ci-dessous. Tu recevras dans les minutes qui suivent un courriel
    te permettant d'accéder à la partie réservée de Frankiz. Une fois authentifié grâce au lien contenu dans
    le courriel, n'oublie pas de changer ton mot de passe.
  </span>
  <div class='formulaire'>
    <div>
      <span class='gauche'>login.promo :</span>
      <span class='droite'><input type='text' name='loginpoly' value='' /></span>
    </div>
    <div>
      <span class='boutons'>
        <input type='submit' name='valider' value='Valider' />
      </span>
    </div>
  </div>
</form>
