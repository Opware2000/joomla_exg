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
		$html='';
		// Include the plugin files
		include_once( dirname( __FILE__ ).'/plugin_exg/exg.class.php' );
		//On calcule le texte à remplacer.
		unset( $galerie );
		$galerie = new exgClass($this->_parametres, $article->text , $article->id);
		if(preg_match_all("@{".$this->_tag_gallery."}(.*){/".$this->_tag_gallery."}@Us", $article->text, $matches, PREG_PATTERN_ORDER) > 0)
		{
			$langue = JFactory::getLanguage()->getTag();
			foreach($matches[1] as $match)
			{
				$html .= '<pre>'.$match.'</pre><br />';
			}
		}
		//traitement si débug.
		$this->_debugMessage['retour_exgClass'] = $galerie->getDebug();
		$html .= $this->showDebug();
		// on effectue le remplacement
		$article->text = preg_replace("@(<p>)?{".$this->_tag_gallery."}"."(.*)"."{/".$this->_tag_gallery."}(</p>)?@s", $html, $article->text);
	}
	
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
	
	 private function nettoyageChemin($chemin) {
	 	if(substr($chemin, -1) == '/')
	 	{
	 		$chemin = substr($chemin, 0, -1);
	 	}
	 	return($chemin);
	 }
}