<?php
/* contains all admin stuff */
class AdminModule extends PlModule
{
    function handlers()
    {
        return array(
            'admin/su'      => $this->make_hook('su', AUTH_MDP, 'admin'),
        );
    }

    function handler_su(&$page, $uid=0)
    {
        if (S::has('suid')) {
            $page->kill("Déjà en SUID !!!");
        }
        $res = XDB::query("SELECT eleve_id
                             FROM compte_frankiz
                            WHERE eleve_id = {?}", $uid);
        if($res->numRows() == 1){
            if(!Platal::session()->startSUID($uid)) {
                $page->trigError('Impossible d\'effectuer un SUID sur ' . $uid);
            } else {
                $page->kill("SU ok");
                pl_redirect('');
            }
        }

    }
}
?>
