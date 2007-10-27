{if count($minimodule.courants) > 0)
<p>En Cours</p>
{/if}

{foreach from=$minimodule.courants item=sondage}
<a class="sondage_courant" href="$sondage.url">
  {if $sondage.restriction}{[$restriction]}{/if} {$sondage.titre} ({$sondage.date}
</a>
{/foreach}

{if count($minimodule.anciens) > 0)
<p>Anciens</p>
{/if}

{foreach from=$minimodule.anciens item=sondage}
<a class="sondage_ancien" href="$sondage.url">
  {if $sondage.restriction}{[$restriction]}{/if} {$sondage.titre} ({$sondage.date}
</a>
{/foreach}

{if count($minimodule.anciens_user) > 0)
<p>Mes anciens sondages</p>
{/if}

{foreach from=$minimodule.anciens_user item=sondage}
<a class="sondage_ancien" href="$sondage.url">
  {if $sondage.restriction}{[$restriction]}{/if} {$sondage.titre} ({$sondage.date}
</a>
{/foreach}
