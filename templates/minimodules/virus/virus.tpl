<div class="warning">

Attention! Tu es actuellement infecté par {if count($minimodule.infections) > 1}des{else}un{/if} virus!
<ul>
{foreach from=$minimodule.infections item=infection}
<li>{$infection.nom_virus} depuis le {$infection.date}.</li>
{/foreach}
</ul>

</div>
