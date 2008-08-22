<?php
/* contains all global stuff (exit, register, ...) */
class FrankizModule extends PlModule
{
    function handlers()
    {
        return array(
            'exit'      => $this->make_hook('exit', AUTH_PUBLIC),
        );
    }

    function handler_exit(&$page)
    {
        if(S::has('suid')) {
            Platal::session()->stopSUID();
            pl_redirect('/');
        }
        Platal::session()->destroy();
        $page->changeTpl('exit.tpl');
    }
}
?>
