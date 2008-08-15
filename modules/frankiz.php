<?php
/* Contains all global stuff (exit, register, ...) */
class FrankizModule extends PLModule
{
    function handlers()
    {
        return array(
            'exit'      => $this->make_hook('exit', AUTH_PUBLIC),
        );
    }

    function handler_exit(&$page)
    {
        Platal::session()->destroy();
        $page->changeTpl('exit.tpl');
    }
}
?>
