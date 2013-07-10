<?php
/**
 * @package		EXG - Easy eXtended Gallery - plugin for Joomla 3.x
 * @copyright 	Nicolas Ogier
 * @author 		Nicolas Ogier {@link http://www.nicolas-ogier.fr}
 * @version 	3-1.0	2013-05-01
 * @link 		http://www.nicolas-ogier.fr/exg/
 * @access 		public
 * @license GNU/GPL
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program. If not, see <http://www.gnu.org/licenses/>.
 */
defined('_JEXEC') or die('Restricted access');
/**
 * class Easy eXtended Gallery : fournit une API pour Easy eXtended Gallery
**/
class exgClass {
	public $html;
	protected $_listeFolder = array();
	protected $_debug = array();
	private $_base_url;
	private $_base_path;
	private $_base_miniatures;
	private $_thumbHeight;
	private $_thumbWidth;
	private $_repminiatures;
	private $_adaptative = true;
	private $_gallerieNombre;
	private $_nombreImageParLigne=array(4,4,3,2,1);
	private $_script;
	private $_margin = 10;
	/**
	 * Constructeur php5
	 *
	 * @param array $botArray parametres administrateurs du plugin
	 * @param array $row article qui appele le plugin
	 * @void
	 */
	function __construct ( &$params, $i) {
		$this->_debug[] = 'tag = '.$params['TAG'];
		$this->_debug[] = 'url = '.$params['URL'];
		$this->_debug[] = 'path = '.$params['PATH'];
		$this->_debug[] = 'articleId = '.$params['ARTICLE_ID'];
		$this->_base_url = $params['URL'];
		$this->_base_path = $params['PATH'];
		$this->_thumbHeight = $params['THUMB_HEIGHT'];
		$this->_thumbWidth = $params['THUMB_WIDTH'];
		$this->_nombreImageParLigne = $params['RESPONSIVE_PARAMETERS'];
		$this->_adaptative = $params['ADAPTATIVE'];
		$this->_margin = $params['MARGIN'];
		$this->_debug[] = 'thumbnail height = '.$this->_thumbHeight;
		$this->_debug[] = 'thumbnail width  = '.$this->_thumbWidth;
		$this->_debug[] = 'responsive parameters = '.print_r($this->_nombreImageParLigne, true);
		$this->_repminiatures = 'thumbs/'.$this->_thumbWidth.'x'.$this->_thumbHeight.'/';
		$this->_gallerieNombre = $i;
	}

	function getDebug() {
		return ( $this->_debug );
	}
	/**
	 * Remplis les bases_path et _base_url avec le répertoire de la galerie
	 * Remplis _listeFolder avec les noms des images du répertoire de la galerie
	 * @param unknown $images
	 * @param unknown $repertoireBase
	 */
	function cheminsImages($images, $repertoireBase,$repertoire){
		$this->_base_url .= '/'.$repertoireBase.'/'.$repertoire;
		$this->_base_path .= '/'.$repertoireBase.'/'.$repertoire;
		$this->_listeFolder = $images;
		//on créé le répertoire qui contiendra les miniatures

		$this->_base_miniatures = $this->_base_path.$this->_repminiatures;
		if(!is_dir($this->_base_miniatures)) mkdir($this->_base_miniatures, 0775, true);
		$this->_debug[] = 'chemin des miniatures : '.$this->_base_miniatures;

	}
	function insertGallerie() {
		$this->_debug[] = 'contenu repertoire = '."<pre>".print_r($this->_listeFolder,true).'</pre>';
		$html = '<div  id="gallery_'.$this->_gallerieNombre.'" class="parent-container'.$this->_gallerieNombre.'" >'."\n";
		$html .='<!-- '.$this->_script->_scriptNom.' -->';
		// il y a des fichiers
		if($this->_listeFolder[0]<>'') {
			foreach($this->_listeFolder as $fichier) {
				$html .=$this->getThumb($fichier, $this->_thumbWidth,$this->_thumbHeight);
			}
		}
		$html .="</div>\n";
		$html .='<script src="/joomla_3/plugins/content/exg/plugin_exg/swipebox/jquery.swipebox.js" type="text/javascript"></script>';
		return $html;
	}

	function getThumb($str_img, $int_largeur, $int_hauteur) {
		$this->_debug['phpThumbs'] = 'miniatures trouvées';
		$nomThumbs = $this->nomMiniature($str_img, $int_hauteur, $int_largeur).'.png';
		if(!file_exists($this->_base_miniatures.$nomThumbs)){
			require_once 'ThumbLib.inc.php';
			$options = array('resizeUp' => true, 'jpegQuality' => 80);
			try
			{
				$thumb = PhpThumbFactory::create($this->_base_path.$str_img, $options);
				if($this->_adaptative){
					$thumb->adaptiveResize($int_largeur, $int_hauteur);
				}else{
					$thumb->resize($int_largeur, $int_hauteur);
				}
				$thumb->save($this->_base_miniatures.$nomThumbs, 'png');
				$this->_debug['phpThumbs'] = 'miniature regénérée';
			}
			catch (Exception $e)
			{
				// handle error here however you'd like
			}
		}
		$text = $this->genereAffichageMiniatures($str_img, $nomThumbs, $int_hauteur, $int_largeur);
		$this->_debug['repminiatures']=$this->_repminiatures;
		return($text);
	}

	private function nomMiniature($str_img,$int_hauteur,$int_largeur){
		return md5($str_img.$int_hauteur.$int_largeur);
	}
	/**
	 * Créer le CSS d'affichage des miniatures ainsi que le CSS généré par la class de popup sélectionnée
	 * @return string
	 */
	function genereCss() {
		// CSS de l'affichage des miniatures
		$css  ='#gallery_'.$this->_gallerieNombre." {width:100%; clear:both;font: 10px/13px 'Lucida Sans',sans-serif;   overflow: hidden; margin : '.$this->_margin.'px;}"."\n";
		if($this->_adaptative == true){
			$css .='#gallery_'.$this->_gallerieNombre.' .box {float: left;position: relative; width: '.(100/$this->_nombreImageParLigne[0]).'%; padding-bottom:'.(100/($this->_nombreImageParLigne[0]+1)).'%; margin-bottom:'.$this->_margin.'px;}'."\n";
		} else {
			$css .='#gallery_'.$this->_gallerieNombre.' .box {float: left;position: relative; width: '.(100/$this->_nombreImageParLigne[0]).'%; padding-bottom:'.($this->_thumbHeight).'px; margin-bottom:'.$this->_margin.'px;}'."\n";
		}
		$css .='#gallery_'.$this->_gallerieNombre.' .boxInner {	position: absolute;	left: 10px;right: 10px;top: 10px;bottom: 10px;overflow: hidden;}'."\n";
		if($this->_adaptative == true){
			$css .='#gallery_'.$this->_gallerieNombre.' .boxInner img {width: 100%;}'."\n";
		} else {
			$css .='#gallery_'.$this->_gallerieNombre.' .boxInner a {text-align:center; margin-left:auto; margin-right:auto; display:block;}'."\n";
			$css .='#gallery_'.$this->_gallerieNombre.' .boxInner  {height:'.$this->_thumbHeight.'px;}'."\n";
				
		}
		$css .='#gallery_'.$this->_gallerieNombre.' .boxInner .titleBox {position: absolute;	bottom: 0;left: 0;right: 0;margin-bottom: -50px;background: #000;background: rgba(0, 0, 0, 0.5);color: #FFF;	padding: 10px;text-align: center;	-webkit-transition: all 0.3s ease-out;-moz-transition: all 0.3s ease-out;	-o-transition: all 0.3s ease-out;transition: all 0.3s ease-out;}'."\n";
		$css .='#gallery_'.$this->_gallerieNombre.' .boxInner:hover .titleBox { margin-bottom: 0;}'."\n";
		$css .='#gallery_'.$this->_gallerieNombre.' body.no-touch .boxInner:hover .titleBox, body.touch .boxInner.touchFocus .titleBox {margin-bottom: 0;}'."\n";
		if($this->_adaptative == true){
			$css .='@media only screen and (max-width : 480px) {/* Smartphone view: 1 tile */ #gallery_'.$this->_gallerieNombre.' .box {position: relative; width: '.(100/$this->_nombreImageParLigne[4]).'%;padding-bottom: '.(100/($this->_nombreImageParLigne[4])).'%;}}'."\n";
			$css .='@media only screen and (max-width : 650px) and (min-width : 481px)   {/* Tablet view: 2 tiles */#gallery_'.$this->_gallerieNombre.' .box {position: relative; width: '.(100/$this->_nombreImageParLigne[3]).'%;padding-bottom: '.(100/($this->_nombreImageParLigne[3])).'%; }}'."\n";
			$css .='@media only screen and (max-width : 1050px) and (min-width : 651px)  {/* Small desktop / ipad view: 3 tiles */ #gallery_'.$this->_gallerieNombre.' .box { position: relative; width:'.(100/$this->_nombreImageParLigne[2]).'%;padding-bottom: '.(100/($this->_nombreImageParLigne[2]+1)).'%; }}'."\n";
			$css .='@media only screen and (max-width : 1290px) and (min-width : 1051px) {/* Medium desktop: 4 tiles */   #gallery_'.$this->_gallerieNombre.' .box {position: relative; width: '.(100/$this->_nombreImageParLigne[1]).'%;padding-bottom: '.(100/($this->_nombreImageParLigne[1]+1)).'%;    }}'."\n";
			
		} else {
		$css .='@media only screen and (max-width : 480px) {/* Smartphone view: 1 tile */ #gallery_'.$this->_gallerieNombre.' .box {position: relative; width: '.(100/$this->_nombreImageParLigne[4]).'%;padding-bottom: '.($this->_thumbHeight).'px; margin-bottom:'.$this->_margin.'px;}}'."\n";
		$css .='@media only screen and (max-width : 650px) and (min-width : 481px)   {/* Tablet view: 2 tiles */#gallery_'.$this->_gallerieNombre.' .box {position: relative; width: '.(100/$this->_nombreImageParLigne[3]).'%;padding-bottom: '.($this->_thumbHeight).'px; margin-bottom:'.$this->_margin.'px; }}'."\n";
		$css .='@media only screen and (max-width : 1050px) and (min-width : 651px)  {/* Small desktop / ipad view: 3 tiles */ #gallery_'.$this->_gallerieNombre.' .box { position: relative; width:'.(100/$this->_nombreImageParLigne[2]).'%;padding-bottom: '.($this->_thumbHeight).'px; margin-bottom:'.$this->_margin.'px;  }}'."\n";
		$css .='@media only screen and (max-width : 1290px) and (min-width : 1051px) {/* Medium desktop: 4 tiles */   #gallery_'.$this->_gallerieNombre.' .box {position: relative; width: '.(100/$this->_nombreImageParLigne[1]).'%;padding-bottom: '.($this->_thumbHeight).'px; margin-bottom:'.$this->_margin.'px;   }}'."\n";
		}
		return $css;
	}
	/**
	 * Retourne le javascript utilisé dans la class de popup sélectionnée.
	 * @return string
	 */
	function genereJS() {
		return $this->_script->javascript();
	}
	/**
	 * Créer le code pour insérer chaque image dans la page.
	 * @param string $str_img
	 * @param string $nomThumbs
	 * @param number $int_hauteur
	 * @param number $int_largeur
	 * @return string
	 */
	function genereAffichageMiniatures($str_img,$nomThumbs,$int_hauteur,$int_largeur ) {
		$text  = '<div class="box">';
		$text .= '<div class="boxInner"><a href="'.$this->_base_url.$str_img.'" title ="image : '.$str_img.'" rel="gallery-'.$this->_gallerieNombre.'" class="swipebox">';
		$text .= '<img src="'.$this->_base_url.$this->_repminiatures.$nomThumbs.'" alt="'.$str_img.'" height="'.$int_hauteur.'" width="'.$int_largeur.'" />';
		$text .= '<div class="titleBox">'.$str_img.'</div>';
		$text .='</a></div></div>';
		return $text;
	}
}