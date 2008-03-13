<span class='note'>Ton login est loginpoly.promo</span>

<form enctype='multipart/form-data' method='post' action='{$smarty.server.REQUEST_URI}'>
  <h2><span>Connexion</span></h2>
  <div class='formulaire'>
    <div>
      <span class='gauche'>Identifiant:</span>
      <span class='droite'><input type='text' name='login' value='' /></span>
    </div>
    <div>
      <span class='gauche'>Mot de passe:</span>
      <span class='droite'><input type='password' name='password' value='' /></span>
    </div>
  </div>
  <div>
    <span class='boutons'><input type='submit' name='start_connexion' value='Connexion' /></span>
  </div>
  <p>
    Si tu as oublié ton mot de passe ou que tu n'as pas encore de compte, clique
    <a href='profil/mdp_perdu'>ici</a>.
  </p>
</form>
