<?PHP
  session_start(); 
  include("lib/Sistema.php");
  include("reportes/configreportes.php");  

  $clase = new Sistema();
  $clase->Nombredb = $_SESSION['DBNOMBRE']; 
 
  $avisos = ""; 
  $opcion = "";
  $opcion = $_GET["opcion"];
  
  /////////////////////////////////////////////////////////////////////////  
  /////////////////////////////////////////////////////////////////////////  
  if($opcion == "guardareditado")
  {
    $tareaid      = $_POST['tareaid'];
	$tipotarea    = $_POST['tipotarea'];
	$descripcion  = strtoupper($_POST['descripcion']);	
	$fecdesde     = $_POST['fecdesde'];
	$fechasta     = $_POST['fechasta'];	
	$hora         = $_POST['hora'];	
	$repeticion   = $_POST['repeticion'];	  

	if($opcion == "guardareditado")
	{
        $vsql = "UPDATE bodegas SET codigo = '".$codigo."' , descripcion = '".$descripcion."' 
	             WHERE bodegaid=".$bodegaid;
    
	    $clase->EjecutarSQL($vsql);
	
   		$clase->Aviso(1,"Bodega modificada Exitosamente");  			  
    }	
	
	header("Location: programador.php");
  }
  
  /////////////////////////////////////////////////////////////////////////  
  if($opcion == "nuevatarea")
  {
     $id = $_GET['id'];
	 $cont = $clase->Header("S","W");	 
	 $cont.='<script src="popcalendar.js" type="text/javascript"></script>
	          <table width="100%">
	           <tr class="CabezoteTabla"> 
			     <td width="10"> </td>
			     <td width="37"> <img src="images/iconos/programador.png" width="32" height="32" border="0"> </td>
				 <td width="550"> Nueva Tarea Programada <td>
				 <td>  <a href="programador.php"> Listado de Tareas </a> </td>
    		     <td width="8"> </td>
			   </tr>	 			   
			 </table> ';	
	
	$cont.='<center>     
	        <table width="650" border="0">
         	  <tr height="120">
                <td align="center"> <a href="?opcion=permanentes"> 
				                    <img src="images/iconos/tar_permanentes.png" border="0"> <br> Actividades Permanentes </a> </td>
                <td align="center"> <a href="?opcion=newbackup"> 
				                    <img src="images/iconos/tar_backup.png" border="0"> <br> Backup a la Base de Datos </a> </td>
                <td align="center"> <a href="?opcion=newpersonalmail"> 
				                    <img src="images/iconos/tar_correopersona.png" border="0"> <br> Correo a Personas </a> </td>
              </tr>
			  <tr>									
				<td align="center"> <a href="?opcion=newgroupmail"> 
				                    <img src="images/iconos/tar_correogrupo.png" border="0"> <br> Correo a Grupo de Personas </a> </td>
     			<td align="center">  
				                    <img src="images/iconos/tar_cumple.png" border="0"> <br> Felicitaciones de Cumpleaños  </td>
     			<td align="center"> 
				                    <img src="images/iconos/tar_recordapagos.png" border="0"> <br> Recordatorios de Pagos </td>
			  </tr>
			</table>';  
  } 


//////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////
   if($opcion == "newgroupmail2")
   {
       $tipotarea    = $_POST['tipotarea'];

	   $destinos     = $_POST['destinos'];
	   $nombre       = $clase->BDLockup($destino,'terceros','email','nombre');
	   $asunto       = $_POST['asunto'];
	   $mensaje      = $_POST['mensaje'];
	   	   	   	   
	   $descripcion  = strtoupper($_POST['descripcion']);	
	   $fecdesde     = $_POST['fecdesde'];
	   $fechasta     = $_POST['fechasta'];	
	   $hora         = $_POST['hora'];	
	   $repeticion   = $_POST['repeticion'];	
	
		// Sin Repeticiones
		if($repeticion == "no")
		{
			 $fechaevento = substr($fecdesde,6,4)."-".substr($fecdesde,3,2)."-".substr($fecdesde,0,2)." ".$hora;
			 $vsql = "INSERT INTO tareasprogramadas(tipotarea,descripcion,usucrea,momcrea) values('".$tipotarea."', 
	            '".$descripcion."','".$_SESSION['USERNAME']."','".$fechaevento."')";			

			 $cant = $clase->EjecutarSQL($vsql);					
			 
			 $vsql = "SELECT MAX(tareaid) FROM tareasprogramadas";			
			 $tareaid = $clase->SeleccionarUno($vsql);

			 $vsql = "INSERT INTO correoselectronicos(tareaid,denombre,demail,paranombre,paramail,asunto,mensaje) values(".$tareaid.",'1Uno.co Sistema Web',
			         'notificaciones.uno@gmail.com','".$nombre."','".$destinos."','".$asunto."','".$mensaje."')";
			 $tareaid = $clase->EjecutarSQL($vsql);	
		}
				
		// Repeticiones Diariamente
		if($repeticion == "dia")
		{
		   $fechatemp = $fecdesde;
		   $i=0;
		   while($fechatemp != $fechasta)
		   {     
             if($i > 0)
			  $fechatemp = sumaDia($fechatemp,1);
			 
			 $fechaevento = substr($fechatemp,6,4)."-".substr($fechatemp,3,2)."-".substr($fechatemp,0,2)." ".$hora;
			 $vsql = "INSERT INTO tareasprogramadas(tipotarea,descripcion,usucrea,momcrea) values('".$tipotarea."', 
	            '".$descripcion."','".$_SESSION['USERNAME']."','".$fechaevento."')";
			 $cant = $clase->EjecutarSQL($vsql);
			 
			 $vsql = "SELECT MAX(tareaid) FROM tareasprogramadas";			
			 $tareaid = $clase->SeleccionarUno($vsql);

			 $vsql = "INSERT INTO correoselectronicos(tareaid,denombre,demail,paranombre,paramail,asunto,mensaje) values(".$tareaid.",'1Uno.co Sistema Web',
			         'notificaciones.uno@gmail.com','".$nombre."','".$destino."','".$asunto."','".$mensaje."')";
			 $tareaid = $clase->EjecutarSQL($vsql);			 
			 			
			 $i++;
		   }
		}
		
		// Repeticiones Mensuales
		if($repeticion == "semana")
		{
		   $fechatemp = $fecdesde;
		   $i=0;
		   while($fechatemp != $fechasta)
		   {     
             if($i > 0)
			  $fechatemp = sumaDia($fechatemp,7);
			 
			 $fechaevento = substr($fechatemp,6,4)."-".substr($fechatemp,3,2)."-".substr($fechatemp,0,2)." ".$hora;
			 $vsql = "INSERT INTO tareasprogramadas(tipotarea,descripcion,usucrea,momcrea) values('".$tipotarea."', 
	            '".$descripcion."','".$_SESSION['USERNAME']."','".$fechaevento."')";
			 $cant = $clase->EjecutarSQL($vsql);			
			 
 			 $vsql = "SELECT MAX(tareaid) FROM tareasprogramadas";			
			 $tareaid = $clase->SeleccionarUno($vsql);

			 $vsql = "INSERT INTO correoselectronicos(tareaid,denombre,demail,paranombre,paramail,asunto,mensaje) values(".$tareaid.",'1Uno.co Sistema Web',
			         'notificaciones.uno@gmail.com','".$nombre."','".$destino."','".$asunto."','".$mensaje."')";
			 $tareaid = $clase->EjecutarSQL($vsql);			 

			 $i++;
		   }
		}   
			
	  if($cant == 1)
    	$clase->Aviso(1,"Backup a la Base de Datos programado Exitosamente");  	
	  else
		$clase->Aviso(2,"Error al Crear el Registro &nbsp;&nbsp; <a href=\"javascript:history.back(-1);\"> Intentar de Nuevo ? </a>");  		
	  
	  header("Location: programador.php");	
   }

  /////////////////////////////////////////////////////////////////////////  
  if($opcion == "newgroupmail")
  {
     $id = $_GET['id'];
	 $cont = $clase->Header("S","W");	 
	 $cont.='<script src="popcalendar.js" type="text/javascript"></script>
	          <table width="100%">
	           <tr class="CabezoteTabla"> 
			     <td width="10"> </td>
			     <td width="37"> <img src="images/iconos/programador.png" width="32" height="32" border="0"> </td>
				 <td width="520"> Programar Envio de Correo Masivo<td>
				 <td> <a href="programador.php?opcion=nuevatarea"> Nueva Tarea </a>  | <a href="programador.php"> Lista de Tareas </a> </td>
    		     <td width="8"> </td>
			   </tr>	 			   
			 </table> ';	
	
	$cont.='<br><center>
            <form action="?opcion=newpersonalmail2" method="POST" name="x">
			<input type="hidden" name="tipotarea" value="EMA">
			<h3> Programar Envio de Correo a Personas</h3>

			<br><center><b>Destinatarios del Correo</b> <br><br>
			<table width="700">
	         <tr height="30"> 
			  <td width="160"> <label class="Texto15"> Destinatarios : </td>
			  <td> <textarea name="destinos" class="Texto15" cols="49" rows="4"></textarea>  </td>
			 </tr>
	         <tr height="30"> 
			  <td width="160"> <label class="Texto15"> Grupo de Personas : </td>
			  <td> 
<!-- ************************************************************************************************ -->
<!-- AJAX AUTOSUGGEST SCRIPT -->
<script type="text/javascript" src="lib/ajax_framework6.js"></script>
<style type="text/css">
#search-wrap6 input{font-size:13px; text-transform:Capitalize; background-color:#D6F0FE; border-style:groove;}
#res6{width:250px; border:solid 1px #DEDEDE; display:none;}
#res6 ul, #res6 li{padding:0; margin:0; border:0; list-style:none; background:#F6F6F6;}
#res6 li {border-top:solid 1px #DEDEDE; background:#CEEAF5;}
#res6 li a{display:block; padding:2px; text-decoration:none; color:#000000; font-weight:bold; font-size:10px; font-family:Verdana;}
#res6 li a small{display:block; text-decoration:none; color:#999999; font-weight:normal; font-size:9px;font-family:Verdana;}
#res6 li a:hover{background:#FFFFFF;}
#res6 ul {padding:4px;}
</style>
<div id="search-wrap6">
<input name="grupo" id="search-q6" type="text" onkeyup="javascript:autosuggest6();" maxlength="12" size="35" autocomplete="off" tabindex="10"/>
<div id="res6"></div>
</div>
<!-- AJAX AUTOSUGGEST SCRIPT -->
<!-- ************************************************************************************************ -->
		  
			  </td>
			 </tr>
			</table>
			 
			<br><br><center><b>Datos del Correo Electronico</b> <br><br>
			<table width="700">			 
			 <tr height="30"> 
			  <td width="160"> <label class="Texto15"> Asunto del E-mail : </td>
			  <td> <input type="text" name="asunto" class="Texto15"  maxlength="50" size="48"> </td>
			 </tr>
	         <tr height="30" valign="top"> 
			  <td width="150"> <br><label class="Texto15"> Mensaje de Correo : </label> <br> <a href=""> Ver Tags Utiles</a> </td>
			  <td> <textarea name="mensaje" class="Texto15" cols="49" rows="4"></textarea> </td>
			 </tr>
            </table>
			
			<br><br>
			
			<center><b>Datos de la Tarea Programada</b> <br> <br>
			<table width="600">
	         <tr height="30"> 
			  <td> <label class="Texto15"> Descripcion : </td>
			  <td> <input type="text" name="descripcion" class="Texto15"  maxlength="48" size="35"> </td>
			 </tr>
	         <tr height="30"> 
			  <td> <label class="Texto15"> Fecha Desde : </td>
			  <td> <input type="text" name="fecdesde" class="Texto15" size="10" value="'.date("d/m/Y").'" id="fecdesde" onClick="popUpCalendar(this, x.fecdesde,\'dd/mm/yyyy\');">  
			       <img src="images/calendario.png" border="0"> </td>
			 </tr>
	         <tr height="30"> 
			  <td> <label class="Texto15"> Fecha Hasta : </td>
			  <td> <input type="text" name="fechasta" class="Texto15" size="10" value="'.date("d/m/Y").'" id="fechasta" onClick="popUpCalendar(this, x.fechasta,\'dd/mm/yyyy\');">  
			       <img src="images/calendario.png" border="0"> </td>
			 </tr>
	         <tr height="30"> 
			  <td> <label class="Texto15"> Hora : </td>
			  <td> <input type="text" name="hora" class="Texto15" value="'.date("h:i:s").'" maxlength="10" size="10">  </td>
			 </tr>
			 </tr>
	         <tr height="30"> 
			  <td> <label class="Texto15"> Repetir : </td>
			  <td> 
			         <input type="radio" name="repeticion" value="no" checked> No Repetir  
			         <input type="radio" name="repeticion" value="dia"> Diariamente  					 
			         <input type="radio" name="repeticion" value="semana"> Semanalmente
			         <input type="radio" name="repeticion" value="mes"> Mensualmente					   		 
			  </td>
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

//////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////
   if($opcion == "newpersonalmail2")
   {
       $tipotarea    = $_POST['tipotarea'];

	   $destino      = $_POST['destino'];
	   $nombre       = $clase->BDLockup($destino,'terceros','email','nombre');
	   $asunto       = $_POST['asunto'];
	   $mensaje      = $_POST['mensaje'];
	   	   	   	   
	   $descripcion  = strtoupper($_POST['descripcion']);	
	   $fecdesde     = $_POST['fecdesde'];
	   $fechasta     = $_POST['fechasta'];	
	   $hora         = $_POST['hora'];	
	   $repeticion   = $_POST['repeticion'];	
	
		// Sin Repeticiones
		if($repeticion == "no")
		{
			 $fechaevento = substr($fecdesde,6,4)."-".substr($fecdesde,3,2)."-".substr($fecdesde,0,2)." ".$hora;
			 $vsql = "INSERT INTO tareasprogramadas(tipotarea,descripcion,usucrea,momcrea) values('".$tipotarea."', 
	            '".$descripcion."','".$_SESSION['USERNAME']."','".$fechaevento."')";			
			echo $vsql;
			 $cant = $clase->EjecutarSQL($vsql);					
			 
			 $vsql = "SELECT MAX(tareaid) FROM tareasprogramadas";			
			 $tareaid = $clase->SeleccionarUno($vsql);

			 $vsql = "INSERT INTO correoselectronicos(tareaid,denombre,demail,paranombre,paramail,asunto,mensaje) values(".$tareaid.",'1Uno.co Sistema Web',
			         'notificaciones.uno@gmail.com','".$nombre."','".$destino."','".$asunto."','".$mensaje."')";
			 $tareaid = $clase->EjecutarSQL($vsql);	
		}
				
		// Repeticiones Diariamente
		if($repeticion == "dia")
		{
		   $fechatemp = $fecdesde;
		   $i=0;
		   while($fechatemp != $fechasta)
		   {     
             if($i > 0)
			  $fechatemp = sumaDia($fechatemp,1);
			 
			 $fechaevento = substr($fechatemp,6,4)."-".substr($fechatemp,3,2)."-".substr($fechatemp,0,2)." ".$hora;
			 $vsql = "INSERT INTO tareasprogramadas(tipotarea,descripcion,usucrea,momcrea) values('".$tipotarea."', 
	            '".$descripcion."','".$_SESSION['USERNAME']."','".$fechaevento."')";
			 $cant = $clase->EjecutarSQL($vsql);
			 
			 $vsql = "SELECT MAX(tareaid) FROM tareasprogramadas";			
			 $tareaid = $clase->SeleccionarUno($vsql);

			 $vsql = "INSERT INTO correoselectronicos(tareaid,denombre,demail,paranombre,paramail,asunto,mensaje) values(".$tareaid.",'1Uno.co Sistema Web',
			         'notificaciones.uno@gmail.com','".$nombre."','".$destino."','".$asunto."','".$mensaje."')";
			 $tareaid = $clase->EjecutarSQL($vsql);			 
			 			
			 $i++;
		   }
		}
		
		// Repeticiones Mensuales
		if($repeticion == "semana")
		{
		   $fechatemp = $fecdesde;
		   $i=0;
		   while($fechatemp != $fechasta)
		   {     
             if($i > 0)
			  $fechatemp = sumaDia($fechatemp,7);
			 
			 $fechaevento = substr($fechatemp,6,4)."-".substr($fechatemp,3,2)."-".substr($fechatemp,0,2)." ".$hora;
			 $vsql = "INSERT INTO tareasprogramadas(tipotarea,descripcion,usucrea,momcrea) values('".$tipotarea."', 
	            '".$descripcion."','".$_SESSION['USERNAME']."','".$fechaevento."')";
			 $cant = $clase->EjecutarSQL($vsql);			
			 
 			 $vsql = "SELECT MAX(tareaid) FROM tareasprogramadas";			
			 $tareaid = $clase->SeleccionarUno($vsql);

			 $vsql = "INSERT INTO correoselectronicos(tareaid,denombre,demail,paranombre,paramail,asunto,mensaje) values(".$tareaid.",'1Uno.co Sistema Web',
			         'notificaciones.uno@gmail.com','".$nombre."','".$destino."','".$asunto."','".$mensaje."')";
			 $tareaid = $clase->EjecutarSQL($vsql);			 

			 $i++;
		   }
		}   
			
	  if($cant == 1)
    	$clase->Aviso(1,"Backup a la Base de Datos programado Exitosamente");  	
	  else
		$clase->Aviso(2,"Error al Crear el Registro &nbsp;&nbsp; <a href=\"javascript:history.back(-1);\"> Intentar de Nuevo ? </a>");  		
	  
	  header("Location: programador.php");	
   }

  /////////////////////////////////////////////////////////////////////////  
  if($opcion == "newpersonalmail")
  {
     $id = $_GET['id'];
	 $cont = $clase->Header("S","W");	 
	 $cont.='<script src="popcalendar.js" type="text/javascript"></script>
	          <table width="100%">
	           <tr class="CabezoteTabla"> 
			     <td width="10"> </td>
			     <td width="37"> <img src="images/iconos/programador.png" width="32" height="32" border="0"> </td>
				 <td width="520"> Programar Envio de Correo <td>
				 <td> <a href="programador.php?opcion=nuevatarea"> Nueva Tarea </a>  | <a href="programador.php"> Lista de Tareas </a> </td>
    		     <td width="8"> </td>
			   </tr>	 			   
			 </table> ';	
	
	$cont.='<br><center>
            <form action="?opcion=newpersonalmail2" method="POST" name="x">
			<input type="hidden" name="tipotarea" value="EMA">
			<h3> Programar Envio de Correo a Personas</h3>

			<br><center><b>Datos del Correo Electronico</b> <br><br>
			<table width="700">
	         <tr height="30"> 
			  <td width="150"> <label class="Texto15"> Destinatario : </td>
			  <td> 
			  
			  <!-- ************************************************************************************************ -->
<!-- AJAX AUTOSUGGEST SCRIPT -->
<script type="text/javascript" src="lib/ajax_framework9.js"></script>
<style type="text/css">
#search-wrap9 input{font-size:13px; text-transform:lowercase; background-color:#D6F0FE; border-style:groove;}
#res9{width:250px; border:solid 1px #DEDEDE; display:none;}
#res9 ul, #res9 li{padding:0; margin:0; border:0; list-style:none; background:#F6F6F6;}
#res9 li {border-top:solid 1px #DEDEDE; background:#CEEAF5;}
#res9 li a{display:block; padding:2px; text-decoration:none; color:#000000; font-weight:bold; font-size:10px; font-family:Verdana;}
#res9 li a small{display:block; text-decoration:none; color:#999999; font-weight:normal; font-size:9px;font-family:Verdana;}
#res9 li a:hover{background:#FFFFFF;}
#res9 ul {padding:4px;}
</style>
<div id="search-wrap9">
<input name="destino" id="search-q9" type="text" onkeyup="javascript:autosuggest9();" maxlength="12" size="35" autocomplete="off" tabindex="5"/>
<div id="res9"></div>
</div>
<!-- AJAX AUTOSUGGEST SCRIPT -->
<!-- ************************************************************************************************ -->

			  </td>
			 </tr>
	         <tr height="30"> 
			  <td width="160"> <label class="Texto15"> Asunto del E-mail : </td>
			  <td> <input type="text" name="asunto" class="Texto15"  maxlength="50" size="48"> </td>
			 </tr>
	         <tr height="30" valign="top"> 
			  <td width="150"> <br><label class="Texto15"> Mensaje de Correo : </label> <br> <a href=""> Ver Tags Utiles</a> </td>
			  <td> <textarea name="mensaje" class="Texto15" cols="49" rows="4"></textarea> </td>
			 </tr>
            </table>
			
			<br><br>
			
			<center><b>Datos de la Tarea Programada</b> <br> <br>
			<table width="600">
	         <tr height="30"> 
			  <td> <label class="Texto15"> Descripcion : </td>
			  <td> <input type="text" name="descripcion" class="Texto15"  maxlength="48" size="35"> </td>
			 </tr>
	         <tr height="30"> 
			  <td> <label class="Texto15"> Fecha Desde : </td>
			  <td> <input type="text" name="fecdesde" class="Texto15" size="10" value="'.date("d/m/Y").'" id="fecdesde" onClick="popUpCalendar(this, x.fecdesde,\'dd/mm/yyyy\');">  
			       <img src="images/calendario.png" border="0"> </td>
			 </tr>
	         <tr height="30"> 
			  <td> <label class="Texto15"> Fecha Hasta : </td>
			  <td> <input type="text" name="fechasta" class="Texto15" size="10" value="'.date("d/m/Y").'" id="fechasta" onClick="popUpCalendar(this, x.fechasta,\'dd/mm/yyyy\');">  
			       <img src="images/calendario.png" border="0"> </td>
			 </tr>
	         <tr height="30"> 
			  <td> <label class="Texto15"> Hora : </td>
			  <td> <input type="text" name="hora" class="Texto15" value="'.date("h:i:s").'" maxlength="10" size="10">  </td>
			 </tr>
			 </tr>
	         <tr height="30"> 
			  <td> <label class="Texto15"> Repetir : </td>
			  <td> 
			         <input type="radio" name="repeticion" value="no" checked> No Repetir  
			         <input type="radio" name="repeticion" value="dia"> Diariamente  					 
			         <input type="radio" name="repeticion" value="semana"> Semanalmente
			         <input type="radio" name="repeticion" value="mes"> Mensualmente					   		 
			  </td>
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


//////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////
   if($opcion == "newbackup2")
   {
       $tipotarea    = $_POST['tipotarea'];
	   $descripcion  = strtoupper($_POST['descripcion']);	
	   $fecdesde     = $_POST['fecdesde'];
	   $fechasta     = $_POST['fechasta'];	
	   $hora         = $_POST['hora'];	
	   $repeticion   = $_POST['repeticion'];	
	
		// Sin Repeticiones
		if($repeticion == "no")
		{
			 $fechaevento = substr($fecdesde,6,4)."-".substr($fecdesde,3,2)."-".substr($fecdesde,0,2)." ".$hora;
			 $vsql = "INSERT INTO tareasprogramadas(tipotarea,descripcion,usucrea,momcrea) values('".$tipotarea."', 
	            '".$descripcion."','".$_SESSION['USERNAME']."','".$fechaevento."')";
			
			 $cant = $clase->EjecutarSQL($vsql);					
		}
				
		// Repeticiones Diariamente
		if($repeticion == "dia")
		{
		   $fechatemp = $fecdesde;
		   $i=0;
		   while($fechatemp != $fechasta)
		   {     
             if($i > 0)
			  $fechatemp = sumaDia($fechatemp,1);
			 
			 $fechaevento = substr($fechatemp,6,4)."-".substr($fechatemp,3,2)."-".substr($fechatemp,0,2)." ".$hora;
			 $vsql = "INSERT INTO tareasprogramadas(tipotarea,descripcion,usucrea,momcrea) values('".$tipotarea."', 
	            '".$descripcion."','".$_SESSION['USERNAME']."','".$fechaevento."')";
			
			 $cant = $clase->EjecutarSQL($vsql);			
			 $i++;
		   }
		}
		
		// Repeticiones Mensuales
		if($repeticion == "semana")
		{
		   $fechatemp = $fecdesde;
		   $i=0;
		   while($fechatemp != $fechasta)
		   {     
             if($i > 0)
			  $fechatemp = sumaDia($fechatemp,7);
			 
			 $fechaevento = substr($fechatemp,6,4)."-".substr($fechatemp,3,2)."-".substr($fechatemp,0,2)." ".$hora;
			 $vsql = "INSERT INTO tareasprogramadas(tipotarea,descripcion,usucrea,momcrea) values('".$tipotarea."', 
	            '".$descripcion."','".$_SESSION['USERNAME']."','".$fechaevento."')";
			 $cant = $clase->EjecutarSQL($vsql);			
			 $i++;
		   }
		}   
			
	  if($cant == 1)
    	$clase->Aviso(1,"Backup a la Base de Datos programado Exitosamente");  	
	  else
		$clase->Aviso(2,"Error al Crear el Registro &nbsp;&nbsp; <a href=\"javascript:history.back(-1);\"> Intentar de Nuevo ? </a>");  		
	  
	  header("Location: programador.php");	
   }
	
  /////////////////////////////////////////////////////////////////////////  
  if($opcion == "newbackup")
  {
     $id = $_GET['id'];
	 $cont = $clase->Header("S","W");	 
	 $cont.='<script src="popcalendar.js" type="text/javascript"></script>
	          <table width="100%">
	           <tr class="CabezoteTabla"> 
			     <td width="10"> </td>
			     <td width="37"> <img src="images/iconos/programador.png" width="32" height="32" border="0"> </td>
				 <td width="520"> Nuevo Backup a la Base de Datos <td>
				 <td> <a href="programador.php?opcion=nuevatarea"> Nueva Tarea </a>  | <a href="programador.php"> Lista de Tareas </a> </td>
    		     <td width="8"> </td>
			   </tr>	 			   
			 </table> ';	
	
	$cont.='<br><br><center>
            <form action="?opcion=newbackup2" method="POST" name="x">
			<input type="hidden" name="tipotarea" value="BAK">
			<h3> Programar Backup a la Base de Datos </h3>
			<table width="600">
	         <tr height="30"> 
			  <td> <label class="Texto15"> Descripcion : </td>
			  <td> <input type="text" name="descripcion" class="Texto15"  maxlength="25" size="35"> </td>
			 </tr>
	         <tr height="30"> 
			  <td> <label class="Texto15"> Fecha Desde : </td>
			  <td> <input type="text" name="fecdesde" class="Texto15" size="10" value="'.date("d/m/Y").'" id="fecdesde" onClick="popUpCalendar(this, x.fecdesde,\'dd/mm/yyyy\');">  
			       <img src="images/calendario.png" border="0"> </td>
			 </tr>
	         <tr height="30"> 
			  <td> <label class="Texto15"> Fecha Hasta : </td>
			  <td> <input type="text" name="fechasta" class="Texto15" size="10" value="'.date("d/m/Y").'" id="fechasta" onClick="popUpCalendar(this, x.fechasta,\'dd/mm/yyyy\');">  
			       <img src="images/calendario.png" border="0"> </td>
			 </tr>
	         <tr height="30"> 
			  <td> <label class="Texto15"> Hora : </td>
			  <td> <input type="text" name="hora" class="Texto15" value="'.date("h:i:s").'" maxlength="10" size="10">  </td>
			 </tr>
			 </tr>
	         <tr height="30"> 
			  <td> <label class="Texto15"> Repetir : </td>
			  <td> 
			         <input type="radio" name="repeticion" value="no" checked> No Repetir  
			         <input type="radio" name="repeticion" value="dia"> Diariamente  					 
			         <input type="radio" name="repeticion" value="semana"> Semanalmente
			         <input type="radio" name="repeticion" value="mes"> Mensualmente					   		 
			  </td>
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
  
  /////////////////////////////////////////  
  if($opcion == "eliminar")
  {
    $id = $_GET['id'];
    $vsql = "DELETE FROM tareasprogramadas WHERE tareaid=".$id;
	$clase->EjecutarSQL($vsql);
	$clase->Aviso(3,"Tarea Programada Eliminada Exitosamente");  		
	header("Location: programador.php");
  }

  /////////////////////////////////////////  
  if($opcion == "masregistros")
  {
    $actual = $_SESSION["NUMREGISTROSXCONSULTA"];
	$ahora = $actual + 50;
	$_SESSION["NUMREGISTROSXCONSULTA"] = $ahora; 
	header("Location: programador.php");
  }

  /////////////////////////////////////////  
  if($opcion == "encontrar")
  {
    $criterio = $_POST['criterio'];
    $vsql = "SELECT TP.* , TT.icono FROM tareasprogramadas TP INNER JOIN tipotarea TT ON (TP.tipotarea = TT.codigo) 
	         WHERE TP.descripcion like '%".$criterio."%' OR TT.descripcion like '%".$criterio."%' ORDER BY descripcion ASC Limit 0,".$_SESSION["NUMREGISTROSXCONSULTA"];
    $_SESSION['SQL_PROGRAMADOR'] = $vsql;
	header("Location: programador.php");
  }

  /////////////////////////////////////////  
  if($opcion == "nofiltro")
  {
    $criterio = $_POST['criterio'];
  	$vsql = "SELECT * FROM tareasprogramadas ORDER BY descripcion ASC limit 0,30";
	$_SESSION['SQL_PROGRAMADOR'] = "";
	header("Location: programador.php");
  }

  /////////////////////////////////////////  
  if($opcion == "filtrartareas")
  {
     $filtro = $_POST['filtro'];
	 
	 if($filtro == "PENDIENTES")
	  $vsql = "SELECT TP.* , TT.icono FROM tareasprogramadas TP INNER JOIN tipotarea TT ON (TP.tipotarea = TT.codigo) 
		       WHERE TP.momrealiza = '0000-00-00- 00:00:00' ORDER BY TP.momcrea ASC , TP.descripcion ASC limit 0,".$_SESSION["NUMREGISTROSXCONSULTA"];
     
	 if($filtro == "REALIZADAS")
	  $vsql = "SELECT TP.* , TT.icono FROM tareasprogramadas TP INNER JOIN tipotarea TT ON (TP.tipotarea = TT.codigo) 
		       WHERE TP.momrealiza > '0000-00-00- 00:00:00' ORDER BY TP.momcrea ASC , TP.descripcion ASC limit 0,".$_SESSION["NUMREGISTROSXCONSULTA"];
     
	 if($filtro == "HOY")
	  $vsql = "SELECT TP.* , TT.icono FROM tareasprogramadas TP INNER JOIN tipotarea TT ON (TP.tipotarea = TT.codigo) 
		       WHERE Date(TP.momcrea) = CURDATE() ORDER BY TP.momcrea ASC , TP.descripcion ASC limit 0,".$_SESSION["NUMREGISTROSXCONSULTA"];
    
	$_SESSION['SQL_PROGRAMADOR'] = $vsql;    
	header("Location: programador.php");
  }

  /////////////////////////////////////////  
  if($opcion == "")
  {
     $cont = $clase->Header("S","W"); ;  	 
	 $cont.='<table width="100%">
	           <tr class="CabezoteTabla"> 
			     <td width="10"> </td>
			     <td width="37"> <img src="images/iconos/programador.png" width="32" height="32" border="0"> </td>
				 <td width="250"> Programador de Tareas <td>
    	         <Form METHOD="POST" NAME="combo" ACTION="programador.php?opcion=filtrartareas">
				 <td width="120"> 
				          <select name="filtro" class="SelectProgramador" onChange="document.combo.submit();"> 
						       <option class="optionProgramador" value="">FILTRO RAPIDO DE ACTIVIDADES</option> 
							   <option class="optionProgramador" value="PENDIENTES"> Solo Tareas Pendientes por Realizar </option> 
							   <option class="optionProgramador" value="REALIZADAS"> Solo Tareas Realizadas </option> 
							   <option class="optionProgramador" value="HOY"> Solo Tareas del Dia de Hoy </option> 
							   <option class="optionProgramador" value="SEMANA"> Solo Tareas de esta Semana </option> 
						  </select>  </form>  </td>
				 <td width="100"> &nbsp; </td>
				 <td width="27"> <a href="?opcion=nuevatarea"> <img src="images/icononuevo.png" border="0"> </a> </td>
				 <form action="?opcion=encontrar" method="POST" name="x">
				 <td> <input type="text" name="criterio" size="20" placeholder="Criterio a Buscar" tabindex="1" id="default"> </td>
				 <td> <input type="submit" value="Encontrar" class="boton"> </td>';

	 if($_SESSION['SQL_PROGRAMADOR'] != "")
         $cont.='<td width="10"> <a href="?opcion=nofiltro"> <img src="images/nofiltro.png"> </a> </td>'; 

		$cont.=' </form>
    		     <td width="8"> </td>
			   </tr>	 			   
			 </table> ';	
	
	
    $vsql = $_SESSION['SQL_PROGRAMADOR'];
	if($vsql == "")
    	$vsql = "SELECT TP.* , TT.icono FROM tareasprogramadas TP INNER JOIN tipotarea TT ON (TP.tipotarea = TT.codigo) 
		         ORDER BY TP.momcrea DESC , TP.descripcion ASC limit 0,".$_SESSION["NUMREGISTROSXCONSULTA"];

	$conex  = $clase->Conectar();
    $result = mysql_query($vsql,$conex);

	 $cont.='<table width="100%">
	           <tr class="TituloTabla"> 
			     <td width="10"> </td>
			     <td width="5"> Tipo </td>				
				 <td width="130"> Descripcion Tarea</td>				  
			     <td width="110"> Fecha Programada </td>
			     <td width="110"> Fecha Ejecucion </td> 
				 <td width="20" align="center"> Estado </td>			
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
				  <td width="5"> <img src="images/'.$row['icono'].'" border="0"> </td>
				  <td width="130"> '.$row['descripcion'].' </td>
				  <td width="110"> '.$clase->FormatoFecha($row['momcrea']).' </td>
				  <td width="110"> '.$clase->FormatoFecha($row['momrealiza']).' </td>				  
				  <td width="20" align="center"> ';
				  
				  if($row['momrealiza'] == '0000-00-00 00:00:00')
					 $cont.='<img src="images/instinpreactivada.png" title="No Realizada" border="0">';
				  else	     
				     $cont.='<img src="images/instactiva.png" title="Realizada" border="0">';
					 
		 $cont.=' </td>
				  <td> <a href="?opcion=eliminar&amp;id='.$row['tareaid'].'"> <img src="images/iconoborrar.png" title="Suspender Tarea" border="0"> </td>				  
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
  
////////////////////////////////////////////////////////////////////////  
 function sumaDia($fecha,$dia)
 {	
     list($day,$mon,$year) = explode('/',$fecha);
	 return date('d/m/Y',mktime(0,0,0,$mon,$day+$dia,$year));		
 }
 
?> 