<?PHP
  session_start(); 
  include("lib/Sistema.php");

  $clase = new Sistema();
 
  $avisos = ""; 
  $opcion = "";
  $opcion = $_GET["opcion"];

  /////////////////////////////////////////////////////////////////////////  
  if($opcion == "abrirhistoria")
  {
    $docuid    = $_GET['id'];   
    $cont.='<h3>Tipo de Examen </h3><center>
			<table width="350">';
			
    $cont.='<tr class="TablaDocsPar">
              <td width="15"> </td>
			    <td> <a href="facturapida.php?opcion=abrirhistoria2&te=INGRESO&id='.$docuid.'"> Examen de Ingreso </a> </td> 
			    <td width="15"> </td>
			</tr>
			<tr class="TablaDocsImPar">
              <td width="15"> </td>
			    <td> <a href="facturapida.php?opcion=abrirhistoria2&te=PERIODICO&id='.$docuid.'"> Examen Periodico </a> </td> 
			    <td width="15"> </td>
			</tr>
			<tr class="TablaDocsPar">
              <td width="15"> </td>
			    <td> <a href="facturapida.php?opcion=abrirhistoria2&te=EGRESO&id='.$docuid.'"> Examen de Egreso </a> </td> 
			    <td width="15"> </td>
			  </tr></table>';
	echo $cont;
	exit(0);		
  }
  
  /////////////////////////////////////////////////////////////////////////  
  if($opcion == "abrirhistoria2")
  {
    $docuid     = $_GET['id'];   
	$tipoexamen = $_GET['te'];   
	$teridpaciente  = $clase->BDLockup($docuid,'documentos','docuid','terid1');

    $hayabiertas = $clase->SeleccionarUno("SELECT COUNT(*) CANT FROM historiacli WHERE teridpaciente=".$teridpaciente." AND estado='A'");
	
	if($hayabiertas == 0)
	{
	    $vsqlx= "SELECT DD.prorea FROM documentos D INNER JOIN dedocumentos DD ON (D.docuid = DD.docuid)
				 INNER JOIN item I ON (I.itemid = DD.itemid) INNER JOIN productos P ON (P.itemid = I.itemid)
				 INNER JOIN gruposprod GP ON (GP.gruposprodid = P.gruposprodid) WHERE D.docuid = ".$docuid." AND GP.codigo = '001'";	
		$profesional =  $clase->SeleccionarUno($vsqlx);
				
		$vsql = "INSERT INTO historiacli(teridpaciente,teridprof,docuid,tipoexamen,creador,momento) 
	    	     values(".$teridpaciente.",".$profesional.",".$docuid.",'".$tipoexamen."','".$_SESSION['USERNAME']."',CURRENT_TIMESTAMP)";

		$cant = $clase->EjecutarSQL($vsql);
	
		if($cant == 1){
  			$Nhistoriaid = $clase->SeleccionarUno("SELECT MAX(historiaid) hid FROM historiacli");
    		$vsql = "INSERT INTO historiaself(historiaid) values(".$Nhistoriaid.")";
	 	    $cant = $clase->EjecutarSQL($vsql);
			
			// Registro en el LOG de Auditoria la creacion de la Venta 
            $clase->CrearLOG('004','Se Abre historia Clinica  - Id : '.$Nhistoriaid,strtoupper($_SESSION["USERNAME"]),'',$docuid);

			$clase->Aviso(1,"Historia Abierta con Exito &nbsp;&nbsp; <a href=\"historiacli.php?opcion=detalles&id=".$Nhistoriaid."\"> Visualizarla? </a>");  		
   		}	
		else
   		  $clase->Aviso(2,"Error al Crear el Registro &nbsp;&nbsp; <a href=\"javascript:history.back(-1);\"> Intentar de Nuevo ? </a>");  		
    }
	else
	{
	  $hid = $clase->SeleccionarUno("SELECT historiaid FROM historiacli WHERE teridpaciente=".$teridpaciente." AND estado='A'");
	  $clase->Aviso(2,"Ya existe una Historia Clinica abierta para ese Paciente <a href=\"historiacli.php?opcion=detalles&id=".$hid."\"> Visualizarla? </a>");  		
    }
	header("Location: facturapida.php?opcion=menups&docuid=".$docuid);
  }
 
  /////////////////////////////////////////////////////////////////////////  
  if($opcion == "ordenesservicio")
  {
     $docuid = $_GET['id'];    
	 
	 // Valido si Hay Servicios de Laboratorio para Crearle la orden
     $vsql = "SELECT COUNT(*) FROM documentos D 
	          INNER JOIN dedocumentos DD ON (D.docuid = DD.docuid)
	          INNER JOIN productos P ON (P.itemid = DD.itemid)
              INNER JOIN gruposprod GP ON (GP.gruposprodid = P.gruposprodid)			  
			  WHERE GP.codigo = '002' AND D.docuid=".$docuid; 

     $HayLaboratorios = $clase->SeleccionarUno($vsql);	 
	 
	 // Valido si Hay Servicios de Laboratorio para Crearle la orden
     $vsql = "SELECT COUNT(*) FROM dedocumentos DD 
	          INNER JOIN productos P ON (P.itemid = DD.itemid)
              INNER JOIN gruposprod GP ON (GP.gruposprodid = P.gruposprodid)			  
			  WHERE GP.codigo = '003' AND D.docuid=".$docuid; 

     $HayRayosx = $clase->SeleccionarUno($vsql);
	 
	 //Genero los Documentos directamente
	 if($HayLaboratorios)
	   GenerarRSA($docuid,"002");
	 if($HayRayosX)
	   GenerarRSA($docuid,"003");  
  }


  /////////////////////////////////////////////////////////////////////////  
  if($opcion == "ordenesmedicos")
  {
    $docuid = $_GET['id'];    
    $vsql = "SELECT DISTINCT D.docuid , DD.prorea  
             FROM documentos D INNER JOIN dedocumentos DD ON (D.docuid = DD.docuid) 
			 INNER JOIN terceros PAC ON (PAC.terid = D.terid1) 
			 INNER JOIN item I ON (I.itemid = DD.itemid) 
			 INNER JOIN productos P ON (I.itemid = P.itemid) 
			 INNER JOIN gruposprod GP ON (GP.gruposprodid = P.gruposprodid) 
			 LEFT JOIN terceros MED ON (DD.prorea = MED.terid) 
			 WHERE D.docuid=".$docuid." ORDER BY DD.prorea";
	
	$conex  = $clase->Conectar();
    $result = mysql_query($vsql,$conex); 

    $i = 1;	
	while($row = mysql_fetch_array($result))
	{
	   ImprimirTicketMedico($row['docuid'],$row['prorea'],"fichero".$i.".txt"); 		
	   $i++;
	}	
	
	if($i>1)
	  echo'<br><center>
	       <h3> Tickets Impresos Exitosamente</h3>
           <img src="images/iconos/observaciones.png" border="0">';           		   
	else
	  echo'<br><center>
	       <h3> No hay Tickets por Imprimir</h3>
           <img src="images/iconos/observaciones.png" border="0">';           		   
  }
  
  /////////////////////////////////////////  
  if($opcion == "eligecontrato")
  {
    $cliente = $_GET['cliente'];
    $cont = $clase->Header("S","W");  
	$cont.='<table width="100%">
	           <tr class="CabezoteTabla"> 
			     <td width="10"> </td>
			     <td width="37"> <img src="images/iconos/pacientes.png" width="32" height="32" border="0"> </td>
				 <td width="400"> Seleccionar Contrato </td> 
			   </tr></table>';
       
    $cont.='<div style="overflow:auto; height:530px;width:796px;">
	         <table width="450">';
			
	$vsql   = "SELECT C.contratoid , C.numero , T.nombre FROM contratos C INNER JOIN terceros T ON (T.terid = C.terid) ORDER BY T.nombre ASC";
    $conex  = $clase->Conectar();
    $result = mysql_query($vsql,$conex); 

    $i = 0;	
	while($row = mysql_fetch_array($result))
	{
	  $i++;
	  if($i%2 == 0)
	    $cont.='<tr class="TablaDocsPar">';
	  else
	    $cont.='<tr class="TablaDocsImPar">';		 

	  $cont.='<td width="15"> </td>
			    <td> No. '.$row['numero'].' </td> 
			    <td> '.$row['nombre'].'</td> 
				<td> <a href="facturapida.php?cliente='.$cliente.'&contrato='.$row['contratoid'].'"> Elegir </a> </td> 
			    <td width="15"> </td>
			  </tr>';		 		
	}
    
	$cont.='</table>';
	echo $cont;
	exit(0);		
  }

  ////////////////////////////////////////////////////////////////////////////////  
  ////////////////////////////////////////////////////////////////////////////////  
  if($opcion == "generarps")
  {
    $cedcliente = $_POST['cliente'];	
	$contratoid = $_POST['contratoid'];	
	
	$vsql = "SELECT itemid FROM decontrato WHERE contratoid=".$contratoid;
    
	$conex  = $clase->Conectar();
    $result = mysql_query($vsql,$conex); 
	
	$i = 0;
	while($row = mysql_fetch_array($result))
	{
      $examen[$i] = $_POST[$row['itemid']];
	  $prorea[$i] = $_POST['prorea_'.$row['itemid']];
	  $precio[$i] = $_POST['pre_'.$row['itemid']];
	  
	  if(($examen[$i] != "")&&($prorea[$i] != "")&&($precio[$i] != ""))
		 $i++;	  
	}
	
	/// Si se eligio algun procedimiento y se selecciono el Profesional - Creo el Documento PSE o FVE
	if($i > 0)
	{
	   $tipodocumento = $clase->BDLockup($contratoid,"contratos","contratoid","tipodocgenera");
	   $numero = $clase->SeleccionarUno("SELECT consecutivo FROM prefijo WHERE tipodoc='".$tipodocumento."' AND prefijo='00'");
 	   	   
	   $clienteid    = $clase->BDLockup($cedcliente,"terceros","nit","terid");
	   $cargo        = $clase->BDLockup($cedcliente,"terceros","nit","cargo");	   
	   $nitempresa   = $clase->SeleccionarUno("SELECT T.nit FROM terceros T INNER JOIN contratos C ON (C.terid = T.terid) WHERE C.contratoid=".$contratoid);	   
	   $vendedorpred = $clase->BDLockup($_SESSION['U_VENDEDORPRED'],"terceros","codigo","terid");
	   $creador      = $_SESSION['USERNAME'];	 
	   $observacion  = "PREST SERV AUTOGENERADA POR EL SISTEMA";
       $bodegaid     = $clase->BDLockup($_SESSION['U_BODEGAPRED'],"bodegas","codigo","bodegaid");
	   
	   $cantidad = 1;
	   $porciva  = 0;

	   // Actualizo el Consecutivo de la Tabla Prefijos
	   $numero2 = str_pad(($numero + 1),4,"0",STR_PAD_LEFT);
       $clase->EjecutarSQL("UPDATE prefijo SET consecutivo = '".$numero2."' WHERE tipodoc='".$tipodocumento."' AND prefijo='00'");
	   
	   //Inserto el Encabezado de la Prestacion de Servicio y/o Factura
       $vsql = "INSERT INTO documentos(tipodoc,prefijo,numero,fechadoc,fecasentado,terid1,terid2,observacion,
	            base,iva,total,creador,momento,contratoid,cargo,nitempresa) 
	            values('".$tipodocumento."','00','".$numero2."',CURRENT_TIMESTAMP,CURRENT_TIMESTAMP,".$clienteid.",".$vendedorpred.",'".
				$observacion."',0,0,0,'".$creador."',CURRENT_TIMESTAMP,".$contratoid.",'".$cargo."','".$nitempresa."')";	  	  	    
	   $clase->EjecutarSQL($vsql);
       $Docuid = $clase->SeleccionarUno("SELECT Max(docuid) FROM documentos WHERE tipodoc='".$tipodocumento."'");
	   
	   
	   /// Inserto los detalles del Documento
	   for($j=0 ; $j<$i ; $j++)
	   {              
	      $vsql2 = "INSERT INTO dedocumentos(docuid,itemid,bodegaid,cantidad,valunitario,valdescuento,porciva,prorea) 
	                VALUES(".$Docuid.",".$examen[$j].",".$bodegaid.",".$cantidad.",".ereg_replace("[^A-Za-z0-9]","",$precio[$j]).",0,".$porciva.",".$prorea[$j].")";
		  $clase->EjecutarSQL($vsql2);
       }	    	
	
	  header("Location: facturapida.php?opcion=menups&docuid=".$Docuid);
    }
    else
    {
      $clase->Aviso(3,"Error al Seleccionar los procedimientos a Facturar");
	  header("Location: facturapida.php?cliente=".$cedcliente."&contrato=".$contratoid);	  
	}	
  }


  ////////////////////////////////////////////////////////////////////////////////  
  if($opcion == "menufv")
  {		
     $docuid = $_GET['docuid'];
 
	 $cont.='<table width="600">
	           <tr class="CabezoteTabla"> 
			     <td width="30"> </td>
				 <td width="560"> <a href="reportes/usuariosatendidosxfv.php?opcion=ver&amp;docuid='.$docuid.'"  target="_blank">
				                  Informe de Prestacion del Servicio (Usuarios Atendidos)</a></td>
				 <td width="8"> </td>
			   </tr>	 			   
			 </table> ';
	 echo $cont;
	 exit();		 
  }			 
 
  ////////////////////////////////////////////////////////////////////////////////  
  if($opcion == "menups")
  {		
     
	 $docuid = $_GET['docuid'];
	 $NumeroDocumento = $clase->SeleccionarUno('SELECT CONCAT(tipodoc," ",prefijo," ",numero) NUM FROM documentos WHERE docuid='.$docuid);
	 $cedcliente      = $clase->SeleccionarUno("SELECT nit FROM terceros T INNER JOIN documentos D ON (T.terid = D.terid1) WHERE D.docuid=".$docuid);
	 
	 $cont = $clase->Header("S","W");		
	 $cont.='<table width="100%">
	           <tr class="CabezoteTabla"> 
			     <td width="10"> </td>
			     <td width="37"> <img src="images/iconos/conceptomed.png" width="32" height="32" border="0"> </td>
				 <td width="400"> Crear Orden de Atencion <td>
				 <td width="8"> </td>
			   </tr>	 			   
			 </table> 
			 <br> <center>
			 <h3> Prestacion Generada Exitosamente </h3>
			 <b>Documento No. '.$NumeroDocumento.'</b> 
			 <br><br>
			 Paciente : '.$cedcliente.'  -  '.$clase->BDLockup($cedcliente,"terceros","nit","nombre").'
			 <br><br>
			 <table width="500">
         	   <tr class="CabezoteTabla"> 
			     <td width="130"> </td> 
				 <td> <a href="#" onclick="window.open(\'ventas.php?opcion=imprimirventa&amp;id='.$docuid.'\',\'ImpFV\',\'width=600,height=650,left=100,top=100\')">
				     <img src="images/iconpdf.png" border="0"> Visualizar Documento </a> </td> 
			   </tr>
               <tr class="CabezoteTabla"> 				 
  			     <td width="130"> </td>
				 <td> <a href="#" onclick="window.open(\'?opcion=ordenesmedicos&amp;id='.$docuid.'\',\'ImpOrdMed\',\'width=300,height=200,left=200,top=100\')">
				     <img src="images/iconoimprimir.png" border="0"> Imprimir Tickets Medico </a> </td> 
               </tr>
               <tr class="CabezoteTabla"> 				 
  			     <td width="130"> </td>
				 <td> <a href="#" onclick="window.open(\'impordenes.php?id='.$docuid.'\',\'ImpOrdMed\',\'width=600,height=650,left=200,top=100\')">
				     <img src="images/iconoimprimir.png" border="0"> Generar Ordenes Laboratorio - RX </a> </td> 
               </tr>
               <tr class="CabezoteTabla"> 				 
  			     <td width="130"> </td>
				 <td> <a href="?opcion=abrirhistoria&amp;id='.$docuid.'" rel="facebox">
				     <img src="images/iconoimprimir.png" border="0"> Abrir Historia al Paciente </a> </td> 
               </tr>
               <tr class="CabezoteTabla"> 				 
			     <td width="130"> </td>			     
				 <td> <a href="pacientes.php"> <img src="images/pacientes.png" border="0"> Ir a Pacientes </a> </td> 
			   </tr></table>';
  }   
  
  /////////////////////////////////////////  
  if($opcion == "")
  {
     $cliente    = $_GET['cliente'];		 
     $contratoid = $_GET['contrato'];	
     $codigocont = $clase->BDLockup($contratoid,"contratos","contratoid","numero");

     $terid   = $clase->BDLockup($cliente,'terceros','nit','terid');
	 $nombre  = $clase->BDLockup($cliente,'terceros','nit','nombre');
	 
	 $cont = $clase->Header("S","W");
	 	 
	 $cont.='<table width="100%">
	           <tr class="CabezoteTabla"> 
			     <td width="10"> </td>
			     <td width="37"> <img src="images/iconos/conceptomed.png" width="32" height="32" border="0"> </td>
				 <td width="400"> Crear Orden de Atencion para <b>'.$nombre.'</b><td>
				 <td width="8"> </td>
			   </tr>	 			   
			 </table> 
			 <form action="?opcion=generarps" method="POST" name="x">
			 <input type="hidden" name="contratoid" value="'.$contratoid.'">
			 <input type="hidden" name="cliente" value="'.$cliente.'">';	
	
	  $vsql = "SELECT I.itemid , I.descripcion producto , GP.descripcion grupo , DC.precio
	           FROM item I 
		       INNER JOIN productos P ON (I.itemid = P.itemid) 
			   INNER JOIN gruposprod GP ON (GP.gruposprodid = P.gruposprodid)
               INNER JOIN decontrato DC ON (DC.itemid = P.itemid)			   
			   WHERE DC.contratoid=".$contratoid."
		       ORDER BY GP.descripcion ASC , I.descripcion ASC";

	  $conex  = $clase->Conectar();
      $result = mysql_query($vsql,$conex);

	  $cont.='<table width="100%">
	           <tr class="TituloTabla"> 
			     <td width="10"> </td>
			     <td width="20"> </td>
				 <td width="240"> Procedimiento o Servicio </td>
				 <td width="150"> Especialidad </td>			
				 <td width="150"> Profesional que Realiza </td>							 
				 <td width="50" align="right"> Valor </td>			
				 <td width="25"> </td>				 				 
			   </tr>';	
    $i = 0;
    while($row = mysql_fetch_array($result)) 
	{
	     $i++;
		 if($i%2 == 0)
		   $cont.='<tr class="TablaDocsPar">';
		 else
		   $cont.='<tr class="TablaDocsImPar">';		 
		          
		 $cont.=' <td width="10"> </td>
				  <td width="20"> <input type="checkbox" name="'.$row['itemid'].'" id="'.$row['itemid'].'" value="'.$row['itemid'].'"> </td>
				  <td width="240"> '.$row['producto'].' </td>
				  <td width="150"> '.substr($row['grupo'],0,18).' </td>
				  <td width="150"> '.ComboProfesionales($row['itemid']).' </td>				  
				  <td width="50" align="right">';
         if($codigocont == "0000")				  
			$cont.='<input type="text" size="5" style="text-align:right" name="pre_'.$row['itemid'].'" value="'.number_format($row['precio']).'"> </td>';
		 else	
	        $cont.='<input type="text" size="5" style="text-align:right" name="pre_'.$row['itemid'].'" value="'.number_format($row['precio']).'" readonly> </td>';
		 
		 $cont.='<td width="10"> </td>				                   
				 </tr>';
	}
	$cont.='</table>
	        </br></br>
			<center> <input type="submit" value="Generar Prestacion de Servicio">
			</br></form>';
			
    mysql_free_result($result); 
    mysql_close($conex);			  
  }
  
  ////////////////////////////////
  echo $cont.$clase->PiePagina();  


  ///////////////////////////////////////////////////////////////////////
  ///////////////////////////////////////////////////////////////////////     
  function ComboProfesionales($id)
  {  
	   $clase = new Sistema();
	   $vsql="SELECT T.terid , T.nombre FROM terceros T INNER JOIN clasificater C ON (C.clasificaterid = T.clasificaterid)
	          WHERE C.codigo = '03' ORDER BY nombre ASC";
	   $conex = $clase->Conectar();
       $result = mysql_query($vsql,$conex); 
       $cont = '<SELECT name="prorea_'.$id.'" onChange="document.getElementById(\''.$id.'\').checked=true">      
	            <option value=""> </option>';
	     
	   while($row = mysql_fetch_array($result))
           $cont.='<option value="'.$row['terid'].'"> '.substr($row['nombre'],0,20).'</option>';		 		
	 	   
   	   $cont.='</SELECT>';	   
	   return($cont);
   }

  //////////////////////////////////////////////////////////////////// 
  function ImprimirTicketMedico($docuid,$prorea,$nombrearchivo)
  {
     $clase = new Sistema();
	 
	 /// Hago la Consulta de los datos de la Factura 		
	 $vsql = "SELECT D.tipodoc , D.prefijo , D.numero , D.fechadoc , PAC.nit nitcliente, PAC.nombre nomcliente , 
	          PRES.nombre nomvendedor, I.descripcion nomproducto , DD.prorea  
              FROM documentos D INNER JOIN dedocumentos DD ON (D.docuid = DD.docuid) 
			  INNER JOIN terceros PAC ON (PAC.terid = D.terid1) 
			  INNER JOIN terceros PRES ON (PRES.terid = DD.prorea) 
			  INNER JOIN item I ON (I.itemid = DD.itemid) 
			  INNER JOIN productos P ON (I.itemid = P.itemid) 
			  INNER JOIN gruposprod GP ON (GP.gruposprodid = P.gruposprodid) 
			  LEFT JOIN terceros MED ON (DD.prorea = MED.terid) 
			  WHERE D.docuid=".$docuid." AND DD.prorea = ".$prorea." AND(GP.codigo <> '002' AND GP.codigo <> '003')";

	 $conex  = $clase->Conectar();
     $result = mysql_query($vsql,$conex);
     $registros = mysql_num_rows($result);
	 
	 $cont = "SALUD EMPRESARIAL IPS SAS".Chr(13).Chr(10);
	 $cont.= "NIT. 900.443.363-1".Chr(13).Chr(10);
	 $cont.= "CLL 3A #3E-09 LA CEIBA".Chr(13).Chr(10);
	 $cont.= "Tels. 5751016 - 5893451".Chr(13).Chr(10).Chr(13).Chr(10);
	 	 
 	 while($row = mysql_fetch_array($result))
	 { 
 	   $prefijo = $row['prefijo'];
 	   $numero  = $row['numero'];
 	   $fecha   = substr($row['fechadoc'],8,2)."/".substr($row['fechadoc'],5,2)."/".substr($row['fechadoc'],0,4);
 	   $hora    = substr($row['fechadoc'],11,2).":".substr($row['fechadoc'],14,2); 	   
	   
   	   $cliente    = substr($row['nomcliente'],0,24); 
	   $nitcliente = substr($row['nitcliente'],0,24); 
   	   $vendedor   = substr($row['nomvendedor'],0,24); 
 	   
 	   $detalles.= str_pad(substr($row['nomproducto'],0,20),23," ",STR_PAD_RIGHT).Chr(13).Chr(10);
     } 
  
	 $cont.= 'ORDEN  DE  SERVICIO No. '.$prefijo.' '.$numero.Chr(13).Chr(10);
	 $cont.= 'Fecha : '.$fecha.'      Hora : '.$hora.Chr(13).Chr(10).Chr(13).Chr(10);
	 $cont.= "- - - - - - - - - - - - - - - - - - - -".Chr(13).Chr(10);	 	 	 
	 $cont.= 'PACIENTE : '.$cliente.Chr(13).Chr(10);
	 $cont.= 'C.C. : '.$nitcliente.Chr(13).Chr(10);
	 $cont.= "- - - - - - - - - - - - - - - - - - - -".Chr(13).Chr(10);	 	 	 
	 $cont.= 'PRESTADOR: '.$vendedor.Chr(13).Chr(10).Chr(13).Chr(10);      
     $cont.= '---------------------------------------'.Chr(13).Chr(10);          
     $cont.= 'DESCRIPCION DEL SERVICIO               '.Chr(13).Chr(10);
     $cont.= '---------------------------------------'.Chr(13).Chr(10);     
	 $cont.= $detalles.Chr(13).Chr(10);	 
	 $cont.= "- - - - - - - - - - - - - - - - - - - -".Chr(13).Chr(10).Chr(13).Chr(10);	 	 	 
	 
	 $cont.= 'Firma : ___________________'.Chr(13).Chr(10);     
	 $cont.= 'C.C.  : ___________________'.Chr(13).Chr(10);     
     $cont.= Chr(13).Chr(10).str_pad("www.swebcolombia.com",39," ",STR_PAD_LEFT).Chr(13).Chr(10);	 	 	 	   
     $cont.= Chr(13).Chr(10).Chr(13).Chr(10).Chr(13).Chr(10).Chr(13).Chr(10).Chr(13).Chr(10).Chr(13).Chr(10);
     $cont.= '.';     
	 
	 // Genero el Archivo para Enviarlo a Impresora
	 
	 if($registros>0)
	 {
	  $archivo= "print/".$nombrearchivo; // el nombre de tu archivo
      $fch= fopen($archivo, "w"); // Abres el archivo para escribir en él
      fwrite($fch, $cont); // Grabas
      fclose($fch); // Cierras el archivo
	 }
	 
  } // Fin de la Funcion
  
  ////////////////////////////////////////////////////////////////////////////
  function GenerarRSA($docuid,$gruposer)
  {
    $tipodocumento = "RSA";
	$prefijo = "00";
	$numero = $clase->SeleccionarUno("SELECT consecutivo FROM prefijo WHERE tipodoc='".$tipodocumento."' AND prefijo='".$prefijo."'");
 	
    $vsql = "SELECT * FROM documentos D INNER JOIN dedocumentos DD ON (D.docuid = DD.docuid) WHERE D.docuid=".$docuid;	
    $conex = $clase->Conectar();
    $result = mysql_query($vsql,$conex); 
    while($row = mysql_fetch_array($result))
	{
       $terid1 = $row['terid1'];
	   $terid2 = $row['prorrea'];
	   $observacion  = "ORDEN DE SERVICIO AUTOGENERADA POR EL SISTEMA";
       $bodegaid     = $clase->BDLockup($_SESSION['U_BODEGAPRED'],"bodegas","codigo","bodegaid");	   
	   $cantidad = 1;
	   $porciva  = 0;
	}   

	// Actualizo el Consecutivo de la Tabla Prefijos
	$numero2 = str_pad(($numero + 1),4,"0",STR_PAD_LEFT);
    $clase->EjecutarSQL("UPDATE prefijo SET consecutivo = '".$numero2."' WHERE tipodoc='".$tipodocumento."' AND prefijo='".$prefijo."'");
	   
    //Inserto el Encabezado de la Prestacion de Servicio y/o Factura
    $vsql = "INSERT INTO documentos(tipodoc,prefijo,numero,fechadoc,fecasentado,terid1,terid2,observacion,base,iva,total,creador,momento) 
	         values('".$tipodocumento."','".$prefijo."','".$numero2."',CURRENT_TIMESTAMP,CURRENT_TIMESTAMP,".$terid1.",".$terid2.",'".
		     $observacion."',0,0,0,'".$_SESSION['USERNAME']."',CURRENT_TIMESTAMP)";	  	  	    
    $clase->EjecutarSQL($vsql);
    $Docuid = $clase->SeleccionarUno("SELECT Max(docuid) FROM documentos WHERE tipodoc='".$tipodocumento."'");
  }  
   
?> 