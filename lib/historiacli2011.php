<?PHP
  session_start(); 
  include("lib/Sistema.php");

  $clase = new Sistema();
  $clase->Nombredb = $_SESSION['DBNOMBRE']; 
 
  $avisos = ""; 
  $opcion = "";
  $opcion = $_GET["opcion"];

  /////////////////////////////////////////////////////////////////////////  
  if($opcion == "guardarcambio")
  {
    $id         = $_POST['id'];
    $tipoexamen = $_POST['tipoexamen'];
    $empresa    = $_POST['empresa'];
	
	/// Cambio el Tipo de Examen en la HC	
    $clase->EjecutarSQL("UPDATE historiacli SET tipoexamen='".$tipoexamen."' WHERE historiaid=".$id);
	
	/// Cambio la Empresa en la Tabla Documento	
    $docuid = $clase->BDLockup($id,'historiacli','historiaid','docuid');
    $clase->EjecutarSQL("UPDATE documentos SET nitempresa='".$empresa."' WHERE docuid=".$docuid);
	
	$clase->Aviso(1,"Datos de la Historia y Certificado Cambiados con Exito");  		
    header("Location: historiacli.php");
 }
  
  /////////////////////////////////////////////////////////////////////////  
  if($opcion == "cambiar")
  {
    $id     = $_GET['id'];   
    $tipoex = $clase->BDLockup($id,'historiacli','historiaid','tipoexamen');
	$docuid = $clase->BDLockup($id,'historiacli','historiaid','docuid');
	$nitempresa = $clase->BDLockup($docuid,'documentos','docuid','nitempresa');

	$cont.='<form action="?opcion=guardarcambio" method="POST">
	        <input type="hidden" name="id" value="'.$id.'">
	        <center><h3> Cambiar Datos de la Historia </h3><center>
			<table width="600">';
			
    $cont.='<tr class="TablaDocsPar">
              <td width="15"> </td>
			    <td> Tipo de Examen </td> 
			    <td> 
				   <select name="tipoexamen">
                     <option value="" selected></option>';
					 
					 
	if($tipoex == "INGRESO")		  	   
	  $cont.=' <option value="INGRESO" selected> Examen de Ingreso </option>';
	else
      $cont.=' <option value="INGRESO"> Examen de Ingreso </option>';	

	if($tipoex == "EGRESO")		  	   
	  $cont.=' <option value="EGRESO" selected> Examen de Egreso </option>';
	else
      $cont.=' <option value="EGRESO"> Examen de Egreso </option>';	

	if($tipoex == "PERIODICO")		  	   
	  $cont.=' <option value="PERIODICO" selected> Examen Periodico </option>';
	else
      $cont.=' <option value="PERIODICO"> Examen Periodico </option>';	

    
	$cont.='</select></td> 				
			    <td width="15"> </td>
			</tr>
			<tr class="TablaDocsImPar">
              <td width="15"> </td>
			    <td> Entidad </td> 
			    <td>'; 

	   $vsql="SELECT T.* FROM terceros T INNER JOIN clasificater CT ON (T.clasificaterid = CT.clasificaterid)
		         WHERE CT.codigo = '02' ORDER BY nombre";
	   $conex = $clase->Conectar();
       $result = mysql_query($vsql,$conex); 
       $cont.= '<SELECT name="empresa">';
	     
	   while($row = mysql_fetch_array($result))
	   {
          if($row['nit'] == $nitempresa) 
		    $cont.='<option value="'.$row['nit'].'" selected> '.$row['nombre'].'</option>';		 		
		  else
		    $cont.='<option value="'.$row['nit'].'"> '.$row['nombre'].'</option>';		 				  	
	   }		   
  	   
	   $cont.=' 
	            </select>
	          </td> 				
			</tr>
            <tr> 			
			  <td width="15"> </td>
			  <td>  </td> 
			  <td> 
				 <input type="submit" value="Modificar Historia">
			  </td> 				
			  <td width="15"> </td>
            </tr></table></form><br>';
	echo $cont;
	exit(0);		
  }

  
  /////////////////////////////////////////////////////////////////////////  
  if($opcion == "guardarnuevo")
  {
    $historiaid   = $_POST['historiaid'];
    $paciente     = $_POST['paciente'];

    $teridpaciente = $clase->BDLockup($paciente,'terceros','nit','terid');
    $teridprof     = $clase->SeleccionarUno("SELECT teridprof FROM usuarios WHERE UCASE(username)='".strtoupper($_SESSION["USUARIO"])."'");
	if($teridprof == "")
	   $teridprof = 0;	

	$hayabiertas = $clase->SeleccionarUno("SELECT COUNT(*) CANT FROM historiacli WHERE teridpaciente=".$teridpaciente." AND estado='A'");
	
	if($hayabiertas == 0)
	{		
	  $vsql = "INSERT INTO historiacli(teridpaciente,teridprof,creador,momento) 
	           values(".$teridpaciente.",".$teridprof.",'".$_SESSION['USERNAME']."',CURRENT_TIMESTAMP)";
 	  echo $vsql;
	  $cant = $clase->EjecutarSQL($vsql);
	
	  if($cant == 1)
	  {
  		$Nhistoriaid = $clase->SeleccionarUno("SELECT MAX(historiaid) hid FROM historiacli");
    	$vsql = "INSERT INTO historiaself(historiaid) values(".$Nhistoriaid.")";
 	    $cant = $clase->EjecutarSQL($vsql);
       	header("Location: historiacli.php?opcion=detalles&id=".$Nhistoriaid);			
      }	
	  else
   	  {
	  	$clase->Aviso(2,"Error al Crear el Registro &nbsp;&nbsp; <a href=\"javascript:history.back(-1);\"> Intentar de Nuevo ? </a>");  		
	  	header("Location: historiacli.php");
	  }	
	}
	else
	{
	  $hid = $clase->SeleccionarUno("SELECT historiaid FROM historiacli WHERE teridpaciente=".$teridpaciente." AND estado='A'");
	  $clase->Aviso(2,"Ya existe una Historia Clinica abierta para ese Paciente <a href=\"historiacli.php?opcion=detalles&id=".$hid."\"> Visualizarla? </a>");
	  header("Location: historiacli.php?opcion=nuevo");  		
    }
  }

 /////////////////////////////////////////////////////////////////////////  
  if($opcion == "guardar")
  {
    $id = $_POST['id'];
    $conceptox = $_POST['conceptomedid'];
    if($conceptox == "")
       $conceptox = "00";

	$clase->EjecutarSQL("DELETE FROM historiaself WHERE historiaid=".$id);
	
	$vsql = "INSERT INTO historiaself(historiaid,observa1,observa2,observa3,observa4,observa5,
	         antefam01,antefam02,antefam03,antefam04,antefam05,antefam06,antefam07,antefam08,antefam09,
	         antefam10,antefam11,antefam12,antefam13,antefam14,antefam15,antefam16,antefam17,antefam18,anteper01,anteper02,anteper03,anteper04,
			 anteper05,anteper06,anteper07,anteper08,anteper09,anteper10,anteper11,anteper12,anteper13,anteper14,anteper15,anteper16,anteper17,
			 anteper18,anteper19,anteper20,anteper21,anteper22,anteper23,anteper24,inmuni01,inmuni02,inmuni03,inmuni04,gineco01,gineco02,gineco03,
			 gineco04,gineco05,gineco06,gineco07,gineco08,gineco09,gineco10,gineco11,gineco12,gineco13,gineco14,revsis01,revsis02,
			 revsis03,revsis04,revsis05,revsis06,revsis07,revsis08,revsis09,revsis10,revsis11,revsis12,revsis13,revsis14,revsis15,revsis16,
			 revsis17,revsis18,revsis19,revsis20,revsis21,revsis22,revsis23,revsis24,revsis25,revsis26,revsis27,efpeso,eftalla,efimc,eftart,
			 effcard,effresp,ef1,ef2,ef3,ef4,ef5,ef6,ef7,ef8,ef9,ef10,ef11,ef12,ef13,ef14,ef15,ef16,ef17,ef18,ef19,ef20,ef21,ef22,ef23,ef24,ef25,
			 ef26,ef27,ef28,ef29,ef30,ef31,ef32,ef33,ef34,ef35,ef36,ef37,ef38,ef39,ef40,ef41,conceptomedid,observa6) 
	         values(".$id.",'".strtoupper($_POST['observa1'])."','".strtoupper($_POST['observa2'])."','".strtoupper($_POST['observa3'])."'
			 ,'".strtoupper($_POST['observa4'])."','".strtoupper($_POST['observa5'])."'
			 ,'".strtoupper($_POST['antefam01'])."','".strtoupper($_POST['antefam02'])."'
			 ,'".strtoupper($_POST['antefam03'])."','".strtoupper($_POST['antefam04'])."','".strtoupper($_POST['antefam05'])."'
			 ,'".strtoupper($_POST['antefam06'])."','".strtoupper($_POST['antefam07'])."','".strtoupper($_POST['antefam08'])."'
			 ,'".strtoupper($_POST['antefam09'])."','".strtoupper($_POST['antefam10'])."','".strtoupper($_POST['antefam11'])."'
			 ,'".strtoupper($_POST['antefam12'])."','".strtoupper($_POST['antefam13'])."','".strtoupper($_POST['antefam14'])."'
			 ,'".strtoupper($_POST['antefam15'])."','".strtoupper($_POST['antefam16'])."','".strtoupper($_POST['antefam17'])."'
			 ,'".strtoupper($_POST['antefam18'])."','".strtoupper($_POST['anteper01'])."','".strtoupper($_POST['anteper02'])."'
			 ,'".strtoupper($_POST['anteper03'])."','".strtoupper($_POST['anteper04'])."','".strtoupper($_POST['anteper05'])."'
			 ,'".strtoupper($_POST['anteper06'])."','".strtoupper($_POST['anteper07'])."','".strtoupper($_POST['anteper08'])."'
			 ,'".strtoupper($_POST['anteper09'])."','".strtoupper($_POST['anteper10'])."','".strtoupper($_POST['anteper11'])."'
			 ,'".strtoupper($_POST['anteper12'])."','".strtoupper($_POST['anteper13'])."','".strtoupper($_POST['anteper14'])."'
			 ,'".strtoupper($_POST['anteper15'])."','".strtoupper($_POST['anteper16'])."','".strtoupper($_POST['anteper17'])."'
			 ,'".strtoupper($_POST['anteper18'])."','".strtoupper($_POST['anteper19'])."','".strtoupper($_POST['anteper20'])."'
			 ,'".strtoupper($_POST['anteper21'])."','".strtoupper($_POST['anteper22'])."','".strtoupper($_POST['anteper23'])."'
			 ,'".strtoupper($_POST['anteper24'])."','".strtoupper($_POST['inmuni01'])."','".strtoupper($_POST['inmuni02'])."'
			 ,'".strtoupper($_POST['inmuni03'])."','".strtoupper($_POST['inmuni04'])."','".strtoupper($_POST['gineco01'])."'
			 ,'".strtoupper($_POST['gineco02'])."','".strtoupper($_POST['gineco03'])."','".strtoupper($_POST['gineco04'])."'
			 ,'".strtoupper($_POST['gineco05'])."','".strtoupper($_POST['gineco06'])."','".strtoupper($_POST['gineco07'])."'
			 ,'".strtoupper($_POST['gineco08'])."','".strtoupper($_POST['gineco09'])."','".strtoupper($_POST['gineco10'])."'			 
			 ,'".strtoupper($_POST['gineco11'])."','".strtoupper($_POST['gineco12'])."','".strtoupper($_POST['gineco13'])."'			 			 
			 ,'".strtoupper($_POST['gineco14'])."','".strtoupper($_POST['revsis01'])."'			 			 
			 ,'".strtoupper($_POST['revsis02'])."','".strtoupper($_POST['revsis03'])."','".strtoupper($_POST['revsis04'])."'
			 ,'".strtoupper($_POST['revsis05'])."','".strtoupper($_POST['revsis06'])."','".strtoupper($_POST['revsis07'])."'
			 ,'".strtoupper($_POST['revsis08'])."','".strtoupper($_POST['revsis09'])."','".strtoupper($_POST['revsis10'])."'
			 ,'".strtoupper($_POST['revsis11'])."','".strtoupper($_POST['revsis12'])."','".strtoupper($_POST['revsis13'])."'
			 ,'".strtoupper($_POST['revsis14'])."','".strtoupper($_POST['revsis15'])."','".strtoupper($_POST['revsis16'])."'
			 ,'".strtoupper($_POST['revsis17'])."','".strtoupper($_POST['revsis18'])."','".strtoupper($_POST['revsis19'])."'
			 ,'".strtoupper($_POST['revsis20'])."','".strtoupper($_POST['revsis21'])."','".strtoupper($_POST['revsis22'])."'
			 ,'".strtoupper($_POST['revsis23'])."','".strtoupper($_POST['revsis24'])."','".strtoupper($_POST['revsis25'])."'
			 ,'".strtoupper($_POST['revsis26'])."','".strtoupper($_POST['revsis27'])."','".strtoupper($_POST['efpeso'])."'
			 ,'".strtoupper($_POST['eftalla'])."','".strtoupper($_POST['efimc'])."','".strtoupper($_POST['eftart'])."'
			 ,'".strtoupper($_POST['effcard'])."','".strtoupper($_POST['effresp'])."','".strtoupper($_POST['ef1'])."','".strtoupper($_POST['ef2'])."'
			 ,'".strtoupper($_POST['ef3'])."','".strtoupper($_POST['ef4'])."','".strtoupper($_POST['ef5'])."','".strtoupper($_POST['ef6'])."'
			 ,'".strtoupper($_POST['ef7'])."','".strtoupper($_POST['ef8'])."','".strtoupper($_POST['ef9'])."','".strtoupper($_POST['ef10'])."'
			 ,'".strtoupper($_POST['ef11'])."','".strtoupper($_POST['ef12'])."','".strtoupper($_POST['ef13'])."','".strtoupper($_POST['ef14'])."'
			 ,'".strtoupper($_POST['ef15'])."','".strtoupper($_POST['ef16'])."','".strtoupper($_POST['ef17'])."','".strtoupper($_POST['ef18'])."'
			 ,'".strtoupper($_POST['ef19'])."','".strtoupper($_POST['ef20'])."','".strtoupper($_POST['ef21'])."','".strtoupper($_POST['ef22'])."'
			 ,'".strtoupper($_POST['ef23'])."','".strtoupper($_POST['ef24'])."','".strtoupper($_POST['ef25'])."','".strtoupper($_POST['ef26'])."'
			 ,'".strtoupper($_POST['ef27'])."','".strtoupper($_POST['ef28'])."','".strtoupper($_POST['ef29'])."','".strtoupper($_POST['ef30'])."'
			 ,'".strtoupper($_POST['ef31'])."','".strtoupper($_POST['ef32'])."','".strtoupper($_POST['ef33'])."','".strtoupper($_POST['ef34'])."'
			 ,'".strtoupper($_POST['ef35'])."','".strtoupper($_POST['ef36'])."','".strtoupper($_POST['ef37'])."','".strtoupper($_POST['ef38'])."'
			 ,'".strtoupper($_POST['ef39'])."','".strtoupper($_POST['ef40'])."','".strtoupper($_POST['ef41'])."',".strtoupper($conceptox)."
			 ,'".strtoupper($_POST['observa6'])."')";
    ///echo $vsql;
    $cant = $clase->EjecutarSQL($vsql);

    $codConcepto = $clase->BDLockup($conceptox,'conceptomed','conceptomedid','codigo'); 
	$clase->EjecutarSQL("UPDATE historiacli SET conceptomed = '".$codConcepto."' WHERE historiaid=".$id);
	
    // Registro en el LOG de Auditoria la creacion de la Venta 
    $clase->CrearLOG('007','Modifica la Historia Clinica Id:'.$Nhistoriaid,strtoupper($_SESSION["USERNAME"]),'',$Nhistoriaid);
	
	$clase->Aviso(1,"Historia Clinica Guardada Exitosamente");  		  	
    header("Location: historiacli.php?opcion=detalles&id=".$id);		 
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
			     <td width="37"> <img src="images/iconos/historias.png" width="32" height="32" border="0"> </td>
				 <td width="520"> Nueva Historia <td>
				 <td>  <a href="historiacli.php"> Listado de Historias Clinicas </a> </td>
    		     <td width="8"> </td>
			   </tr>	 			   
			 </table> ';	
	
	
	$cont.='<br><br><br><center>
            <form action="?opcion=guardarnuevo" method="POST">
			<table width="400">
	         <tr> 
			   <td width="83"> Paciente : </td> 
			   <td width="180"> 
			   

<!-- ************************************************************************************************ -->
<!-- AJAX AUTOSUGGEST SCRIPT -->
<script type="text/javascript" src="lib/ajax_framework7.js"></script>
<style type="text/css">
#search-wrap7 input{font-size:13px; text-transform:Capitalize; background-color:#D6F0FE; border-style:groove;}
#res7{width:150px; border:solid 1px #DEDEDE; display:none;}
#res7 ul, #res7 li{padding:0; margin:0; border:0; list-style:none; background:#F6F6F6;}
#res7 li {border-top:solid 1px #DEDEDE; background:#CEEAF5;}
#res7 li a{display:block; padding:2px; text-decoration:none; color:#000000; font-weight:bold; font-size:10px; font-family:Verdana;}
#res7 li a small{display:block; text-decoration:none; color:#999999; font-weight:normal; font-size:9px;font-family:Verdana;}
#res7 li a:hover{background:#FFFFFF;}
#res7 ul {padding:4px;}
</style>
<div id="search-wrap7">
<input name="paciente" id="search-q7" type="text" onkeyup="javascript:autosuggest7();" maxlength="12" size="15" tabindex="5" autocomplete=OFF/>
<div id="res7"></div>
</div>
<!-- AJAX AUTOSUGGEST SCRIPT -->
<!-- ************************************************************************************************ -->
			   			  
			   </td>			   
			   <td width="20"> &nbsp; </td>
			 </tr>
			</table>
			
			<br><br>
			
			<table>
			   <tr>
    		    <td>  <button type="submit" name="guardar" tabindex="4" id="continuar" class="Botonverde"> Crear Historia </button>  </td>
				</form>
			  </tr>
			</table>'; 
  }

  /////////////////////////////////////////  
  if($opcion == "detalles")
  {
    $id = $_GET['id'];
    $cont = $clase->Header("N","W"); ;  	 
  	$vsql = "SELECT * FROM historiacli H INNER JOIN historiaself HS ON (H.historiaid = HS.historiaid) 
	         INNER JOIN terceros T ON (H.teridpaciente = T.terid) WHERE H.historiaid=".$id;
        
	$conex  = $clase->Conectar();
    $result = mysql_query($vsql,$conex);
	if($row = mysql_fetch_array($result))
	{

	 $cont.='<table width="100%">
	           <tr class="CabezoteTabla"> 
			     <td width="10"> </td>
			     <td width="37"> <img src="images/iconos/historias.png" width="32" height="32" border="0"> </td>
				 <td width="580"> <b>Historia Clinica del Paciente </b><td>
				 <td> <a href="historiacli.php" title="Regresar al Listado de Historias">
				      <img src="images/iconos/volver.png" width="32" height="32" border="0"></a> </td> 
				 <td> <input title="Guardar Historia" alt="Guardar Historia" src="images/iconos/guardar.png" type="image" width="32" height="32" border="0"> </td> 
				 <td> <a href="#" OnClick="window.open(\'imphistoria.php?id='.$id.'\',\'IHC\',\'width=600,height=650,left=40,top=40\')" title="PreVisualizar Historia">
				      <img src="images/iconos/vistaprevia.png" width="32" height="32" border="0"></a> </td>';
					  
					  
	 if($row['estado'] == 'A'){
    	 $cont.='    <td> <a href="?opcion=cerrar&id='.$id.'" title="Cerrar Historia">
				      <img src="images/iconos/cerrar.png" width="32" height="32" border="0"></a> </td>';
	 }
	 else{
	     $cont.='    <td> <a href="?opcion=abrir&id='.$id.'" title="Abrir Historia">
				      <img src="images/iconos/conceptomed.png" width="32" height="32" border="0"></a> </td>';
	 }
	 			      
	 $cont.=' <td> <a href="#" OnClick="window.open(\'impcertificado.php?&id='.$id.'\',\'ImpCer\',\'width=800,height=600,left=40,top=40\');" title="Generar Certificado">
				      <img src="images/iconos/certificado.png" width="32" height="32" border="0"></a> </td>				 
				 <td> <a href="?opcion=preeliminar&id='.$id.'" title="Eliminar Historia" rel="facebox">
				      <img src="images/iconos/alertas.png" width="32" height="32" border="0"></a> </td>				 
				 </td>
    		     <td width="8"> </td>
			   </tr>	 			   
			 </table> ';	
	
	
		$cont.='<center>
            <form action="?opcion=guardar" method="POST" name="x">
	        <input type="hidden" name="id" value="'.$id.'">
			<table width="950">
			<tr class="CabezoteTabla">
			  <td width="50" align="center">'.FotoPaciente($row['rutafoto']).'<td>
			  <td width="900">			  
			<table width="900">
	         <tr class="BarraDocumentos"> 
			  <td width="5"> </td>
			  <td width="70"><b>Apellidos :</td>
			  <td width="180">'.$row['apellido1'].' '.$row['apellido2'].'</td>
			  <td width="70"><b> Nombres :</td>
			  <td width="180">'.$row['nombre1'].' '.$row['nombre2'].'</td>
			  <td width="70"><b> Documento :</td>
			  <td width="80">'.$row['nit'].'</td>
			  <td width="5"> </td>			  			  
			</tr>
			</table>
			<table width="900">			
	         <tr class="BarraDocumentos">  
			  <td width="5"> </td>		
			  <td width="100"><b> Fecha Nacim : </td>
			  <td width="80">'.$row['fechanac'].'</td>
			  <td width="30"><b> Edad :</td>
			  <td width="60">'.$row['edad'].' A&ntilde;os </td>
			  <td width="15"><b> Genero :</td>
			  <td width="30">'.$row['genero'].'</td>
			  <td width="50"><b> Telefono :</td>
			  <td width="100">'.$row['telefono'].'</td>
			  <td width="50"><b> Celular :</td>
			  <td width="100">'.$row['celular'].'</td>
			  <td width="5"> </td>			  
			 </tr>
			</table>
			
			</td> </tr> </table>
			
			<div style="overflow:auto; height:550px;width:950px;">
            <A name="top"></A>
			<br>
			<table width="100%">
	           <tr class="CabezoteTabla"> 
			     <td width="10"> </td>
			     <td width="37"><img src="images/iconos/ocupacionales.png" width="32" height="32" border="0"></td>
				 <td width="480"> <b>Antecedentes Ocupacionales</b> (Iniciar por Ultima empresa)<td>
				 <td> <a href="?opcion=antocupacionales&id='.$id.'" rel="facebox"><img src="images/icononuevo.png" border="0"></a> Click Aqui para Adicionar </td>
				 <td> <input type="checkbox" name="notiene1"> No Aplica </td>
    		     <td width="8"> </td>
			   </tr>	 			   
			 </table>';
			 
	$cont.= ListadoAnteOcupacionales($id);		 
    $cont.='<br>
	        <script language="javascript">
		 	  function CalcularIMC()
 			  {
              	var peso  = document.x.efpeso.value;
				if(isNaN(peso)){
				  alert(\'PESO No es un valor valido\');
				}  
				 
				var talla = document.x.eftalla.value;
				
				if(isNaN(talla)){
				  alert(\'TALLA No es un valor valido\');
				}  
				
				if((peso != "")&&(talla != ""))
   				  document.x.efimc.value = (peso/(talla*talla));
				  document.x.efimc.value = document.x.efimc.value.substring(0,5) + "%";
			  }	
			</script>
            <table width="100%">
	           <tr class="CabezoteTabla"> 
			     <td width="10"> </td>
			     <td width="37"> <img src="images/iconos/detrabajo.png" width="32" height="32" border="0"> </td>
				 <td width="480"> <b> Accidentes de Trabajo / 	Enfermedad Profesional </b> <td>
				 <td><a href="?opcion=antdetrabajo&id='.$id.'" rel="facebox"><img src="images/icononuevo.png" border="0"></a> Click Aqui para Adicionar </td>
                 <td> <input type="checkbox" name="notiene1"> No Aplica </td>    		     
				 <td width="8"> </td>
			   </tr>	 			   
			 </table>';
	    
		$cont.= ListadoAccidentestra($id);		 			 
		$cont.= '<br>		
            <table width="100%">
	           <tr class="CabezoteTabla"> 
			     <td width="10"> </td>
			     <td width="37"> <img src="images/iconos/familiares.png" width="32" height="32" border="0"> </td>
				 <td width="680"> <b>Antecedentes Familiares </b> <td>
				 <td width="40"> 
				    <input title="Guardar Historia" alt="Guardar Historia" src="images/iconos/guardar.png" type="image" width="32" height="32" border="0"> </td> 
				 <td width="40"> 
				    <a href="#top" title="Ir Arriba"><img src="images/iconos/top.png" width="24" height="24" border="0"></a> </td> 				 
    		     <td width="8"> </td>
			   </tr>	 			   
			 </table>		
			<br> 
            <table width="100%">
	           <tr> 
			     <td width="30"> </td>
			     <td width="100"> Hipertension </td>
			     <td width="250"> <input type="text" size="40" name="antefam01" value="'.$row['antefam01'].'"> </td>			     
			     <td width="20"> </td>
			     <td width="100"> Sordera Congenita </td>
			     <td width="250"> <input type="text" size="40" name="antefam02" value="'.$row['antefam02'].'"> </td>			     
    		     <td width="20"> </td>
			   </tr>	 			   
	           <tr> 
			     <td width="30"> </td>
			     <td width="100"> Cardiopatias </td>
			     <td width="250"> <input type="text" size="40" name="antefam03" value="'.$row['antefam03'].'"> </td>			     
			     <td width="20"> </td>
			     <td width="100"> Alergias </td>
			     <td width="250"> <input type="text" size="40" name="antefam04" value="'.$row['antefam04'].'"> </td>			     
    		     <td width="20"> </td>
			   </tr>	 			   
	           <tr> 
			     <td width="30"> </td>
			     <td width="100"> Diabetes </td>
			     <td width="250"> <input type="text" size="40" name="antefam05" value="'.$row['antefam05'].'"> </td>			     
			     <td width="20"> </td>
			     <td width="100"> Asma </td>
			     <td width="250"> <input type="text" size="40" name="antefam06" value="'.$row['antefam06'].'"> </td>			     
    		     <td width="20"> </td>
			   </tr>	 			   
	           <tr> 
			     <td width="30"> </td>
			     <td width="100"> Cancer </td>
			     <td width="250"> <input type="text" size="40" name="antefam07" value="'.$row['antefam07'].'"> </td>			     
			     <td width="20"> </td>
			     <td width="100"> Tuberculosis </td>
			     <td width="250"> <input type="text" size="40" name="antefam08" value="'.$row['antefam08'].'"> </td>			     
    		     <td width="20"> </td>
			   </tr>	 			   
	           <tr> 
			     <td width="30"> </td>
			     <td width="100"> Enf. Pulmonares </td>
			     <td width="250"> <input type="text" size="40" name="antefam09" value="'.$row['antefam09'].'"> </td>			     
			     <td width="20"> </td>
			     <td width="100"> A.C.V. </td>
			     <td width="250"> <input type="text" size="40" name="antefam10" value="'.$row['antefam10'].'"> </td>			     
    		     <td width="20"> </td>
			   </tr>	 			   
	           <tr> 
			     <td width="30"> </td>
			     <td width="100"> Enf. Mentales </td>
			     <td width="250"> <input type="text" size="40" name="antefam11" value="'.$row['antefam11'].'"> </td>			     
			     <td width="20"> </td>
			     <td width="100"> Varices </td>
			     <td width="250"> <input type="text" size="40" name="antefam12" value="'.$row['antefam12'].'"> </td>			     
    		     <td width="20"> </td>
			   </tr>	 			   
	           <tr> 
			     <td width="30"> </td>
			     <td width="100"> OsteoMusculares </td>
			     <td width="250"> <input type="text" size="40" name="antefam13" value="'.$row['antefam13'].'"> </td>			     
			     <td width="20"> </td>
			     <td width="100"> Artritis </td>
			     <td width="250"> <input type="text" size="40" name="antefam14" value="'.$row['antefam14'].'"> </td>			     
    		     <td width="20"> </td>
			   </tr>	 			   
	           <tr> 
			     <td width="30"> </td>
			     <td width="100"> Ceguera Congenita </td>
			     <td width="250"> <input type="text" size="40" name="antefam15" value="'.$row['antefam15'].'"> </td>			     
			     <td width="20"> </td>
			     <td width="100"> Sindrome Convulsivo </td>
			     <td width="250"> <input type="text" size="40" name="antefam16" value="'.$row['antefam16'].'"> </td>			     
    		     <td width="20"> </td>
			   </tr>	 			   
	           <tr> 
			     <td width="30"> </td>
			     <td width="100"> Daltonismo </td>
			     <td width="250"> <input type="text" size="40" name="antefam17" value="'.$row['antefam17'].'"> </td>			     
			     <td width="20"> </td>
			     <td width="100"> Psiquiatricos </td>
			     <td width="250"> <input type="text" size="40" name="antefam18" value="'.$row['antefam18'].'"> </td>			     
    		     <td width="20"> </td>
			   </tr>	 			   
			 </table>			
			 <table width="100%"> 
			  <tr> <td width="30"> </td>
			       <td width="110"><b>Observaciones :</b></td><td> <textarea name="observa1" cols="88" rows="2">'.$row['observa1'].'</textarea> </td> </tr>
			 </table>
			<br> 		 	
            <table width="100%">
	           <tr class="CabezoteTabla"> 
			     <td width="10"> </td>
			     <td width="37"> <img src="images/iconos/terceros.png" width="32" height="32" border="0"> </td>
				 <td width="670"> <b>Antecedentes Personales </b> <td>
				 <td width="40"> 
				    <input title="Guardar Historia" alt="Guardar Historia" src="images/iconos/guardar.png" type="image" width="32" height="32" border="0"> </td> 
				 <td width="40"> 
				    <a href="#top" title="Ir Arriba"><img src="images/iconos/top.png" width="24" height="24" border="0"></a> </td> 				 
    		     <td width="8"> </td>
			   </tr>	 			   
			</table>		
			<br>
            <table width="100%">
	           <tr> 
			     <td width="20"> </td>
			     <td width="10"><input type="checkbox" name="anteper01" value="checked" '.$row['anteper01'].'></td>			     
			     <td width="100">Congenitas </td>			     			     
			     <td width="20"> </td>
			     <td width="10"><input type="checkbox" name="anteper02" value="checked"  '.$row['anteper02'].'></td>			     
			     <td width="100">Neurologico </td>			     			     
			     <td width="20"> </td>
			     <td width="10"><input type="checkbox" name="anteper03" value="checked"  '.$row['anteper03'].'></td>			     
			     <td width="100">Inmunodeprimibles </td>			     			     
			     <td width="20"> </td>
			     <td width="10"><input type="checkbox" name="anteper04" value="checked"  '.$row['anteper04'].'></td>			     
			     <td width="100">Problemas de Piel </td>			     			     
			     <td width="20"> </td>
			     <td width="10"><input type="checkbox" name="anteper05" value="checked"  '.$row['anteper05'].'></td>			     
			     <td width="100">Infecciosas </td>			     			     
			     <td width="20"> </td>
			     <td width="10"><input type="checkbox" name="anteper06" value="checked"  value="'.$row['anteper06'].'"></td>			     
			     <td width="100">Alergicos </td>			     			     
    		     <td width="20"> </td>
			   </tr>	 			  
	           <tr> 
			     <td width="20"> </td>
			     <td width="10"><input type="checkbox" name="anteper07" value="checked" '.$row['anteper07'].'></td>			     
			     <td width="100">Ojos </td>			     			     
			     <td width="20"> </td>
			     <td width="10"><input type="checkbox" name="anteper08" value="checked" '.$row['anteper08'].'></td>			     
			     <td width="100">Toxicos </td>			     			     
			     <td width="20"> </td>
			     <td width="10"><input type="checkbox" name="anteper09" value="checked" '.$row['anteper09'].'></td>			     
			     <td width="100">Agudeza Visual </td>			     			     
			     <td width="20"> </td>
			     <td width="10"><input type="checkbox" name="anteper10" value="checked" '.$row['anteper10'].'></td>			     
			     <td width="100">Farmacologicos </td>			     			     
			     <td width="20"> </td>
			     <td width="10"><input type="checkbox" name="anteper11" value="checked" '.$row['anteper11'].'></td>			     
			     <td width="100">Oidos </td>			     			     
			     <td width="20"> </td>
			     <td width="10"><input type="checkbox" name="anteper12" value="checked" '.$row['anteper12'].'></td>			     
			     <td width="100">Quirurgicos </td>			     			     
    		     <td width="20"> </td>
			   </tr>	 			  
	           <tr> 
			     <td width="20"> </td>
			     <td width="10"><input type="checkbox" name="anteper13" value="checked" '.$row['anteper13'].'></td>			     
			     <td width="100">Agudeza Auditiva </td>			     			     
			     <td width="20"> </td>
			     <td width="10"><input type="checkbox" name="anteper14" value="checked" '.$row['anteper14'].'></td>			     
			     <td width="100">Traumaticos </td>			     			     
			     <td width="20"> </td>
			     <td width="10"><input type="checkbox" name="anteper15" value="checked" '.$row['anteper15'].'></td>			     
			     <td width="100">Nasofaringea </td>			     			     
			     <td width="20"> </td>
			     <td width="10"><input type="checkbox" name="anteper16" value="checked" '.$row['anteper16'].'></td>			     
			     <td width="100">Tranfusiones </td>			     			     
			     <td width="20"> </td>
			     <td width="10"><input type="checkbox" name="anteper17" value="checked" '.$row['anteper17'].'></td>			     
			     <td width="100">Cardiovascular </td>			     			     
			     <td width="20"> </td>
			     <td width="10"><input type="checkbox" name="anteper18" value="checked" '.$row['anteper18'].'></td>			     
			     <td width="100">E.T.S. (Sida) </td>			     			     
    		     <td width="20"> </td>
			   </tr>	 			  
	           <tr> 
			     <td width="20"> </td>
			     <td width="10"><input type="checkbox" name="anteper19" value="checked" '.$row['anteper19'].'></td>			     
			     <td width="100">Pulmonar </td>			     			     
			     <td width="20"> </td>
			     <td width="10"><input type="checkbox" name="anteper20" value="checked" '.$row['anteper20'].'></td>			     
			     <td width="100">Deformidades </td>			     			     
			     <td width="20"> </td>
			     <td width="10"><input type="checkbox" name="anteper21" value="checked" '.$row['anteper21'].'></td>			     
			     <td width="100">Gastrointestinal </td>			     			     
			     <td width="20"> </td>
			     <td width="10"><input type="checkbox" name="anteper22" value="checked" '.$row['anteper22'].'></td>			     
			     <td width="100">Psiquiatricos </td>			     			     
			     <td width="20"> </td>
			     <td width="10"><input type="checkbox" name="anteper23" value="checked" '.$row['anteper23'].'></td>			     
			     <td width="100">Genitourinaria </td>			     			     
			     <td width="20"> </td>
			     <td width="10"><input type="checkbox" name="anteper24" value="checked" '.$row['anteper24'].'></td>			     
			     <td width="100">Farmacodependencia </td>			     			     
    		     <td width="20"> </td>
			   </tr>	 			  
            </table>
			 <table width="100%"> 
			  <tr> <td width="30"> </td>
			       <td width="110"><b>Observaciones :</b></td><td> <textarea name="observa2" cols="88" rows="2">'.$row['observa2'].'</textarea> </td> </tr>
			 </table>			
			<br> 		 	
            <table width="100%">
	           <tr class="CabezoteTabla"> 
			     <td width="10"> </td>
			     <td width="37"> <img src="images/iconos/examenes.png" width="32" height="32" border="0"> </td>
				 <td width="670"> <b>Inmunizaciones</b> <td>
				 <td width="40"> <input title="Guardar Historia" alt="Guardar Historia" src="images/iconos/guardar.png" type="image" width="32" height="32" border="0"> </td> 
				 <td width="40"> 
				    <a href="#top" title="Ir Arriba"><img src="images/iconos/top.png" width="24" height="24" border="0"></a> </td> 				 
    		     <td width="8"> </td>
			   </tr>	 			   
			</table>		
			<br>
            <table width="97%">
	           <tr> 
			     <td width="20"> </td>
			     <td width="10"><input type="checkbox" name="inmuni01" value="checked" '.$row['inmuni01'].'></td>			     
			     <td width="200">Hepatitis B </td>			     			     
			     <td width="20"> </td>
			     <td width="10"><input type="checkbox" name="inmuni02" value="checked" '.$row['inmuni02'].'></td>			     
			     <td width="200">Fiebre Amarilla </td>			     			     
			     <td width="20"> </td>
			     <td width="10"><input type="checkbox" name="inmuni03" value="checked" '.$row['inmuni03'].'></td>			     
			     <td width="200">Tetano</td>			     			     
			     <td width="100">Otras Cual :</td>			     			     
			     <td width="100"> <input type="text" size="30" name="inmuni04" value="'.$row['inmuni04'].'"></td>			     			     				 
			     <td width="50"> </td>
			   </tr>	 			   
			</table>		
			 <table width="100%"> 
			  <tr> <td width="30"> </td>
			       <td width="110"><b>Observaciones :</b></td><td> <textarea name="observa3" cols="88" rows="2">'.$row['observa3'].'</textarea> </td> </tr>
			 </table>
			<br> 		 	
            <table width="100%">
	           <tr class="CabezoteTabla"> 
			     <td width="10"> </td>
			     <td width="37"> <img src="images/iconos/examenes.png" width="32" height="32" border="0"> </td>
				 <td width="670"> <b>GinecObstetricos</b> <td>
				 <td width="40"> <input title="Guardar Historia" alt="Guardar Historia" src="images/iconos/guardar.png" type="image" width="32" height="32" border="0"> </td> 
				 <td width="40"> 
				    <a href="#top" title="Ir Arriba"><img src="images/iconos/top.png" width="24" height="24" border="0"></a> </td> 				 
    		     <td width="8"> </td>
			   </tr>	 			   
			</table>		
            <br>
			<table width="100%">
	           <tr> 
			     <td width="20"> </td>
			     <td width="80">Menarca : </td>			     
			     <td width="100"> <input type="text" name="gineco01" size="20" value="'.$row['gineco01'].'"> </td>			     			     
				 <td width="20"> </td>
			     <td width="80">Ciclos : </td>			     
			     <td width="100"> <input type="text" name="gineco02" size="20" value="'.$row['gineco02'].'"> </td>			     			     
				 <td width="20"> </td>
			     <td width="80">Gestaciones : </td>			     
			     <td width="100"> <input type="text" name="gineco03" size="20" value="'.$row['gineco03'].'"> </td>			     			     
               </tr>
	           <tr> 
			     <td width="20"> </td>
			     <td width="80"> Partos : </td>			     
			     <td width="100"> <input type="text" name="gineco04" size="20" value="'.$row['gineco04'].'"> </td>			     			     
			     <td width="20"> </td>
				 <td width="80"> Abortos : </td>			     
			     <td width="100"> <input type="text" name="gineco05" size="20" value="'.$row['gineco05'].'"> </td>			     			     
				 <td width="20"> </td>
			     <td width="80"> Ces&aacute;reas : </td>			     
			     <td width="100"> <input type="text" name="gineco06" size="20" value="'.$row['gineco06'].'"> </td>			     			     
               </tr>
	           <tr> 
			     <td width="20"> </td>
			     <td width="80">  <input type="checkbox" name="gineco07" value="CHECKED" '.$row['gineco07'].'> Citologia </td>			     
			     <td width="100"> <input type="text"  name="gineco08" size="20"  value="'.$row['gineco08'].'"> </td>			     			     
			     <td width="20"> </td>
				 <td width="80">  <input type="checkbox" name="gineco09" value="CHECKED" '.$row['gineco09'].'> Mamografia </td>			     
			     <td width="100"> <input type="text"  name="gineco10" size="20" value="'.$row['gineco10'].'"> </td>			     			     
				 <td width="20"> </td>
			     <td width="80"> </td>			     
			     <td width="100"></td>			     			     
               </tr>
			 </table>  
			 <br>
			 <table width="100%">
	           <tr> 
			     <td width="20"> </td>
			     <td width="55">Hijos Vivos : </td>			     
			     <td width="20"> <input type="text" name="gineco11" size="2" value="'.$row['gineco11'].'"> </td>			     			     
				 <td width="20"> </td>
			     <td width="125">Fecha Ultima Menstruacion : </td>			     
			     <td width="50"> <input type="text" name="gineco12" size="8" value="'.$row['gineco12'].'"> </td>			     			     
				 <td width="20"> </td>
			     <td width="100"> <input type="checkbox" name="gineco13" value="CHECKED" '.$row['gineco13'].'> Planifica </td>			     
			     <td width="200"> Metodo : <input type="text" name="gineco14" size="30" value="'.$row['gineco14'].'"> </td>			     			     
               </tr>
             </table>    
			 <br> 		 	
            <table width="100%">
	           <tr class="CabezoteTabla"> 
			     <td width="10"> </td>
			     <td width="37"> <img src="images/iconos/porsistemas.png" width="32" height="32" border="0"> </td>
				 <td width="670"> <b>Revisi&oacute;n por Sistemas </b> <td>
				 <td width="40"> <input title="Guardar Historia" alt="Guardar Historia" src="images/iconos/guardar.png" type="image" width="32" height="32" border="0"> </td> 
				 <td width="40"> 
				    <a href="#top" title="Ir Arriba"><img src="images/iconos/top.png" width="24" height="24" border="0"></a> </td> 				 
    		     <td width="8"> </td>
			   </tr>	 			   
			</table>		
			<br>
            <table width="100%">
	           <tr> 
			     <td width="20"> </td>
			     <td width="10"><input type="checkbox" name="revsis01" value="checked" '.$row['revsis01'].'></td>			     
			     <td width="100">Cabeza</td>			     			     
			     <td width="20"> </td>
			     <td width="10"><input type="checkbox" name="revsis02" value="checked" '.$row['revsis02'].'></td>			     
			     <td width="100">E.D.A. </td>			     			     
			     <td width="20"> </td>
			     <td width="10"><input type="checkbox" name="revsis03" value="checked" '.$row['revsis03'].'></td>			     
			     <td width="100">Ojos </td>			     			     
			     <td width="20"> </td>
			     <td width="10"><input type="checkbox" name="revsis04" value="checked" '.$row['revsis04'].'></td>			     
			     <td width="100">Disuria, hematuria </td>			     			     
			     <td width="20"> </td>
			     <td width="10"><input type="checkbox" name="revsis05" value="checked" '.$row['revsis05'].'></td>			     
			     <td width="100">Hipoacusia </td>			     			     
			     <td width="20"> </td>
			     <td width="10"><input type="checkbox" name="revsis06" value="checked" '.$row['revsis06'].'></td>			     
			     <td width="100">Osteo-muscular </td>			     			     
    		     <td width="20"> </td>
			   </tr>	 			  
	           <tr> 
			     <td width="20"> </td>
			     <td width="10"><input type="checkbox" name="revsis07" value="checked" '.$row['revsis07'].'></td>			     
			     <td width="100">Tinnitus acufenos </td>			     			     
			     <td width="20"> </td>
			     <td width="10"><input type="checkbox" name="revsis08" value="checked" '.$row['revsis08'].'></td>			     
			     <td width="100">Artralgias </td>			     			     
			     <td width="20"> </td>
			     <td width="10"><input type="checkbox" name="revsis09" value="checked" '.$row['revsis09'].'></td>			     
			     <td width="100">Vertigo </td>			     			     
			     <td width="20"> </td>
			     <td width="10"><input type="checkbox" name="revsis10" value="checked" '.$row['revsis10'].'></td>			     
			     <td width="100">Deformidades </td>			     			     
			     <td width="20"> </td>
			     <td width="10"><input type="checkbox" name="revsis11" value="checked" '.$row['revsis11'].'></td>			     
			     <td width="100">Otorrea </td>			     			     
			     <td width="20"> </td>
			     <td width="10"><input type="checkbox" name="revsis12" value="checked" '.$row['revsis12'].'></td>			     
			     <td width="100">Sindrome Convulsivo </td>			     			     
    		     <td width="20"> </td>
			   </tr>	 			  
	           <tr> 
			     <td width="20"> </td>
			     <td width="10"><input type="checkbox" name="revsis13" value="checked" '.$row['revsis13'].'></td>			     
			     <td width="100">Nariz </td>			     			     
			     <td width="20"> </td>
			     <td width="10"><input type="checkbox" name="revsis14" value="checked" '.$row['revsis14'].'></td>			     
			     <td width="100">Disestesias</td>			     			     
			     <td width="20"> </td>
			     <td width="10"><input type="checkbox" name="revsis15" value="checked" '.$row['revsis15'].'></td>			     
			     <td width="100">Garganta</td>			     			     
			     <td width="20"> </td>
			     <td width="10"><input type="checkbox" name="revsis16" value="checked" '.$row['revsis16'].'></td>			     
			     <td width="100">Plejias</td>			     			     
			     <td width="20"> </td>
			     <td width="10"><input type="checkbox" name="revsis17" value="checked" '.$row['revsis17'].'></td>			     
			     <td width="100">Precordialgias</td>			     			     
			     <td width="20"> </td>
			     <td width="10"><input type="checkbox" name="revsis18" value="checked" '.$row['revsis18'].'></td>			     
			     <td width="100">Neurologico</td>			     			     
    		     <td width="20"> </td>
			   </tr>	 			  
	           <tr> 
			     <td width="20"> </td>
			     <td width="10"><input type="checkbox" name="revsis19" value="checked" '.$row['revsis19'].'></td>			     
			     <td width="100">Disnea</td>			     			     
			     <td width="20"> </td>
			     <td width="10"><input type="checkbox" name="revsis20" value="checked" '.$row['revsis20'].'></td>			     
			     <td width="100">Endocrino</td>			     			     
			     <td width="20"> </td>
			     <td width="10"><input type="checkbox" name="revsis21" value="checked" '.$row['revsis21'].'></td>			     
			     <td width="100">Expectoracion</td>			     			     
			     <td width="20"> </td>
			     <td width="10"><input type="checkbox" name="revsis22" value="checked" '.$row['revsis22'].'></td>			     
			     <td width="100">Lopotimia</td>			     			     
			     <td width="20"> </td>
			     <td width="10"><input type="checkbox" name="revsis23" value="checked" '.$row['revsis23'].'></td>			     
			     <td width="100">Tos</td>			     			     
			     <td width="20"> </td>
			     <td width="10"><input type="checkbox" name="revsis24" value="checked" '.$row['revsis24'].'></td>			     
			     <td width="100">Psicologia</td>			     			     
    		     <td width="20"> </td>
			   </tr>	 			  
	           <tr> 
			     <td width="20"> </td>
			     <td width="10"><input type="checkbox" name="revsis25" value="checked" '.$row['revsis25'].'></td>			     
			     <td width="100">Acedias, pirosis</td>			     			     
			     <td width="20"> </td>
			     <td width="10"><input type="checkbox" name="revsis26" value="checked" '.$row['revsis26'].'></td>			     
			     <td width="100">Vascular</td>			     			     
			     <td width="20"> </td>
			     <td width="10"><input type="checkbox" name="revsis27" value="checked" '.$row['revsis27'].'></td>			     
			     <td width="100">Estreñimiento</td>			     			     
			     <td width="20">  </td>
			     <td width="10">  </td>			     
			     <td width="200"> </td>			     			     
			     <td width="20">  </td>
			     <td width="10">  </td>			     
			     <td width="200"> </td>			     			     
    		     <td width="20">  </td>
			   </tr>
            </table>
			 <table width="100%"> 
			  <tr> <td width="30"> </td>
			       <td width="110"><b>Observaciones :</b></td><td> <textarea name="observa4" cols="88" rows="2">'.$row['observa4'].'</textarea> </td> </tr>
		     </table>		             
			<br> 		 	
            <table width="100%">
	           <tr class="CabezoteTabla"> 
			     <td width="10"> </td>
			     <td width="37"> <img src="images/iconos/fisico.png" width="32" height="32" border="0"> </td>
				 <td width="670"> <b>Examen Fisico </b> <td>
				 <td width="40"> <input title="Guardar Historia" alt="Guardar Historia" src="images/iconos/guardar.png" type="image" width="32" height="32" border="0"> </td> 
				 <td width="40"> 
				    <a href="#top" title="Ir Arriba"><img src="images/iconos/top.png" width="24" height="24" border="0"></a> </td> 				 
    		     <td width="8"> </td>
			   </tr>	 			   
			</table>		
            <table width="100%">
	           <tr height="40"> 
			     <td width="20"> </td>
			     <td width="40">Peso : </td>			     			     
			     <td width="60"><input type="text" name="efpeso" size="3" value="'.$row['efpeso'].'"></td>	
				 <td width="20"></td>		     			     			     
			     <td width="40">Talla : </td>			     			     
			     <td width="60"><input type="text" name="eftalla" size="3"  value="'.$row['eftalla'].'"></td>			     			     			     
				 <td width="20"></td>
			     <td width="40">I.M.C. : </td>			     			     
			     <td width="60"><input type="text" name="efimc" size="7"  value="'.$row['efimc'].'" OnFocus="CalcularIMC();"></td>			     			     			     
				 <td width="20"></td>
			     <td width="40">T. Art : </td>			     			     
			     <td width="60"><input type="text" name="eftart" size="3"  value="'.$row['eftart'].'" OnFocus="CalcularIMC();"></td>			     			     			     
				 <td width="20"></td>
			     <td width="40">F.Card. : </td>			     			     
			     <td width="60"><input type="text" name="effcard" size="3"  value="'.$row['effcard'].'"></td>			     			     			     
				 <td width="20"></td>
			     <td width="40">F.Resp. : </td>			     			     
			     <td width="60"><input type="text" name="effresp" size="3"  value="'.$row['effresp'].'"></td>			     			     			     
				 <td width="20"></td>			     
               </tr>
            </table>   
            <table width="100%">
	           <tr> 
			     <td width="10"> </td>
			     <td width="150">Estado Nutricional</td>			     			     
			     <td width="105"> '.ValorRadioExFisico("ef1",$row['ef1']).' </td>			       
			     <td width="40"> </td>
			     <td width="150">Inspeccion Torax Senos</td>			     			     
			     <td width="105"> '.ValorRadioExFisico("ef2",$row['ef2']).' </td>			       
			     <td width="40"> </td>
			     <td width="150">Piel Coloracion Cicat</td>			     			     
			     <td width="105"> '.ValorRadioExFisico("ef3",$row['ef3']).' </td>			       
			     <td width="10"> </td>
               </tr>          
	           <tr> 
			     <td width="20"> </td>
			     <td width="150">Palpacion Torax</td>			     			     
			     <td width="105"> '.ValorRadioExFisico("ef4",$row['ef4']).' </td>			       
			     <td width="40"> </td>
			     <td width="150">Faneras</td>			     			     
			     <td width="105"> '.ValorRadioExFisico("ef5",$row['ef5']).' </td>			       
			     <td width="40"> </td>
			     <td width="150">Auxcultacion respiratoria</td>			     			     
			     <td width="105"> '.ValorRadioExFisico("ef6",$row['ef6']).' </td>			       
			     <td width="20"> </td>
               </tr>          
	           <tr> 
			     <td width="20"> </td>
			     <td width="150">Fondo de Ojo</td>			     			     
			     <td width="105"> '.ValorRadioExFisico("ef7",$row['ef7']).' </td>			       
			     <td width="40"> </td>
			     <td width="150">Auscultacion Cardiaca</td>			     			     
			     <td width="105"> '.ValorRadioExFisico("ef8",$row['ef8']).' </td>			       
			     <td width="40"> </td>
			     <td width="150">Palpacion Craneo</td>			     			     
			     <td width="105"> '.ValorRadioExFisico("ef9",$row['ef9']).' </td>			       
			     <td width="20"> </td>
               </tr>                         
	           <tr> 
			     <td width="20"> </td>
			     <td width="150">Palpacion Craneo</td>			     			     
			     <td width="105"> '.ValorRadioExFisico("ef10",$row['ef10']).' </td>			       
			     <td width="40"> </td>
			     <td width="150">Inspeccion Abdominal</td>			     			     
			     <td width="105"> '.ValorRadioExFisico("ef11",$row['ef11']).' </td>			       
			     <td width="40"> </td>
			     <td width="150">Parpados</td>			     			     
			     <td width="105"> '.ValorRadioExFisico("ef12",$row['ef12']).' </td>			       
			     <td width="20"> </td>
               </tr>              
	           <tr> 
			     <td width="20"> </td>
			     <td width="150">Palpacion Abdomen</td>			     			     
			     <td width="105"> '.ValorRadioExFisico("ef13",$row['ef13']).' </td>			       
			     <td width="40"> </td>
			     <td width="150">Conjuntivas</td>			     			     
			     <td width="105"> '.ValorRadioExFisico("ef14",$row['ef14']).' </td>			       
			     <td width="40"> </td>
			     <td width="150">Exploracion Higado</td>			     			     
			     <td width="105"> '.ValorRadioExFisico("ef15",$row['ef15']).' </td>			       
			     <td width="20"> </td>
               </tr>                         
	           <tr> 
			     <td width="10"> </td>
			     <td width="150">Corneas</td>			     			     
			     <td width="105"> '.ValorRadioExFisico("ef16",$row['ef16']).' </td>			       
			     <td width="40"> </td>
			     <td width="150">Exploracion Bazo</td>			     			     
			     <td width="105"> '.ValorRadioExFisico("ef17",$row['ef17']).' </td>			       
			     <td width="40"> </td>
			     <td width="150">Pupilas</td>			     			     
			     <td width="105"> '.ValorRadioExFisico("ef18",$row['ef18']).' </td>			       
			     <td width="10"> </td>
               </tr>          
	           <tr> 
			     <td width="20"> </td>
			     <td width="150">Exploracion Ri&ntilde;ones</td>			     			     
			     <td width="105"> '.ValorRadioExFisico("ef19",$row['ef19']).' </td>			       
			     <td width="40"> </td>
			     <td width="150">Reflejo Fotomotor</td>			     			     
			     <td width="105"> '.ValorRadioExFisico("ef20",$row['ef20']).' </td>			       
			     <td width="40"> </td>
			     <td width="150">Region Anal</td>			     			     
			     <td width="105"> '.ValorRadioExFisico("ef21",$row['ef21']).' </td>			       
			     <td width="20"> </td>
               </tr>          
	           <tr> 
			     <td width="20"> </td>
			     <td width="150">Reflejo Corneal</td>			     			     
			     <td width="105"> '.ValorRadioExFisico("ef22",$row['ef22']).' </td>			       
			     <td width="40"> </td>
			     <td width="150">Genitales Externos</td>			     			     
			     <td width="105"> '.ValorRadioExFisico("ef23",$row['ef23']).' </td>			       
			     <td width="40"> </td>
			     <td width="150">Inspeccion Externa Oidos</td>			     			     
			     <td width="105"> '.ValorRadioExFisico("ef24",$row['ef24']).' </td>			       
			     <td width="20"> </td>
               </tr>                         
	           <tr> 
			     <td width="20"> </td>
			     <td width="150">Inspeccion Miembros Super</td>			     			     
			     <td width="105"> '.ValorRadioExFisico("ef25",$row['ef25']).' </td>			       
			     <td width="40"> </td>
			     <td width="150">Otoscopia</td>			     			     
			     <td width="105"> '.ValorRadioExFisico("ef26",$row['ef26']).' </td>			       
			     <td width="40"> </td>
			     <td width="150">Inspeccion Miembros Infer</td>			     			     
			     <td width="105"> '.ValorRadioExFisico("ef27",$row['ef27']).' </td>			       
			     <td width="20"> </td>
               </tr>              
	           <tr> 
			     <td width="20"> </td>
			     <td width="150">Inspeccion Ext Nariz</td>			     			     
			     <td width="105"> '.ValorRadioExFisico("ef28",$row['ef28']).' </td>			       
			     <td width="40"> </td>
			     <td width="150">Inspeccion Columna Vert</td>			     			     
			     <td width="105"> '.ValorRadioExFisico("ef29",$row['ef29']).' </td>			       
			     <td width="40"> </td>
			     <td width="150">Rinoscopia</td>			     			     
			     <td width="105"> '.ValorRadioExFisico("ef30",$row['ef30']).' </td>			       
			     <td width="20"> </td>
               </tr>                         
	           <tr> 
			     <td width="10"> </td>
			     <td width="150">Palpacion Columna Vert</td>			     			     
			     <td width="105"> '.ValorRadioExFisico("ef31",$row['ef31']).' </td>			       
			     <td width="40"> </td>
			     <td width="150">Boca</td>			     			     
			     <td width="105"> '.ValorRadioExFisico("ef32",$row['ef32']).' </td>			       
			     <td width="40"> </td>
			     <td width="150">Pulso (Radial)</td>			     			     
			     <td width="105"> '.ValorRadioExFisico("ef33",$row['ef33']).' </td>			       
			     <td width="10"> </td>
               </tr>          
	           <tr> 
			     <td width="20"> </td>
			     <td width="150">Faringe</td>			     			     
			     <td width="105"> '.ValorRadioExFisico("ef34",$row['ef34']).' </td>			       
			     <td width="40"> </td>
			     <td width="150">Reflejos Tendinosos</td>			     			     
			     <td width="105"> '.ValorRadioExFisico("ef35",$row['ef35']).' </td>			       
			     <td width="40"> </td>
			     <td width="150">Amigdalas</td>			     			     
			     <td width="105"> '.ValorRadioExFisico("ef36",$row['ef36']).' </td>			      
			     <td width="20"> </td>
               </tr>          
	           <tr> 
			     <td width="20"> </td>
			     <td width="150">Articulaciones</td>			     			     
			     <td width="105">'.ValorRadioExFisico("ef37",$row['ef37']).'</td>			      
			     <td width="40"> </td>
			     <td width="150">Inspeccion de Cuello</td>			     			     
			     <td width="105">'.ValorRadioExFisico("ef38",$row['ef38']).'</td>			      
			     <td width="40"> </td>
			     <td width="150">Neurologico</td>			     			     
			     <td width="105">'.ValorRadioExFisico("ef39",$row['ef39']).'</td>			      
			     <td width="20"> </td>
               </tr>                         
	           <tr> 
			     <td width="20"> </td>
			     <td width="150">Palpacion Cuello y tiroides</td>			     			     
			     <td width="105">'.ValorRadioExFisico("ef40",$row['ef40']).'</td>			      
			     <td width="40"> </td>
			     <td width="150">Esfera Mental</td>			     			     
			     <td width="105">'.ValorRadioExFisico("ef41",$row['ef41']).'</td>			      	     
			     <td width="40"> </td>
			     <td width="150"></td>			     			     
			     <td width="105"></td>			     
			     <td width="20"> </td>
               </tr>                           
              </table>
             <br>			  
			 <table width="100%"> 
			  <tr> <td width="30"> </td>
			       <td width="110"><b>Observaciones :</b></td><td> <textarea name="observa6" cols="88" rows="2">'.$row['observa6'].'</textarea> </td> </tr>
			 </table>			
			<br>
            <table width="100%">
	           <tr class="CabezoteTabla"> 
			     <td width="10"> </td>
			     <td width="37"> <img src="images/iconos/faq.png" width="32" height="32" border="0"> </td>
				 <td width="160"> <b>Concepto del Profesional</b> <td>
				 <td width="490">'.$clase->CrearCombo("conceptomedid","conceptomed","descripcion","conceptomedid",$row['conceptomedid'],"S","codigo").'<td>				 
				 <td width="40"> <input title="Guardar Historia" alt="Guardar Historia" src="images/iconos/guardar.png" type="image" width="32" height="32" border="0"> </td> 
				 <td width="40"> 
				    <a href="#top" title="Ir Arriba"><img src="images/iconos/top.png" width="24" height="24" border="0"></a> </td> 				 
    		     <td width="8"> </td>
			   </tr>	 			   
			</table>		
            <br>
			 <table width="100%"> 
			  <tr> <td width="30"> </td>
			       <td width="110"><b>Recomendaciones <br>(Certificado)</b></td><td> <textarea name="observa5" cols="88" rows="2">'.$row['observa5'].'</textarea> </td> </tr>
			 </table>			
			<table>
			   <tr>
    		    <td>  <button type="submit" name="guardar" tabindex="4" id="continuar" class="Botonverde"> Guardar </button>  </td>
				</form>
			  </tr>
			</table></div><br><br>';  //onclick="location.href=\'?opcion=eliminar&amp;id='.$row['ciudadid']
	 }
  }
 
 
 /////////////////////////////////////////  
  if($opcion == "guardaranteocupacionales")
  { 
     $id = $_POST['id'];
	 $fecdesde = substr($_POST['fecdesde'],6,4).'-'.substr($_POST['fecdesde'],3,2).'-'.substr($_POST['fecdesde'],0,2);
	 $fechasta = substr($_POST['fechasta'],6,4).'-'.substr($_POST['fechasta'],3,2).'-'.substr($_POST['fechasta'],0,2);	 
	 $empresa   = strtoupper($_POST['empresa']);
	 $ocupacion = strtoupper($_POST['ocupacion']);
	 $tiempo   = strtoupper($_POST['tiempo']);
	 $riesgos  = strtoupper($_POST['riesgos']);
	 $expuesto1  = strtoupper($_POST['expuesto1']);
	 $expuesto2  = strtoupper($_POST['expuesto2']);
	 $expuesto3  = strtoupper($_POST['expuesto3']);
	 $expuesto4  = strtoupper($_POST['expuesto4']);
	 
     $vsql = "INSERT INTO anteocupacionales(historiaid,fecdesde,fechasta,empresa,ocupacion,tiempo,riesgos,expuesto1,expuesto2,expuesto3,expuesto4,creador,momento) values(".$id.",
	          '".$fecdesde."','".$fechasta."','".$empresa."','".$ocupacion."','".$tiempo."','".$riesgos."','".$expuesto1."','".$expuesto2.
			  "','".$expuesto3."','".$expuesto4."','".$_SESSION['USERNAME']."',CURRENT_TIMESTAMP)";
	 $clase->EjecutarSQL($vsql);
 	 $clase->Aviso(1,"Antecedente Ocupacional Adicionado Exitosamente");  		
	 header("Location: historiacli.php?opcion=detalles&id=".$id);	 
  }

  /////////////////////////////////////////  
  if($opcion == "delanteocupacional")
  {
    $id = $_GET['id'];
	$hid = $clase->BDLockup($id,"anteocupacionales","anteid","historiaid");
    $vsql = "DELETE FROM anteocupacionales WHERE anteid=".$id;
	$clase->EjecutarSQL($vsql);
	$clase->Aviso(2,"Antecedente Ocupacional Eliminado Exitosamente");  		
	header("Location: historiacli.php?opcion=detalles&id=".$hid);
  }
  
  /////////////////////////////////////////  
  if($opcion == "antocupacionales")
  {
       $id  = $_GET['id'];
	   $diahoy = date("d/m/Y");
	   
	   echo'<form action="?opcion=guardaranteocupacionales" method="POST"> 
	        <input type="hidden" name="id" value="'.$id.'">
			<table width="500"> 
	           <tr height="25">
				 <td align="center"> <b> Ingreso de Antecedentes Ocupacionales </b> </td> 
			   </tr> 
			</table>
			<br>
			<center>
			<table width="500">   
			   <tr height="25"> 
				  <td align="left" width="70"> Desde : </td> 
				  <td align="left"> <input type="text" name="fecdesde" size="10" value="'.$diahoy.'"> </td>
				  <td width="100"></td> 
	              <td align="left"> Hasta : </td> 
				  <td align="left"> <input type="text" name="fechasta" size="10" value="'.$diahoy.'"> </td>
			   </tr>
			</table>
			<table width="500">
			  <tr>
			    <td width="70"> Empresa : </td>
				<td> <input type="text" name="empresa" size="59"> </td>
			  </tr>	    			
			  <tr>
			    <td width="70"> Ocupacion : </td>
				<td> <input type="text" name="ocupacion" size="59"> </td>
			  </tr>	    			
			  <tr>
			    <td width="50"> Tiempo : </td>
				<td> <input type="text" name="tiempo" size="59"> </td>
			  </tr>	    			
			  <tr>
			    <td width="50"> Riesgos : </td>
				<td> <input type="text" name="riesgos" size="59"> </td>
			  </tr>	    			
			  <tr>
			    <td width="50"> Expuesto  : </td>
				<td> 
				  <input type="checkbox" name="expuesto1" value="CHECKED"> Ergonometrico  
				  <input type="checkbox" name="expuesto2" value="CHECKED"> Fisiologicos
				  <input type="checkbox" name="expuesto3" value="CHECKED"> Psicosocial
				  <input type="checkbox" name="expuesto4" value="CHECKED"> Quimicos
				</td>
			  </tr>	    						  
			</table>
			<br> <center> <input type="submit" value="Guardar Antecedentes">
			</form>
			<br>';
		exit();		
  }

  /////////////////////////////////////////  
  if($opcion == "guardaraccidente")
  { 
     $id = $_POST['id'];
	 $fecha = substr($_POST['fecha'],6,4).'-'.substr($_POST['fecha'],3,2).'-'.substr($_POST['fecha'],0,2);
	 $descripcion  = strtoupper($_POST['descripcion']);
	 $accitrabajo = strtoupper($_POST['accitrabajo']);
	 $tipo   = strtoupper($_POST['tipo']);
	 $lesionparte  = strtoupper($_POST['lesionparte']);
	 
     $vsql = "INSERT INTO accidentestra(historiaid,descripcion,accitrabajo,fecha,tipo,lesionparte,creador,momento) values(".$id.",
	          '".$descripcion."','".$accitrabajo."','".$fecha."','".$tipo."','".$lesionparte."','".$_SESSION['USERNAME']."',CURRENT_TIMESTAMP)";
	 $clase->EjecutarSQL($vsql);
 	 $clase->Aviso(1,"Accidente de Trabajo Adicionado Exitosamente");  		
	 header("Location: historiacli.php?opcion=detalles&id=".$id);	 
  }

  /////////////////////////////////////////  
  if($opcion == "delaccidente")
  {
    $id = $_GET['id'];
	$hid = $clase->BDLockup($id,"anteocupacionales","anteid","historiaid");
    $vsql = "DELETE FROM anteocupacionales WHERE anteid=".$id;
	$clase->EjecutarSQL($vsql);
	$clase->Aviso(2,"Antecedente Ocupacional Eliminado Exitosamente");  		
	header("Location: historiacli.php?opcion=detalles&id=".$hid);
  }

  /////////////////////////////////////////  
  if($opcion == "antdetrabajo")
  {
       $id  = $_GET['id'];
	   echo'<table width="500"> 
	           <tr height="25">
				 <td align="center"> <b> Ingreso de Antecedentes de Trabajo / Enfermedad Profesional </b> </td> 
			   </tr> 
			</table>
			<br>
			<center>
			<form action="?opcion=guardaraccidente" method="POST"> 
	        <input type="hidden" name="id" value="'.$id.'">			
			<table width="500">
			  <tr>
			    <td width="70"> Accidente : </td>
				<td> <input type="text" name="descripcion" size="35"> 
				     <input type="checkbox" name="accitrabajo" value="checked"> Accidente de Trabajo  </td>
			  </tr>	    			
			  <tr>
			    <td width="70"> Tipo : </td>
				<td>  <input type="text" name="tipo" size="35">  
				     Fecha : <input type="text" name="fecha" size="10" value="'.date("d/m/Y").'">
				     </td>
			  </tr>	    			
			  <tr>
			    <td width="50"> Lesion : </td>
				<td> <input type="text" name="lesionparte" size="59"> </td>
			  </tr>	    			
			</table>
			<br> <center> <input type="submit" value="Guardar Accidentes"><br>';
		exit();		
  }
 
  
  /////////////////////////////////////////  
  if($opcion == "preeliminar")
  {
       $id  = $_GET['id'];
	   echo'<table width="400" bgcolor="#F5A9A9"> 
	           <tr height="25">
				 <td align="center"> <b> Est&aacute; seguro de Eliminar esta Historia Clinica? </b> </td> 
			   </tr> <tr height="25"> 
				  <td align="center"> Este proceso es Irreversible </td> 
			   </tr> <tr height="25"> 
			      <td align="center"> <b><a href="?opcion=eliminar&amp;id='.$id.'"> Eliminar Historia Clinica</a> </b> </td> 
			   </tr>
			</table>';
		exit();		
  }

  /////////////////////////////////////////  
  if($opcion == "eliminar")
  {
    $id = $_GET['id'];
    $vsql = "DELETE FROM historiacli WHERE historiaid=".$id;
	$clase->EjecutarSQL($vsql);
    $vsql = "DELETE FROM historiacliself WHERE historiaid=".$id;
	$clase->EjecutarSQL($vsql);
	$clase->Aviso(3,"Historia Clinica Eliminada Exitosamente");  		
	header("Location: historiacli.php");
  }

  /////////////////////////////////////////  
  if($opcion == "masregistros")
  {
    $actual = $_SESSION["NUMREGISTROSXCONSULTA"];
	$ahora = $actual + 50;
	$_SESSION["NUMREGISTROSXCONSULTA"] = $ahora; 
	header("Location: historiacli.php");
  }

  /////////////////////////////////////////  
  if($opcion == "encontrar")
  {
    $criterio = $_POST['criterio'];
    $vsql = "SELECT H.* , T1.nombre NombrePaciente , T2.nombre NombreMedico 
	         FROM historiacli H INNER JOIN terceros T1 ON (H.teridpaciente = T1.terid) LEFT JOIN terceros T2 ON (H.teridprof = T2.terid) 
	         WHERE T1.nit like '%".$criterio."%' OR T1.nombre like '%".$criterio."%' OR T2.nit like '%".$criterio."%' OR T2.nombre like '%".$criterio."%' 
			 OR H.conceptomed like '%".$criterio."%' ORDER BY H.momento DESC Limit 0,".$_SESSION["NUMREGISTROSXCONSULTA"];

    $_SESSION['SQL_HISTORIAS'] = $vsql;
	header("Location: historiacli.php");
  }

  /////////////////////////////////////////  
  if($opcion == "nofiltro")
  {
    $criterio = $_POST['criterio'];
  	$vsql = "SELECT * FROM historiacli ORDER BY fecha DESC limit 0,30";
	$_SESSION['SQL_HISTORIAS'] = "";
	header("Location: historiacli.php");
  }

  /////////////////////////////////////////  
  if(($opcion == "abrir")||($opcion == "cerrar"))
  {
    $id = $_GET['id'];
    
	if($opcion == "abrir")
	{
       $vsql = "SELECT COUNT(*) FROM historiacli WHERE estado='A' and historiaid=".$id;
	   $hayabiertas = $clase->SeleccionarUno($vsql);
	   
	   if($hayabiertas == 0)
	   {
	     $vsql = "UPDATE historiacli SET estado = 'A' WHERE historiaid=".$id;
         $clase->EjecutarSQL($vsql);  
	     $clase->Aviso(2,"La Historia Clinica ha Sido Abierta Exitosamente");  		
	   }
	}   
	if($opcion == "cerrar"){
	   $vsql = "UPDATE historiacli SET estado = 'C' WHERE historiaid=".$id;
       $clase->EjecutarSQL($vsql);  
	   $clase->Aviso(1,"La Historia Clinica se ha Cerrado con Exito");  		
	}   
		
	header("Location: historiacli.php?opcion=detalles&id=".$id);
  }

  /////////////////////////////////////////  
  if($opcion == "soloabiertas")
  {
    $criterio = $_POST['criterio'];
     	$vsql = "SELECT HC.* , T1.nombre NombrePaciente , T2.nombre NombreMedico FROM historiacli HC 
		         INNER JOIN terceros T1 ON (HC.teridpaciente = T1.terid) 
		         INNER JOIN terceros T2 ON (HC.teridprof = T2.terid) 
	             WHERE HC.estado='A' ORDER BY HC.momento DESC Limit 0,".$_SESSION["NUMREGISTROSXCONSULTA"];

	$_SESSION['SQL_HISTORIAS'] = $vsql;
	header("Location: historiacli.php");
  }

  /////////////////////////////////////////  
  if($opcion == "solohoy")
  {
    $fechahoy = date("Y-m-d");
    $criterio = $_POST['criterio'];
   	$vsql = "SELECT HC.* , T1.nombre NombrePaciente , T2.nombre NombreMedico FROM historiacli HC 
	         INNER JOIN terceros T1 ON (HC.teridpaciente = T1.terid) 
	         INNER JOIN terceros T2 ON (HC.teridprof = T2.terid) 
             WHERE HC.momento LIKE '".$fechahoy."%' Limit 0,".$_SESSION["NUMREGISTROSXCONSULTA"];
	$_SESSION['SQL_HISTORIAS'] = $vsql;
	header("Location: historiacli.php");
  }

  /////////////////////////////////////////  
  if($opcion == "")
  {
     $teridprof = $clase->BDLockup($_SESSION['USERNAME'],'terceros','username','terid');
	 $cont = $clase->Header("S","W"); ;  	 
	 $cont.='<table width="100%">
	           <tr class="CabezoteTabla"> 
			     <td width="10"> </td>
			     <td width="37"> <img src="images/iconos/historias.png" width="32" height="32" border="0"> </td>
				 <td width="400"> Historias Clinias. Usuario <b>'.$_SESSION['USERNAME'].'</b><td>
				 <td width="27"> <a href="?opcion=nuevo"> <img src="images/icononuevo.png" border="0"> </a> </td>
				 <form action="?opcion=encontrar" method="POST" name="x">
				 <td> <input type="text" name="criterio" size="30" placeholder="Criterio a Buscar" tabindex="1" id="default"> </td>
				 <td> <input type="submit" value="Encontrar"> </td> 
				 <td width="25" align="center"> <a href="?opcion=solohoy" title="Solo Historias de Hoy"> <img src="images/calendario.png" border="0"> </a> </td>
				 <td width="25" align="center"> <a href="?opcion=soloabiertas" title="Solo Historias Abiertas"> 
				 <img src="images/servicios.png" border="0" width="14" height="16"> </a> </td>';

	 if($_SESSION['SQL_HISTORIAS'] != "")
         $cont.='<td width="25"> <a href="?opcion=nofiltro"> <img src="images/nofiltro.png"> </a> </td>'; 

		$cont.=' </form>
    		     <td width="10"> </td>
			   </tr>	 			   
			 </table> ';	
	
	
    $vsql = $_SESSION['SQL_HISTORIAS'];
	if($vsql == "")
	{
    	$vsql = "SELECT HC.* , T1.nombre NombrePaciente , T2.nombre NombreMedico FROM historiacli HC 
		         INNER JOIN terceros T1 ON (HC.teridpaciente = T1.terid) 
		         LEFT JOIN terceros T2 ON (HC.teridprof = T2.terid)";
	    
		if($teridprof != "")	
		   $vsql.= " WHERE HC.teridprof = ".$teridprof;	
		     
		$vsql.= " ORDER BY momento DESC limit 0,".$_SESSION["NUMREGISTROSXCONSULTA"];
    }
	$conex  = $clase->Conectar();
    $result = mysql_query($vsql,$conex);

	 $cont.='<div style="overflow:auto; height:580px;width:796px;">
	          <table width="100%">
	           <tr class="TituloTabla"> 
			     <td width="10"> </td>
			     <td width="30">Est</td>			     
			     <td width="110"> Fecha / Hora </td>
				 <td width="250"> Nombre del Paciente </td>
				 <td width="110"> Examen </td>			
				 <td width="25"><img src="images/iconoimprimir.png" border="0"></td>
				 <td width="25"><img src="images/seleccion.png" border="0"></td>
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
				  <td width="30"> '.IconoEstado($row['estado']).' </td>
				  <td width="110"> '.$row['momento'].' </td>
				  <td width="250"> '.$row['NombrePaciente'].' </td>
				  <td width="110"> '.$row['tipoexamen'].' </td>
				  <td width="25"> <a href="#" OnClick="window.open(\'imphistoria.php?id='.$row['historiaid'].'\',\'ImpHC\',\'width=800,height=600\');"> <img src="images/iconoimprimir.png" border="0"> </td>				  
				  <td width="25"> <a href="?opcion=cambiar&amp;id='.$row['historiaid'].'" title="Modificar Historia" rel="facebox"> <img src="images/funciones.png" border="0"> </td>				  
				  <td width="25"> <a href="?opcion=detalles&amp;id='.$row['historiaid'].'" title="Diligenciar Historia"> <img src="images/seleccion.png" border="0"> </td>				  
				 <td width="20"> </td>				  
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
  
  ////////////////////////////////
  ////////////////////////////////

  //////////////////////////////////////////////////////////////////////////////   
  function ValorRadioExFisico($nombreRad,$valor)
  {	
	if($valor == "NO")
       $cont='<input type="radio" name="'.$nombreRad.'" value="NO" checked>No<input type="radio" value="AN" name="'.$nombreRad.'">An<input type="radio" value="NE" name="'.$nombreRad.'">NE';	
	if($valor == "AN")
       $cont='<input type="radio" name="'.$nombreRad.'" value="NO" >No<input type="radio" value="AN" name="'.$nombreRad.'" checked>An<input type="radio" value="NE" name="'.$nombreRad.'">NE';	
	if(($valor == "NE")||($valor == ""))
       $cont='<input type="radio" name="'.$nombreRad.'" value="NO" >No<input type="radio" value="AN" name="'.$nombreRad.'">An<input type="radio" value="NE" name="'.$nombreRad.'" checked>NE';	
	return($cont);	 
  } 
  
  //////////////////////////////////////////////////////////////////////////////   
  function FotoPaciente($rutafoto)
  {	
	if($rutafoto == "")
       $cont='<img src="fotos/nofoto.png" width="50" height="60" border="0">';	
	else      
	   $cont='<a href="fotos/'.$rutafoto.'" rel="facebox" title="Agrandar Foto"> <img src="fotos/'.$rutafoto.'" width="50" height="60" border="0"> </a>';    
	return($cont);	 
  }

  //////////////////////////////////////////////////////////////////////////////   
  function IconoEstado($estado)
  {
    if($estado == "A")	
	  $cont='<img src="images/iconoeditar.png" border="0">';
	else   
	  $cont='<img src="images/asentado.png" border="0">';	
	return($cont);  
  }      
  
  //////////////////////////////////////////////////////////////////////////////   
  function ListadoAnteOcupacionales($id)
  {
    $clase = new Sistema();
	$hay = $clase->SeleccionarUno("SELECT COUNT(*) cant FROM anteocupacionales WHERE historiaid=".$id);
	if($hay > 0)
	{
  	  $cont.='<table width="935">
	           <tr class="BarraDocumentos">
	            <td width="10"> </td>
	            <td width="20"> </td>				
	            <td width="20"> </td>					
				<td width="90"> <b>Desde </td>
			    <td width="90"> <b>Hasta </td>
			    <td width="120"> <b>Duracion </td>				
			    <td width="120"> <b>Hace </td>								
			    <td width="120"> <b>Empresa </td>
			    <td width="120"> <b>Ocupacion </td>
			    <td width="120"> <b>Tiempo </td>
			    <td width="120"> <b>Riesgos </td>
	            <td width="20"> </td>	
	            <td width="10"> </td>				
			   </tr>';
	  
	  $conex  = $clase->Conectar();
      $vsql='SELECT * , datediff(fechasta,fecdesde) diastrab , datediff(CURDATE(),fechasta) hacedias FROM anteocupacionales WHERE historiaid='.$id;
	  $result = mysql_query($vsql,$conex);
      while($row = mysql_fetch_array($result)) 
	  {
     	$cont.=' <tr class="BarraDocumentos">
		          <td width="10"> </td>
				  <td width="20"><a href="?opcion=delanteocupacional&id='.$row['anteid'].'" title="Editar"><img src="images/iconobuscar.png" border="0"></a></td>				  
                  <td width="20"><a href="?opcion=delanteocupacional&id='.$row['anteid'].'" title="Borrar"><img src="images/iconoborrar.png" border="0"></a></td>				  				  
				  <td width="60"> '.$row['fecdesde'].' </td>
				  <td width="60"> '.$row['fechasta'].' </td>
			      <td width="120"> Laboró '.$row['diastrab'].' dias</td>
				  <td width="120"> Hace '.$row['hacedias'].' dias</td>
				  <td width="120"> '.$row['empresa'].' </td>
				  <td width="120"> '.$row['ocupacion'].' </td>
                  <td width="120"> '.$row['tiempo'].' </td>				  
				  <td width="120"> '.$row['riesgos'].' </td>				  
                  <td width="20"><a href="?opcion=delanteocupacional&id='.$row['anteid'].'" title="Borrar"><img src="images/iconoborrar.png" border="0"></a></td>				  				  				  
		          <td width="10"> </td>
				</tr>';  
 	  }
	  $cont.='</table>';	  
	} /// Si Hay registros
	return($cont);
  }


  //////////////////////////////////////////////////////////////////////////////   
  function ListadoAccidentestra($id)
  {
    $clase = new Sistema();
	$hay = $clase->SeleccionarUno("SELECT COUNT(*) cant FROM accidentestra WHERE historiaid=".$id);
	if($hay > 0)
	{
  	  $cont.='<table width="935">
	           <tr class="BarraDocumentos">
	            <td width="10"> </td>
	            <td width="20"> </td>				
	            <td width="20"> </td>					
				<td width="100"> <b>Accidente </td>
			    <td width="40"> <b>Acc.Trab.</td>				
			    <td width="70"> <b>Fecha </td>								
			    <td width="70"> <b>Hace </td>
			    <td width="120"> <b>Tipo </td>
			    <td width="120"> <b>Lesion / Parte </td>
	            <td width="10"> </td>				
			   </tr>';
	  
	  $conex  = $clase->Conectar();
      $vsql='SELECT * , datediff(CURDATE(),fecha) hacedias FROM accidentestra WHERE historiaid='.$id;
	  $result = mysql_query($vsql,$conex);
      while($row = mysql_fetch_array($result)) 
	  {
     	if($row['accitrabajo'] == 'CHECKED')
		   $esaccidentetrab = "SI";
        else		   
		   $esaccidentetrab = "NO";		
		   
		$cont.=' <tr class="BarraDocumentos">
		          <td width="10"> </td>
				  <td width="20"><a href="?opcion=delaccidente&id='.$row['anteid'].'" title="Editar"><img src="images/iconobuscar.png" border="0"></a></td>				  
                  <td width="20"><a href="?opcion=delaccidente&id='.$row['anteid'].'" title="Borrar"><img src="images/iconoborrar.png" border="0"></a></td>				  				  
				  <td width="100"> '.$row['descripcion'].' </td>
				  <td width="40"> '.$esaccidentetrab.' </td>
			      <td width="70"> '.$row['fecha'].'</td>
				  <td width="70"> Hace '.$row['hacedias'].' dias</td>
				  <td width="120"> '.$row['tipo'].' </td>
				  <td width="120"> '.$row['lesionparte'].' </td>
		          <td width="10"> </td>
				</tr>';  
 	  }
	  $cont.='</table>';	  
	} /// Si Hay registros
	return($cont);
  }  
?> 