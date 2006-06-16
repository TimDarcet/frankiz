<?php

class Graph
{
	// Stockage du dessin
	private $im;

	// Définition du dessin
	private $abs, $ord;
	private $hauteur, $largeur;

	// Données pour le dessin
	private $rows;
	private $max, $numrows;
	private $alternate;

	public $valid;
	
	// Couleurs pour le dessin
	private $blanc, $noir, $jaune, $rouge, $orange;
	
	/** Construit un graph avec :
	 * @param INT hauteur la hauteur indiquée en pixels
	 * @param INT largeur la largeur indiquée en pixels
	 * @param STRING abs le titre de l'axe des abscisses
	 * @param STRING ord le titre de l'axe des ordonnées
	 */
	public function Graph($largeur, $hauteur, $abs, $ord)
	{
		$this->valid   = false;
		$this->hauteur = $hauteur;
		$this->largeur = $largeur;
		$this->abs     = $abs;
		$this->ord     = $ord;
		$this->numrows = 0;
		$this->max     = 0;
		$this->alternate = false;

		if (!($this->im = @ImageCreate($largeur, $hauteur))) {
			return null;
		}
		
		// on place aussi le noir dans notre palette, ainsi qu'un bleu foncé et un bleu clair
		$this->blanc  = ImageColorAllocate($this->im, 255, 255, 255);
		$this->noir   = ImageColorAllocate($this->im, 0,   0,   0);
		$this->jaune  = ImageColorAllocate($this->im, 255, 220, 0);
		$this->rouge  = ImageColorAllocate($this->im, 255, 0,   0);
		$this->orange = ImageColorAllocate($this->im, 255, 128, 0);

		$this->valid = true;
	}

	/** Retourne la taille en pixel à utiliser pour le cas donné
	 * @param INT nb nombre d'éléments
	 */
	private function height($nb)
	{
		if ($this->alternate) {
			return ceil((($nb * ($this->hauteur - 80)) / $this->max));
		} else {
			return ceil((($nb * ($this->hauteur - 60)) / $this->max));
		}
	}

	/** Retourne la position du nieme objet
	 * @param INT nb numéro de l'objet
	 */
	private function width($nb)
	{
		return $nb * $this->largeur / ($this->numrows + 1);
	}

	public function addRow($jone, $rouje, $oranje, $title)
	{
		$total = $jone + $rouje + $oranje;
		if ($total > $this->max) {
			$this->max = $total;
		}
		$this->numrows++;
		if ($this->numrows >= 7) {
			$this->alternate = true;
		}
		$this->rows[$title] = Array('total' => $total, 'jones' => $jone, 'roujes' => $rouje, 'oranjes' => $oranje);
	}

	/** Dessine les axes
	 * @param INT padding taille de l'espace à laisser pour la légende sous l'axe des abscisses
	 */
	private function drawAxis($padding)
	{
		ImageLine ($this->im, 20, $this->hauteur - $padding, $this->largeur - 15, $this->hauteur - $padding, $this->noir);
		ImageLine ($this->im, 20, 30, 20, $this->hauteur - $padding, $this->noir);

		ImageString($this->im, 4, $this->largeur - 70, $this->hauteur - 20, utf8_decode($this->abs), $this->noir);
		ImageString($this->im, 4, 10, 0, utf8_decode($this->ord), $this->noir);
	}

	/** Dessine l'histogramme d'un champ
	 */
	private function drawHisto($nom, $padding, $pos)
	{
		$row = $this->rows[$nom];
		$width = $this->width($pos);	

		$textpos = $width + 7 - (strlen($nom) * 3);
	    if($pos%2 != 0 && $this->alternate) {
    	    ImageString ($this->im, 2, $textpos, $this->hauteur - 48, $nom, $this->noir);
    	} else {
			ImageString ($this->im, 2, $textpos, $this->hauteur - 28, $nom, $this->noir);
		}

        $base    = $this->hauteur - $padding - 1;
        $jone    = $this->height($row['jones']);
        $rouje   = $this->height($row['roujes']);
        $oranje  = $this->height($row['oranjes']);
		$taille  = $jone + $oranje + $rouje;

        if($jone != 0) {
			ImageFilledRectangle($this->im, $width, $base - $jone + 1,   $width + 14, $base, $this->jaune);
            $base = $base - $jone;
        }
        if($rouje != 0) {
			ImageFilledRectangle($this->im, $width, $base - $rouje + 1,  $width + 14, $base, $this->rouge);
            $base = $base - $rouje;
        }
        if($oranje != 0) {
			ImageFilledRectangle($this->im, $width, $base - $oranje + 1, $width + 14, $base, $this->orange);
		}
		ImageString($this->im, 2, $width, min($this->hauteur - $taille - $padding, $this->hauteur) - 21, $row['total'], $this->noir);
	}

	/** Réalise le dessin à partir des informations en stock
	 */
	public function run()
	{
		if ($this->alternate) {
			$padding = 50;
		} else {
			$padding = 30;
		}

		$this->drawAxis($padding);
		$i = 1;
		foreach ($this->rows as $nom => $row) {
			$this->drawHisto($nom, $padding, $i++);
		}
		
		// on spécifie le type d'image que l'on va créer, ici ce sera une image au format PNG
		ImageColorTransparent($this->im, $this->blanc);
		ImagePNG($this->im);
	}
}
?>
