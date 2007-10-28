<h2><span>Le Classement QDJ</span></h2>
<span class="commentaire">
Le classement QDJ est remis à jour tous les deux mois (1er mars, 1er mai, ...).<br/>
A l'issue de ces deux mois, le premier du classement se voit décerner une coupe, le dernier une cuillère en bois et celui qui a le diagramme le plus "homogène" un coup au Bôb avec les deux autres.<br/>
<br/>
Les règles du classement QDJ sont simples. Suivant le moment de la journée où vous votez, vous obtenez plus ou moins de points. Le but étant d'en accumuler le maximum :)<br/>
Il y a 10 façons de gagner ou de perdre des points:
<ul>
  <li>Voter premier rapporte 5 points</li>
  <li>Voter second rapporte 2 points</li>
  <li>Voter troisième rapporte 1 points</li>
  <li>Voter 42 rapporte 4.2 points</li>
  <li>Voter 69 rapporte 6.9 points</li>
  <li>Voter 314 rapporte 3.14 points</li>
  <li>Voter avec la même position que les derniers chiffres de l'ip fait gagner 3 points (c'est bien de savoir lire l'infoBR :) )</li>
  <li>Voter treizième vous fait perdre 13 points, (C'est mal d'essayer de prendre l'ip de la passerelle !)</li>
  <li>Règle bonus qui rapporte 7 points au réveil !</li>
  <li>Enfin, proposer une "bonne" QDJ, c'est-à-dire que le QDJMaster acceptera de passer, rapporte 7.1 points le jour où elle passe (utilisez votre cerveau pour battre ceux qui utilisent des scripts)</li>
</ul>

Amusez-vous bien et surtout, lisez la QDJ avant de voter.
</span>

<form enctype="multipart/form-data" method="post" accept-charset="UTF-8" id="form" action="classement_qdj.php">
  <h2><span>Choix de la période</span></h2>
  <div class="formulaire">
    <div>
      <span class="gauche">Quelle période afficher ? :</span>
      <span class="droite">
        <select id="formperiode" name="periode">
          <option value="actuelle" selected="selected">La période actuelle</option>
          <option value="tout">Tous les scores</option>
          {foreach from=$qdj_periodes key=index item=periode}
	  <option value="{$index}">Du {$periode.debut|date_format "%D-%M-%Y"} au {$periode.fin|date_format "%D-%M-%Y"}</option>
          {/foreach}
        </select>
      </span>
    </div>
    <div>
      <span class="boutons">
        <input type="submit" name="afficher" value="afficher"/>
      </span>
    </div>
  </div>
</form>
<table class="liste">
  <tr>
    <td class="entete" valign="top">Nom</td>
    <td class="entete" valign="top">Détail</td>
    <td class="entete" valign="top">Total (moyenne, écart type)</td>
  </tr>
  {foreach from=$qdj_voteurs item=voteur}
  <tr>
    <td class="element" valign="top">{print_eleve_name eleve=$voteur.eleve show_promo=1}</td>
    <td class="element" valign="top">
      <span class="image" style="display:block;text-align:center">
        <img src="classement_qdj.php?graph&amp;nb1={$voteur.nb1}&amp;nb2={$voteur.nb2}&amp;nb3={$voteur.nb3}&amp;nb4={$voteur.nb4}&amp;nb5={$voteur.nb5}&amp;nb6={$voteur.nb6}&amp;nb7={$voteur.nb7}&amp;nb8={$voteur.nb8}&amp;nb9={$voteur.nb9}&amp;nb10={$voteur.nb10}" alt="image" />
      </span>
    </td>
    <td class="element" valign="top">{$voteur.total} ({$voteur.moyenne}, {$voteur.ecarttype})</td>
  </tr>
  {/foreach}
</table>
