<?php

include("../../lib/Sistema.php");

/**
 * Clase API del WebService
 * Permite hacer las 4 Actividades Básicas CRUD a la Tabla Usuarios
 * Desde WebService
 * @author Said Rodriguez
 * @version 2017-01-28
 */

class api
{
	private $db;

	/**
	 * Constructor - Abre una conexion a la Base de Datos
	 *
	 * @param Vacío
	 * @return Conexion a la Base de Datos
	 */
	
	function __construct()
	{
		$clase = new Sistema();
		$clase->Conectar();
		$this->db = new mysqli($clase->ServidorBD,$clase->UsuarioBD,$clase->ClaveBD,$clase->NombreBD);
	}

	/**
	 * Destructor - close DB connection
	 *
	 * @param Vacío
	 * @return Vacío
	 */

	function __destruct()
	{
		$this->db->close();
	}

	/**
	 * Obtiene un Listado de Usuarios (Todos)
	 *
	 * @param Vacio	 
	 * @return Lista de Productos en Formato JSON
	 */
    function getListaUsuarios()
	{
		$query = 'SELECT * FROM usuarios';
		$list = array();
		$result = $this->db->query($query);
		while ($row = $result->fetch_assoc())
		{
			$list[] = $row;
		}
		return $list;
	}

	/**
	 * Obtiene los datos completos de un Usuario
	 *
	 * @param Id del Producto	 
	 * @return Lista de Datos del Usuario en Formato JSON
	 */
    function getDetalleUsuario($params)
	{
		$query = 'SELECT * FROM usuarios 
		          WHERE usuid='.$params['id'];
		$list = array();
		$result = $this->db->query($query);
		while ($row = $result->fetch_assoc())
		{
			$list[] = $row;
		}
		return $list;
	}

} /// Fin de la Clase API
