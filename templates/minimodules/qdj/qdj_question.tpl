<div class="fkz_qdj_question">{$minimodule.question}</div>
<div>
  <div class="fkz_qdj_rouje">
    <a href="?qdj={$minimodule.date}&amp;vote=1">{$minimodule.reponse1}</a>
  </div>
  <div class="fkz_qdj_jone">
    <a href="?qdj={$minimodule.date}&amp;vote=2">{$minimodule.reponse2}</a>
  </div>
  <div class="fkz_end_qdj">
    <br />
    {if count($minimodule.votants)}
    <div class="fkz_qdj_dernier_votant">Derniers à répondre :</div>
    <ul class="fkz_qdj_last">
      {foreach from=$minimodule.votants item=votant name=foo}
      {if $smarty.foreach.foo.iteration <= 6}
      <li class="fkz_qdj_last">{$votant.ordre} {$votant.eleve.surnom}</li>
      {/if}
      {/foreach}
    </ul>
    {/if}
    <a class="class_qdj" href="qdj/">Classement QDJ</a>
  </div>
</div>
