<div class="fkz_qdj_question">{$minimodule.question}</div>
<div>
  {if $minimodule.compte1 + $minimodule.compte2 == 0}
    {assign='rouje' value=0}
    {assign='jone'  value=0}
  {else}
    {math assign='rouje' equation="(100 * x) / (x + y)" x=$minimodule.compte1 y=$minimodule.compte2 format="%.1f"}
    {math assign='jone'  equation="(100 * y) / (x + y)" x=$minimodule.compte1 y=$minimodule.compte2 format="%.1f"}
  {/if}
  <div class="fkz_qdj_rouje_reponse">
    <div class="col">
      <span class="blanc" style="height:{$jone}%"></span>
      <span class="rouje" style="height:{$rouje}%"></span>
      <br />
    </div>
    {$minimodule.reponse1}<br />
    {$minimodule.compte1} soit {$rouje}%<br />
  </div>
  <div class="fkz_qdj_jone_reponse">
    <div class="col">
      <span class="blanc" style="height:{$rouje}%"></span>
      <span class="jone"  style="height:{$jone}%"></span>
      <br />
    </div>
    {$minimodule.reponse2}<br />
    {$minimodule.compte2} soit {$jone}%<br />
  </div>
  <div class="fkz_end_qdj">
    <br />
    {if count($minimodule.votants)}
    <div class="fkz_qdj_dernier_votant">Derniers à répondre :</div>
    <ul class="fkz_qdj_last">
      {foreach from=$minimodule.votants item=votant name=foo}
      {if $smarty.foreach.foo.iteration <= 6}
      <li class="fkz_qdj_last">{$votant.ordre}. {$votant.eleve.surnom}</li>
      {/if}
      {/foreach}
    </ul>
    <br />
    <a class="class_qdj" href="qdj/">Classement QDJ</a>
    {/if}
  </div>
</div>
