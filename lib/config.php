<?PHP
  session_start(); 
  include("lib/Sistema.php");

  $clase = new Sistema();
  $clase->Nombredb = $_SESSION['DBNOMBRE']; 

  $dir = "db/copias/"; 
  $rutaCopias = "C:\\Appserv\\www\\uno\\sistema\\db\\copias\\";
   
  $avisos = ""; 
  $opcion = "";
  $opcion = $_GET["opcion"];


  ///////////////////////////////////////////////////////////////////////////////// 
  if($opcion == "descargar")
  {
    $db = $_GET["db"];
    $filename=basename($db); 
	$size=filesize($db); 
	header("Content-Type: application/octet-stream"); 
	header("Content-Disposition: attachment; filename=\"$db\"");  
	header("Content-Length: ".$size); 
	header("Content-Transfer-Encoding: binary"); 
	readfile($db); 
	header("Location: config.php?opcion=backup");
    exit(0);
  }

  ///////////////////////////////////////////////////////////////////////////////// 
  if($opcion == "borrarcopia2")
  {
    $db = $_POST["db"];
    unlink($dir.$db);    
    $clase->Aviso(1,"Archivo Eliminado Exitosamente");  			  
    header("Location: config.php?opcion=backup");
  }
  
  ///////////////////////////////////////////////////////////////////////////////// 
  if($opcion == "borrarcopia")
  {
    $db = $_GET["db"];
	$cont.='<center><h3> Confirma Eliminar el Archivo </h3>
            <form action="config.php?opcion=borrarcopia2" method="POST">
	        <input type="hidden" name="db" value="'.$db.'">
			<table width="180">
	         <tr> 
			  <td> <button type="submit" name="guardar" tabindex="4" id="continuar" class="Botonverde"> Si, Eliminar </button> </td>
			 </tr></table><br>'; 
	echo $cont;
	exit(0);		 
  }			 

  ///////////////////////////////////////////////////////////////////////////////// 
  if($opcion == "nuevobackup")
  {
    
	$nombrecopia = $rutaCopias.$_SESSION['DBNOMBRE']."_".date("d-m-Y")."_".date("h-i_a").".sql";
	$command = "C:\\Appserv\\mysql\\bin\\mysqldump.exe -uroot -p123 --routines=TRUE ".$_SESSION['DBNOMBRE']." > ".$nombrecopia;
    system($command);
    $cont.='<center><h3> Realizando Backup </h3>
	    	<table width="250">
	         <tr> 
			  <td> <img src="images/iconos/basedatos.png" width="32" height="32" border="0">
			       <b> <a href="config.php?opcion=backup"> Backup Realizado con Exito</a> </b> </td>
			 </tr></table><br>'; 
    $clase->Aviso(1,"Backup Realizado Exitosamente");  			  
	echo $cont;
	exit(0);	  
  }


  //////////////////////////////////////////////////////////////////////////////////  
  if($opcion == "backup")
  {
    $cont = Cabezote($clase);	
	$cont.= FuncionesEspeciales(5);


    $cont.='<table width="100%">
	           <tr class="CabezoteTabla"> 
			     <td width="10"> </td>
			     <td width="37"> <img src="images/iconos/basedatos.png" width="32" height="32" border="0"> </td>
				 <td width="400"> Backups de Datos <td>
				 <td width="24"> <a href="config.php?opcion=nuevobackup" rel="facebox"> <img src="images/icononuevo.png" border="0"> </a> </td>
				 <td width="300"> <a href="config.php?opcion=nuevobackup" rel="facebox"> Nuevo Backup a la Base de Datos </a> </td>
			   </tr></table>';
	
    $cont.='<table width="100%">
	           <tr class="TituloTabla"> 
			     <td width="10"> </td>
			     <td width="300"> Listado de Backups Guardados</td>
				 <td width="50">  </td>
				 <td width="50">  </td>			
				 <td width="50">  </td>					 
				 <td width="20"> </td>
			   </tr>';	

	$directorio=opendir($dir); 
    $i = 0;
    
    while ($archivo = readdir($directorio))
    {
       if(($archivo != ".")&&($archivo != ".."))
	   {
	     $i++;

         $peso_archivo = filesize($dir.$archivo);
         $tam = tamano_archivo($peso_archivo);
         
		 if($i%2 == 0)
	        $cont.='<tr class="TablaDocsPar">';
	     else
	        $cont.='<tr class="TablaDocsImPar">';		 
		          

    	 $cont.= '<td> </td>
	              <td> <b>'.$archivo.'</b> ( '.$tam.' ) </td>
			      <td> <a href="?opcion=descargar&db=db/copias/'.$archivo.'" target="_blank"><img src="images/descargar.png" border="0"> Descargar </a></td>
			      <td> <a href="?opcion=restaurar&db='.$archivo.'" rel="facebox"><img src="images/restaurardb.png" border="0"> Restaurar </a></td>			   
			      <td> <a href="?opcion=borrarcopia&db='.$archivo.'" rel="facebox"><img src="images/iconoborrar.png" border="0">Borrar</a> </td>			   			      
			      <td> </td>			   
			      </tr>';    
	   }		      
	}
	$cont.='</table>';
	closedir($directorio); 		   
  }
  
  ///////////////////////////////////////////////////////////////////////////////// 
  if($opcion == "guardar4")
  {
    /*
	$nombreemp   = strtoupper($_POST['nombreemp']);
	$monedalocal = strtoupper($_POST['monedalocal']);	
	$monedaext   = strtoupper($_POST['monedaext']);		

    GuardarConfig($clase,$nombreemp,"G_NOMBREEMP");
    GuardarConfig($clase,$monedalocal,"G_MONEDALOCAL");
    GuardarConfig($clase,$monedaext,"G_MONEDAEXT");

	$clase->Aviso(1,"Configuraciones Guardadas con Exito");  	
	header("Location: config.php?opcion=documentos");	
	*/
  }

  //////////////////////////////////////////////////////////////////////////////////  
  if($opcion == "informes")
  {
    $cont = Cabezote($clase);	
	$cont.= FuncionesEspeciales(4);
  }

  ///////////////////////////////////////////////////////////////////////////////// 
  if($opcion == "guardar3")
  {
  /*
    $nombreemp   = strtoupper($_POST['nombreemp']);
	$monedalocal = strtoupper($_POST['monedalocal']);	
	$monedaext   = strtoupper($_POST['monedaext']);		

    GuardarConfig($clase,$nombreemp,"G_NOMBREEMP");
    GuardarConfig($clase,$monedalocal,"G_MONEDALOCAL");
    GuardarConfig($clase,$monedaext,"G_MONEDAEXT");

	$clase->Aviso(1,"Configuraciones Guardadas con Exito");  	
	header("Location: config.php?opcion=documentos");	
	*/
  }

  //////////////////////////////////////////////////////////////////////////////////  
  if($opcion == "documentos")
  {
    $cont = Cabezote($clase);	
	$cont.= FuncionesEspeciales(3);
  }
 

  ///////////////////////////////////////////////////////////////////////////////// 
  if($opcion == "guardar2")
  {
    $cliente   = strtoupper($_POST['cliente']);
    $vendedor  = strtoupper($_POST['vendedor']);
	$bodega    = strtoupper($_POST['bodega']);	
	$sucursal  = strtoupper($_POST['sucursal']);		

    GuardarConfig($clase,$vendedor,"U_CLIENTEPRED");
    GuardarConfig($clase,$vendedor,"U_VENDEDORPRED");
    GuardarConfig($clase,$bodega,"U_BODEGAPRED");
    GuardarConfig($clase,$sucursal,"U_SUCURSALPRED");

	$clase->Aviso(1,"Configuraciones Guardadas con Exito");  	
	header("Location: config.php?opcion=usuario");	
  }

  //////////////////////////////////////////////////////////////////////////////////  
  if($opcion == "usuario")
  {
    $cont = Cabezote($clase);	
	$cont.= FuncionesEspeciales(2);
	
    $cont.='<br/> <br/>
	        <form action="?opcion=guardar2" method="POST" name="x">
	        <table width="730"> 
	         <tr height="350" valign="Top">
			   <td width="20"> &nbsp; </td>
			   <td width="330"> 
			    
   				 <fieldset style="width:300px;">
                   <legend> <b> Configuracion del Usuario </b> </legend>
                    <br/>
					Cliente Predeterminado : <br> 
					<input type="text" name="cliente" value="'.$clase->BDLockup("U_CLIENTEPRED","configuraciones","variab","contenido").'" size="25"/>  <br />
                    <br/>
					Vendedor Predeterminado : <br> 
					<input type="text" name="vendedor" value="'.$clase->BDLockup("U_VENDEDORPRED","configuraciones","variab","contenido").'" size="25"/>  <br />
				    <br/>
					Bodega Predeterminada : <br> 
					<input type="text" name="bodega" value="'.$clase->BDLockup("U_BODEGAPRED","configuraciones","variab","contenido").'" size="25"/>  <br />
					<br/>
					Sucursal de Ingreso : <br> 
					<input type="text" name="sucursal" value="'.$clase->BDLockup("U_SUCURSALPRED","configuraciones","variab","contenido").'" size="25"/>  <br />
					<br/>
					Tipo de Impresion : <br> 
					  <select name="tipoimp">  
					    <option value="POS"> Pos </option> 
					    <option value="CAR"> Carta </option> 					
					    <option value="MCA"> Media Carta </option> 											
					  </select> <br />
					
				 </fieldset>				 
				 
               </td>
			   <td width="330"> 

				 <fieldset style="width:300px;">
                   <legend> <b> Prefijos Predeterminados </b> </legend>
				   <br/>
                    <table>
					  <tr>  <td> Ventas : </td> <td> <input type="text" name="prefac" size="4"> </td> </tr>
                      <tr>  <td> Compras : </td> <td> <input type="text" name="prefac" size="4">  </td> </tr>
                      <tr>  <td> Remisiones Salida : </td> <td><input type="text" name="prefac" size="4">  </td> </tr>
                      <tr>  <td> Remisiones Entrada : </td> <td><input type="text" name="prefac" size="4">  </td> </tr>
                      <tr>  <td> Pedidos : </td> <td> <input type="text" name="prefac" size="4">  </td> </tr>
                      <tr>  <td> C x Cobrar : </td> <td> <input type="text" name="prefac" size="4">  </td> </tr>
                      <tr>  <td> C x Pagar : </td> <td> <input type="text" name="prefac" size="4">  </td> </tr>
				     </tr>
			    	</table>	 		
				 </fieldset>


			   </td>
			 </tr>    
			</table>
			
    		<center><table width="10%"> <tr> <td align="center">  
			<button type="submit" name="guardar" tabindex="4" id="continuar" class="Botonverde"> Guardar </button>  
			</td> </tr>	</table>
			</form>'; 

  }

  ///////////////////////////////////////////////////////////////////////////////// 
  if($opcion == "guardar1")
  {
    $nombreemp    = strtoupper($_POST['nombreemp']);
    $nitemp       = strtoupper($_POST['nitemp']);	
	$monedalocal  = strtoupper($_POST['monedalocal']);	
	$monedaext    = strtoupper($_POST['monedaext']);		
	$diaoperativo = strtoupper($_POST['diaoperativo']);			
	
	$diaopeFVE = strtoupper($_POST['diaopeFVE']);			
	$diaopeRSA = strtoupper($_POST['diaopeRSA']);				
	$diaopeFCO = strtoupper($_POST['diaopeFCO']);			
	$diaopeCCO = strtoupper($_POST['diaopeCCO']);				

    GuardarConfig($clase,$nombreemp,"G_NOMBREEMP");
    GuardarConfig($clase,$nitemp,"G_NITEMP");	
    GuardarConfig($clase,$monedalocal,"G_MONEDALOCAL");
    GuardarConfig($clase,$monedaext,"G_MONEDAEXT");
    GuardarConfig($clase,$diaoperativo,"G_FECHADIAOPERATIVO");

    GuardarConfig($clase,$diaopeFVE,"G_diaopeFVE");    
    GuardarConfig($clase,$diaopeRSA,"G_diaopeRSA");    
    GuardarConfig($clase,$diaopeFCO,"G_diaopeFCO");  
    GuardarConfig($clase,$diaopeCCO,"G_diaopeCCO");  	    

	$clase->Aviso(1,"Configuraciones Guardadas con Exito");  	
	header("Location: config.php?opcion=sistema");	
  }
  
  //////////////////////////////////////////////////////////////////////////////////  
  if(($opcion == "")||($opcion == "sistema"))
  {
    $cont = Cabezote($clase);	
	$cont.= FuncionesEspeciales(1);

    $diaopeFVE = $clase->BDLockup("G_diaopeFVE","configuraciones","variab","contenido");
    $diaopeRSA = $clase->BDLockup("G_diaopeRSA","configuraciones","variab","contenido");
    $diaopeFCO = $clase->BDLockup("G_diaopeFCO","configuraciones","variab","contenido");
    $diaopeCCO = $clase->BDLockup("G_diaopeCCO","configuraciones","variab","contenido");		    
    
	$cont.='<br/> <br/>
	        <form action="?opcion=guardar1" method="POST" name="x">
	        <table width="730"> 
	         <tr height="350" valign="Top">
			   <td width="20"> &nbsp; </td>
			   <td width="330"> 
			    
   				 <fieldset style="width:300px;">
                   <legend> <b> Configuracion General </b> </legend>
                    <br/>
					Razon Social de la Empresa : <br> 
					<input type="text" name="nombreemp" value="'.$clase->BDLockup("G_NOMBREEMP","configuraciones","variab","contenido").'" size="40"/>  <br />
					NIT de la Empresa : <br> 
					<input type="text" name="nitemp" value="'.$clase->BDLockup("G_NITEMP","configuraciones","variab","contenido").'" size="20"/>  <br />
					Moneda Local : <br> 
					<input type="text" name="monedalocal" value="'.$clase->BDLockup("G_MONEDALOCAL","configuraciones","variab","contenido").'" size="20"/> <br />
					Moneda Extranjera : <br> 
					<input type="text" name="monedaext" value="'.$clase->BDLockup("G_MONEDAEXT","configuraciones","variab","contenido").'" size="20"/> <br />
				 </fieldset>				 
				 
               </td>
			   <td width="330"> 
			   
				 <fieldset style="width:300px;">
                   <legend> <b> Dia Operativo </b> </legend>
                    Dia Operativo : <input type="text" name="diaoperativo" value="'.$clase->BDLockup("G_FECHADIAOPERATIVO","configuraciones","variab","contenido").'" size="8" maxlength="10"> <br>
					<input type="checkbox" name="diaopeFVE" value="checked" '.$diaopeFVE.'> Dia Operativo para Ventas <br />
                    <input type="checkbox" name="diaopeRSA" value="checked" '.$diaopeRSA.'> Dia Operativo para Remisiones <br />
                    <input type="checkbox" name="diaopeFCO" value="checked" '.$diaopeFCO.'> Dia Operativo para Compras <br />
                    <input type="checkbox" name="diaopeCCO" value="checked" '.$diaopeCCO.'> Dia Operativo para Cuenta por Cobrar <br />															
                    <input type="checkbox" name="diaopeCPA" value="checked"> Dia Operativo para Cuenta por Pagar <br />																				
                    <input type="checkbox" name="diaopeRCA" value="checked"> Dia Operativo para Recibos de Caja <br />
                    <input type="checkbox" name="diaopeCEG" value="checked"> Dia Operativo para Comprobantes de Egreso <br />
				 </fieldset>
		   
			   </td>
			 </tr>    
			</table>
			
    		<center><table width="10%"> <tr> <td align="center">  
			<button type="submit" name="guardar" tabindex="4" id="continuar" class="Botonverde"> Guardar </button>  
			</td> </tr>	</table>
			</form>'; 
  }
  
  ////////////////////////////////
  echo $cont.$clase->PiePagina();  
  
  ///////////////////////////////////////////////////////////////////////    
  function FuncionesEspeciales($item)
  {
  	 $cont.='<table width="100%">	
	          <tr class="BarraDocumentos">';
     if($item == 1)			  
	    $cont.='<td width="20%" class="BarraDocumentosSel" align="center"> <img src="images/funciones.png"> Sistema </td>';
     else
	    $cont.='<td width="20%" align="center"> <a href="?opcion=sistema"> <img src="images/funciones.png"> Sistema </a> </td>';

     if($item == 2)			  
	    $cont.='<td width="20%" class="BarraDocumentosSel" align="center"> <img src="images/funciones.png"> Usuario </td>';
     else
	    $cont.='<td width="20%" align="center"> <a href="?opcion=usuario"> <img src="images/funciones.png"> Usuario </a> </td>';

     if($item == 3)			  
	    $cont.='<td width="20%" class="BarraDocumentosSel" align="center"> <img src="images/funciones.png"> Documentos </td>';
     else
	    $cont.='<td width="20%" align="center"> <a href="?opcion=documentos"> <img src="images/funciones.png"> Documentos </a> </td>';

     if($item == 4)			  
	    $cont.='<td width="20%" class="BarraDocumentosSel" align="center"> <img src="images/funciones.png"> Auditor de Sucesos </td>';
     else
	    $cont.='<td width="20%" align="center"> <a href="?opcion=informes"> <img src="images/funciones.png"> Auditor de Sucesos </a> </td>';

     if($item == 5)			  
	    $cont.='<td width="20%" class="BarraDocumentosSel" align="center"> <img src="images/basedatos.png"> Backups de Datos </td>';
     else
	    $cont.='<td width="20%" align="center"> <a href="?opcion=backup"> <img src="images/basedatos.png"> Backups de Datos </a> </td>';

	 $cont.='</tr> </table> ';	
     return($cont);			 
  }
  
  ///////////////////////////////////////////////////////////////////////
  function Cabezote($clase)
  {
     $cont = $clase->Header("S","W"); ;  	 
	 $cont.='<table width="100%">
	           <tr class="CabezoteTabla"> 
			     <td width="10"> </td>
			     <td width="37"> <img src="images/iconos/configuracion.png" width="32" height="32" border="0"> </td>
				 <td width="400"> Configuracion del Sistema <td>
				 <td width="300">  </td>
			   </tr>	 			   
			 </table> ';	
     return($cont);
  }
  
  ///////////////////////////////////////////////////////////////////////    
  function GuardarConfig($clase,$valor,$variable)
  {
 	$vsql = "UPDATE configuraciones SET contenido = '".$valor."' WHERE VARIAB = '".$variable."'";    
	$clase->EjecutarSQL($vsql);
	$_SESSION[$variable] = $valor;
  }
  

  ///////////////////////////////////////////////////////////////////////      
  function tamano_archivo($peso , $decimales = 2 ) {
    $claseZ = array(" Bytes", " KB", " MB", " GB", " TB");    
    return round($peso/pow(1024,($i = floor(log($peso, 1024)))),$decimales ).$claseZ[$i];
  }
  
?> 


