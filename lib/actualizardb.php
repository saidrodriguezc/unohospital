<?PHP
  session_start(); 
  include("lib/Sistema.php");

  $clase = new Sistema();
  $clase->Nombredb = $_SESSION['DBNOMBRE']; 
 
  $avisos = ""; 
  $opcion = "";
  $opcion = $_GET["opcion"];
  
  $cont = $clase->Header("S","W"); 
  $cont.='<table width="100%">
	           <tr class="CabezoteTabla"> 
			     <td width="10"> </td>
			     <td width="37"> <img src="images/iconos/configuracion.png" width="32" height="32" border="0"> </td>
				 <td width="520"> Actualizar Bases de Datos <td>
    		     <td width="8"> </td>
			   </tr>	 			   
			 </table>';	
	
	
  $cont.='<br><br><center>';
    

  // Si NO tiene la Variable G_GECHA es porque es el dia 01-Marzo-2012
  $fechadb = $clase->BDLockup("G_FECHADB","configuraciones","variab","contenido");	
  // Si No existe ... la Creo
  if($fechadb == "")
  {
       $vsql="INSERT INTO `configuraciones` (`variab`, `contenido`, `usuario`, `desde`, `hasta`, `activa`) VALUES   
		 ('G_FECHADB', '01/03/2012', '', '2012-03-06 20:38:49', '0000-00-00', 'S')";	   
       $clase->EjecutarSQL($vsql);  
  }  


  ///////////////////////////////////////////////////////////////////////////////////////////////////
  if($opcion == "")
  {
     $fechadb = $clase->BDLockup("G_FECHADB","configuraciones","variab","contenido");
     $fechasistema = $_SESSION['FECHADBACTUAL'];
	 $dias = compararFechas($fechasistema , $fechadb);

	 if($dias > 0)
     {
	    $cont.='<center>
		        <h3>Base de datos Desactualizada. Desea Actualizarla?</h3>
				<br><br>
		        <a href="?opcion=actualizar">Si, Actualizarla</a>';	
	 }  
	 else
	 {
	    $cont.='<center>
		        <h3>Base de datos Actualizada</h3>';	
		
	 }
  }  
  
  //////////////////////////////////////////////////////////////////////////////////  
  if($opcion == "actualizar")
  {

    $FechaAct = "07/03/2012";
	$fechadb = $clase->BDLockup("G_FECHADB","configuraciones","variab","contenido");	
    if(compararFechas($FechaAct , $fechadb) > 0)
    {
	   $vsql="INSERT INTO `configuraciones` (`confid`, `variab`, `contenido`, `usuario`, `desde`, `hasta`, `activa`) VALUES   
	     (14, 'G_TIPOVENTAS', 'MESASABIERTAS', '', '2012-03-06 20:37:25', '0000-00-00', 'S'),
		 (15, 'G_INGRESORAPIDO', 'CHECKED', '', '2012-03-06 20:38:14', '0000-00-00', 'S')";	   
	   $clase->EjecutarSQL($vsql);
	   
	   $cont.="Actualizada al 07 Marzo ... <br> ";
   	   GuardarConfig($clase,$FechaAct,"G_FECHADB");
    }		  	 

    /////////////////////////////////////////
    $FechaAct = "08/03/2012";
    $fechadb = $clase->BDLockup("G_FECHADB","configuraciones","variab","contenido");
    if(compararFechas($FechaAct , $fechadb) > 0)
    {
	   $vsql="INSERT INTO `configuraciones` (`confid`, `variab`, `contenido`, `usuario`, `desde`, `hasta`, `activa`) VALUES   
		 (16, 'G_VERSOLOABIERTAS', 'CHECKED', '', '2012-03-06 20:38:49', '0000-00-00', 'S'),
		 (17, 'G_REIMPRESIONDOC', 'CHECKED', '', '2012-03-08 06:04:32', '0000-00-00', 'S')";	   
       $clase->EjecutarSQL($vsql);
       
  	   $vsql="ALTER TABLE productos ADD costoprom DOUBLE DEFAULT 0";	   
       $clase->EjecutarSQL($vsql);

  	   $vsql="ALTER TABLE productos ADD ultcosto DOUBLE DEFAULT 0";	   
       $clase->EjecutarSQL($vsql);
       
	   $cont.="Actualizada al 08 Marzo ... <br> ";
	   GuardarConfig($clase,$FechaAct,"G_FECHADB");
    }		  	 

    /////////////////////////////////////////
    $FechaAct = "11/03/2012";
    $fechadb = $clase->BDLockup("G_FECHADB","configuraciones","variab","contenido");
    if(compararFechas($FechaAct , $fechadb) > 0)
    {
	   $vsql="CREATE TABLE tablas (tablaid INTEGER(11) NOT NULL AUTO_INCREMENT, nomtabla VARCHAR(40) DEFAULT NULL, PRIMARY KEY (tablaid)) ENGINE=InnoDB;";	   
       $clase->EjecutarSQL($vsql);
       
  	   $vsql="INSERT INTO tablas(nomtabla) values('Productos'),('Terceros'),('Grupos de Productos'),('Lineas de Productos'),('Config Documentos')
		      ,('Localidades'),('Medios de Pago'),('Grupos de Personas'),('Zonas de Ubicacion'),('Config Documentos'),('Bodegas'),('Promociones')
			  ,('Config Documentos'),('Conceptos'),('Usuarios del Sistema')";	   
       $clase->EjecutarSQL($vsql);
       
       $vsql="CREATE TABLE usuariosxtablas (username varchar(20) NOT NULL, tablaid INTEGER(11) NOT NULL, insertar varchar(8) , 
	          modificar varchar(8), eliminar varchar(8) , ver varchar(8) , PRIMARY KEY (username,tablaid)) ENGINE=InnoDB;";	   
       $clase->EjecutarSQL($vsql);
       
	   $cont.="Actualizada al 11 Marzo 2012 <br> ";
	   GuardarConfig($clase,$FechaAct,"G_FECHADB");
    }		  	 

    /////////////////////////////////////////
    $FechaAct = "18/03/2012";
    $fechadb = $clase->BDLockup("G_FECHADB","configuraciones","variab","contenido");
    if(compararFechas($FechaAct , $fechadb) > 0)
    {
	   $vsql="CREATE TABLE plancuentas (pucid int(11) NOT NULL auto_increment, codigo varchar(5) NOT NULL, descripcion varchar(20) NOT NULL, 
	          creador varchar(20) NOT NULL, momento timestamp NOT NULL default CURRENT_TIMESTAMP,  PRIMARY KEY (pucid),  KEY codigo(codigo),
              KEY creador(creador)) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";	   
       $clase->EjecutarSQL($vsql);
       
  	   $vsql="INSERT INTO plancuentas(codigo,descripcion,creador,momento) values('1','ACTIVO','ADMINISTRADOR',NOW)";	   
       $clase->EjecutarSQL($vsql);

	   $vsql="CREATE TABLE centroscosto (cenid int(11) NOT NULL auto_increment, codigo varchar(5) NOT NULL, descripcion varchar(20) NOT NULL, 
	          creador varchar(20) NOT NULL, momento timestamp NOT NULL default CURRENT_TIMESTAMP,  PRIMARY KEY (cenid),  KEY codigo(codigo),
              KEY creador(creador)) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";	   
       $clase->EjecutarSQL($vsql);
       
  	   $vsql="INSERT INTO centroscosto(codigo,descripcion,creador,momento) values('00','Predeterminado','ADMINISTRADOR',NOW)";	   
       $clase->EjecutarSQL($vsql);
              
	   $cont.="Actualizada al 18 Marzo 2012 <br> ";
	   GuardarConfig($clase,$FechaAct,"G_FECHADB");
    }		  	 

    /////////////////////////////////////////
    $FechaAct = "19/03/2012";
    $fechadb = $clase->BDLockup("G_FECHADB","configuraciones","variab","contenido");
    if(compararFechas($FechaAct , $fechadb) > 0)
    {
  	   $vsql="INSERT IGNORE INTO prefijo(tipodoc,prefijo,descripcion,consecutivo,impresionpos) values('RSA','00','REMISIONES DE SALIDA','00001','N')";	   
       $clase->EjecutarSQL($vsql);
  	   $vsql="INSERT IGNORE INTO prefijo(tipodoc,prefijo,descripcion,consecutivo,impresionpos) values('PVE','00','PEDIDOS DE VENTA','00001','N')";	   
       $clase->EjecutarSQL($vsql);
  	   $vsql="INSERT IGNORE INTO prefijo(tipodoc,prefijo,descripcion,consecutivo,impresionpos) values('FCO','00','FACTURAS DE COMPRA','00001','N')";	   
       $clase->EjecutarSQL($vsql);
  	   $vsql="INSERT IGNORE INTO prefijo(tipodoc,prefijo,descripcion,consecutivo,impresionpos) values('REN','00','REMISIONES DE ENTRADA','00001','N')";	   
       $clase->EjecutarSQL($vsql);
  	   $vsql="INSERT IGNORE INTO prefijo(tipodoc,prefijo,descripcion,consecutivo,impresionpos) values('PCO','00','PEDIDOS DE COMPRA','00001','N')";	   
       $clase->EjecutarSQL($vsql);
  	   $vsql="INSERT IGNORE INTO prefijo(tipodoc,prefijo,descripcion,consecutivo,impresionpos) values('NIN','00','NOTAS DE INVENTARIO','00001','N')";	   
       $clase->EjecutarSQL($vsql);
  	   $vsql="INSERT IGNORE INTO prefijo(tipodoc,prefijo,descripcion,consecutivo,impresionpos) values('RCA','00','RECIBOS DE CAJA','00001','N')";	   
       $clase->EjecutarSQL($vsql);
  	   $vsql="INSERT IGNORE INTO prefijo(tipodoc,prefijo,descripcion,consecutivo,impresionpos) values('COE','00','COMPROBANTES DE EGRESO','00001','N')";	   
       $clase->EjecutarSQL($vsql);
  	   $vsql="INSERT IGNORE INTO prefijo(tipodoc,prefijo,descripcion,consecutivo,impresionpos) values('CXC','00','CUENTAS POR COBRAR','00001','N')";	   
       $clase->EjecutarSQL($vsql);
  	   $vsql="INSERT IGNORE INTO prefijo(tipodoc,prefijo,descripcion,consecutivo,impresionpos) values('CXP','00','CUENTAS POR PAGAR','00001','N')";	   
       $clase->EjecutarSQL($vsql);

              
	   $cont.="Actualizada al 19 Marzo 2012 <br> ";
	   GuardarConfig($clase,$FechaAct,"G_FECHADB");
    }
    
	/////////////////////////////////////////
    $FechaAct = "23/03/2012";
    $fechadb = $clase->BDLockup("G_FECHADB","configuraciones","variab","contenido");
    if(compararFechas($FechaAct , $fechadb) > 0)
    {
	   $vsql="INSERT INTO `configuraciones` (`variab`, `contenido`, `usuario`, `desde`, `hasta`, `activa`) VALUES   
('G_CREARVENTAS', 'CHECKED', '', '2012-03-06 20:38:49', '0000-00-00', 'S'),
('G_ELIMINARVENTAS', 'CHECKED', '', '2012-03-06 20:38:49', '0000-00-00', 'S'),
('G_EDITARVENTAS', 'CHECKED', '', '2012-03-06 20:38:49', '0000-00-00', 'S'),
('G_ASENTARVENTAS', 'CHECKED', '', '2012-03-06 20:38:49', '0000-00-00', 'S'),
('G_REVERSARVENTAS', 'CHECKED', '', '2012-03-06 20:38:49', '0000-00-00', 'S'),
('G_IMPRIMIRVENTAS', 'CHECKED', '', '2012-03-06 20:38:49', '0000-00-00', 'S'),
('G_REIMPRIMIRVENTAS', 'CHECKED', '', '2012-03-06 20:38:49', '0000-00-00', 'S'),
('G_ANULARVENTAS', 'CHECKED', '', '2012-03-06 20:38:49', '0000-00-00', 'S'),
('G_ELIMINARDETVENTAS', 'CHECKED', '', '2012-03-06 20:38:49', '0000-00-00', 'S'),
('G_MODIFICARDETVENTAS', 'CHECKED', '', '2012-03-06 20:38:49', '0000-00-00', 'S'),
('G_CAMBIARPRECIOVENTAS', 'CHECKED', '', '2012-03-06 20:38:49', '0000-00-00', 'S'),
('G_CAMBIARMOMENTOVENTAS', 'CHECKED', '', '2012-03-06 20:38:49', '0000-00-00', 'S')";	   
	   
       $clase->EjecutarSQL($vsql);
       
  	   $vsql="ALTER TABLE productos ADD costoprom DOUBLE DEFAULT 0";	   
       $clase->EjecutarSQL($vsql);

  	   $vsql="ALTER TABLE productos ADD ultcosto DOUBLE DEFAULT 0";	   
       $clase->EjecutarSQL($vsql);
       
	   $cont.="Actualizada al 23 Marzo ... <br> ";
	   GuardarConfig($clase,$FechaAct,"G_FECHADB");
    }		
	
	////////////////////////////////////////////////////////////////////////////////////////////////////
	$FechaAct = "11/07/2012";
	$fechadb = $clase->BDLockup("G_FECHADB","configuraciones","variab","contenido");	
    if(compararFechas($FechaAct , $fechadb) > 0)
    {
	   $vsql="CREATE TABLE tipotarea (codigo VARCHAR( 3 ) NOT NULL ,descripcion VARCHAR(25) NOT NULL , icono VARCHAR(25) , PRIMARY KEY (codigo)) 
	          ENGINE = MYISAM COMMENT = 'Tipos de Tarea programada';";	   
	   $clase->EjecutarSQL($vsql);
	
	   $vsql="INSERT INTO tipotarea(codigo,descripcion,icono) VALUES('EMA','ENVIO DE CORREOS ELECTRONICOS','enviarmail.png'),
	         ('BAK','BACKUP A LA BASE DE DATOS','copiaseguridad.png');";	   
	   $clase->EjecutarSQL($vsql);
	   
	   $vsql="CREATE TABLE tareasprogramadas(tareaid BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY ,tipotarea VARCHAR( 3 ) NOT NULL ,
              descripcion VARCHAR( 50 ) NOT NULL , usucrea VARCHAR( 20 ) NOT NULL , momcrea TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
              usurealiza VARCHAR( 20 ) NOT NULL , momrealiza TIMESTAMP NOT NULL , INDEX (tipotarea)) ENGINE = MYISAM COMMENT = 'Tareas Programadas';";	   
	   $clase->EjecutarSQL($vsql);
	 
	   $vsql="CREATE TABLE correoselectronicos(correoid BIGINT NOT NULL AUTO_INCREMENT, tareaid BIGINT NOT NULL , denombre VARCHAR( 80 ) NOT NULL ,
              demail VARCHAR( 80 ) NOT NULL , paranombre VARCHAR( 80 ) NOT NULL , paramail VARCHAR( 80 ) NOT NULL , ccmail VARCHAR( 100 ) NOT NULL ,
			  asunto VARCHAR( 80 ) NOT NULL , mensaje TEXT NOT NULL , adjunto1 VARCHAR( 60 ) NOT NULL , adjunto2 VARCHAR( 60 ) NOT NULL , 
			  adjunto3 VARCHAR( 60 ) NOT NULL , comandourl VARCHAR( 150 ) NOT NULL , PRIMARY KEY (correoid) , INDEX ( tareaid )) ENGINE = MYISAM ;";	   
	   $clase->EjecutarSQL($vsql);	 	 
	   
	   $cont.="Actualizada al 11 de Julio de 2012 ... <br> ";
   	   GuardarConfig($clase,$FechaAct,"G_FECHADB");
    }		  	 

	////////////////////////////////////////////////////////////////////////////////////////////////////
	$FechaAct = "20/07/2012";
	$fechadb = $clase->BDLockup("G_FECHADB","configuraciones","variab","contenido");	
    if(compararFechas($FechaAct , $fechadb) > 0)
    {
	   $vsql="CREATE TABLE eventolog (codigo VARCHAR( 3 ) NOT NULL ,descripcion VARCHAR( 50 ) NOT NULL , icono VARCHAR( 25 ) , PRIMARY KEY (codigo)) 
	          ENGINE = MYISAM COMMENT = 'Tipos de Eventos';";	   
	   $clase->EjecutarSQL($vsql);
	
	   $vsql="INSERT INTO eventolog(codigo,descripcion,icono) VALUES('001','Ingreso Exitoso al Sistema','ingreso.png'),
	         ('002','Datos Incorrectos al Ingresar al Sistema','ingresoerror.png'),('003','Cierre de Session en el Sistema','salida.png'),
 	         ('004','Creacion de Documento','crear.png'),('005','Edicion de Documento','editar.png'),('006','Borrado de Documento','borrar.png'),
			 ('007','Edicion de Documento','editar.png'),('008','Visualizacion de Informe','informe.png'),
			 ('009','Modifico la Configuracion del Sistema','configuracion.png'),('010','Anulacion de Documento','anular.png'),
			 ('011','Cambio de Contraseña a Usuarios','cambioclave.png'),('012','Creacion Usuario del Sistema','nuevousuario.png'),
			 ('013','Modificacion Datos del Usuario','modificousuario.png'),('014','Cambio de Permisos de Usuario','cambiopermisos.png'),
			 ('015','Creacion Nuevo Registro','nuevoregistro.png'),('016','Edicion de Registro','editarregistro.png'),
			 ('017','Borrado de Registro','borrarregistro.png'),('018','Impresion de Documento','imprimirdoc.png');";	   
	   $clase->EjecutarSQL($vsql);
	   
	   $vsql="CREATE TABLE logauditoria(logid BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY , evento VARCHAR( 3 ) NOT NULL , equipo varchar (20) , 
              direccionip varchar (18) , sentencia TEXT , descripcion VARCHAR( 255 ) NOT NULL , usuario VARCHAR( 20 ) NOT NULL , docuid BIGINT , 
			  momento TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , INDEX (evento)) ENGINE = MYISAM COMMENT = 'Log de Auditoria';";	   
	   $clase->EjecutarSQL($vsql);

	   $vsql="CREATE TABLE notasdoc(notaid BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY , docuid BIGINT NOT NULL , nota TEXT NOT NULL , 
	          creador VARCHAR( 20 ) NOT NULL , momento TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , INDEX (docuid) ) ENGINE = MYISAM COMMENT = 'Notas a Documentos'";	
	   $clase->EjecutarSQL($vsql); 

	   $cont.="Actualizada al 20 de Julio de 2012 ... <br> ";
   	   GuardarConfig($clase,$FechaAct,"G_FECHADB");
    }		  	 


}
  
  //////////////////////////////////////////////////////////
  echo $cont.$clase->PiePagina();  
  
  
  //////////////////////////////////////////////////////////
  //////////////////////////////////////////////////////////  
  
 function compararFechas($fecha1,$fecha2)          
 {
     if (preg_match("/[0-9]{1,2}\/[0-9]{1,2}\/([0-9][0-9]){1,2}/",$fecha1))            
              list($dia1,$mes1,$año1)=split("/",$fecha1);
			              
      if (preg_match("/[0-9]{1,2}-[0-9]{1,2}-([0-9][0-9]){1,2}/",$fecha1))            
              list($dia1,$mes1,$año1)=split("-",$fecha1);
              
        if (preg_match("/[0-9]{1,2}\/[0-9]{1,2}\/([0-9][0-9]){1,2}/",$fecha2))            
              list($dia2,$mes2,$año2)=split("/",$fecha2);
            
      if (preg_match("/[0-9]{1,2}-[0-9]{1,2}-([0-9][0-9]){1,2}/",$fecha2))            
              list($dia2,$mes2,$año2)=split("-",$fecha2);
              
        $dif = mktime(0,0,0,$mes1,$dia1,$año1) - mktime(0,0,0, $mes2,$dia2,$año2);
        return ($dif);                                     
 }
 
 ///////////////////////////////////////////////////////////////////////    
 function GuardarConfig($clase,$valor,$variable)
 {
 	$vsql = "UPDATE configuraciones SET contenido = '".$valor."' WHERE VARIAB = '".$variable."'";    
	$clase->EjecutarSQL($vsql);
	$_SESSION[$variable] = $valor;
 }
?>	 	  
