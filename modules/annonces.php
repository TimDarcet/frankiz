<?php
/***************************************************************************
 *  Copyright (C) 2009 Binet Réseau                                       *
 *  http://www.polytechnique.fr/eleves/binets/reseau/                     *
 *                                                                         *
 *  This program is free software; you can redistribute it and/or modify   *
 *  it under the terms of the GNU General Public License as published by   *
 *  the Free Software Foundation; either version 2 of the License, or      *
 *  (at your option) any later version.                                    *
 *                                                                         *
 *  This program is distributed in the hope that it will be useful,        *
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of         *
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the          *
 *  GNU General Public License for more details.                           *
 *                                                                         *
 *  You should have received a copy of the GNU General Public License      *
 *  along with this program; if not, write to the Free Software            *
 *  Foundation, Inc.,                                                      *
 *  59 Temple Place, Suite 330, Boston, MA  02111-1307  USA                *
 ***************************************************************************/

class AnnoncesModule extends PLModule
{
    const FLAG_IMPORTANT = 'important';
    const FLAG_EXT = 'ext';

    const CAT_IMPORTANT = 'important';
    const CAT_NEW = 'new';
    const CAT_OLD = 'old';
    const CAT_OTHER = 'other';

    public function handlers()
    {
        return array("annonces" => $this->make_hook("annonces", AUTH_PUBLIC));
    }

    private static function get_cat($flags, $begin, $end) {
        if ($flags->hasFlag(self::FLAG_IMPORTANT)) {
            return self::CAT_IMPORTANT;
        } else if ($begin > date("Y-m-d H:i:s", time() - 12*3600)) {
            return self::CAT_NEW;
        } else if ($end < date("Y-m-d H:i:s", time() + 24*3600)) {
            return self::CAT_OLD;
        } else {
            return self::CAT_OTHER;
        }
    }

    function handler_annonces(&$page)
    {

        $res=XDB::query("
            SELECT  annonce_id,
                    DATE_FORMAT(begin, '%d/%m/%Y') as date,
                    begin, end, title, content, flags, eleve_id
              FROM  annonces
             WHERE  end > NOW()
          ORDER BY  end DESC");
        $annonces_liste = $res->fetchAllRow();

        $annonces = array(
            self::CAT_OLD       => array('desc' => "Demain, c'est fini", 'annonces' => array()),
            self::CAT_NEW       => array('desc' => "Nouvelles fraiches", 'annonces' => array()),
            self::CAT_IMPORTANT => array('desc' => "Important", 'annonces' => array()),
            self::CAT_OTHER     => array('desc' => "En attendant", 'annonces' => array()));

        foreach ($annonces_liste as $annonce)
        {
            list($id, $date, $begin, $end, $title, $content, $sql_flags, $eleve_id) = $annonce;

            $eleve = new User($eleve_id);
            // FIXME : implement "hide" functionnality
            $visible = true;
            $flags = new PlFlagSet($sql_flags);

            // Skip internal items when outside
            if (!$flags->hasFlag(self::FLAG_EXT) && S::v('auth', AUTH_PUBLIC) < AUTH_INTERNE){
                continue;
            }

            $cat = self::get_cat($flags, $begin, $end);
            $annonces[$cat]['annonces'][$id] = array(
                'id'     => $id,
                'title'  => $title,
                'date'   => $date,
                'img'    => file_exists(DATA_DIR_LOCAL.'annonces/'.$id),
                'eleve'  => $eleve,
                'content' => $content,
                'visible' => $visible);
        }

        $page->assign('title', "Annonces");
        $page->assign('annonces', $annonces);
        $page->changeTpl('annonces/annonces.tpl');
    }
}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>
