<?php
/**
 * @author    Teysseire Guillaume
 * @version   1.0
 * @date      09/05/2016
 * @brief     WGE / Container stockage
 */

namespace WGE;
class Container
{
	private $data = [];

	/**
	* @brief Ajoute un nouvel élément au container
	* @param $name:string nom du sotckage
	* @param $obj:class
	*/
	public function add( $name, $obj )
	{
		if( isset( $this->data[$name] ))
			die('Container: '.$name.' already exists !');

		$this->data[$name] = $obj;
	}


	/**
	* @brief renvoi une référence du stockage
	* @param $name:string
	* @return référence class
	*/
	public function &get( $name )
	{
		if( isset( $this->data[$name] ))
			return $this->data[$name];
		die('Container lost item: '.$name.' !');
	}


	/**
	* @brief renvoi tout le stockage
	* @param référence array
	*/
	public function &getAll()
	{
		return $this->data;
	}


	/**
	* @brief si un stockage existe
	* @param $name:string
	* @return boolean true si existe sinon false
	*/
	public function is( $name )
	{
		if( isset( $this->data[$name] ))
			return true;
		return false;
	}
};