{foreach from=$fetes item=fete name=foo}
{$fete}{if !$smarty.foreach.foo.last}, {/if}
{/foreach}