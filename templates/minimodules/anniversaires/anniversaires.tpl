{foreach from=$anniversaires key=promo item=anniversaires_promo}
{if count($anniversaires_promo)}
<a href='trombino.php/anniversaires&promo={$promo}'>{promo}</a>: 
{/if}
{foreach from=$anniversaires_promo}
{$anniv.prenom} {$anniv.nom}{if !$smarty.foreach.last}, {/if}
{/foreach}
{/foreach}
