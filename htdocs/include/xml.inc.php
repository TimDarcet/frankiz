<?php
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
/*
	Code permettant de parser du code XML. La fonction xml_get_tree renvoit la traduction sous
	forme d'arbre du contenu du fichier XML.
	
	$Log$
	Revision 1.5  2005/04/13 17:10:00  pico
	Passage de tous les fichiers en utf8.

	Revision 1.4  2004/10/21 22:19:37  schmurtz
	GPLisation des fichiers du site
	
	Revision 1.3  2004/09/15 23:19:31  schmurtz
	Suppression de la variable CVS "Id" (fait double emploi avec "Log")
	
	Revision 1.2  2004/09/15 21:42:08  schmurtz
	Commentaires et ajout de la variable cvs "Log"
	
*/

/*
	Exemple : le fichier contenant le code XML suivant
				<xml>
					<blah>coucou</blah>
					<chombier etat="inexistant">
						<1>un</1>
						<2>deux</2>
					</chombier>
				</xml>
	
	donnera l'arbre :
	
				array (
					[0] => array (
						[tag] => xml
						[children] => array (
							[0] => array (
								[tag] => blah
								[value] => coucou
							)
							[1] => array (
								[tag] => chombier
								[attributes] => array (
									[etat] => inexistant
								)
								[children] => array (
									[0] => array (
										[tag] => 1
										[value] => un
									)
									[1] => array (
										[tag] => 2
										[value] => deux
									)
								)
							)
						)
					)
				)

*/

function xml_get_tree($file) {
	$data = join('', file($file));

	$p = xml_parser_create();
	xml_parser_set_option($p, XML_OPTION_CASE_FOLDING, 0);
	xml_parser_set_option($p, XML_OPTION_SKIP_WHITE, 1);
	xml_parse_into_struct($p, $data, $values);
	xml_parser_free($p);

	return xml_get_children($values, $i=0);
}

function xml_get_child(&$v, $children=NULL) {
	$c = array(); // the "child"
	if (isset($v['tag'])) $c['tag'] = $v['tag'];
	if (isset($v['value'])) $c['value'] = $v['value'];
	if (isset($v['attributes'])) $c['attributes'] = $v['attributes'];
	if (is_array($children)) $c['children'] = $children;
	return $c;
}

function xml_get_children(&$values, &$i) {
	$children = array();
	while ($i < count($values)) {
		$v = &$values[$i++];
		switch ($v['type']) {
			case 'cdata':
			case 'complete':
				$children[] = xml_get_child($v);
				break;
			case 'open':
				$children[] = xml_get_child($v, xml_get_children($values, $i));
				break;
			case 'close':
				break 2; // leave "while" loop
		}
	}
	return $children;
}

?>