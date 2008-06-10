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
public class EtatBob{
    public static int FERME = 0;
    public static int OUVERT = 1;
    public static int INCONTACTABLE = -1;
    private static String TITRE_OUVERT = "ETRON : Le bôb est ouvert :-)";
    private static String TITRE_FERME = "ETRON : Le bôb est fermé :-(";
    private static String TITRE_INCONTACTABLE = "ETRON : Le serveur est incontactable :-\\";
    private static String TEXTE_OUVERT = "Le bôb est ouvert :-)";
    private static String TEXTE_FERME = "Le bôb est fermé :-(";
    private static String TEXTE_INCONTACTABLE = "Le serveur est incontactable :-\\";
    private static String BOUTON_OUVERT = "Fermer le bôb :-(";
    private static String BOUTON_FERME = "Ouvrir le bôb :-)";
    private static String BOUTON_INCONTACTABLE = "Le serveur est incontactable :-\\";
    private static Color COULEUR_OUVERT = Color.green;
    private static Color COULEUR_FERME = Color.red;
    private static Color COULEUR_INCONTACTABLE = Color.orange;
    private static boolean ENABLE_OUVERT = true;
    private static boolean ENABLE_FERME = true;
    private static boolean ENABLE_INCONTACTABLE = false;
            
    public String titre;
    public String texte;
    public String bouton;
    public Color couleur_fond;
    public int etat;
    public boolean enable;
    
    
    public EtatBob(){
        incontactibiliser();
    }
    
    public void ouvrir(){
        ouvrir(TEXTE_OUVERT);
    }
    
    public void ouvrir(String s){
        titre = TITRE_OUVERT;
        texte = s;
        bouton = BOUTON_OUVERT;
        etat = OUVERT;
        enable = ENABLE_OUVERT;
        couleur_fond = COULEUR_OUVERT;
    }
    
    public void fermer(){
        fermer(TEXTE_FERME);
    }
    
    public void fermer(String s){
        titre = TITRE_FERME;
        texte = s;
        bouton = BOUTON_FERME;
        etat = FERME;
        enable = ENABLE_FERME;
        couleur_fond = COULEUR_FERME;
    }
    
    public void incontactibiliser(){
        titre = TITRE_INCONTACTABLE;
        texte = TEXTE_INCONTACTABLE;
        bouton = BOUTON_INCONTACTABLE;
        etat = INCONTACTABLE;
        enable = ENABLE_INCONTACTABLE;
        couleur_fond = COULEUR_INCONTACTABLE;
    }
    
    public boolean isContactable(){
        return (etat != INCONTACTABLE);
    }
    
    public boolean estOuvert(){
        return (etat == OUVERT);
    }
    
    public boolean estFerme(){
        return (etat == FERME);
    }

}