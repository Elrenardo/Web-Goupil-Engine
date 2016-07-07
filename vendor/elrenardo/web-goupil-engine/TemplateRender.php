<?php
/**
 * @author    Teysseire Guillaume
 * @version   1.0
 * @date      03/05/2016
 * @brief     WGE / TemplateRender Gestion des templates
 */
use \WGE\App;

namespace WGE;
class TemplateRender extends Multiton
{
	private $templates = [];
	private $render    = NULL;

	/**
	* @brief  Constructeur de la class template
	*/
	protected function __construct()
	{
		//App::getRealPath()
		$loader = new \Twig_Loader_Filesystem( App::getRealPath() );
		$this->render = new \Twig_Environment($loader, array( 'cache'=>false ));
	}

	/**
	* @brief fait un rendu de template a partir du nom
	* @param $name:string nom de la template
	* @param $param array
	*/
	public function render( $name, array &$param )
	{
		if( isset( $this->templates[ $name ] ))
		{
			$kernel  = App::getService('kernel');
			$plugins = App::getService('plugins'); 

			$ret    = '';
			$buffer = $kernel->getPluginPath();
			$tpl    = $this->templates[ $name ];
			$home   = Host::getHome();

			if( isset( $plugins[ $home ] ))
			{
				$kernel->setPluginPath( $plugins[ $tpl->getPlugin() ]->getPath() );
				//var_dump( $kernel->getRealPath( $tpl->getPath() ));
				//$r = 'C:\xampp\htdocs\dd0\homes\default\theme\index.twig';
				$ret =  $this->render->render( $tpl->getPath(), $param );
			}
			else
				$ret = 'Error lost "home" Host::$home !';
			
			$kernel->setPluginPath( $buffer );
			return $ret;
		}
		return 'Error lost template: '.$name;
	}


	/**
	* @brief  Ajoute une nouvelle template, le nom utilisé sera le nom du fichier sans extension
	* @param $tpl:Template 
	*/
	public function addTemplate( Template $tpl )
	{
		$this->templates[ $tpl->getName() ] = $tpl;
	}


	/**
	* @brief Renvoi le chemain d'un fichier
	* @param $name:string
	*/
	public function getTemplate( $name )
	{
		if( isset( $this->templates[ $name ] ))
			return $this->templates[ $name ]->getPath();
		return '';
	}

	/**
	* @brief ajoute une method au template twig
	* @param $namesstring nom de la fonction
	* @param $fonction fonction a ajouter
	*/
	public function addFuncTpl( $name, $fonction )
	{
		$this->render->addFunction( new \Twig_SimpleFunction( $name, $fonction, array('is_safe' => array('html')) ));
	}

	/**
	* @brief Ajoute un object a twig
	* @param $name:string, nom de l'object
	* @param $obj class
	*/
	public function addGlobalTpl( $name, $obj )
	{
		$this->render->addGlobal( $name, $obj );
	}
};