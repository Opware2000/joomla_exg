<?php
/**
 * @package		EXG - Easy eXtended Gallery - plugin for Joomla 3.x
 * @copyright 	Nicolas Ogier
 * @author 		Nicolas Ogier {@link http://www.nicolas-ogier.fr}
 * @version 	3-1.0	2013-05-01
 * @link 		http://www.nicolas-ogier.fr/exg/
 * 
 * @license 	GNU/GPL http://www.gnu.org/copyleft/gpl.html
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
// Import library dependencies
//jimport('joomla.plugin.plugin');
class PlgContentEXG extends JPlugin
{
	protected $_tag_gallery = 'gallery';
	protected $_live_site;
	protected $_absolute_path;
	protected $_parametres;
	private   $_debug;
	private	  $_debugMessage = array();
	protected $_pathRoot='images';
//	protected $_html;
	
	function __construct(&$subject, $params) {
		$app = JFactory::getApplication();
		if($app->isAdmin())
		{
			return;
		}
		parent::__construct($subject, $params);
		// activation de la langue
		$this->loadLanguage('plg_content_exg', JPATH_ADMINISTRATOR);
		// on récupere le chemin absolu et l'URL du site
		$this->_absolute_path = JPATH_SITE;
		$this->_live_site = $this->nettoyageChemin(JURI::base());
		// Initialisation
		$this->_debug = true;
		// on récupère quelques paramètres
		$tag  = $this->params->get('exg_tag', $this->_tag_gallery);
		$root = $this->params->get('path_root', $this->_pathRoot);
		$root = $this->nettoyageChemin($root);
		//vérification que le tag est correctement formaté
		if(is_string($tag) && ctype_alnum($tag)) {
			$this->_tag_gallery = $tag;
		}
		$this->_parametres = array('TAG'=> $this->_tag_gallery, 'URL' => $this->_live_site, 'PATH' => $this->_absolute_path);
		$this->_debugMessage['parametres_initiaux']=$this->_parametres;
		$this->_debugMessage['parametres_plugin']=array('tag'=>$tag, 'root'=>$root);
		//initialisation
		//$this->_html = '';
	}
	/**
	 * Fonction onContentPrepare qui permet de modifier l'article pour insérer la galerie à la place du tag d'appel.
	 * @param unknown $context
	 * @param unknown $article
	 * @param unknown $params
	 * @param number $limitstart
	 * @return boolean
	 */
	public function onContentPrepare($context, &$article, &$params, $limitstart=0) 
	{
		// Ne pas utiliser ce plugin lorsque le contenu est indexé
		if ($context === 'com_finder.indexer')
		{
			return true;
		}
		// Vérification simple si le plugin a quelque chose à traiter
		if (strpos($article->text, $this->_tag_gallery) === false && strpos($article->text, '/'.$this->_tag_gallery) === false)
		{
			return true;
		}
		// Oui il y a bien le tag alors on continue
		$galerie_html='';
		// Include the plugin files
		include_once( dirname( __FILE__ ).'/plugin_exg/exg.class.php' );
		$this->_debugMessage['galeries']=array();
		$this->_parametres['ARTICLE_ID']=$article->id;
		if(preg_match_all("@{".$this->_tag_gallery."}(.*){/".$this->_tag_gallery."}@Us", $article->text, $matches, PREG_PATTERN_ORDER) > 0)
		{
			$langue = JFactory::getLanguage()->getTag();
			$i=0;
			foreach($matches[1] as $match)
			{
				$galerie = new exgClass($this->_parametres);
				$exg_repertoire = preg_replace("@{.+?}@", "", $match);
				$regex = "@{".$this->_tag_gallery."}".$exg_repertoire."{/".$this->_tag_gallery."}@s";
				$galerie_html ='galerie #'.$i;
				$galerie->cheminsImages($this->listePath( $this->_absolute_path.'/'.$this->_pathRoot.'/'.$match),$this->_pathRoot,$match);
				//$galerie->_listeFolder = $this->listePath( $this->_absolute_path.'/'.$this->_pathRoot.'/'.$match);
				$galerie_html .= $galerie->createUrl();
				$this->_debugMessage['galeries'][$i]=$match;	
				$i++;
				//remplacement du texte d'appel par la galerie
				$article->text = preg_replace($regex, $galerie_html, $article->text);
				$this->_debugMessage['code_html'][$i]='<b>Galerie #'.$i.'</b><pre>'.htmlentities($galerie_html).'</pre>';
				$this->_debugMessage['retour_exgClass'][$i] = $galerie->getDebug();
				unset( $galerie );
			}
		}

		//traitement si débug.
		$html_debug = $this->showDebug();
		// on effectue le remplacement
	$article->text .=$html_debug;

	}
	/**
	 * Affichage du débugage
	 * @return string
	 */
	private function showDebug() {
		$retour_html='';
		if($this->_debug){
			$retour_html = '<pre>';
			foreach ($this->_debugMessage as $k => $v) {
				$retour_html .= "[$k] => ".print_r($v,true)."\n";
			}
			$retour_html .='</pre>';
		}
		return( $retour_html);
	 }
	/**
	 * Enlève le dernier / s'il est présent dans le chemin donné en paramètre $chemin
	 * @param string $chemin
	 * @return string
	 */
	 private function nettoyageChemin($chemin) {
	 	if(substr($chemin, -1) == '/')
	 	{
	 		$chemin = substr($chemin, 0, -1);
	 	}
	 	return($chemin);
	 }
	 /**
	  * Renvoie la liste des fichiers présents dans le répertoire passé en paramètre.
	  * @param unknown $searchpath
	  */
	 private function listePath($searchpath) {
	 	//Importe les bibliothèques du système de fichiers. Peut-être pas nécessaire, mais ne fait pas mal
	 	jimport('joomla.filesystem.folder');
	 	return(JFolder::files($searchpath, '.jpg'));
	 }
}