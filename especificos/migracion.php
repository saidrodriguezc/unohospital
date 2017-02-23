<?PHP
  session_start(); 
  include("../lib/Sistema.php");

  $clase = new Sistema();
  $clase->Nombredb = "unohospital";
  $conex  = $clase->Conectar();
    
  $clase2 = new Sistema();
  $clase2->Nombredb = "unohospicamara";
  $conex2  = $clase2->Conectar();
    
  $vsql2 = "SELECT * FROM documentos D INNER JOIN terceros T ON (D.terid1 = T.terid) WHERE tipodoc='PSE' AND prefijo='00' AND iva = 0 ORDER BY numero ASC limit 0,10";
  $result2 = mysql_query($vsql2,$conex2);

  while($row2 = mysql_fetch_array($result2)) 
  {
     // Datos del Tercero
	 $nit     = $row2['nit'];
	 $tipodoc = $row2['tipodoc'];
	 $prefijo = $row2['prefijo'];
	 $numero  = $row2['numero'];	 	 	 
	 	 
	 echo'<h2> Migrando Documento : '.$tipodoc.$prefijo.$numero."--".$nit.'</h2>';
	 
	 // Busco el Paciente en la Tabla Terceros de la DB del Sistema para ver si existe
     $cantidad = $clase->SeleccionarUno("SELECT COUNT(*) FROM terceros WHERE nit='".$nit."'");

	 // Si no existe - Lo creamos 
	 if($cantidad == 0)
	 {
	   $vsql3 = "SELECT * FROM terceros WHERE nit='".$nit."'";
       $result3 = mysql_query($vsql3,$conex2);
	   
	   $row2 = mysql_fetch_array($result3);
	   $vsql = "INSERT INTO terceros(codigo,nit,nombre,direccion,ciudadid,telefono,celular,email,cargo,zonaid,clasificaterid,nombre1,nombre2,
                 apellido1,apellido2,fechanac,edad,genero,estadocivilid,nivelid,entidadid) VALUES('".$row2['codigo']."','".$row2['nit']."','".$row2['nombre'].
				 "','".$row2['direccion']."','".$row2['ciudadid']."','".$row2['telefono']."','".$row2['celular']."','".$row2['email']."','".$row2['cargo'].
				 "',".$row2['zonaid'].",".$row2['clasificaterid'].",'".$row2['nombre1']."','".$row2['nombre2']."','".$row2['apellido1']."','".$row2['apellido2'].
				 "','".$row2['fechanac']."',".$row2['edad'].",'".$row2['genero']."',".$row2['estadocivilid'].",".$row2['nivelid'].",";

	   $clase->EjecutarSQL(vsql);			 
	   	   
	 }  // Fin de "Si No Existe Tercero"
  
     /// Finalmente asigno el Terid en la Base de Datos UNOHOSPITAL
	 $TERIDPACIENTE = $clase->BDLockup($nit,'terceros','nit','terid');
	 
	 //////////////////////////////////////////////////////////////////
	 // Encabezado del Documento PSE
	 //////////////////////////////////////////////////////////////////
	 
	 $clase->Nombredb = "unohospital";
     $conex  = $clase->Conectar();
    
     $clase2->Nombredb = "unohospicamara";
     $conex2  = $clase2->Conectar();

	 // Busco la PSE para ver si Existe
     $cantidad = $clase->SeleccionarUno("SELECT COUNT(*) FROM documentos WHERE tipodoc='".$tipodoc."' AND prefijo='CC' AND numero='".$numero."'");

     // Si no existe el Documento -> Lo Creo
	 if($cantidad == 0)
	 {
     
 	   $vsql3 = "SELECT * FROM documentos WHERE tipodoc='".$tipodoc."' AND prefijo='".$prefijo."' AND numero='".$numero."'";
       $result3 = mysql_query($vsql3,$conex2);
	  	   
	   $row2 = mysql_fetch_array($result3);
	   $vsql = "INSERT INTO documentos(tipoexamen,nitempresa,cargo,tipodoc,prefijo,numero,fechadoc,fecasentado,fecanulado,terid1,terid2,observacion,
	            base,iva,total,impreso,totalitems,creador,momento,contratoid)	   
			    VALUES('".$row2['tipoexamen']."','".$row2['nitempresa']."','".$row2['cargo']."','".$row2['tipodoc']."','CC','".$row2['numero']."','".$row2['fechadoc'].
				"','".$row2['fecasentado']."','".$row2['fecanulado']."',".$TERIDPACIENTE.",1,'".$row2['observacion']."',0,0,0,'".
				$row2['impreso']."',0,'".$row2['creador']."','".$row2['momento']."',".$row2['contratoid'].")";

	   $clase->EjecutarSQL($vsql);		    
	 } 	
	 
	 $clase2->EjecutarSQL("UPDATE documentos SET iva=1 WHERE tipodoc='".$tipodoc."' AND prefijo='".$prefijo."' AND numero='".$numero."'");
 	 
	 $DOCUIDPSE = $clase->SeleccionarUno("SELECT docuid FROM documentos WHERE tipodoc='PSE' AND prefijo='CC' AND numero='".$numero."'");
	 	 
	 /////////////////////////////////////////////////////////////
	 // Detalles del Documento PSE
	 /////////////////////////////////////////////////////////////
	 
  	 $clase->Nombredb = "unohospital";
     $conex  = $clase->Conectar();
    
     $clase2->Nombredb = "unohospicamara";
     $conex2  = $clase2->Conectar();

     /// Elimino los detalles de ese documento, si los tiene
     $clase->EjecutarSQL("DELETE FROM dedocumentos WHERE docuid=".$DOCUIDPSE);

     /// Busco los detalles del la Prestacion de Servicio para Migrarlos 
 	 $vsql3 = "SELECT DD.* FROM documentos D INNER JOIN dedocumentos DD ON (D.docuid = DD.docuid) 
	           WHERE D.tipodoc='".$tipodoc."' AND D.prefijo='".$prefijo."' AND D.numero='".$numero."'";

     $result3 = mysql_query($vsql3,$conex2);

	 while($row2 = mysql_fetch_array($result3))
	 {
		 $vsql = "INSERT INTO dedocumentos(docuid,itemid,bodegaid,cantidad,valunitario,valdescuento,valparcial,porciva,valbase,valiva,productoidvar,prorea)	   
		  	      VALUES(".$DOCUIDPSE.",".$row2['itemid'].",".$row2['bodegaid'].",".$row2['cantidad'].",".$row2['valunitario'].",".$row2['valdescuento'].
				  ",".$row2['valparcial'].",".$row2['porciva'].",".$row2['valbase'].",".$row2['valiva'].",".$row2['productoidvar'].",".$row2['prorea'].")";
		 /// Inserto los detalles del Documento
		 $clase->EjecutarSQL($vsql);			 
	 } 


	 /////////////////////////////////////////////////////////////
	 // Registro Historia Clinica
	 /////////////////////////////////////////////////////////////
	 
  	 $clase->Nombredb = "unohospital";
     $conex  = $clase->Conectar();
    
     $clase2->Nombredb = "unohospicamara";
     $conex2  = $clase2->Conectar();

     /// Busco los detalles del la Prestacion de Servicio para Migrarlos 
 	 $vsql3 = "SELECT DISTINCT HC.* , HS.observa1 , HS.observa2, HS.observa3, HS.observa4, HS.observa5, HS.observa6, HS.conceptomedid  
	           FROM documentos D INNER JOIN historiacli HC ON (HC.docuid = D.docuid) 
	           LEFT JOIN historiaself HS ON (HC.historiaid = HS.historiaid)
	           WHERE D.tipodoc='".$tipodoc."' AND D.prefijo='".$prefijo."' AND D.numero='".$numero."'";
     $result3 = mysql_query($vsql3,$conex2);

     if($row2 = mysql_fetch_array($result3))
	 {
	    $vsql = "INSERT INTO historiacli(teridpaciente,teridprof,conceptomed,estado,docuid,tipoexamen,creador,momento)	   
		  	      VALUES(".$TERIDPACIENTE.",".$row2['teridprof'].",'".$row2['conceptomed']."','".$row2['estado']."',".$DOCUIDPSE.
				  ",'".$row2['tipoexamen']."','".$row2['creador']."','".$row2['momento']."')";

		 /// Inserto los detalles del Documento
		 $clase->EjecutarSQL($vsql);
		 
		 $HISTORIAID = $clase->SeleccionarUno('SELECT MAX(historiaid) FROM historiacli');
		 
		 $vsql2 = "INSERT INTO historiaself(historiaid,observa1,observa2,observa3,observa4,observa5,observa6,conceptomedid)	   
		  	      VALUES(".$HISTORIAID.",'".$row2['observa1']."','".$row2['observa2']."','".$row2['observa3']."','".$row2['observa4'].
				  "','".$row2['observa5']."','".$row2['observa6']."',".$row2['conceptomedid'].")";

		 /// Inserto los detalles del Documento
		 $clase->EjecutarSQL($vsql2);			 
	 } 		
	
	//// Al Finalizar todos los procesos mando el mensaje de Exito
	echo'<h3> Proceso Terminado Exitosamente </h3>';
 	  
  }  /// Fin del Recorrido de las PSEs
  
 // echo"<script language=\"javascript\"> setTimeout('document.location.reload()',3*1000); </script>";
  
?> 