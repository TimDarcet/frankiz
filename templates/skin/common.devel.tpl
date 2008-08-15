{if #globals.debug#}
@@BACKTRACE@@

{if $validate}
  <div id="dev">
    @HOOK@
    Validation&nbsp;:
    <a href="http://jigsaw.w3.org/css-validator/validator?uri={#globals.baseurl#}/valid.html">CSS</a>
    &nbsp;&nbsp;|&nbsp;&nbsp;
    références&nbsp;:
    <a href="http://www.w3schools.com/xhtml/xhtml_reference.asp">XHTML</a>
    <a href="http://www.w3schools.com/css/css_reference.asp">CSS2</a>
  </div>
{/if}
{/if}

{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}

