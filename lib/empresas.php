<?PHP
  session_start(); 
  include("lib/Sistema.php");

  $clase = new Sistema();
  $clase->Nombredb = $_SESSION['DBNOMBRE']; 
 
  $avisos = ""; 
  $opcion = "";
  $opcion = $_GET["opcion"];

  /////////////////////////////////////////////////////////////////////////  
  if($opcion == "guardareditado2")
  {
       $terid        = $_POST['terid'];

	   $regimen       = strtoupper($_POST['regimen']);			
       $grancon       = strtoupper($_POST['grancon']);				
       $agenteret     = strtoupper($_POST['agenteret']);				
       $reteiva       = strtoupper($_POST['reteiva']);					
       $reteica       = strtoupper($_POST['reteica']);						
       $retefte       = strtoupper($_POST['retefte']);						
       $ventacredito  = strtoupper($_POST['ventacredito']);					
       $cupocredito   = $_POST['cupocredito'];							

	   $vsql = "UPDATE terceros SET regimen = '".$regimen."' , grancon = '".$grancon."' , agenteret = '".$agenteret."' ,
		         reteiva = '".$reteiva."' , reteica = '".$reteica."' , retefte = '".$retefte."' ,
		         ventacredito = '".$ventacredito."' , cupocredito = ".$cupocredito.",
				 momento = CURRENT_TIMESTAMP WHERE terid=".$terid;
        
        $clase->EjecutarSQL($vsql);
  		$clase->Aviso(1,"Datos Tributarios de la Empresa modificados Exitosamente");  			  
	    header("Location: empresas.php");
  }	

  
  /////////////////////////////////////////////////////////////////////////  
  if(($opcion == "guardareditado")||($opcion == "guardarnuevo"))
  {
    $terid        = $_POST['terid'];

	$codigo        = strtoupper($_POST['codigo']);
	$nit           = strtoupper($_POST['nit']);	
    $nombre1       = strtoupper($_POST['nombre1']);
	$nombre2       = strtoupper($_POST['nombre2']);
	$apellido1     = strtoupper($_POST['apellido1']);
	$apellido2     = strtoupper($_POST['apellido2']);
	$nombre        = strtoupper($_POST['nombre']);	
    $direccion     = strtoupper($_POST['direccion']);		
    $ciudad        = trim($_POST['ciudad']);			
    $zona          = trim($_POST['zona']);				
	$telefono      = strtoupper($_POST['telefono']);
	$celular       = strtoupper($_POST['celular']);		
	$email         = strtolower($_POST['email']);    

	$bancoid       = $_POST['bancoid'];	
	$cuenta        = strtoupper($_POST['cuenta']);		
    $percontacto   = strtoupper($_POST['percontacto']);		
    $replegal      = strtoupper($_POST['replegal']);			
	
	// Valido que el grupo y la linea esten en la base de datos
	$ciudadid = $clase->BDLockup($ciudad,"ciudades","codigo","ciudadid");
    $zonaid   = $clase->BDLockup($zona,"zonater","codigo","zonaid"); 
    $clasificaterid = $clase->BDLockup("02","clasificater","codigo","clasificaterid"); 	

	$terid_este_codigo =  $clase->BDLockup($codigo,"terceros","codigo","terid"); 
	 	
	if($ciudadid == ""){
	  $clase->Aviso(2,"Ciudad Incorrecta. &nbsp;&nbsp; <a href=\"javascript:history.back(-1);\"> Intentar de Nuevo ? </a>");  		
      header("Location: empresas.php");
	  exit();
	}
    
	if($zonaid == ""){	
	  $clase->Aviso(2,"Zona del Tercero Incorrecta. &nbsp;&nbsp; <a href=\"javascript:history.back(-1);\"> Intentar de Nuevo ? </a>");  		
      header("Location: empresas.php");
	  exit();
	}	
	
	if($bancoid == "")
      $bancoid = 1;
	  
	/// Realizo la operacion necesaria
	if($opcion == "guardarnuevo")
	{
		$vsql = "INSERT INTO terceros(codigo,nit,nombre,direccion,ciudadid,telefono,celular,email,zonaid,clasificaterid,bancoid,
		         cuenta,percontacto,replegal,nombre1,nombre2,apellido1,apellido2,creador,momento)
		         values('".$codigo."','".$nit."','".$nombre."','".$direccion."',".$ciudadid.",'".$telefono."','".$celular."','".$email."',".
				 $zonaid.",".$clasificaterid.",".$bancoid.",'".$cuenta."','".$percontacto."','".$replegal."','".
				 $nombre1."','".$nombre2."','".$apellido1."','".$apellido2."','".$_SESSION['USERNAME']."',CURRENT_TIMESTAMP)";    
        
		$cant = $clase->EjecutarSQL($vsql);
	
		if($cant == 1)
    		$clase->Aviso(1,"Empresa creada Exitosamente");  	
		else
			$clase->Aviso(2,"Error al Crear el Registro &nbsp;&nbsp; <a href=\"javascript:history.back(-1);\"> Intentar de Nuevo ? </a>");  		
    }
	
	if($opcion == "guardareditado")
	{
        $vsql = "UPDATE terceros SET codigo = '".$codigo."' , nit = '".$nit."' , nombre = '".$nombre."' ,
		         direccion = '".$direccion."' , ciudadid = ".$ciudadid." , telefono = '".$telefono."' ,
		         celular = '".$celular."' , email = '".$email."' , zonaid = ".$zonaid." ,				 
		         clasificaterid = ".$clasificaterid." , bancoid = ".$bancoid." , cuenta ='".$cuenta."' ,
				 percontacto ='".$percontacto."' , replegal ='".$replegal."' , nombre1 ='".$nombre1."' ,
				 nombre2 ='".$nombre2."' , apellido1 ='".$apellido1."' , apellido2 ='".$apellido2."' ,
				 momento = CURRENT_TIMESTAMP WHERE terid=".$terid;

        $clase->EjecutarSQL($vsql);
		
  		$clase->Aviso(1,"Datos Generales de la Empresa modificados Exitosamente");  			  
    }	
	
	header("Location: empresas.php");
  }
  
  /////////////////////////////////////////////////////////////////////////  
  /////////////////////////////////////////////////////////////////////////    
  if($opcion == "nuevo")
  {
     $id = $_GET['id'];
	 $cont = $clase->Header("S","W"); ;  	 
	 $cont.='<table width="100%">
	           <tr class="CabezoteTabla"> 
			     <td width="10"> </td>
			     <td width="37"> <img src="images/iconos/terceros.png" width="32" height="32" border="0"> </td>
				 <td width="520"> Nueva Empresa Contratante <td>
				 <td>  <a href="empresas.php"> Listado de Terceros </a> </td>
    		     <td width="8"> </td>
			   </tr>	 			   
			 </table> ';	
	
	
	$cont.='<br><br><br><center>
            <script language="javascript">
			  function CopiarCodigoaNit()
			  {
			    if(document.x.nit.value == "")
   				  document.x.nit.value=document.x.codigo.value; 
			  }
			</script>
			<form action="?opcion=guardarnuevo" method="POST" name="x">
			<table width="700">
	        <tr> 
			  <td> <label class="Texto15"> Codigo : </label> </td>
			  <td> <input type="text" name="codigo" class="Texto15" size="15" maxlength="15" id="default"> 
			 </tr>
	         <tr> 
			  <td> <label class="Texto15"> CC o NIT : </label> </td>
			  <td> <input type="text" name="nit" class="Texto15" size="15" maxlength="15" tabindex="2" OnFocus="CopiarCodigoaNit();"> 
			 </tr>
	         <tr> 
			  <td> <label class="Texto15"> Nombre : </td>
			  <td> <input type="text" name="nombre" class="Texto15" maxlength="60" size="45" tabindex="3"> </td>
			 </tr>
	         <tr> 
			  <td> <label class="Texto15"> Direccion : </td>
			  <td> <input type="text" name="direccion" class="Texto15" maxlength="60" size="45" tabindex="7"> 
			 </tr>			
             <tr height="30"> 
			  <td> <label class="Texto15"> Ciudad : </td>
			  <td> 

<!-- ************************************************************************************************ -->
<!-- AJAX AUTOSUGGEST SCRIPT -->
<script type="text/javascript" src="lib/ajax_framework4.js"></script>
<style type="text/css">
#search-wrap4 input{font-size:13px; text-transform:Capitalize; background-color:#D6F0FE; border-style:groove;}
#res4{width:150px; border:solid 1px #DEDEDE; display:none;}
#res4 ul, #res4 li{padding:0; margin:0; border:0; list-style:none; background:#F6F6F6;}
#res4 li {border-top:solid 1px #DEDEDE; background:#CEEAF5;}
#res4 li a{display:block; padding:2px; text-decoration:none; color:#000000; font-weight:bold; font-size:10px; font-family:Verdana;}
#res4 li a small{display:block; text-decoration:none; color:#999999; font-weight:normal; font-size:9px;font-family:Verdana;}
#res4 li a:hover{background:#FFFFFF;}
#res4 ul {padding:4px;}
</style>
<div id="search-wrap4">
<input name="ciudad" id="search-q4" type="text" onkeyup="javascript:autosuggest4();" maxlength="12" size="15" autocomplete="off" tabindex="8"/>
<div id="res4"></div>
</div>
<!-- AJAX AUTOSUGGEST SCRIPT -->
<!-- ************************************************************************************************ -->
		  
			  </td> 
			 </tr>
             <tr height="30"> 
			  <td> <label class="Texto15"> Zona : </td>
			  <td> 

<!-- ************************************************************************************************ -->
<!-- AJAX AUTOSUGGEST SCRIPT -->
<script type="text/javascript" src="lib/ajax_framework5.js"></script>
<style type="text/css">
#search-wrap5 input{font-size:13px; text-transform:Capitalize; background-color:#D6F0FE; border-style:groove;}
#res5{width:150px; border:solid 1px #DEDEDE; display:none;}
#res5 ul, #res5 li{padding:0; margin:0; border:0; list-style:none; background:#F6F6F6;}
#res5 li {border-top:solid 1px #DEDEDE; background:#CEEAF5;}
#res5 li a{display:block; padding:2px; text-decoration:none; color:#000000; font-weight:bold; font-size:10px; font-family:Verdana;}
#res5 li a small{display:block; text-decoration:none; color:#999999; font-weight:normal; font-size:9px;font-family:Verdana;}
#res5 li a:hover{background:#FFFFFF;}
#res5 ul {padding:4px;}
</style>
<div id="search-wrap5">
<input name="zona" id="search-q5" type="text" onkeyup="javascript:autosuggest5();" maxlength="12" size="15" autocomplete="off" tabindex="9"/>
<div id="res5"></div>
</div>
<!-- AJAX AUTOSUGGEST SCRIPT -->
<!-- ************************************************************************************************ -->
		  
			  </td> 
			 </tr>			 			 
	         <tr> 
			  <td> <label class="Texto15"> Telefono : </label> </td>
			  <td> <input type="text" name="telefono" class="Texto15" size="25" maxlength="25" tabindex="10"> 
			 </tr>
	         <tr> 
			  <td> <label class="Texto15"> Movil : </label> </td>
			  <td> <input type="text" name="celular" class="Texto15" size="25" maxlength="25" tabindex="11"> 
			 </tr>
	         <tr> 
			  <td> <label class="Texto15"> E-mail: </label> </td>
			  <td> <input type="text" name="email" class="Texto15" size="25" maxlength="50" tabindex="12"> 
			 </tr>
	         <tr> 
			  <td> <label class="Texto15"> Cuenta Bancaria No. </label> </td>
			  <td> <input type="text" name="cuenta" class="Texto15" size="25" maxlength="50" tabindex="13"> 
			 </tr>
	         <tr> 
			  <td> <label class="Texto15"> Banco : </label> </td>
			  <td> '.$clase->CrearCombo("bancoid","bancos","descripcion","codigo","","S","descripcion").' </td> 
			 </tr>		 
			</table>			
			<br><br>			
			<table>
			   <tr>
    		    <td>  <button type="submit" name="guardar" tabindex="4" id="continuar" class="Botonverde"> Guardar </button>  </td>
				</form>
			  </tr>
			</table>';  
  }

  /////////////////////////////////////////  
  if($opcion == "infotri")
  {
     $id = $_GET['id'];
	 $cont = $clase->Header("S","W"); ;  	 
	 $cont.='<table width="100%">
	           <tr class="CabezoteTabla"> 
			     <td width="10"> </td>
			     <td width="37"> <img src="images/iconos/terceros.png" width="32" height="32" border="0"> </td>
				 <td width="510"> Empresas Contratantes <td>
				 <td>  <a href="empresas.php"> Listado de Empresas </a> </td>
			     <td width="27"> <a href="?opcion=eliminar&id='.$id.'"> <img src="images/iconoborrar.png" border="0"> </a> </td>				 				 				 
    		     <td width="8"> </td>
			   </tr>	 			   
			 </table> ';	
			 
	$cont.= FuncionesEspeciales(2);
			 
  	$vsql = "SELECT * FROM terceros WHERE terid=".$id;
    $conex  = $clase->Conectar();
    $result = mysql_query($vsql,$conex);
	if($row = mysql_fetch_array($result))
	{
	$cont.='<br><br><br><center>
            <form action="?opcion=guardareditado2" method="POST" name="x">
			<input type="hidden" name="terid" value="'.$id.'">
			<table width="600">
			 <tr height="40">
			  <td width="250"> <label class="Texto15"> Regimen : </label> </td>
			  <td> <label class="Texto15">';
			  
			  if($row['regimen'] == "SIMPLIFICADO")
                 $cont.='<input type="radio" name="regimen" value="SIMPLIFICADO" checked> Regimen Simplificado
				         <input type="radio" name="regimen" value="COMUN"> Regimen Comun';
		      else
                 $cont.='<input type="radio" name="regimen" value="SIMPLIFICADO"> Regimen Simplificado
				         <input type="radio" name="regimen" value="COMUN" checked> Regimen Comun';			  

			  $cont.='</td>	
			 </tr>
			 <tr height="40">
			  <td> <label class="Texto15"> Contribuyente : </label> </td>
			  <td> <label class="Texto15"> <input type="checkbox" name="grancon" value="checked" '.$row['grancon'].'>Gran Contribuyente  
			       <input type="checkbox" name="agenteret" value="checked" '.$row['agenteret'].'> Agente Retenedor </td>	
			 </tr>
			 <tr height="40">
			  <td> <label class="Texto15"> Agente Retenedor de : </label> </td>
			  <td> <label class="Texto15"> 
			          <input type="checkbox" name="reteiva" value="checked" '.$row['reteiva'].'> Rete IVA 
					  <input type="checkbox" name="reteica" value="checked" '.$row['reteica'].'> Rete ICA
					  <input type="checkbox" name="retefte" value="checked" '.$row['retefte'].'> Rete Fuente					  				  
			  </td>	
			 </tr>
			 <tr height="40">
			  <td> <label class="Texto15"> Venta a Credito : </td>
			  <td> <label class="Texto15"> 
			                    <input type="radio" name="credito" checked> Autorizado
								<input type="radio" name="credito"> Restringido </label> </td>	
			 </tr>
			 <tr height="40">
			  <td> <label class="Texto15"> Cupo de Credito : </td>
			  <td> <label class="Texto15"> Hasta <input type="text" size="5" name="cupocredito" value='.$row['cupocredito'].'> Pesos </label> </td>	
			 </tr>			 
	        </table>
			<br><br>			
			<table>
			   <tr>
    		    <td>  <button type="submit" name="guardar" tabindex="4" id="continuar" class="Botonverde"> Guardar </button>  </td>
				</form>
			  </tr>
			</table>';  
	}
}	


  /////////////////////////////////////////  
  if($opcion == "detalles")
  {
     $id = $_GET['id'];
	 $cont = $clase->Header("S","W"); ;  	 
	 $cont.='<table width="100%">
	           <tr class="CabezoteTabla"> 
			     <td width="10"> </td>
			     <td width="37"> <img src="images/iconos/terceros.png" width="32" height="32" border="0"> </td>
				 <td width="510"> Empresas Contratantes <td>
				 <td>  <a href="empresas.php"> Listado de Empresas </a> </td>
			     <td width="27"> <a href="?opcion=eliminar&id='.$id.'"> <img src="images/iconoborrar.png" border="0"> </a> </td>				 				 				 
    		     <td width="8"> </td>
			   </tr>	 			   
			 </table> ';	
			 
	$cont.= FuncionesEspeciales(1);
			 
  	$vsql = "SELECT * FROM terceros WHERE terid=".$id;
    $conex  = $clase->Conectar();
    $result = mysql_query($vsql,$conex);
	if($row = mysql_fetch_array($result))
	{
	$cont.='<br><br><br><center>
            <form action="?opcion=guardareditado" method="POST" name="x">
			<input type="hidden" name="terid" value="'.$id.'">
			<table width="700">
	        <tr> 
			  <td> <label class="Texto15"> Codigo : </label> </td>
			  <td> <input type="text" name="codigo" class="Texto15" size="15" maxlength="15" id="default" value="'.$row['codigo'].'"> 
			 </tr>
	         <tr> 
			  <td> <label class="Texto15"> CC o NIT : </label> </td>
			  <td> <input type="text" name="nit" class="Texto15" size="15" maxlength="15" tabindex="2" value="'.$row['nit'].'"> 
			 </tr>
	         <tr> 
			  <td> <label class="Texto15"> Nombre : </td>
			  <td> <input type="text" name="nombre" class="Texto15" maxlength="60" size="45" tabindex="3" value="'.$row['nombre'].'"> </td>
			 </tr>
	         <tr> 
			  <td> <label class="Texto15"> Direccion : </td>
			  <td> <input type="text" name="direccion" class="Texto15" maxlength="60" size="45" tabindex="4" value="'.$row['direccion'].'"> 
			 </tr>			
             <tr height="30"> 
			  <td> <label class="Texto15"> Ciudad : </td>
			  <td> 

<!-- ************************************************************************************************ -->
<!-- AJAX AUTOSUGGEST SCRIPT -->
<script type="text/javascript" src="lib/ajax_framework4.js"></script>
<style type="text/css">
#search-wrap4 input{font-size:13px; text-transform:Capitalize; background-color:#D6F0FE; border-style:groove;}
#res4{width:150px; border:solid 1px #DEDEDE; display:none;}
#res4 ul, #res4 li{padding:0; margin:0; border:0; list-style:none; background:#F6F6F6;}
#res4 li {border-top:solid 1px #DEDEDE; background:#CEEAF5;}
#res4 li a{display:block; padding:2px; text-decoration:none; color:#000000; font-weight:bold; font-size:10px; font-family:Verdana;}
#res4 li a small{display:block; text-decoration:none; color:#999999; font-weight:normal; font-size:9px;font-family:Verdana;}
#res4 li a:hover{background:#FFFFFF;}
#res4 ul {padding:4px;}
</style>
<div id="search-wrap4">
<input name="ciudad" id="search-q4" type="text" onkeyup="javascript:autosuggest4();" maxlength="12" size="15" autocomplete="off" tabindex="5" value="'.$clase->BDLockup($row['ciudadid'],"ciudades","ciudadid","codigo").'"/>
<div id="res4"></div>
</div>
<!-- AJAX AUTOSUGGEST SCRIPT -->
<!-- ************************************************************************************************ -->
		  
			  </td> 
			 </tr>
             <tr height="30"> 
			  <td> <label class="Texto15"> Zona : </td>
			  <td> 

<!-- ************************************************************************************************ -->
<!-- AJAX AUTOSUGGEST SCRIPT -->
<script type="text/javascript" src="lib/ajax_framework5.js"></script>
<style type="text/css">
#search-wrap5 input{font-size:13px; text-transform:Capitalize; background-color:#D6F0FE; border-style:groove;}
#res5{width:150px; border:solid 1px #DEDEDE; display:none;}
#res5 ul, #res5 li{padding:0; margin:0; border:0; list-style:none; background:#F6F6F6;}
#res5 li {border-top:solid 1px #DEDEDE; background:#CEEAF5;}
#res5 li a{display:block; padding:2px; text-decoration:none; color:#000000; font-weight:bold; font-size:10px; font-family:Verdana;}
#res5 li a small{display:block; text-decoration:none; color:#999999; font-weight:normal; font-size:9px;font-family:Verdana;}
#res5 li a:hover{background:#FFFFFF;}
#res5 ul {padding:4px;}
</style>
<div id="search-wrap5">
<input name="zona" id="search-q5" type="text" onkeyup="javascript:autosuggest5();" maxlength="12" size="15" autocomplete="off" tabindex="6" value="'.$clase->BDLockup($row['zonaid'],"zonater","zonaid","codigo").'"/>
<div id="res5"></div>
</div>
<!-- AJAX AUTOSUGGEST SCRIPT -->
<!-- ************************************************************************************************ -->
		  
			  </td> 
			 </tr>			 			 
	         <tr> 
			  <td> <label class="Texto15"> Telefono : </label> </td>
			  <td> <input type="text" name="telefono" class="Texto15" size="25" maxlength="25" tabindex="7" value="'.$row['telefono'].'"> 
			 </tr>
	         <tr> 
			  <td> <label class="Texto15"> Movil : </label> </td>
			  <td> <input type="text" name="celular" class="Texto15" size="25" maxlength="25" tabindex="8" value="'.$row['celular'].'"> 
			 </tr>
	         <tr> 
			  <td> <label class="Texto15"> E-mail: </label> </td>
			  <td> <input type="text" name="email" class="Texto15" size="25" maxlength="50" tabindex="9" value="'.$row['email'].'"> 
			 </tr>
	         <tr> 
			  <td> <label class="Texto15"> Cuenta Bancaria No. </label> </td>
			  <td> <input type="text" name="cuenta" class="Texto15" size="25" maxlength="50" tabindex="9" value="'.$row['cuenta'].'"> 
			 </tr>
	         <tr> 
			  <td> <label class="Texto15"> Banco : </label> </td>
			  <td> '.$clase->CrearCombo("bancoid","bancos","descripcion","codigo",$row['bancoid'],"S","descripcion").' </td> 
			 </tr>		 
	         <tr> 
			  <td> <label class="Texto15"> Persona de Contacto </label> </td>
			  <td> <input type="text" name="percontacto" class="Texto15" size="40" maxlength="50" tabindex="9" value="'.$row['percontacto'].'"> 
			 </tr>
	         <tr> 
			  <td> <label class="Texto15"> Representante Legal </label> </td>
			  <td> <input type="text" name="replegal" class="Texto15" size="40" maxlength="50" tabindex="9" value="'.$row['replegal'].'"> 
			 </tr>
			</table>
			
			<br><br>
			
			<table>
			   <tr>
    		    <td>  <button type="submit" name="guardar" tabindex="4" id="continuar" class="Botonverde"> Guardar </button>  </td>
				</form>
			  </tr>
			</table>';  //onclick="location.href=\'?opcion=eliminar&amp;id='.$row['ciudadid']	 	
    }
  }
  
  /////////////////////////////////////////  
  if($opcion == "eliminar")
  {
    $id = $_GET['id'];

    if($id == 1)
	  $clase->Aviso(3,"No se puede Eliminar este Tercero porque es un Tercero del Sistema");
	else
	{    			
       $asociadoadoc = $clase->SeleccionarUno("SELECT COUNT(*) FROM documentos WHERE terid1=".$id." OR terid2=".$id);
       if($asociadoadoc > 0)
	      $clase->Aviso(3,"No se puede Eliminar este Tercero porque esta Asociado a Documentos");  			
       else
       {
	     $vsql = "DELETE FROM terceros WHERE terid=".$id;
         $clase->EjecutarSQL($vsql);
		 $clase->Aviso(1,"Tercero eliminado Exitosamente");  			
	   }
	}       
	header("Location: empresas.php");
  }

  /////////////////////////////////////////  
  if($opcion == "masregistros")
  {
    $actual = $_SESSION["NUMREGISTROSXCONSULTA"];
	$ahora = $actual + 50;
	$_SESSION["NUMREGISTROSXCONSULTA"] = $ahora; 
	header("Location: empresas.php");
  }

  /////////////////////////////////////////  
  if($opcion == "encontrar")
  {
    $criterio = $_POST['criterio'];
    $vsql = "SELECT T.* FROM terceros T INNER JOIN clasificater CT ON (T.clasificaterid = CT.clasificaterid)
		     WHERE CT.codigo = '02' AND (T.codigo like '%".$criterio."%' OR T.nit like '%".$criterio."%' OR T.nombre like '%".$criterio."%') ORDER BY nombre ASC Limit 0,".$_SESSION["NUMREGISTROSXCONSULTA"];
    $_SESSION['SQL_EMPRESAS'] = $vsql;
	header("Location: empresas.php");
  }

  /////////////////////////////////////////  
  if($opcion == "nofiltro")
  {
    $criterio = $_POST['criterio'];
    $vsql = "SELECT T.* FROM terceros T INNER JOIN clasificater CT ON (T.clasificaterid = CT.clasificaterid)
	         WHERE CT.codigo = '02' ORDER BY nombre ASC limit 0,".$_SESSION["NUMREGISTROSXCONSULTA"];
	$_SESSION['SQL_EMPRESAS'] = "";
	header("Location: empresas.php");
  }

  /////////////////////////////////////////  
  if($opcion == "")
  {
     $cont = $clase->Header("S","W"); ;  	 
	 $cont.='<table width="100%">
	           <tr class="CabezoteTabla"> 
			     <td width="10"> </td>
			     <td width="37"> <img src="images/iconos/terceros.png" width="32" height="32" border="0"> </td>
				 <td width="400"> Empresas Contratantes <td>
				 <td width="27"> <a href="?opcion=nuevo"> <img src="images/icononuevo.png" border="0"> </a> </td>
				 <form action="?opcion=encontrar" method="POST" name="x">
				 <td> <input type="text" name="criterio" size="30" placeholder="Criterio a Buscar" tabindex="1" id="default"> </td>
				 <td> <input type="submit" value="Encontrar" class="boton"> </td> ';

	 if($_SESSION['SQL_EMPRESAS'] != "")
         $cont.='<td width="10"> <a href="?opcion=nofiltro"> <img src="images/nofiltro.png"> </a> </td>'; 

		$cont.=' </form>
    		     <td width="8"> </td>
			   </tr>	 			   
			 </table> ';	
	
	
    $vsql = $_SESSION['SQL_EMPRESAS'];
	if($vsql == "")
    	$vsql = "SELECT T.* FROM terceros T INNER JOIN clasificater CT ON (T.clasificaterid = CT.clasificaterid)
		         WHERE CT.codigo = '02' ORDER BY nombre ASC limit 0,".$_SESSION["NUMREGISTROSXCONSULTA"];

	$conex  = $clase->Conectar();
    $result = mysql_query($vsql,$conex);

	 $cont.='<table width="100%">
	           <tr class="TituloTabla"> 
			     <td width="10"> </td>
			     <td width="30">  Codigo </td>
			     <td width="30">  NIT  </td>				 
				 <td width="150"> Nombres y Apellidos </td>
				 <td width="35"> Correo Electr&oacute;nico </td>			
				 <td width="20"> </td>
				 <td width="20"> </td>				 
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
				  <td width="30"> '.$row['codigo'].' </td>
				  <td width="30"> '.$row['nit'].' </td>
				  <td width="150"> '.$row['nombre'].' </td>
				  <td width="35"> '.$row['email'].'</td>
				  <td width="20"> <a href="?opcion=estadisticas&amp;id='.$row['terid'].'" rel="facebox"> <img src="images/funciones.png" border="0"> </td>				  
                  <td width="20"> <a href="?opcion=detalles&amp;id='.$row['terid'].'"> <img src="images/seleccion.png" border="0"> </td>				  				  
				 </tr>';
	}
	$cont.='</table>
	        <table width="100%">
	           <tr class="PieTabla"> 
			     <td width="10"> </td>
			     <td width="100"> <a href="?opcion=masregistros"> Mas Registros </a> </td>
			     <td width="100"> </td>
				 <td width="100"> <a href="#arriba"> Arriba </a> </td>
			   </tr>
			</table>';
			
    mysql_free_result($result); 
    mysql_close($conex);			  
  }
  
  ////////////////////////////////
  echo $cont.$clase->PiePagina();  

  ///////////////////////////////////////////////////////////////////////
  ///////////////////////////////////////////////////////////////////////    
  function FuncionesEspeciales($item)
  {
     $id = $_GET['id'];
	 $clase = new Sistema();
	 $nombre = $clase->BDLockup($id,"terceros","terid","nombre");
	 
	 // Barra de Acciones Especiales
  	 $cont.='<table width="100%">	
	          <tr class="BarraDocumentos">';
     if($item == 1)			  
	    $cont.='<td width="25%" class="BarraDocumentosSel" align="center"> <img src="images/estadisticas.png">Datos Generales</td>';
	 else	
        $cont.='<td width="25%" align="center"> <a href="?opcion=detalles&id='.$id.'"><img src="images/estadisticas.png">Datos Generales</a> </td>';	 

     if($item == 2)			  
	    $cont.='<td width="25%" class="BarraDocumentosSel" align="center"> <img src="images/estadisticas.png">Informacion Tributaria </td>';
	 else	
        $cont.='<td width="25%" align="center"> <a href="?opcion=infotri&id='.$id.'"><img src="images/estadisticas.png">Informacion Tributaria</a> </td>';	 

	 $cont.='   <td width="5%"> </td>				 
	            <td width="45%"> <b>Empresa : </b>'.$nombre.'</td>				 
			  </tr>	 			   
			 </table>';	
     return($cont);			 
  }
    
?> 