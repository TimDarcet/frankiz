function FindProxyForURL(url, host)
{
    var sProxy = "PROXY 129.104.247.2:8080; DIRECT";
    var sDirect = "DIRECT";

    if (isResolvable(host)) {
       // 129.104.a.b avec a.b > 196.0 : zone privee
       // 129.104.30.0/24 : DMZ publique

       if (isInNet(host, "129.104.192.0", "255.255.192.0") || isInNet(host, "127.0.0.0", "255.0.0.0") || isInNet(host, "192.168.0.0", "255.255.0.0") || isInNet(host, "129.104.30.0", "255.255.255.0")) {
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
