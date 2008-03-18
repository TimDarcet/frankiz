<table>
  {foreach from=$smarty.session.liens_perso key=k item=lien}
  <form method='post' action='profil/liens_perso/del'>
    <tr>
      <td>
        <input type='hidden' name='lien_perso' value='{$k}' />
        {$lien}
      </td>
      <td>
        <input type='submit' value='Supprimer' />
      </td>
    </tr>
  </form>
  {/foreach}
  <form method='post' action='profil/liens_perso/add'>
    <tr>
      <td>
        <input type='text' name='lien_perso'>
      </td>
      <td>
	<input type='submit' value='Ajouter' />
      </td>
    </tr>
  </form>
</table>

