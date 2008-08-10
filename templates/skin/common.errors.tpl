{foreach from=$pl_triggers key=type item=triggers}
{if $triggers|@count}
<div class="{$type}">
  <ul>
    {foreach from=$triggers item=err}
    <li>{$err|smarty:nodefaults}</li>
    {/foreach}
  </ul>
</div>
{/if}
{/foreach}

