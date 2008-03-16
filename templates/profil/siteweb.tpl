{assign var=siteweb_path value="`$globals->paths->pagespersos`/`$smarty.session.loginpoly`-`$smarty.session.promo`"}

{if isset($demande_ext|smarty:nodefaults)}
<span class='note'>Ta demande a été enregistrée, elle sera validée dans les meilleurs délais.</span>
{/if}

{if isset($siteweb_updated|smarty:nodefaults)}
<span class='note'>Ton site web a été mis à jour.</span>
{/if}

<span class='note'>
  Tu peux soumettre des archives .zip, .tar.gz, .tar ou .tar.bz2 qui seront décompressées.<br />
  Tu remplacera ainsi l'intégralité de ton site perso. <br />
  Attention, tu es limité à 10Mo. <br />
</span>

<form method='post' enctype='multipart/form-data' action='profil/siteweb/upload'>
  <div class='formulaire'>
    <input type='hidden' id='MAX_FILE_SIZE' value='10000000' />
    <span class='gauche'>Ton site :</span>
    <span class='droite'><input type='file' name='file' /></span>
    <span class='boutons'><input type='submit' value='Upload' /></span>
  </div>
</form>

{if is_dir($siteweb_path)}
<span class='note'>Nous te conseillons de sauvegarder ton site avant d'uploader le nouveau en cas de problème.</span>
<a href='profil/siteweb/download/zip'>Télécharger en zip</a><br />
<a href='profil/siteweb/download/tar.gz'>Télécharger en tar.gz</a><br />

<span class='note'>
  Si tu souhaites que ton site apparaisse sur la liste des sites élèves visibles de l'extérieur, clique sur le bouton "Extérieur". Cette demande est soumise à validation.<br />
  Dans tous les cas, ton site sera listé sur la liste des sites perso accessibles pour les gens loggués.
</span>
<form method='post' action='profil/siteweb/demande_ext'>
  <input type='submit' value='Extérieur' />
</form>
{/if}

{if is_dir($siteweb_path)}
<h2>Contenu actuel du site web.</h2>
{print_dir dir=$siteweb_path}
{/if}
