{if count($sondages_courants) > 0)
<p>En Cours</p>
{/if}

{foreach from=$sondages_courants item=sondage}
<a class="sondage_courant" href="$sondage.url">
  {if $sondage.restriction}{[$restriction]}{/if} {$sondage.titre} ({$sondage.date}
</a>
{/foreach}

{if count($sondages_anciens) > 0)
<p>Anciens</p>
{/if}

{foreach from=$sondages_anciens item=sondage}
<a class="sondage_ancien" href="$sondage.url">
  {if $sondage.restriction}{[$restriction]}{/if} {$sondage.titre} ({$sondage.date}
</a>
{/foreach}

{if count($sondages_anciens) > 0)
<p>Mes anciens sondages</p>
{/if}

{foreach from=$sondages_anciens_user item=sondage}
<a class="sondage_ancien" href="$sondage.url">
  {if $sondage.restriction}{[$restriction]}{/if} {$sondage.titre} ({$sondage.date}
</a>
{/foreach}
