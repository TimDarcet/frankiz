{css_block class='fkz_trombino_eleve'}
<form class='trombino' enctype='multipart/form-data' method='post' action='tol/'>
  <h3>Rechercher sur le trombino</h3>
  <table>
    <tr>
      <td>
        <span class='gauche'>Prénom :</span>
	<input type='text' name='prenom' value='' />
      </td>
      <td>
        <span class='gauche'>Nom :</span>
	<input type='text' name='nom' value='' />
      </td>
      <td>
        <span class='gauche'>Surnom :</span>
	<input type='text' name='surnom' value='' />
      </td>
    </tr>
    <tr>
      <td>
        <span class='gauche'>Promo :<span>
	<select name='promo'>
	  {foreach from=$promos key=id item=promo}
	  <option value='{$id}'>{$promo}</option>
	  {/foreach}
	</select>
      </td>
      <td>
        <span class='gauche'>Section :</span>
	<select name='section'>
	  {foreach from=$sections key=id item=sect}
	  <option value='{$id}'>{$sect}</option>
	  {/foreach}
	</select>
      </td>
      <td>
        <span class='gauche'>Binet :</span>
	<select name='binet'>
	  {foreach from=$binets key=id item=binet}
	  <option value='{$id}'>{$binet}</option>
	  {/foreach}
	</select>
      </td>
    </tr>
    <tr>
      <td>
        <span class='gauche'>Login :</span>
	<input type='text' name='loginpoly' value='' />
      </td>
      <td>
        <span class='gauche'>Tel :</span>
	<input type='text' name='phone' value='' />
      </td>
      <td>
        <span class='gauche'>Casert :</span>
	<input type='text' name='casert' value='' />
      </td>
    </tr>
    <tr>
      <td>
        <span class='gauche'>IP :</span>
	<input type='text' name='ip' value='' />
      </td>
      <td>
        <span class='gauche'>DNS :</span>
	<input type='text' name='dns' value='' />
      </td>
      <td>
        <span class='gauche'>Prise :</span>
	<input type='text' name='prise' value='' />
      </td>
    </tr>
    <tr>
      <td>
        <span class='gauche'>Nationalité :</span>
	<select name='nation'>
	  {foreach from=$nations key=id item=nation}
	  <option value='{$nation}'>{$nation}</option>
	  {/foreach}
	</select>
      </td>
      <td>
        <span class='gauche'>@Mac :</span>
	<input type='text' name='mac' value='' />
      </td>
      <td>
        <span class='gauche'>Tol Admin :</span>
	<input type='checkbox' name='toladmin' />
      </td>
    </tr>
  </table>
  <p class='bouton'>
    <input type='reset' name='effacer' value='Remise à Zéro' />
    <input type='submit' name='chercher' value='Chercher' />
  </p>
</form>
{/css_block}
