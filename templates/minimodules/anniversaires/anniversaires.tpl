{foreach from=$minimodule.anniversaires key=promo item=anniversaires_promo}
{if count($anniversaires_promo)}
<a href='trombino.php/anniversaires&amp;romo={$promo}'>{$promo}</a>: 
{/if}
{foreach from=$anniversaires_promo item=anniv name=foo}
{$anniv.prenom} {$anniv.nom}{if !$smarty.foreach.foo.last}, {/if}
{/foreach}
<br />
{/foreach}
