{if $original}
<img src='tol/photo/img/{$promo}/{$login}/original' /><br />
<a href='tol/photo/{$promo}/{$login}'>Photo actuelle</a>
{else}
<img src='tol/photo/img/{$promo}/{$login}' /><br />
<a href='tol/photo/{$promo}/{$login}/original'>Photo originale</a>
{/if}
