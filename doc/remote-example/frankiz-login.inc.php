<?php
/**
 * Configuration
 */
// Frankiz website
define('FRANKIZ_REMOTE', 'https://www.frankiz.net/remote');
// Your website, as it was given to Frankiz
define('FRANKIZ_SITE', 'http://localhost/path/to/frankiz-site.php');
// Secret Key
define('FRANIZ_KEY', '#?_this-must!be@secret^#');

/**
 * Do authentication
 */
function frankiz_auth_ask()
{
    // Result page
    $location = 'http://localhost/my-user-page';

    // Request content
    $request = json_encode(array('names', 'email', 'sport', 'promo', 'photo'));

    // Frankiz security protocol
    $timestamp = time();
    $hash = md5($timestamp . FRANKIZ_SITE . FRANIZ_KEY . $request);
    $remote  = FRANKIZ_REMOTE . '?timestamp=' . $timestamp .
        '&site=' . FRANKIZ_SITE .
        '&location=' . urlencode($location) .
        '&hash=' . $hash . '&request=' . $request;
    header('Location: ' . $remote);
    echo '<a href="' . $remote . '" />';
    exit();
}

/**
 * Receive auth results
 */
function frankiz_auth_response()
{
    // Read request
    $timestamp = (isset($_REQUEST['timestamp']) ? $_REQUEST['timestamp'] : 0);
    $response  = (isset($_REQUEST['response'])  ? $_REQUEST['response']  : '');
    $hash      = (isset($_REQUEST['hash'])      ? $_REQUEST['hash']      : '');
    $location  = (isset($_REQUEST['location'])  ? $_REQUEST['location']  : '');

    // Frankiz security protocol
    if (abs($timestamp - time()) > 600)
        die("Délai de réponse dépassé. Annulation de la requête");
    if (md5($timestamp . FRANIZ_KEY . $response) != $hash)
        die("Session compromise, annulation en urgence");

    $response = json_decode($response, true);
    $response['location'] = $location;

    // Set empty fields
    $fields = array('hruid',
        'firstname', 'lastname', 'nickname', // 'names' query
        'email', 'sport', 'promo', 'photo');
    foreach ($fields as $k) {
        if (!isset($response[$k]))
            $response[$k] = '';
    }
    return $response;
}
?>
