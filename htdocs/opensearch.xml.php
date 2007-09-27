<?php
	require_once "include/global_func.inc.php";
	header("Content-Type: application/opensearchdescription+xml");
	$prefix="www.polytechnique.fr/eleves";
	echo '<?xml version="1.0" ?>';
?>
<OpenSearchDescription xmlns="http://a9.com/-/spec/opensearch/1.1/">
<ShortName>TOL</ShortName>
<Description>Trombino On Line</Description>
<Image height="16" width="16" type="image/x-icon">http://<?php echo $prefix; ?>/favicon.ico</Image>
<Url type="text/html" method="get" template="http://<?php echo $prefix ?>/tol?cherchertol&amp;q_search={searchTerms}"/>
</OpenSearchDescription>
