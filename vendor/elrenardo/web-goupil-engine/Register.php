<?php
/**
 * @author    Teysseire Guillaume
 * @date      08/05/2016
 * @brief     WGE / Register gestion des registres
 */

namespace WGE;

use WGE\Host;
use WGE\RegisterKey;

class Register extends Multiton
{
	private $config = [];
	private $file   = NULL;

	/**
	* @brief charge le fichier de configuration
	* @param $path:string chemain vers le fichier
	*/
	public function load( $path )
	{
		$kernel = App::getService('kernel');

		$path = $kernel->getRealPath( $path );
		if( !file_exists($path))
			file_put_contents( $path , '{}');
		
		$this->file = $path;

		$data = file_get_contents( $path );
		$tab  = json_decode($data,true);
		//Création des clefs
		foreach ($tab as $key => $value)
		{
			$buffer = new RegisterKey( $value['key'] );
			$buffer->value( $value['value'] );
			$buffer->desc( $value['desc'] );
			$buffer->lock( $value['lock'] );
			//Ajout du nouveau registre
			$this->addRegisterKey( $buffer );
		}
	}


	/**
	* @brief sauvegarde le contenu du fichier
	*/
	public function save()
	{
		if( is_null($this->file))
			return;
		//Sauvegarde
		$buffer = array();
		foreach ($this->config as $key => $value)
			array_push($buffer, $value->getArray() );

		$json = json_encode( $buffer );
		file_put_contents( $this->file, $json );
	}


	/**
	* @brief ajoute une nouvelle clef au registre
	* @param $rk:RegisterKey
	* @param $force:bool créer la cle meme si elle exsite (suppression encienne)
	*/
	public function addRegisterKey( RegisterKey &$rk, $force=false )
	{
		if( $force )
			$this->config[ $rk->getKey() ] = $rk;
		else
		{
			if(!isset($this->config[ $rk->getKey() ] ))
				$this->config[ $rk->getKey() ] = $rk;
		}
	}



	/**
	* @brief renvoie une clef
	* @param $name:string
	* @return class registreKey
	*/
	public function &get( $name )
	{
		if( isset( $this->config[ $name ]))
			return $this->config[ $name ];
		return NULL;
	}

	/**
	* @brief renvoi la valeur d'une clef
	* @param $name:string
	* @return string
	*/
	public function getValue( $name )
	{
		if( isset( $this->config[ $name ]))
			return $this->config[ $name ]->getValue();
		return '';
	}


	/**
	* @brief renvoi la description d'une clef
	* @param $name:string
	* @return string
	*/
	public function getDesc( $name )
	{
		if( isset( $this->config[ $name ]))
			return $this->config[ $name ]->getDesc();
		return '';
	}

};