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
	public $_listeFolder = array();
	protected $_debug = array();
	private $base_url;
	private $base_path;
	/**
	 * Constructeur php5
	 *
	 * @param array $botArray parametres administrateurs du plugin
	 * @param array $row article qui appele le plugin
	 * @void
	 */
	function __construct ( &$params, &$row, &$id ) {
		$this->_debug[] = 'tag = '.$params['TAG'];
		$this->_debug[] = 'url = '.$params['URL'];
		$this->_debug[] = 'path ='.$params['PATH'];
		$this->base_url = $params['URL'];
		$this->base_path = $params['PATH'];
	}
	
	function getDebug() {
		return ( $this->_debug );
	}
	
	function createUrl($galerie) {
		$this->_debug[] = 'contenu repertoire = '.print_r($this->_listeFolder,true);
		$html = '<ul>';
		// il y a des fichiers
		if($this->_listeFolder[0]<>'') {
			foreach($this->_listeFolder as $fichier) {
				$html .= '<li><a href="'.$this->base_url.'/'.$galerie.$fichier.'" target="_blank">'.$fichier.'</a></li>';
			}
		}
		$html .='</ul>';
		return $html;
	}
}