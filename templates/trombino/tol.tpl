{css_block class='fkz_trombino'}
{if isset($results|smarty:nodefaults)}
{if count($results) > 99}
<span class='warning'>Trop de résultats : seuls les 100 premiers sont affichés</span>
{elseif count($results) > 0}
<span class='note'>{$nbr_results} résultats trouvés</span>
{else}
<span class='warning'>Aucun résultat trouvé</span>
{/if}
{foreach from=$results item=result}
{include file=trombino/resultat.tpl eleve=$result}
{/foreach}
{/if}

{include file=trombino/recherche.tpl}
{/css_block}
