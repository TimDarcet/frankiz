<?xml version='1.0' encoding='UTF-8' ?>
<!DOCTYPE frankiz PUBLIC \"-//BR//DTD FRANKIZ 1.0//FR\" \"http://frankiz.polytechnique.fr/frankiz.dtd\">
<html xmlns="http://www.w3.org/1999/xhtml" lang="fr">
  {include file="header.tpl"}
  <body>
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
        {include file="minimodule.tpl" module_name="Fetes"}
        {include file="minimodule.tpl" module_name="LienTol"}
        {include file="minimodule.tpl" module_name="LienIK"}
        {include file="minimodule.tpl" module_name="LienWikix"}
        {include file="minimodule.tpl" module_name="TourKawa"}
        {include file="minimodule.tpl" module_name="Sondages"}
        {include file="minimodule.tpl" module_name="Qdj"}
        {include file="minimodule.tpl" module_name="QdjHier"}
        {include file="minimodule.tpl" module_name="Meteo"}
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
        {include file="minimodule.tpl" module_name="Activites"}
        {include file="minimodule.tpl" module_name="LiensNavigation"}
        {include file="minimodule.tpl" module_name="LiensProfil"}
        {include file="minimodule.tpl" module_name="LiensPerso"}
	{include file="minimodule.tpl" module_name="LiensContacts"}
	{include file="minimodule.tpl" module_name="LiensUtiles"}
	{include file="minimodule.tpl" module_name="Stats"}
      </div>
      <div class="fkz_centre">
	{include file="content.tpl"}
      </div>
    </div>
    <div class="fkz_end_page"></div>
  </body>
</html>
