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
	  <option value='courantes'>Promos courantes</option>
	  <option value='toutes'>Toutes les promos</option>
      {iterate from=$promos item=promo}
	  <option value='{$promo.promo}'>{$promo.promo}</option>
	  {/iterate}
	</select>
      </td>
      <td>
        <span class='gauche'>Section :</span>
	<select name='section'>
      <option value='toutes'>Toutes</option>
	  {iterate from=$sections item=sect}
	  <option value='{$sect.section_id}'>{$sect.section_nom}</option>
	  {/iterate}
	</select>
      </td>
      <td>
        <span class='gauche'>Binet :</span>
	<select name='binet'>
      <option value='tous'>Tous</option>
	  {iterate from=$binets item=binet}
	  <option value='{$binet.binet_id}'>{$binet.binet_nom}</option>
	  {/iterate}
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
{if $tol_admin}
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
{/if}
    <tr>
      <td>
        <span class='gauche'>Nationalité :</span>
	<select name='nation'>
      <option value='toutes'>Toutes</option>
	  {iterate from=$nations item=nation}
	  <option value='{$nation.nation_id}'>{$nation.nation_name}</option>
	  {/iterate}
	</select>
      </td>
{if $tol_admin}
      <td>
        <span class='gauche'>@Mac :</span>
	<input type='text' name='mac' value='' />
      </td>
      <td>
        <span class='gauche'>Tol Admin :</span>
	<input type='checkbox' name='toladmin' />
      </td>
{else}
      <td>
      </td>
      <td>
      </td>
{/if}
    </tr>
  </table>
  <p class='bouton'>
    <input type='reset' name='effacer' value='Remise à Zéro' />
    <input type='submit' name='chercher' value='Chercher' />
  </p>
</form>
{/css_block}
