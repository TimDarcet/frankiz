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
import java.awt.*;
import java.awt.event.*;

public class ChangerEtat implements ActionListener{
    private Thread thread;
    private String adresse = Conf.racine + "etat_bob_automatique.php";
    private EtatBob etatBob;
    private boolean forcerFermeture = false;
    
    ChangerEtat(Thread th, EtatBob etat){
        thread = th;
        etatBob = etat;
    }
    
    private synchronized void changerEtat(){
        if(etatBob.isContactable()){
            //System.out.println("Le serveur est contactable");
            StringBuffer sb = new StringBuffer();
            
            
            //sb.append(adresse + "?");
            
            try{
                sb.append(URLEncoder.encode("nouvel_etat", "UTF-8") + "=");
                if(forcerFermeture || etatBob.estOuvert()){
                    //System.out.println("Le bob etait ferme");
                    sb.append(URLEncoder.encode("0", "UTF-8") );
                }
                else{
                    //System.out.println("Le bob etait ouvert");
                    sb.append(URLEncoder.encode("1", "UTF-8") );
                }
                sb.append("&" + URLEncoder.encode("mdp_bob", "UTF-8") + "=");
                sb.append( URLEncoder.encode(Conf.mdpBob, "UTF-8"));
                String aEnvoyer = sb.toString();
                //System.out.println(aEnvoyer);
                
                URL url = new URL(adresse);
                //url.openStream();
                HttpURLConnection urlcon =  (HttpURLConnection) url.openConnection();
                urlcon.setRequestMethod("POST");
                urlcon.setRequestProperty("Content-type","application/x-www-form-urlencoded");
                urlcon.setDoOutput(true);
                urlcon.setDoInput(true);
                PrintWriter pout = new PrintWriter(new OutputStreamWriter(urlcon.getOutputStream(), "UTF-8"), true);
                pout.print(aEnvoyer);
                pout.flush();
                urlcon.getResponseCode();
            }
            catch(Exception e)
            {
                //System.out.println("ca merde");
            }
            if(!forcerFermeture){
                thread.interrupt();
            }
        }
    }
    
    
    public void actionPerformed( ActionEvent e){
        //System.out.println("Une action a ete commise");
        changerEtat();
    }
    public void fermer(){
        forcerFermeture = true;
        changerEtat();
        
    }
}