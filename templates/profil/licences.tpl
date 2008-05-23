<h2><span>Mes Licences</span></h2>
  <table class="liste">
    <tr>
      <td class="entete">Logiciel</td>
      <td class="entete">Licence</td>
      <td class="entete"> </td>
    </tr>
    {foreach from=$licences item=licence}
    <tr>
      <td class="element">{$licence.nom_logiciel}</td>
      <td class="element">{$licence.cle}</td>
      <td class="element">{if !$licence.attrib}Demande en attente{/if}</td>
    </tr>
    {/foreach}
  </table>

<h2><span>Demande de licence</span></h2>
<p class="note">Dans le cadre de l'accord MSDNAA, chaque étudiant de polytechnique a le droit à une version de Windows XP Pro et une de Windows Vista Business gratuites, légales et attibuées à vie.<br />
Si tu as besoin d'une clé pour un logiciel téléchargé sur ftp://enez/, et qu'il n'est pas proposé dans la liste, envoi un mail aux <a href="mailto:msdnaa-licences@frankiz.polytechnique.fr">Admins Windows</a>.</p>

<form method="post" action="profil/licences/cluf" id="licences_liste_logiciels">
  <h3>Tu peux demander une licence pour :</h3>
  <div class="formulaire">
    <div>
      <span class="gauche">Demander une licence pour :</span>
      <span class="droite">
        <select name="logiciel">
        {foreach from=$logiciels key=k item=logiciel}
          <option value="{$k}">{$logiciel}</option>
        {/foreach}
        </select>
      </span>
    </div>
    <div>
      <span class="boutons">
        <input type="submit" name="valid" value="Demander">
      </span>
    </div>
  </div>
</form>
