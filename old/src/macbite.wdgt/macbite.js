if (window.widget)
{
	widget.onshow = onshow;
	widget.onhide = onhide;
}

function onshow ()
{
	document.getElementById("bite").innerHTML = "Attente de l'ETRON";

	httpreq = new XMLHttpRequest();
	httpreq.setRequestHeader("Cache-Control","no-cache");
	httpreq.onreadystatechange = function()
	{
		if (httpreq.readyState == 4)
		{	
			if (httpreq.status == 200)
			{
				text = httpreq.responseText;
				if (text.charAt(0) == '0')
				{	
					document.getElementById("body").style.backgroundImage = 'url("macbitef.png")';
					if (text.length > 2)
					{
						document.getElementById("bite").innerHTML = text.substr(1, text.length); 
					}
					else
					{
						document.getElementById("bite").innerHTML = "Le Bôb est fermé";

					}
				}
               			else if (text.charAt(0) == '1')
                       		{
					document.getElementById("body").style.backgroundImage = 'url("macbiteo.png")';
                                	if (text.length > 2)
                                	{
                                 	      	 document.getElementById("bite").innerHTML = text.substr(1, text.length);                    
                                	}
                                	else
                                	{
                                        	document.getElementById("bite").innerHTML = "Le Bôb est ouvert";
                                	}
                        	}
				else
				{
					document.getElementById("bite").innerHTML = "Erreur";
				}
			}
			else
			{	
				document.getElementById("bite").innerHTML  = "Le serveur est incontactable";
			}
		}
	}

	httpreq.open("GET", "http://frankiz/gestion/bob/etat_bob_automatique.php?estOuvert", true);
	httpreq.send(null);
}


function onhide ()
{}
