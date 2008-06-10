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

import java.awt.*;
import java.awt.event.*;
import javax.swing.*;

public class InterfaceGraphique extends WindowAdapter implements Runnable{
    JFrame f;
    JPanel contentT;
    //JPanel contentB;
    JLabel texte;
//    JButton bouton;
    EtatBob etatBob;
    VerifEtat verifEtat;
    //ChangerEtat changerEtat;
    private Thread thread;
    private boolean maintenirOuvert = true;
    
    
    InterfaceGraphique(){
        f = new JFrame("Le bob est ouvert");
        f.addWindowListener(this);
        f.setDefaultCloseOperation(JFrame.EXIT_ON_CLOSE);
        contentT = new JPanel();
        //contentB = new JPanel();
        texte = new JLabel("Le bob est ouvert");
        texte.setFont(new Font(null, Font.BOLD, 20));
        //contentT.setVerticalAlignment(contentT.CENTER);
        //bouton = new JButton("Fermer le bob");
        contentT.add(texte, BorderLayout.CENTER);
        //contentB.add(bouton, BorderLayout.CENTER);
        f.add(contentT, BorderLayout.CENTER);
        //f.add(contentB, BorderLayout.SOUTH);
        etatBob = new EtatBob();
        mettreAJour();
        f.pack();
        f.setResizable(false);
        verifEtat = new VerifEtat(etatBob);
        f.setVisible(true);
        thread = new Thread(this);
        thread.start();
        //changerEtat = new ChangerEtat(thread, etatBob);
        //bouton.addActionListener(changerEtat);
    }
    
    public void mettreAJour(){
        f.setTitle(etatBob.titre);
        f.setBackground(etatBob.couleur_fond);
        contentT.setBackground(etatBob.couleur_fond);
        //contentB.setBackground(etatBob.couleur_fond);
        //bouton.setText(etatBob.bouton);
        //bouton.setEnabled(etatBob.enable);
        texte.setText(etatBob.texte);
    }
    
    public void run(){
        while(maintenirOuvert){
            verifEtat.verifierEtat();
            mettreAJour();
            try{
                if(etatBob.isContactable()){
                    Thread.sleep(Conf.intervalContactable);
                }else{
                    Thread.sleep(Conf.intervalIncontactable);
                }
            }
            catch(InterruptedException e){}
        }
        System.exit(0);
    }
    
    /*public void windowClosing(WindowEvent e){
        int reponse = 0;
        if(etatBob.estOuvert()){
            reponse = JOptionPane.showConfirmDialog(null, "Le bôb est ouvert, voulez vous le fermer ?", "Fermeture ?", JOptionPane.YES_NO_CANCEL_OPTION);
            if (reponse == 0){
                changerEtat.fermer();
            }
        }
        else{
            reponse = JOptionPane.showConfirmDialog(null, "Êtes vous sûr de vouloir quitter ?", "Fermeture ?", JOptionPane.YES_NO_OPTION);
            if (reponse == 1) reponse = 2;
        }
        if(reponse != 2){
            maintenirOuvert = false;
        }
        thread.interrupt();
        
    }*/
}