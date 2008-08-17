{include file="skin/common.doctype.tpl"}
    <link rel="stylesheet" type="text/css" href="css/tranquille/tranquille.css" media=all />
{include file="skin/common.header.tpl"}
  </head>
  <body>
  {include file=skin/common.devel.tpl}
    <div class="fkz_entetes">
      <div class="fkz_logo">
        <a href="index.php"><span class="fkz_logo">Frankiz</span></a>
      </div>
      <div class="fkz_logo_eleves">
	<span class="fkz_logo_eleves">le site Web des élèves de l'École Polytechnique</span>
      </div>
    </div>
    <div class="fkz_page">
      <div class="fkz_droite">
        {include file="minimodule.tpl" module_name="fetes"}
        {include file="minimodule.tpl" module_name="lienTol"}
{*        {include file="minimodule.tpl" module_name="lienIK"} *}
        {include file="minimodule.tpl" module_name="lien_wikix"}
{*         {include file="minimodule.tpl" module_name="tour_kawa"} *}
{*         {include file="minimodule.tpl" module_name="sondages"} *}
{*         {include file="minimodule.tpl" module_name="qdj"} *}
{*        {include file="minimodule.tpl" module_name="qdj_hier"} *}
{*        {include file="minimodule.tpl" module_name="meteo"} *}
        <p class="valid">
	  <a href="http://validator.w3.org/check?uri=referer">
	    <span class="valid_html"></span>
	  </a>
	  <a href="http://jigsaw.w3.org/css-validator/check/referer">
	    <span class="valid_css"></span>
	  </a>
	</p>
      </div>
      <div class="fkz_gauche">
         {include file="minimodule.tpl" module_name="activites"}
         {include file="minimodule.tpl" module_name="liensnavigation"}
         {include file="minimodule.tpl" module_name="liensprofil"}
         {include file="minimodule.tpl" module_name="lienspropositions"}
{* 	{include file="minimodule.tpl" module_name="liens_perso"} *}
{* 	{include file="minimodule.tpl" module_name="liens_contacts"} *}
{* 	{include file="minimodule.tpl" module_name="liens_utiles"} *}
{* 	{include file="minimodule.tpl" module_name="stats"} *}
      </div>
      <div class="fkz_centre">
 {*       {include file="minimodule.tpl" module_name="anniversaires"} *}
{*	{include file="minimodule.tpl" module_name="virus"} *}
	{include file="content.tpl"}
      </div>
    </div>
    <div class="fkz_end_page"></div>
  </body>
</html>
