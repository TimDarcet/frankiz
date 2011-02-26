#!/usr/bin/php -q
<?php
/***************************************************************************
 *  Copyright (C) 2011 Binet Réseau                                       *
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
 
 /* This script send the mails waiting in the database */

require '../connect.db.inc.php';

set_time_limit(0);

XDB::startTransaction();
$res = XDB::query('SELECT  id, target, writer, writername, title, body, ishtml
                     FROM  mails
                    WHERE  processed IS NULL')->fetchAllRow();

$ids = array();

if (count($res) == 0) {
    exit;
}

foreach($res as $r) {
    $ids[] = $r[0];
} 

XDB::execute('UPDATE  mails
                 SET  processed = NOW()
               WHERE  id IN {?}', $ids);
XDB::commit();

foreach($res as $r) {
    $uf = new UserFilter($r[1]);
    $users = $uf->get();
    $users->select(UserSelect::base());
    foreach($users as $user) {
        $mail = new FrankizMailer();
        $mail->addAddress($user->bestEmail(), $user->displayName());
        $mail->SetFrom($r[2], $r[3]);
        $mail->subject($r[4]);
        $mail->body($r[5]);
        $mail->send($r[6]);
    }
    XDB::execute('UPDATE  mails
                     SET  done = NOW()
                   WHERE  id = {?}', $r[0]);
}


// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>
