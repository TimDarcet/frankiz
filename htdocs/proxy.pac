function FindProxyForURL(url, host)
{
    var sProxy = "PROXY 129.104.247.2:8080; DIRECT";
    var sDirect = "DIRECT";

    if ((isResolvable(host)) && ((isPlainHostName(host)) || (dnsDomainIs(host, ".eleves.polytechnique.fr")) || (dnsDomainIs(host, ".polytechnique.fr")) || (isInNet(host, "129.104.0.0", "255.255.0.0")) || (isInNet(host, "192.168.0.0", "255.255.0.0")) || (host=="localhost") || (host=="loopback") || (host.substring(0,5)=="127.0"))) {
       // il s'agit d'une machine de l'X, et on connait son IP
       // pour savoir si on peut y acceder directement,
       // on regarde si l'IP est du type 129.104.a.b avec a.b > 196.0
       
       var sIp = dnsResolve(host);

       if (((sIp.substring(0,7) == "129.104") && (parseFloat(sIp.substring(8,sIp.length)) > 196)) || sIp.substring(0,5)=="127.0" || sIp.substring(0,7) == "192.168") {
           return sDirect;
       }
       else {
           return sProxy;
       }
    }
    else {
    // c'est une machine a l'exterieur ou alors on ne connait pas son IP
    // passons par le proxy
	return sProxy;
    }
}
