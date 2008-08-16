<?php

class FrankizSkin
{
    public static function is_minimodule_disabled($name)
    {
        return false;
        return in_array($name, S::v('minimodules_disabled'));
    }

}

?>
