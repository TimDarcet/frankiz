/*
        Copyright (C) 2006 Binet Réseau
        http://www.polytechnique.fr/eleves/binets/br/

        This program is free software; you can redistribute it and/or
        modify it under the terms of the GNU General Public License
        as published by the Free Software Foundation; either version 2
        of the License, or (at your option) any later version.

        This program is distributed in the hope that it will be useful,
        but WITHOUT ANY WARRANTY; without even the implied warranty of
        MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
        GNU General Public License for more details.

        You should have received a copy of the GNU General Public License
        along with this program; if not, write to the Free Software
        Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
*/
import java.net.*;
import java.io.*;
import java.util.*;
public class VerifEtat{
    private static String destination = Conf.racine + "etat_bob_automatique.php?estOuvert";
    private URL url;
    private EtatBob etatBob;
    private InputStreamReader input;
    private Scanner scan;
    private int lu = -1;
    VerifEtat(EtatBob etat){
        etatBob = etat;
    }
    
    public void verifierEtat(){
        try{
            url = new URL(destination);
            input = new InputStreamReader( url.openStream(), "utf8");
            lu = input.read();
            if(lu == '1'){
                scan = new Scanner(input);
                if (scan.hasNextLine()){
                    etatBob.ouvrir(scan.nextLine());
                }else{
                    etatBob.ouvrir();
                }
            }else{
                if(lu == '0'){
                    scan = new Scanner(input);
                    if (scan.hasNextLine()){
                        etatBob.fermer(scan.nextLine());
                    }else{
                        etatBob.fermer();
                    }
                }
                else{
                    etatBob.incontactibiliser();
                }

            }
        }catch (Exception e){
            etatBob.incontactibiliser();
        }
    }
    
}