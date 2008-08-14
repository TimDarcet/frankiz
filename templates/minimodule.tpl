{$module_name}
{* {if not $minimodules.$module_name->is_empty()} *}
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
	        {include file=$minimodules.$module_name->get_template() minimodule=$minimodules.$module_name->get_params()}
	      </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
{* {/if} *}
