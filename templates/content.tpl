{if not $page_raw}
<div class="fkz_divers_1">
  <div class="fkz_divers_2">
    <div class="fkz_divers_3">
      <div class="fkz_divers_4">
        <div class="fkz_divers_5">
	  <div class="fkz_divers_6">
	    <div class="fkz_page_divers">
	      <div class="fkz_page_titre">
	        {$title}
	      </div>
	      <div class="fkz_page_corps">
{/if}
		{if !$pl_no_errors || $pl_failure}
		{include file="skin/common.errors.tpl"}
		{/if}
		
		{include file=$pl_tpl}
{if not $page_raw}
	      </div>
	    </div>
	  </div>
	</div>
      </div>
    </div>
  </div>
</div>
{/if}
