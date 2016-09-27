<?php

class ApiModule extends PLModule
{
    function handlers()
    {
        return array(
	    'api' => $this->make_hook('api', AUTH_COOKIE),
            'api/birthdays' => $this->make_hook('api_birthdays', AUTH_INTERNAL),
	    'api/activities' => $this->make_hook('api_activities', AUTH_INTERNAL),
	);
    }

    function handler_api($page)
    {
         return PL_FORBIDDEN;
    }

    function handler_api_activities($page)
    {
        if(IPAddress::getInstance()->is_x_internal()) {
	    global $globals;
	    $mysqli = new mysqli($globals->dbhost, $globals->dbuser, $globals->dbpwd, $globals->dbdb);
	    $req = $mysqli->query("SELECT ai.id, a.title, a.description, ai.comment, ai.begin, ai.end FROM activities_instances AS ai JOIN activities AS a ON ai.activity = a.aid WHERE a.days = '' AND ai.end > NOW();");
	    if($req) {
	      while($activity = $req->fetch_object()) {
	        $activities[] = $activity;
	      }
	    }
	    $req->close();
	    $page->jsonAssign('success', true);
	    $page->jsonAssign('activities', $activities);
	    return PL_JSON; 
	}
	return PL_FORBIDDEN;
    }

    function handler_api_birthdays($page)
    {
        if(IPAddress::getInstance()->is_x_internal()) {
	    global $globals;
	    $mysqli = new mysqli($globals->dbhost, $globals->dbuser, $globals->dbpwd, $globals->dbdb);
	    $req = $mysqli->query("SELECT hruid, firstname, lastname, birthdate FROM account;");
	    if($req) {
	      while($birthday = $req->fetch_object()) {
	        $birthdays[] = $birthday;
	      }
	    }
	    $req->close();
	    $page->jsonAssign('success', true);
	    $page->jsonAssign('birthdays', $birthdays);
	    return PL_JSON; 
	}
	return PL_FORBIDDEN;
    }
}
