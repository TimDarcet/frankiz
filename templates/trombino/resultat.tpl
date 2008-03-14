{css_block class='fkz_trombino_eleve'}
<h3 class='nom'>{$eleve.prenom} {$eleve.nom}</h3>
<div class='fkz_trombino_photo'>
  <a href="trombino.php?image=show&amp;login={$eleve.login}&amp;promo={$eleve.promo}">
    <img height='122' src='trombino.php?image=true&amp;login={$eleve.login}&amp;promo={$eleve.promo}' />
  </a>
</div>
<div class='fkz_trombino_infos'>
  <p class='telephone'>
    Tel: {$eleve.tel} Port: {$eleve.port}
  </p>
  <p class='mail'>
    Mail: <a href='mailto:{$eleve.mail}'>{$eleve.mail}</a>
  </p>
  <p class='casert'>
    Casert: {$eleve.piece_id}
  </p>
  <p class='section'>
    Section: {$eleve.section}
  </p>
  <p class='nation'>
    Nation: {$eleve.nation}
  </p>
</div>
<div class='fkz_trombino_section'>
  <a href='tol/?section={$eleve.section_id}'>
    <img height='84' width='63' alt='{$eleve.section}' src='skins/xhtml-default/images/sections/{$eleve.section|lower}{if $eleve.promo % 2 eq 0}0{else}1{/if}.jpg' />
  </a>
</div>
<div class='fkz_trombino_infos2'>
  <p class='promo'>
    {$eleve.promo}
  </p>
  <p class='surnom'>
    {$eleve.surnom}
  </p>
  <p class='date_naissance'>
    {$eleve.date_nais|date_format:"%D"}
  </p>
</div>
<div class='binets'>
  {if $tol_admin}
  Prise: {$eleve.prise}
  <ul>
    {foreach from=$eleve.prise_log item=log}
    <li>
      {$log.ip} ({$log.dns}) - Client xNet : {$log.client}
      <ul>
        {foreach from=$log.mac_log item=mac}
	<li>
	  {$mac.time} : 
	  <a href='tol/?chercher&amp;mac={$mac.id}'>{$mac.id}</a>
	  <em>{$mac.constructeur}</em>
	</li>
	{/foreach}
      </ul>
    </li>
    {/foreach}
  </ul>
  <br />
  {/if}
  Binets:
  <ul>
    {foreach from=$eleve.binets item=binet}
    <li>
      <a href='tol/?binet={$binet.id}'>{$binet.nom}</a>
      <em>({$binet.remarque})</em>
    </li>
    {foreachelse}
    <li>Aucun</li>
    {/foreach}
  </ul>
</div>
<div>
  <p>{$eleve.commentaire}</p>
</div>
<a class='lien' href='https://www.polytechnique.org/profile/{$eleve.prenompolyorg}.{$eleve.nompolyorg}.{$eleve.promo}'>Fiche sur polytechnique.org</a><br />
{if $tol_admin}
<a class='lien' href='admin/user.php?id={$eleve.id}'>Administrer {$eleve.prenom} {$eleve.nom}</a><br />
<a class='lien' href='?su={$eleve.id}'>Prendre l'identité de {$eleve.prenom} {$eleve.nom}</a><br />
{/if}
{/css_block}
