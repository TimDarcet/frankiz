{**************************************************************************}
{*                                                                        *}
{*  Copyright (C) 2009 Binet Réseau                                       *}
{*  http://www.polytechnique.fr/eleves/binets/reseau/                     *}
{*                                                                        *}
{*  This program is free software; you can redistribute it and/or modify  *}
{*  it under the terms of the GNU General Public License as published by  *}
{*  the Free Software Foundation; either version 2 of the License, or     *}
{*  (at your option) any later version.                                   *}
{*                                                                        *}
{*  This program is distributed in the hope that it will be useful,       *}
{*  but WITHOUT ANY WARRANTY; without even the implied warranty of        *}
{*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         *}
{*  GNU General Public License for more details.                          *}
{*                                                                        *}
{*  You should have received a copy of the GNU General Public License     *}
{*  along with this program; if not, write to the Free Software           *}
{*  Foundation, Inc.,                                                     *}
{*  59 Temple Place, Suite 330, Boston, MA  02111-1307  USA               *}
{*                                                                        *}
{**************************************************************************}

<h2><span>Contrat d'utilisation étudiant</span></h2>
  <p>En sa qualité de membre de MSDN® Academic Alliance (MSDNAA), l'établissement auquel vous êtes inscrit est autorisé à vous fournir des logiciels à utiliser sur votre ordinateur personnel. Vous devez respecter les instructions d'utilisation générales de MSDNAA citées ci-dessous, ainsi que les termes et conditions du Contrat de Licence Utilisateur final (CLUF) MSDN, l'Amendement du Contrat de Licence et les conditions imposées par votre établissement.</p>
  <p>L'administrateur du programme MSDNAA de votre établissement devra consigner toutes les données relatives à l'utilisation des éléves, fournir des données consolidées à Microsoft® sur demande et s'assurer que tous les utilisateurs, notamment les élèves, les enseignants et le personnel technique, respectent strictement toutes les conditions du programme.</p>
  <p>Par l'installation, la copie ou toute autre utilisation des logiciels, vous acceptez de vous conformer aux termes et conditions du CLUF et de l'Amendement du Contrat de Licence. Si vous refusez de vous y conformer, il vous est interdit d'installer, copier ou utiliser les logiciels.</p>
  <h3>Instructions relatives à l'installation</h3>
    <p>Pour pouvoir installer des logiciels sur votre ordinateur personnel, vous devez être inscrit à au moins un cours dispensé par l'établissement abonné.</p>
    <p>Votre établissement peut soit vous donner accès à un serveur de téléchargement, soit vous prêter une copie des logiciels de façon temporaire afin que l'installiez sur votre ordinateur personnel.</p>
    <p>Dans le cas de certains produits, une clé de produit vous sera remise pour installer les logiciels. Il est interdit de divulguer cette clé à un tiers.</p>
  <h3>Instructions relatives à l'utilisation</h3>
    <p>Vous n'avez pas le droit de donner à un tiers des copies des logiciels empruntés ou téléchargés. Les autres élèves autorisés doivent se procurer les logiciels conformément aux procédures définies par l'administrateur du programme MSDNAA.</p>
    <p>Vous pouvez utiliser les logiciels à des fins non lucratives, notamment à des fins d'enseignement, de recherche et/ou de conception, de développement et de test dans le cadre de projets pédagogiques personnels. Il est interdit d'utiliser les logiciels MSDNAA pour le développement de logiciels à but lucratif.</p>
    <p>Lorsque vous n'êtes plus inscrit à aucun cours dispensé par l'établissement abonné, vous ne pouvez plus vous procurer des logiciels MSDNAA. Toutefois, vous pouvez continuer à utiliser les produits précédemment installés sur votre ordinateur, à condition de vous conformer toujours aux instructions du programme MSDNAA.</p>
    <p>Si vous contrevenez aux termes et conditions stipulés dans le CLUF et l'Amendement du Contrat de Licence, l'administrateur du programme MSDNAA exigera la confirmation de la désinstallation des logiciels de votre ordinateur personnel.</p>

<form method="post" action="profil/licences/raison" id="licence_accept_cuf">
  <input type="hidden" name="logiciel" value="{$logiciel}">
  <div class="formulaire">
    <span class="boutons">
      <input type="submit" name="accord" value="J'accepte">
      <input type="submit" name="refus" value="Je refuse" onClick="return window.confirm('Tu refuses ta clé gratuite ?')">
    </span>
  </div>
</form>


{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}
