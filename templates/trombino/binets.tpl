{foreach from=$categories key=cat_id item=cat_desc}
<h2>{$cat_desc}</h2>
{foreach from=$binets.$cat_id item=binet}
<h3>
  <a href='{$binet.http}'>{$binet.nom}</a>
</h3>
<span class='image' style='display:block; text-align:center'>
  <img src='tol/binets/logo/{$binet.id}' alt='{$binet.nom}' />
</span>
{if $binet.description}
<p>
  {$binet.description}
</p>
{/if}
{/foreach}
{/foreach}
