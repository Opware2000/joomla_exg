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
	private $base_url;
	private $base_path;
	private $galerie;
	/**
	 * Constructeur php5
	 *
	 * @param array $botArray parametres administrateurs du plugin
	 * @param array $row article qui appele le plugin
	 * @void
	 */
	function __construct ( &$params) {
		$this->_debug[] = 'tag = '.$params['TAG'];
		$this->_debug[] = 'url = '.$params['URL'];
		$this->_debug[] = 'path = '.$params['PATH'];
		$this->_debug[] = 'articleId = '.$params['ARTICLE_ID'];
		$this->base_url = $params['URL'];
		$this->base_path = $params['PATH'];
	}
	
	function getDebug() {
		return ( $this->_debug );
	}
	/**
	 * Remplis les bases_path et base_url avec le répertoire de la galerie
	 * Remplis _listeFolder avec les noms des images du répertoire de la galerie
	 * @param unknown $images
	 * @param unknown $repertoireBase
	 */
	function cheminsImages($images, $repertoireBase){
		$this->base_url .= '/'.$repertoireBase.'/';
		$this->base_path .= '/'.$repertoireBase.'/';
		$this->_listeFolder = $images;
	}
	
	
	
	function createUrl() {
		$this->_debug[] = 'contenu repertoire = '.print_r($this->_listeFolder,true);
		$html = "<ul>\n";
		// il y a des fichiers
		if($this->_listeFolder[0]<>'') {
			foreach($this->_listeFolder as $fichier) {
			//	$html .= "\t".'<li><a href="'.$this->base_url.'/'.$galerie.$fichier.'" target="_blank">'.$fichier.'</a></li>'."\n";
				$html .= "\t".'<li>'.$this->getThumb($fichier, 120,120).'</li>'."\n";
				
			}
		}
		$html .="</ul>\n";
		return $html;
	}
	
	function getThumb($str_img, $int_largeur, $int_hauteur) {
		require_once 'ThumbLib.inc.php';
		$options = array('resizeUp' => true, 'jpegQuality' => 80);
		try
		{
			$thumb = PhpThumbFactory::create($this->base_path.$str_img, $options);
		}
		catch (Exception $e)
		{
			// handle error here however you'd like
		}
		$thumb->adaptiveResize($int_largeur, $int_hauteur);
		$repertoire_temporaire = $this->base_path.'/'.$this->galerie.'thumbs/';
		if(!is_dir($repertoire_temporaire)) mkdir($repertoire_temporaire, 0775, true);
		$thumb->save($repertoire_temporaire.md5($str_img.$int_hauteur.$int_largeur).'.png', 'png');
		$text = '<img src="'.$this->base_url.'/'.$this->galerie.'thumbs/'.md5($str_img.$int_hauteur.$int_largeur).'.png" alt="'.$str_img.'" />';
		return($text);
	}
}