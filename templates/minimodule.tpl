{if isset($minimodules.$module_name) and not $minimodules.$module_name->is_empty()}
<div class="fkz_module_1">
  <div class="fkz_module_2">
    <div class="fkz_module_3">
      <div class="fkz_module_4">
        <div class="fkz_module_5">
          <div class="fkz_module_6">
            <div class="fkz_module" id="{$module_name}">
	      <div class="fkz_titre">
	        <span id="{$module_name}_logo"></span>
		{$minimodules.$module_name->get_titre()}
	      </div>
	      <div class="fkz_module_corps">
	        {$minimodules.$module_name->print_template()}
	      </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
{/if}
