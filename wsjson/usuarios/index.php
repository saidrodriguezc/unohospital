<?php
/**
 * Ejemplo simple de Uso de un Webservice
 * @author Said Rodriguez
 * @version v1.0
 * @return  Datos del Producto en Formato JSON
 **/

// Vinculo el Archivo de la API
require_once 'api.php';

// Crea una nueva Instancia de la API del Producto
$api = new api();

// message to return
$message = array();

// Capturo el Metodo -> GET -> POST -> PUT -> DELETE
$method = $_SERVER['REQUEST_METHOD'];

//// Parametros que se le pasan a los métodos del WebService
$params = array();

switch($method)
{
	///////////////////////////////////////////////////////////////////////////////////////
	///// GET -> Obtiene la Lista de Productos o los datos de un producto con id = ID
	case 'GET':
	
		$params['id']  = $_GET["id"];
		if(strlen($_GET["id"]) != "")
        {
          if (is_array($data = $api->getDetalleUsuario($params))) 
		  {
			$message["code"] = "1";
		 	$message["data"] = $data;
		  } else {
			$message["code"] = "0";
			$message["message"] = "Parametros Incorrectos al Webservice";
		  }	
        }
		else
		{
            $data = $api->getListaUsuarios();
		  	$message["code"] = "1";
		 	$message["data"] = $data;		  
		}

	break;

	///////////////////////////////////////////////////////////////////////////////////////
    //// POST -> Crear Nuevo Producto
    case 'POST':
		    $data = "ERROR : No está Admitida la creacion de Usuarios Via WebService";
		    $message["code"] = "0";
		 	$message["data"] = $data;		  
	break;

	///////////////////////////////////////////////////////////////////////////////////////
    //// PUT -> Actualizar Datos del Producto
    case 'PUT':
		    $data = "ERROR : No está Admitida la Actualizacion de Usuarios Via WebService";
		    $message["code"] = "0";
		 	$message["data"] = $data;		  
	break;

	///////////////////////////////////////////////////////////////////////////////////////
    //// POST -> Crear Nuevo Producto
    case 'DELETE':
		    $data = "ERROR : No está Admitida la eliminacion de Usuarios Via WebService";
		    $message["code"] = "0";
		 	$message["data"] = $data;		  
	break;

	///////////////////////////////////////////////////////////////////////////////////////
    //// En caso de no ser ninguno de los Metodos permitidos muestro el error
	default://metodo NO soportado
       echo 'METODO NO SOPORTADO';
    break;
}

//the JSON message
header('Access-Control-Allow-Origin: *');
header('Content-type: application/json; charset=utf-8');
echo json_encode($message);

?>
