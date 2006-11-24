<?php header('Content-type: text/css'); ?>
/* $Id$ */
/*
	Copyright (C) 2004 Binet Réseau
	http://www.polytechnique.fr/eleves/binets/br/
	
	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	as published by the Free Software Foundation; either version 2
	of the License, or (at your option) any later version.
	
	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.
	
	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
*/

body {
	font-size:80%;
	font-family: Aria,Helvetica,Verdana, sans-serif;
	color:#000;
	background-color:#000;
	margin:0;
	padding:2px;
	background-image:url(images/BR.png);
	background-repeat: no-repeat;
	background-attachment: fixed;
}

img {
	border: 0;
}

a:link { 
<?php
$tmp=rand() % 100;
if ($tmp<50){
?>
	color:#d5e2ce;
<?php
}else{
?>
	color:#D37C00;
<?php
}
?>
	text-decoration:none;
	}
a:visited { 
<?php
$tmp=rand() % 100;
if ($tmp<50){
?>
	color:#d5e2ce;
<?php
}else{
?>
	color:#D37C00;
<?php
}
?>
	text-decoration:none;
	}
a:hover, a:active { 
<?php
$tmp=rand() % 100;
if ($tmp<50){
?>
	color:#d5e2ce;
	border-bottom:1px dashed #D37C00;
<?php
}else{
?>
	color:#D37C00;
	border-bottom:1px dashed #d5e2ce;
<?php
}
?>
	text-decoration:none;
	margin-bottom:1px;
}

/* Structure de la page */

.fkz_page {
	border-bottom:3px double #D37C00;
	clear: both;
	width: 100%;     
}

.fkz_entetes {
}
<?php
$tmp=rand() % 100;
if ($tmp<33){
	$tmp2=rand() % 100;
	if ($tmp2<50){
?>
.fkz_gauche {
        width: 20%;
        float: left;
	font-size: 9pt;
	margin-top: 20px;
	min-height: 800px;
}
.fkz_droite {
        float: right;
        width: 20%;
	font-size: 9pt;
	margin-top: 20px;
	min-height: 800px;
}
.fkz_centre {
	font-size: 9pt;
	position: absolute;
	left: 21%;
	width: 58%;
	margin-top: 20px;
}
<?php
	}else{
?>
.fkz_gauche {
        width: 20%;
        float: right;
	font-size: 9pt;
	margin-top: 20px;
	min-height: 800px;
}
.fkz_droite {
        float: left;
        width: 20%;
	font-size: 9pt;
	margin-top: 20px;
	min-height: 800px;
}
.fkz_centre {
	font-size: 9pt;
	position: absolute;
	left: 21%;
	width: 58%;
	margin-top: 20px;
}
<?php
	}
}elseif ($tmp<66){
?>
.fkz_gauche {
        width: 20%;
        float: right;
	font-size: 9pt;
	margin-top: 20px;
	min-height: 800px;
}
.fkz_droite {
        float: right;
        width: 20%;
	font-size: 9pt;
	margin-top: 20px;
	min-height: 800px;
}
.fkz_centre {
	font-size: 9pt;
	position: absolute;
	left: 0%;
	width: 58%;
	margin-top: 20px;
}
<?php
}else{
?>
.fkz_gauche {
        width: 20%;
        float: left;
	font-size: 9pt;
	margin-top: 20px;
	min-height: 800px;
}
.fkz_droite {
        float: left;
        width: 20%;
	font-size: 9pt;
	margin-top: 20px;
	min-height: 800px;
}
.fkz_centre {
	font-size: 9pt;
	position: absolute;
	left: 41%;
	width: 58%;
	margin-top: 20px;
}
<?php
}
?>

div.fkz_end_page{clear:both;margin:3px;}
/* Fin Structure de la page */

/* Logos Modules */

span.fkz_logo,span.fkz_logo_eleves{
	display:none;
}

div.fkz_logo {
	background-image:url("images/home_logo.png");
	background-repeat:no-repeat;
	height:93px;
	width:373px;
 }

 div.fkz_logo_eleves {
	background-image:url("images/home_logo_eleve.png");
	background-repeat:no-repeat;
	height:42px;
	width:326px;
	position:relative;
	left:90px;
	top:-5px;
 }
 
 /* Fin Logos Modules */


 
/* QDJ */

.fkz_qdj_question {
<?php
$tmp=rand() % 100;
if ($tmp<50){
?>
	color:#d5e2ce;
<?php
}else{
?>
	color:#D37C00;
<?php
}
?>
    font-size: 11pt;
    text-align: center;
    width: 99%;
    /*border: 1px solid black ; SUPP*/
}

.fkz_qdj_jone, div.fkz_qdj_rouje, div.fkz_qdj_jone_reponse, div.fkz_qdj_rouje_reponse{
	text-align: center;
	width: 48%;
}

.fkz_qdj_jone {
	float: right;
}

.fkz_qdj_rouje {
	float: left;
}

<?php
$tmp=rand() % 100;
if ($tmp<50){
?>
.fkz_qdj_jone a {
<?php
$tmp=rand() % 100;
if ($tmp<50){
?>
	color:#d5e2ce;
<?php
}else{
?>
	color:red;
<?php
}
?>
	font-weight:bold;
}
<?php
}else{
?>
.fkz_qdj_jone a {
<?php
$tmp=rand() % 100;
if ($tmp<50){
?>
	color:#d5e2ce;
<?php
}else{
?>
	color:#FFBB00;
<?php
}
?>
	font-weight:bold;
}
<?php
}
$tmp=rand() % 100;
if ($tmp<50){
?>
.fkz_qdj_rouje a {
	color:red;
	font-weight:bold;
}
<?php
}
else{
?>
.fkz_qdj_rouje a {
	color:#FFBB00;
	font-weight:bold;
}
<?php
}
?>

<?php
$tmp=rand() % 100;
if ($tmp<50){
?>
.fkz_qdj_jone_reponse {
	color: red;
	font-weight: bold;
	float: right;
}
<?php
}else{
?>
.fkz_qdj_jone_reponse {
	color: #FFBB00;
	font-weight: bold;
	float: right;
}
<?php
}
$tmp=rand() % 100;
if ($tmp<50){
?>
.fkz_qdj_rouje_reponse {
	color: red; 
	font-weight: bold;
	float: left;
}
<?php
}else{
?>
.fkz_qdj_rouje_reponse {
	color: #FFBB00;
	font-weight: bold;
	float: left;
}
<?php
}
?>

.fkz_qdj_last{
	list-style-type:  none;  
	text-align:left; 
}

.col {
	height:50px;
}
.col span {
	display:block;
	height:50px;
}
.col span.jone {
	background:url(../images/jaune.gif) repeat-y top center;
}
.col span.rouje {
	background:url(../images/rouge.gif) repeat-y top center;
}

/* Fin QDJ */


.fkz_liens,.fkz_contact,.fkz_stats,.fkz_liens_nav {
	list-style-type:none;
	color:#333;
}

div.fkz_anniversaire_titre,div.fkz_page_titre,div.fkz_annonces_titre {
	text-align : center;
	position:relative;
	background-color:#aacaca;
	border:1px solid #D37C00;
	width:75%;
	padding:10px;
	z-index:20;
	font-family:Georgia, serif;
	font-variant:small-caps;
	font-style:oblique;
	/*color:#D37C00;*/
<?php
$tmp=rand() % 100;
if ($tmp<50){
?>
	color:aacaca;
<?php
}else{
?>
	color:#FFBB00;
<?php
}
?>
	font-weight:bold;
	font-size:120%;
	background-image:url(images/BR3.png);
	background-repeat: no-repeat;
	background-attachment: fixed;
}

div.fkz_page_titre {
	font-size:200%;
	background-image:url(images/BR.png);
	background-repeat: no-repeat;
	background-attachment: fixed;
}


.fkz_anniversaire,.fkz_annonces_corps,div.fkz_page_corps {
	position:relative;
	top:-20px;
	left:10px;
	width: 98%;
	padding-top:30px;
	background-color:#d5e2ce;
	border:1px solid #D37C00;
	background-image:url(images/BR2.png);
	background-repeat: no-repeat;
	background-attachment: fixed;
}

.fkz_titre {
	height:20px;
	border:2px solid #D37C00;
	margin:0px;
	padding:0px;
<?php
$tmp=rand() % 100;
if ($tmp<50){
?>
	color:aacaca;
<?php
}else{
?>
	color:#FFBB00;
<?php
}
?>
	text-align : center;
	font-weight: bold;
	text-align: center;
	background-color:#aacaca;
	background-image:url(images/BR3.png);
	background-repeat: no-repeat;
	background-attachment: fixed;
}

.fkz_module {
	color:#333;
	margin-bottom: 10px;
	background-color:#d5e2ce;
	background-image:url(images/BR2.png);
	background-repeat: no-repeat;
	background-attachment: fixed;
}

#mod_anniversaires {
	background:none;
}

.fkz_sommaire_corps {
	font-size: 9pt;
	text-align: left;
	width: 100%;
<?php
$tmp=rand() % 100;
if ($tmp<50){
?>
	color:d5e2ce;
<?php
}else{
?>
	color:#FFBB00;
<?php
}
?>
 
}

.fkz_sommaire_titre {
	font-size: 9pt;
	font-weight: bold;
	text-align: center;
	width: 100%; 
	margin: 0;
	padding: 0;
}

.fkz_trombino,.fkz_sommaire{
	font-size: 9pt;
	text-align: left;
	width: 98%; 
	margin:10px;
	margin-left:10px;
	margin-right:10px;
	margin-top:10px;
	margin-bottom:10px;
}

.fkz_sommaire,.fkz_trombino_eleve{
	border:1px solid #D37C00;
	background-color:#d5e2ce;
	background-image:url(images/BR2.png);
	background-repeat: no-repeat;
	background-attachment: fixed;
<?php
$tmp=rand() % 100;
if ($tmp<50){
?>
	color:d5e2ce;
<?php
}else{
?>
	color:#FFBB00;
<?php
}
?>
}

.fkz_trombino{
	background-color:#aacaca;
	background-image:url(images/BR3.png);
	background-repeat: no-repeat;
	background-attachment: fixed;
}



.nom{
	text-align:center;
<?php
$tmp=rand() % 100;
if ($tmp<50){
?>
	color:d5e2ce;
<?php
}else{
?>
	color:black;
<?php
}
?>
}

.fkz_trombino_photo{
	float:left;
	margin-left:5px;
}

.fkz_trombino_section{
	float:right;
	margin-right:5px;
}
<?php
$tmp=rand() % 100;
if ($tmp<50){
?>
.fkz_trombino_infos{
	float:left;
	margin-left:5px;
<?php
	$tmp2=rand() % 100;
	if ($tmp2<50){
?>
	color:#d5e2ce;
<?php
	}
?>
}
.fkz_trombino_infos2{
	float:right;
	margin-right:5px;
<?php
	$tmp2=rand() % 100;
	if ($tmp2<50){
?>
	color:#d5e2ce;
<?php
	}
?>
}
<?php
}else{
?>
.fkz_trombino_infos{
	float:right;
	margin-left:5px;
}
.fkz_trombino_infos2{
	float:left;
	margin-right:5px;
}
<?php
}
?>
.binets{
	clear:both;
<?php
$tmp=rand() % 100;
if ($tmp<50){
?>
	color:d5e2ce;
<?php
}else{
?>
	color:aacaca;
<?php
}
?>
}


.fkz_signature{
	text-align:right;
	font-weight: bold;
<?php
$tmp=rand() % 100;
if ($tmp<50){
?>
	color:d5e2ce;
<?php
}else{
?>
	color:aacaca;
<?php
}
?>
}

form.trombino { 
	padding:20px 0px;
}

form.trombino div{
	text-align:center;
	padding:7px;
}

form.trombino table{
	font-size: 9pt;
	text-align:right;
}


form.trombino input,form.trombino select {
<?php
$tmp=rand() % 100;
if ($tmp<50){
?>
	width:150px;
<?php
}else{
?>
	width:1px;
<?php
}
?>
}

.fkz_signature{
	text-align:right;
	font-weight: bold;
}
/* Logos Catégories d'annonce */

span.fkz_annonces_vieux,span.fkz_annonces_nouveau, span.fkz_annonces_important,span.fkz_annonces_reste,span.fkz_annonces_reste {
	background-repeat:no-repeat;
	height:16px;
	padding-left:16px;
 }

span.fkz_annonces_vieux {
	background-image:url("../default/images/vieux.gif");
}

 span.fkz_annonces_nouveau {
	background-image:url("../default/images/nouveau.gif");
}
 
 span.fkz_annonces_important {
	background-image:url("../default/images/important.gif");
 } 
 
 span.fkz_annonces_reste {
	background-image:url("../default/images/reste.gif");
 } 
  
 span.fkz_annonces_cat {display:none;}
 
 /* Fin Logos Catégories d'annonce */
 
 /* Logos W3C */

span.valid_xhtml,span.valid_css {
	width: 80px;
	height:15px;
	position: relative;
	display:block;
}
 
span.valid_xhtml {
	background-image:url("../images/xhtml10.png");
	left: 5px;
}

span.valid_css {
	background-image:url("../images/css2.png");
	left: 90px;
	top: -15px;
}
 
 /* Fin Logos W3C */


/* Stats */
 
.fkz_stats{list-style-type:  none;}

.serveur_nom {
	position:relative;
	left:5px;
	color: inherit;
}

.serveur_up {
	color: inherit;
	background: lime;

}

.serveur_down {
	color: inherit;
	background: red;

}

/* FAQ et Xshare */
.noeud_ferme{list-style-image:url('../default/fold_close.gif')}
.noeud_ouvert{list-style-image:url('../default/fold_open.gif')}
.feuille{margin:5px; border: thin dotted #ccc; list-style-image:url('../default/images/question.gif')}



/* Commentaires et Warning */
.commentaire,.warning,.note {
	color: inherit;
	padding: 6px;
	margin:5px;
	margin-left:5px;
	margin-right:5px;
	margin-top:5px;
	margin-bottom:5px;
	display:block;
}

.commentaire {
	border: 1px solid #FFDD00;
<?php
$tmp=rand() % 100;
if ($tmp<50){
?>
	background:red;
<?php
}else{
?>
	background:inherit;
<?php
}
?>
}

.warning {
	border: 1px solid #FF3333;
<?php
$tmp=rand() % 100;
if ($tmp<50){
?>
	background:black;
<?php
}else{
?>
	background:inherit;
<?php
}
?>
}

.note {
	font-size: 75%;
	border: 1px solid #FFDD00;
<?php
$tmp=rand() % 100;
if ($tmp<50){
?>
	background:pink;
<?php
}else{
?>
	background:inherit;
<?php
}
?>
}

/* Formulaires */

div.formulaire{
	display:block;
}


div.formulaire span.droite{
	float:right;
	width:78%;
	text-align: left;
}

div.formulaire span.gauche{
	float:left;
	width:20%;
	padding-left:5px;
}

div.formulaire div{
	clear:both;
}
div.formulaire span.boutons{
	display:block;
	padding-left: 22%;
	padding-top:10px;
}
.fkz_module_corps div.formulaire span.droite,.fkz_module_corps div.formulaire span.gauche{
	display:block;
}

/* Formulaires */

/* Listes */
table.liste{
	clear:both;
	width:100%;
}

table.liste .entete{
	text-decoration: underline;
}


#choix_skin h1, #profil h1,.fkz_page_meteo h1,#xshare  h1,#faq  h1,#vocabulaire  h1{
	display:none;
}

span.meteo{
	display: block;
	text-align:center;
}

div.meteo{
	display: block;
	border-top: 2px solid #E9D2FF;
}

div.image {
	text-align: center;
}
