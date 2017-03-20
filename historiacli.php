<?PHP
  session_start(); 
  include("lib/Sistema.php");

  $clase = new Sistema();
  $clase->Nombredb = $_SESSION['DBNOMBRE']; 
 
  $avisos = ""; 
  $opcion = "";
  $opcion = $_GET["opcion"];


  /////////////////////////////////////////  
  if($opcion == "menuhc")
  {
     $historiaid = $_GET['id'];
     $estado     = $clase->BDLockup($historiaid,"historiacli","historiaid","estado");
     $cont='<table width="400">
	           <tr class="CabezoteTabla"> 
				 <td align="center"> <b>Opciones de Historia Clinica </b><td> 
			   </tr> 
			</table>
		    <table width="400">
		      <tr class="TablaDocsImPar">				    
				<td width="40" align="center"> <img src="images/iconoimprimir.png" border="0"> </td> 
 				<td width="360"> <a href="#" OnClick="window.open(\'imphistoria.php?id='.$historiaid.'\',\'ImpHC\',\'width=800,height=600\');"> Ver Historia Clinica en formato PDF </a> </td>				
			  </tr>
			  <tr class="TablaDocsPar">				
				<td width="40" align="center"> <img src="images/iconoimprimir.png" border="0"> </td> 			  
			    <td width="360"> <a href="#" OnClick="window.open(\'impconsentimiento.php?id='.$historiaid.'\',\'ImpCon\',\'width=800,height=600\');"> Ver Consentimiento Informado </a></td>			
			  <tr class="TablaDocsImPar">				
				<td width="40" align="center"> <img src="images/firma.png" border="0"> </td> 			  
			    <td width="360"> <a href="#" OnClick="window.open(\'remisiones.php?opcion=nuevo&id='.$historiaid.'\',\'Remi\',\'width=800,height=600\');"> Generar una Remision </a></td>				
			  </tr>';

	 ////////////////////////////////////////////////////////////////
	 // Solo Muestro el Certificado a Historias Cerradas	
	 ////////////////////////////////////////////////////////////////	  
	 if($estado == "C")
	 {
	 	$cont.='</table>
	 	        <table width="400">
	              <tr class="CabezoteTabla"> 
				   <td align="center"> <b> Emisi&oacute;n de Certificados </b> <td> 
			      </tr> 
			    </table>
			    <table width="400">
			      <tr class="TablaDocsImPar">				    
					<td width="40" align="center"> <img src="images/iconoimprimir.png" border="0"> </td> 
	 				<td width="360"> <a href="impcertificado2013.php?opcion=media&id='.$historiaid.'" target="_blank">Impresion Media Carta</a> </td>				
				  </tr>
				  <tr class="TablaDocsPar">				
					<td width="40" align="center"> <img src="images/iconoimprimir.png" border="0"> </td> 			  
				    <td width="360"> <a href="impcertificado2013.php?opcion=carta&id='.$historiaid.'" target="_blank">Impresion Hoja Carta Completa</a> </td>
				  </tr>';
	 }

	 $cont.='</table><br>';
	 echo $cont; exit();
  }


  /////////////////////////////////////////////////////////////////////////  
  if($opcion == "anteriores")
  {
     $id     = $_GET['id'];   
     $terid  = $clase->BDLockup($id,'historiacli','historiaid','teridpaciente');
     $vsql="SELECT HC.historiaid , HC.momento , MED.nombre , HC.tipoexamen
	        FROM historiacli HC INNER JOIN terceros MED ON (MED.terid = HC.teridprof) WHERE HC.historiaid <> ".$id." AND HC.teridpaciente=".$terid;
	 $conex = $clase->Conectar();
     $result = mysql_query($vsql,$conex); 	     
     $cant   = mysql_num_rows($result);
	 
	 if($cant == 0)
        $cont.='<br><br><center><h3>No hay Historias Clinicas Anteriores</h3><center><br><br>';
     else
	 {
  
	   
 	 $cont.='<center><h3>Ultimos Registros de Historia Clinica</h3><center>
			 <table width="600">';			 

	 while($row = mysql_fetch_array($result))
	 {
       $i++;
	   if($i%2 == 0)
		 $cont.='<tr class="TablaDocsImPar">';
	   else
		 $cont.='<tr class="TablaDocsPar">';	
	   $cont.='<td width="15"> </td>
			   <td width="170"> '.$row['momento'].' </td>
			   <td width="300"> '.$row['nombre'].' </td>
			   <td width="130"> '.$row['tipoexamen'].' </td>
			   <td width="50"> <a href="imphistoria2012.php?id='.$row['historiaid'].'" target="_blank">Visualizar</a> </td>			   
			   <td width="15"> </td></tr>';
	 }		     	   
	 $cont.='</table>';	 
	 
	}
  }  
 
  /////////////////////////////////////////////////////////////////////////  
  if($opcion == "eliminarparacli")
  {
    $paraid     = $_GET['paraid'];
	$id         = $clase->BDLockup($paraid,'paraclinicos','paraid','historiaid');
    $clase->EjecutarSQL("DELETE FROM paraclinicos WHERE paraid=".$paraid);
	 
	$clase->Aviso(2,"Examen Paraclinico eliminado Exitosamente");  		
    header("Location: historiacli.php?opcion=detalles&id=".$id);
 }


  /////////////////////////////////////////////////////////////////////////  
  if($opcion == "guardarparacli")
  {
    $id         = $_POST['id'];
    $examen     = strtoupper($_POST['examen']);
	$resultado  = strtoupper($_POST['resultado']);
       
    $clase->EjecutarSQL("INSERT INTO paraclinicos(historiaid,examen,resultado) values(".$id.",'".$examen."','".$resultado."')");
	 
	$clase->Aviso(1,"Examenes Paraclinicos Guardados Exitosamente");  		
    header("Location: historiacli.php?opcion=detalles&id=".$id);
 }
  
  /////////////////////////////////////////////////////////////////////////  
  if($opcion == "paraclinicos")
  {
    $id     = $_GET['id'];   
    $tipoex = $clase->BDLockup($id,'historiacli','historiaid','tipoexamen');
	$docuid = $clase->BDLockup($id,'historiacli','historiaid','docuid');
	$nitempresa = $clase->BDLockup($docuid,'documentos','docuid','nitempresa');
	$teridpac =   $clase->BDLockup($id,'historiacli','historiaid','teridpaciente');

	$cont.='<form action="?opcion=guardarparacli" method="POST">
	        <input type="hidden" name="id" value="'.$id.'">
	        <center><h3> Agregar Examenes Paraclinicos </h3><center>
			<br>
              <table width="600"> 
	            <tr class="TablaDocsPar">  
	              <td width="15"> </td>
			      <td width="205"> <b>Paraclinico:</b> </td> 
                  <td width="385"> <select name="examen">';

    $vsql="SELECT * FROM item I WHERE ( I.codigo <> '001.01' AND I.codigo <> '001.02' AND I.codigo <> '001.03' AND I.codigo <> '001.04')";
	$conex = $clase->Conectar();
    $result = mysql_query($vsql,$conex); 
    while($row = mysql_fetch_array($result))
	{
       $cont.='<option value="'.$row['descripcion'].'">'.$row['descripcion'].'</option>';
	}
	$cont.='</select></td></tr>
	            <tr class="TablaDocsPar">  
	              <td width="15"> </td>
			      <td width="385"> <b> Resultado : </b><br> </td> 
			      <td width="300"> <textarea name="resultado" cols="50" rows="4"></textarea> </td>
				</tr>
                <tr class="TablaDocsPar">  
	             <td width="15"> </td>
	             <td width="385"> </td> 
			     <td width="300"> <input type="submit" value="Guardar Paraclinicos"> </td>
			    </tr></table><br><br><h3> Listado de Resultados </h3><center>'; 				
  
    // Muestro los resultados 
    $vsql2 = "SELECT * FROM paraclinicos WHERE historiaid=".$id;
	$conex2 = $clase->Conectar();
    $result2 = mysql_query($vsql2,$conex2); 
    $cant2 = mysql_num_rows($result2);
	
    $cont.='<table width="700">';	     
	while($row2 = mysql_fetch_array($result2))
	{
          
	   $cont.='<tr class="TablaDocsPar">  
	              <td width="50"><a href="historiacli.php?opcion=eliminarparacli&paraid='.$row2['paraid'].'"><img src="images/eliminar.png" border="0"></a></td>
			      <td width="100"> <b>'.$row2['examen'].'</b> : </td>
				  <td width="500"> '.$row2['resultado'].' </td> 
				</tr>'; 

    }	
	$cont.='</table></form><br>';    
	/// Imprimo el resultado
	echo $cont;
	exit(0);		
  }

  /////////////////////////////////////////////////////////////////////////  
  if($opcion == "guardarcambio")
  {
    $id           = $_POST['id'];
    $tipoexamen   = $_POST['tipoexamen'];
    $empresa      = $_POST['empresa'];
    $profesional  = $_POST['profesional'];	
	
	/// Busco la Informacion Necesaria para los UPDATE	
	$prof_actual = $clase->BDLockup($id,"historiacli","historiaid","teridprof");
	$docuidps    = $clase->BDLockup($id,"historiacli","historiaid","docuid");
	$nitempresa = $clase->BDLockup($empresa,"terceros","nit","terid");
    $contratoid = $clase->SeleccionarUno("SELECT MAX(contratoid) FROM contratos WHERE terid=".$nitempresa);    
	
	/// Cambio el Tipo de Examen
	$clase->EjecutarSQL("UPDATE historiacli SET tipoexamen='".$tipoexamen."' WHERE historiaid=".$id);

    /// Cambio el Medico
	$clase->EjecutarSQL("UPDATE dedocumentos SET prorea = '".$profesional."' WHERE prorea = '".$prof_actual."' AND docuid = ".$docuidps);
    $clase->EjecutarSQL("UPDATE historiacli SET teridprof=".$profesional." WHERE historiaid=".$id);
	
	/// Cambio la Empresa en la Tabla Documento	
    $clase->EjecutarSQL("UPDATE documentos SET nitempresa='".$empresa."' , contratoid = ".$contratoid." WHERE docuid=".$docuidps);
	
	$clase->Aviso(1,"Datos de la Historia y Certificado Cambiados con Exito");  		
    header("Location: historiacli.php");
 }
  
  /////////////////////////////////////////////////////////////////////////  
  if($opcion == "cambiar")
  {
    $id     = $_GET['id'];   
    $tipoex = $clase->BDLockup($id,'historiacli','historiaid','tipoexamen');
	$docuid = $clase->BDLockup($id,'historiacli','historiaid','docuid');
	$teridprof = $clase->BDLockup($id,'historiacli','historiaid','teridprof');	
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

    if($tipoex == "APTITUD DE TRABAJO EN ALTURAS")		  	   
	  $cont.=' <option value="APTITUD DE TRABAJO EN ALTURAS" selected> Examen de APTITUD DE TRABAJO EN ALTURAS </option>';
	else
      $cont.=' <option value="APTITUD DE TRABAJO EN ALTURAS"> Examen de APTITUD DE TRABAJO EN ALTURAS </option>';	
    
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
			<tr class="TablaDocsImPar">
              <td width="15"> </td>
			    <td> Profesional </td> 
			    <td>'; 

	   $vsql="SELECT T.terid , T.nombre
		         FROM terceros T INNER JOIN clasificater CT ON (T.clasificaterid = CT.clasificaterid)
				 LEFT JOIN especialidades E ON (E.especialidadid = T.especialidadid)
     	         WHERE CT.codigo = '03' ORDER BY 2 ASC";

	   $conex = $clase->Conectar();
       $result = mysql_query($vsql,$conex); 
       $cont.= '<SELECT name="profesional">';

	   while($row = mysql_fetch_array($result))
	   {
		  if($row['terid'] == $teridprof) 
		    $cont.='<option value="'.$row['terid'].'" selected> '.$row['nombre'].'</option>';		 		
		  else
		    $cont.='<option value="'.$row['terid'].'"> '.$row['nombre'].'</option>';		 				  	
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
   		     effcard,effresp,lateralidad,fuma,bebe,deporte,ef1,ef2,ef3,ef4,ef5,ef6,ef7,ef8,ef9,ef10,ef11,ef12,ef13,ef14,ef15,ef16,ef17,ef18,ef19,
			 ef20,ef21,ef22,ef23,ef24,ef25,ef26,ef27,ef28,ef29,ef30,ef31,ef32,ef33,ef34,ef35,ef36,ef37,ef38,ef39,ef40,ef41,codcie,conceptomedid,observa6) 
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
			 ,'".strtoupper($_POST['effcard'])."','".strtoupper($_POST['effresp'])."','".strtoupper($_POST['lateralidad'])."'
			 ,'".strtoupper($_POST['fuma'])."','".strtoupper($_POST['bebe'])."','".strtoupper($_POST['deporte'])."'
			 ,'".strtoupper($_POST['ef1'])."','".strtoupper($_POST['ef2'])."'
			 ,'".strtoupper($_POST['ef3'])."','".strtoupper($_POST['ef4'])."','".strtoupper($_POST['ef5'])."','".strtoupper($_POST['ef6'])."'
			 ,'".strtoupper($_POST['ef7'])."','".strtoupper($_POST['ef8'])."','".strtoupper($_POST['ef9'])."','".strtoupper($_POST['ef10'])."'
			 ,'".strtoupper($_POST['ef11'])."','".strtoupper($_POST['ef12'])."','".strtoupper($_POST['ef13'])."','".strtoupper($_POST['ef14'])."'
			 ,'".strtoupper($_POST['ef15'])."','".strtoupper($_POST['ef16'])."','".strtoupper($_POST['ef17'])."','".strtoupper($_POST['ef18'])."'
			 ,'".strtoupper($_POST['ef19'])."','".strtoupper($_POST['ef20'])."','".strtoupper($_POST['ef21'])."','".strtoupper($_POST['ef22'])."'
			 ,'".strtoupper($_POST['ef23'])."','".strtoupper($_POST['ef24'])."','".strtoupper($_POST['ef25'])."','".strtoupper($_POST['ef26'])."'
			 ,'".strtoupper($_POST['ef27'])."','".strtoupper($_POST['ef28'])."','".strtoupper($_POST['ef29'])."','".strtoupper($_POST['ef30'])."'
			 ,'".strtoupper($_POST['ef31'])."','".strtoupper($_POST['ef32'])."','".strtoupper($_POST['ef33'])."','".strtoupper($_POST['ef34'])."'
			 ,'".strtoupper($_POST['ef35'])."','".strtoupper($_POST['ef36'])."','".strtoupper($_POST['ef37'])."','".strtoupper($_POST['ef38'])."'
			 ,'".strtoupper($_POST['ef39'])."','".strtoupper($_POST['ef40'])."','".strtoupper($_POST['ef41'])."','".strtoupper($_POST['codcie'])."'
			 ,".strtoupper($conceptox).",'".strtoupper($_POST['observa6'])."')";

    $cant = $clase->EjecutarSQL($vsql);

    /// Si el Concepto medico No esta y se digita abajo
	$nconceptopro = $_POST['nconceptopro'];
	if($nconceptopro != "")
	{
	   $idconce  = $clase->SeleccionarUno("SELECT MAX(conceptomedid) FROM conceptomed");
	   $codconce = $clase->BDLockup($idconce,'conceptomed','conceptomedid','codigo');
	   $ncod = $codconce + 1;
	   $clase->EjecutarSQL("INSERT INTO conceptomed(codigo,descripcion) values('".$ncod."','".strtoupper($nconceptopro)."')");
	   $clase->EjecutarSQL("UPDATE historiacli SET conceptomed = '".$ncod."' WHERE historiaid=".$id);
	   $concid =  $clase->BDLockup($ncod,'conceptomed','codigo','conceptomedid');
	   $clase->EjecutarSQL("UPDATE historiaself SET conceptomedid = ".$concid." WHERE historiaid=".$id);	   
	}	
	else
	{
	   $codConcepto = $clase->BDLockup($conceptox,'conceptomed','conceptomedid','codigo'); 
	   $clase->EjecutarSQL("UPDATE historiacli SET conceptomed = '".$codConcepto."' WHERE historiaid=".$id);	
	}
	
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
    
    // Si es un medico - la Marca como vista por él
    if($_SESSION['ROL'] == "MED")
    {	
       $teridprofesional = $clase->BDLockup($_SESSION['USERNAME'],"usuarios","username","teridprof");
       $vsql2 = "UPDATE historiacli SET usuariomed = '".$_SESSION['USERNAME']."' , teridprof = ".$teridprofesional." , momento2 = CURRENT_TIMESTAMP, momento3 = NULL WHERE historiaid=".$id; 
       $clase->EjecutarSQL($vsql2);
    }

    // Imprimi en Pantalla
    $cont = $clase->Header("N","W");
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
				      <img src="images/iconos/vistaprevia.png" width="32" height="32" border="0"></a> </td>
                 <td> <a href="?opcion=anteriores&id='.$id.'"" rel="facebox" title="Historias Anteriores">
				      <img src="images/iconos/hcanteriores.png" width="32" height="32" border="0"></a> </td>';
					  
					  
	 if($row['estado'] == 'A'){
    	 $cont.='    <td> <a href="?opcion=cerrar&id='.$id.'" title="Cerrar Historia">
				      <img src="images/iconos/cerrar.png" width="32" height="32" border="0"></a> </td>';
	 }
	 else{
	     $cont.='    <td> <a href="?opcion=abrir&id='.$id.'" title="Abrir Historia">
				      <img src="images/iconos/conceptomed.png" width="32" height="32" border="0"></a> </td>';
	 }
	 			      
	 $cont.=' <td> <a href="impcertificado2013.php?&id='.$id.'" title="Visualizar Certificado" rel="facebox">
				      <img src="images/iconos/certificado.png" width="32" height="32" border="0"></a> </td>				 
				 <td> <a href="?opcion=paraclinicos&id='.$id.'" title="Resultados Paraclinicos" rel="facebox">
				      <img src="images/iconos/paraclinicos.png" width="32" height="32" border="0"></a> </td>
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
			
			<div style="overflow:auto; height:700px;width:950px;">
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
				  document.x.efimc.value = document.x.efimc.value.substring(0,5);
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
			     <td width="20"> </td>
			     <td width="100"> Enfermedades Cong&eacute;nitas </td>
			     <td width="250"> <input type="text" size="120" name="antefam01" value="'.$row['antefam01'].'"> </td>			     
				<td width="20"> </td>				 
               </tr><tr>
			   <td width="35"> </td>
			     <td width="100"> Alergias </td>
			     <td width="250"> <input type="text" size="120" name="antefam02" value="'.$row['antefam02'].'"> </td>			     
    		     <td width="20"> </td>
			   </tr><tr> 
			     <td width="35"> </td>
			     <td width="100"> Enfermedades Pulmonares </td>
			     <td width="250"> <input type="text" size="120" name="antefam03" value="'.$row['antefam03'].'"> </td>			     
			     <td width="20"> </td>
			  </tr><tr>	
			     <td width="35"> </td>  
			     <td width="100"> Asma </td>
			     <td width="250"> <input type="text" size="120" name="antefam04" value="'.$row['antefam04'].'"> </td>			     
    		     <td width="20"> </td>
			   </tr><tr> 
			     <td width="35"> </td>
			     <td width="100"> Tuberculosis </td>
			     <td width="250"> <input type="text" size="120" name="antefam05" value="'.$row['antefam05'].'"> </td>			     
				 <td width="20"> </td>				 
			   </tr><tr>
				 <td width="35"> </td>
			     <td width="100"> Hipertension </td>
			     <td width="250"> <input type="text" size="120" name="antefam06" value="'.$row['antefam06'].'"> </td>			     
    		     <td width="20"> </td>
			   </tr><tr> 
			     <td width="35"> </td>
			     <td width="100"> Cardiopatias </td>
			     <td width="250"> <input type="text" size="120" name="antefam07" value="'.$row['antefam07'].'"> </td>			     
				 <td width="20"> </td>
			   </tr><tr>
				 <td width="35"> </td>
			     <td width="100"> E.C.V. </td>
			     <td width="250"> <input type="text" size="120" name="antefam08" value="'.$row['antefam08'].'"> </td>			     
    		     <td width="20"> </td>
			   </tr><tr> 
			     <td width="35"> </td>
			     <td width="100"> Diabetes </td>
			     <td width="250"> <input type="text" size="120" name="antefam09" value="'.$row['antefam09'].'"> </td>			     
				 <td width="20"> </td>
			     </tr><tr>
				 <td width="35"> </td>
			     <td width="100"> Cancer </td>
			     <td width="250"> <input type="text" size="120" name="antefam10" value="'.$row['antefam10'].'"> </td>			     
    		     <td width="20"> </td>
			   </tr>	 			   
	           <tr> 
			     <td width="35"> </td>
			     <td width="100"> Osteomusculares </td>
			     <td width="250"> <input type="text" size="120" name="antefam11" value="'.$row['antefam11'].'"> </td>			     
				 <td width="20"> </td>
			     </tr><tr>
				 <td width="35"> </td>
			     <td width="100"> Artritis </td>
			     <td width="250"> <input type="text" size="120" name="antefam12" value="'.$row['antefam12'].'"> </td>			     
    		     <td width="20"> </td>
			   </tr>	 			   
	           <tr> 
			     <td width="35"> </td>
			     <td width="100"> Varices </td>
			     <td width="250"> <input type="text" size="120" name="antefam13" value="'.$row['antefam13'].'"> </td>			     
				 <td width="20"> </td>
			     </tr><tr>
				 <td width="35"> </td>
			     <td width="100"> Sindrome Convulsivo </td>
			     <td width="250"> <input type="text" size="120" name="antefam14" value="'.$row['antefam14'].'"> </td>			     
    		     <td width="20"> </td>
			   </tr>	 			   
	           <tr> 
			     <td width="35"> </td>
			     <td width="100"> Psiquiatricos </td>
			     <td width="250"> <input type="text" size="120" name="antefam15" value="'.$row['antefam15'].'"> </td>			     
				 <td width="20"> </td>
			     </tr><tr>
				 <td width="35"> </td>
			     <td width="100"> Otros </td>
			     <td width="250"> <input type="text" size="120" name="antefam16" value="'.$row['antefam16'].'"> </td>			     
    		     <td width="20"> </td>
			   </tr>	 			   
			 </table>			
			 <table width="100%"> 
			  <tr> <td width="30"> </td>
			       <td width="110"><b>Observaciones</b></td><td> <textarea name="observa1" cols="91" rows="2">'.$row['observa1'].'</textarea> </td> </tr>
			 </table>
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
			     <td width="100">Enf. de la Piel </td>			     			     
			     <td width="20"> </td>
			     <td width="10"><input type="checkbox" name="anteper07" value="checked"  '.$row['anteper07'].'></td>			     
			     <td width="100">Otitis </td>			     			     
			     <td width="20"> </td>
			     <td width="10"><input type="checkbox" name="anteper13" value="checked"  '.$row['anteper13'].'></td>			     
			     <td width="100">Cardiovasculares </td>			     			     
			     <td width="20"> </td>
			     <td width="10"><input type="checkbox" name="anteper19" value="checked"  '.$row['anteper19'].'></td>			     
			     <td width="100"> Lumbalgia </td>			     			     
			     <td width="20"> </td>
			     <td width="10"><input type="checkbox" name="anteper25" value="checked"  '.$row['anteper25'].'></td>			     
			     <td width="100">Cancer </td>			     			     
			     <td width="20"> </td>
			     <td width="10"><input type="checkbox" name="anteper31" value="checked"  '.$row['anteper31'].'></td>			     
			     <td width="100">Transfusionales </td>			     			     
    		     <td width="20"> </td>
			   </tr>	 			  
	           <tr> 
			     <td width="20"> </td>
			     <td width="10"><input type="checkbox" name="anteper02" value="checked" '.$row['anteper02'].'></td>			     
			     <td width="100">Alergias </td>			     			     
			     <td width="20"> </td>
			     <td width="10"><input type="checkbox" name="anteper08" value="checked" '.$row['anteper08'].'></td>			     
			     <td width="100">Hipoacusia </td>			     			     
			     <td width="20"> </td>
			     <td width="10"><input type="checkbox" name="anteper14" value="checked" '.$row['anteper14'].'></td>			     
			     <td width="100">Acidocepticas </td>			     			     
			     <td width="20"> </td>
			     <td width="10"><input type="checkbox" name="anteper20" value="checked" '.$row['anteper20'].'></td>			     
			     <td width="100">Hernias</td>			     			     
			     <td width="20"> </td>
			     <td width="10"><input type="checkbox" name="anteper26" value="checked" '.$row['anteper26'].'></td>			     
			     <td width="100">Cirugias </td>			     			     
			     <td width="20"> </td>
			     <td width="10"><input type="checkbox" name="anteper32" value="checked" '.$row['anteper32'].'></td>			     
			     <td width="100">E.T.S. </td>			     			     
    		     <td width="20"> </td>
			   </tr>	 			  
	           <tr> 
			     <td width="20"> </td>
			     <td width="10"><input type="checkbox" name="anteper03" value="checked" '.$row['anteper03'].'></td>			     
			     <td width="100">Cefalea </td>			     			     
			     <td width="20"> </td>
			     <td width="10"><input type="checkbox" name="anteper09" value="checked" '.$row['anteper09'].'></td>			     
			     <td width="100">Enf. Tiroideas</td>			     			     
			     <td width="20"> </td>
			     <td width="10"><input type="checkbox" name="anteper15" value="checked" '.$row['anteper15'].'></td>			     
			     <td width="100">Hepatitis </td>			     			     
			     <td width="20"> </td>
			     <td width="10"><input type="checkbox" name="anteper21" value="checked" '.$row['anteper21'].'></td>			     
			     <td width="100">Varicocele </td>			     			     
			     <td width="20"> </td>
			     <td width="10"><input type="checkbox" name="anteper27" value="checked" '.$row['anteper27'].'></td>			     
			     <td width="100">Traumatico </td>			     			     
			     <td width="20"> </td>
			     <td width="10"></td>			     
			     <td width="100"> </td>			     			     
    		     <td width="20"> </td>
			   </tr>	 			  
	           <tr> 
			     <td width="20"> </td>
			     <td width="10"><input type="checkbox" name="anteper04" value="checked" '.$row['anteper04'].'></td>			     
			     <td width="100">Defecto Visual </td>			     			     
			     <td width="20"> </td>
			     <td width="10"><input type="checkbox" name="anteper10" value="checked" '.$row['anteper10'].'></td>			     
			     <td width="100">Asma </td>			     			     
			     <td width="20"> </td>
			     <td width="10"><input type="checkbox" name="anteper16" value="checked" '.$row['anteper16'].'></td>			     
			     <td width="100">Colitis </td>			     			     
			     <td width="20"> </td>
			     <td width="10"><input type="checkbox" name="anteper22" value="checked" '.$row['anteper22'].'></td>			     
			     <td width="100">Artritis </td>			     			     
			     <td width="20"> </td>
			     <td width="10"><input type="checkbox" name="anteper28" value="checked" '.$row['anteper28'].'></td>			     
			     <td width="100">Farmacologico</td>			     			     
			     <td width="20"> </td>
			     <td width="10"></td>			     
			     <td width="100"> </td>			     			     
    		     <td width="20"> </td>
			   </tr><tr> 
			     <td width="20"> </td>
			     <td width="10"><input type="checkbox" name="anteper05" value="checked" '.$row['anteper05'].'></td>			     
			     <td width="100">Sinusitis </td>			     			     
			     <td width="20"> </td>
			     <td width="10"><input type="checkbox" name="anteper11" value="checked" '.$row['anteper11'].'></td>			     
			     <td width="100">Bronquitis </td>			     			     
			     <td width="20"> </td>
			     <td width="10"><input type="checkbox" name="anteper17" value="checked" '.$row['anteper17'].'></td>			     
			     <td width="100">Inf. Urinarias</td>			     			     
			     <td width="20"> </td>
			     <td width="10"><input type="checkbox" name="anteper23" value="checked" '.$row['anteper23'].'></td>			     
			     <td width="100">Varices MM.II </td>			     			     
			     <td width="20"> </td>
			     <td width="10"><input type="checkbox" name="anteper29" value="checked" '.$row['anteper29'].'></td>			     
			     <td width="100">Convulsiones </td>			     			     
			     <td width="20"> </td>
			     <td width="10"></td>			     
			     <td width="100"> </td>			     			     
    		     <td width="20"> </td>
			   </tr>	 			  
			   </tr><tr> 
			     <td width="20"> </td>
			     <td width="10"><input type="checkbox" name="anteper06" value="checked" '.$row['anteper06'].'></td>			     
			     <td width="100">Displidemias </td>			     			     
			     <td width="20"> </td>
			     <td width="10"><input type="checkbox" name="anteper12" value="checked" '.$row['anteper12'].'></td>			     
			     <td width="100">HTA </td>			     			     
			     <td width="20"> </td>
			     <td width="10"><input type="checkbox" name="anteper18" value="checked" '.$row['anteper18'].'></td>			     
			     <td width="100">Insuficiencia Renal</td>			     			     
			     <td width="20"> </td>
			     <td width="10"><input type="checkbox" name="anteper24" value="checked" '.$row['anteper24'].'></td>			     
			     <td width="100">Sind. Tunel Carpo</td>			     			     
			     <td width="20"> </td>
			     <td width="10"><input type="checkbox" name="anteper30" value="checked" '.$row['anteper30'].'></td>			     
			     <td width="100">Paralisis </td>			     			     
			     <td width="20"> </td>
			     <td width="10"> </td>			     
			     <td width="100"> </td>			     			     
    		     <td width="20"> </td>
			   </tr>	 			  
            </table>
			<br>
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
			     <td width="100">Piel y Faneras</td>			     			     
			     <td width="20"> </td>
			     <td width="10"><input type="checkbox" name="revsis02" value="checked" '.$row['revsis03'].'></td>			     
			     <td width="100">ORL</td>			     			     
			     <td width="20"> </td>
			     <td width="10"><input type="checkbox" name="revsis03" value="checked" '.$row['revsis05'].'></td>			     
			     <td width="100">Respiratorio </td>			     			     
			     <td width="20"> </td>
			     <td width="10"><input type="checkbox" name="revsis04" value="checked" '.$row['revsis07'].'></td>			     
			     <td width="100">GenitoUrinario </td>			     			     
			     <td width="20"> </td>
			     <td width="10"><input type="checkbox" name="revsis05" value="checked" '.$row['revsis09'].'></td>			     
			     <td width="100">Hematologico </td>			     			     
			     <td width="20"> </td>
			     <td width="10"><input type="checkbox" name="revsis06" value="checked" '.$row['revsis11'].'></td>			     
			     <td width="100">Neurol&oacute;gico </td>			     			     
    		     <td width="20"> </td>
			   </tr>	 			  
	           <tr> 
			     <td width="20"> </td>
			     <td width="10"><input type="checkbox" name="revsis07" value="checked" '.$row['revsis02'].'></td>			     
			     <td width="100">Visual </td>			     			     
			     <td width="20"> </td>
			     <td width="10"><input type="checkbox" name="revsis08" value="checked" '.$row['revsis04'].'></td>			     
			     <td width="100">CardioVascular </td>			     			     
			     <td width="20"> </td>
			     <td width="10"><input type="checkbox" name="revsis09" value="checked" '.$row['revsis06'].'></td>			     
			     <td width="100">GastroIntestinal </td>			     			     
			     <td width="20"> </td>
			     <td width="10"><input type="checkbox" name="revsis10" value="checked" '.$row['revsis08'].'></td>			     
			     <td width="100">OsteoMuscular </td>			     			     
			     <td width="20"> </td>
			     <td width="10"><input type="checkbox" name="revsis11" value="checked" '.$row['revsis10'].'></td>			     
			     <td width="100">Inmunologico </td>			     			     
			     <td width="20"> </td>
			     <td width="10"><input type="checkbox" name="revsis12" value="checked" '.$row['revsis12'].'></td>			     
			     <td width="100">Otros </td>			     			     
    		     <td width="20"> </td>
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
			     <td width="40">Peso (en Kg)</td>			     			     
			     <td width="60"><input type="text" name="efpeso" size="3" value="'.$row['efpeso'].'"></td>	
				 <td width="20"></td>		     			     			     
			     <td width="40">Talla (en M): </td>			     			     
			     <td width="60"><input type="text" name="eftalla" size="3"  value="'.$row['eftalla'].'"></td>			     			     			     
				 <td width="20"></td>
			     <td width="40">I.M.C. : </td>			     			     
			     <td width="60"><input type="text" name="efimc" size="7"  value="'.$row['efimc'].'" OnFocus="CalcularIMC();">%</td>			     			     			     
				 <td width="20"></td>
			     <td width="40">T.A.: </td>			     			     
			     <td width="60"><input type="text" name="eftart" size="3"  value="'.$row['eftart'].'" OnFocus="CalcularIMC();"></td>			     			     			     
				 <td width="20"></td>
			     <td width="40">F.C. x/min : </td>			     			     
			     <td width="60"><input type="text" name="effcard" size="3"  value="'.$row['effcard'].'"></td>			     			     			     
				 <td width="20"></td>
			     <td width="40">F.R. x/min: </td>			     			     
			     <td width="60"><input type="text" name="effresp" size="3"  value="'.$row['effresp'].'"></td>			     			     			     
				 <td width="20"></td>			     
               </tr>
            </table>   
            <table width="100%">
	           <tr height="40"> 
			     <td width="20"> </td>
			     <td width="40">Lateralidad</td>			     			     
			     <td width="60">'.$clase->CrearCombo("lateralidad","lateralidad","descripcion","codigo",$row['lateralidad'],"S","codigo").'</td>	
				 <td width="20"></td>		     			     			     
			     <td width="40">Habito Cigarrillo</td>			     			     			     
				 <td width="60">'.$clase->CrearCombo("fuma","habitos","descripcion","codigo",$row['fuma'],"S","codigo").'</td>     
				 <td width="20"></td>
			     <td width="40">Habito Licor</td>			     			     
			     <td width="60">'.$clase->CrearCombo("bebe","habitos","descripcion","codigo",$row['bebe'],"S","codigo").'</td>			     		
				 <td width="20"></td>
			     <td width="40">Habitos Deportivos</td>			     			     
			     <td width="60">'.$clase->CrearCombo("deporte","habitos","descripcion","codigo",$row['deporte'],"S","codigo").'</td>
				 <td width="20"></td>
               </tr>
            </table>   
            <table width="100%">
	           <tr> 
			     <td width="20"> </td>
			     <td width="150">Estado Nutricional</td>			     			     
			     <td width="250"> '.ValorRadioExFisico("ef1",$row['ef1']).' </td>			       
			     <td width="20"> </td>
			  </tr><tr>	 
			     <td width="20"> </td>			  
			     <td width="150"> Piel y Faneras </td>			     			     
			     <td width="250"> '.ValorRadioExFisico("ef2",$row['ef2']).' </td>			       
			     <td width="20"> </td>
			  </tr><tr>	 
			     <td width="20"> </td>			  				 
			     <td width="150"> Craneo </td>			     			     
			     <td width="250"> '.ValorRadioExFisico("ef3",$row['ef3']).' </td>			       
			     <td width="20"> </td>
			  </tr><tr>	 
			     <td width="20"> </td>			  
			     <td width="150">Cara</td>			     			     
			     <td width="250"> '.ValorRadioExFisico("ef4",$row['ef4']).' </td>			       
			     <td width="20"> </td>
			  </tr><tr>	 
			     <td width="20"> </td>			  
			     <td width="150">Parpados</td>			     			     
			     <td width="250"> '.ValorRadioExFisico("ef5",$row['ef5']).' </td>			       
			     <td width="20"> </td>
			  </tr><tr>	 
			     <td width="20"> </td>			  
			     <td width="150">Pupilas</td>			     			     
			     <td width="250"> '.ValorRadioExFisico("ef6",$row['ef6']).' </td>			       
			     <td width="20"> </td>
			  </tr><tr>	 
			     <td width="20"> </td>			  
			     <td width="150">Corneas</td>			     			     
			     <td width="105"> '.ValorRadioExFisico("ef7",$row['ef7']).' </td>			       
			     <td width="20"> </td>
			  </tr><tr>	 
			     <td width="20"> </td>			  				 
			     <td width="150">Conjuntivas</td>			     			     
			     <td width="250"> '.ValorRadioExFisico("ef8",$row['ef8']).' </td>			       
			     <td width="20"> </td>
			  </tr><tr>	 
			     <td width="20"> </td>			  
			     <td width="150">Nariz</td>			     			     
			     <td width="250"> '.ValorRadioExFisico("ef9",$row['ef9']).' </td>			       
			  </tr><tr>	 
			     <td width="20"> </td>			  
			     <td width="150">Boca</td>			     			     
			     <td width="250"> '.ValorRadioExFisico("ef10",$row['ef10']).' </td>			       
			     <td width="20"> </td>
			  </tr><tr>	 
			     <td width="20"> </td>			  
			     <td width="150">Faringe</td>			     			     
			     <td width="250"> '.ValorRadioExFisico("ef11",$row['ef11']).' </td>			       
			     <td width="20"> </td>
			  </tr><tr>	 
			     <td width="20"> </td>			  
			     <td width="150">Inspeccion Ext Oidos</td>			     			     
			     <td width="250"> '.ValorRadioExFisico("ef12",$row['ef12']).' </td>			       
			     <td width="20"> </td>
			  </tr><tr>	 
			     <td width="20"> </td>			  
			     <td width="150">Otoscopia</td>			     			     
			     <td width="250"> '.ValorRadioExFisico("ef13",$row['ef13']).' </td>			       
			     <td width="20"> </td>
			  </tr><tr>	 
			     <td width="20"> </td>			  
			     <td width="150">Inspeccion Cuello</td>			     			     
			     <td width="250"> '.ValorRadioExFisico("ef14",$row['ef14']).' </td>			       
			     <td width="20"> </td>
			  </tr><tr>	 
			     <td width="20"> </td>			  
			     <td width="150">Palpacion Cuello y Tiroides</td>			     			     
			     <td width="250"> '.ValorRadioExFisico("ef15",$row['ef15']).' </td>			       
			     <td width="20"> </td>
			  </tr><tr>	 
			     <td width="20"> </td>			  
			     <td width="150"> Inspeccion Torax </td>			     			     
			     <td width="250"> '.ValorRadioExFisico("ef16",$row['ef16']).' </td>			       
			  </tr><tr>	 
			     <td width="20"> </td>			  
			     <td width="150"> Palpacion Torax </td>			     			     
			     <td width="250"> '.ValorRadioExFisico("ef17",$row['ef17']).' </td>			       
			     <td width="20"> </td>
			  </tr><tr>	 
			     <td width="20"> </td>			  
			     <td width="150"> Auscultacion Torax </td>			     			     
			     <td width="250"> '.ValorRadioExFisico("ef18",$row['ef18']).' </td>			       
			     <td width="20"> </td>
			  </tr><tr>	 
			     <td width="20"> </td>			  
			     <td width="150"> Inspeccion Abdominal </td>			     			     
			     <td width="250"> '.ValorRadioExFisico("ef19",$row['ef19']).' </td>			       
			     <td width="20"> </td>
			  </tr><tr>	 
			     <td width="20"> </td>			  
			     <td width="150"> Palpacion Abdominal </td>			     			     
			     <td width="250"> '.ValorRadioExFisico("ef20",$row['ef20']).' </td>			       
			     <td width="20"> </td>
			  </tr><tr>	 
			     <td width="20"> </td>			  				 
			     <td width="150"> Inspeccion Columna Vert </td>			     			     
			     <td width="250"> '.ValorRadioExFisico("ef21",$row['ef21']).' </td>			       
			     <td width="20"> </td>
			  </tr><tr>	 
			     <td width="20"> </td>			  
			     <td width="150">Palpacion Columna Vert</td>			     			     
			     <td width="250"> '.ValorRadioExFisico("ef22",$row['ef22']).' </td>			       
			     <td width="20"> </td>
			  </tr><tr>	 
			     <td width="20"> </td>			  
			     <td width="150">Inspeccion Miembros Superiores</td>			     			     
			     <td width="250"> '.ValorRadioExFisico("ef23",$row['ef23']).' </td>			       
			     <td width="20"> </td>
			  </tr><tr>	 
			     <td width="20"> </td>			  				 
			     <td width="150">Pulso Radial</td>			     			     
			     <td width="250"> '.ValorRadioExFisico("ef24",$row['ef24']).' </td>			       
			     <td width="20"> </td>
			  </tr><tr>	 
			     <td width="20"> </td>			  
			     <td width="150">Inspeccion Miembros Inferiores</td>			     			     
			     <td width="250"> '.ValorRadioExFisico("ef25",$row['ef25']).' </td>			       
			     <td width="20"> </td>
			  </tr><tr>	 
			     <td width="20"> </td>			  
			     <td width="150">Reflejos Tendinosos</td>			     			     
			     <td width="250"> '.ValorRadioExFisico("ef26",$row['ef26']).' </td>			       
			     <td width="20"> </td>
			  </tr><tr>	 
			     <td width="20"> </td>			  
			     <td width="150">Esfera Mental</td>			     			     
			     <td width="250"> '.ValorRadioExFisico("ef27",$row['ef27']).' </td>			       
			     <td width="20"> </td>
			  </tr><tr>	 
			     <td width="20"> </td>			  
			     <td width="150">Neurologico</td>			     			     
			     <td width="250"> '.ValorRadioExFisico("ef28",$row['ef28']).' </td>			       
			     <td width="20"> </td>
			  </tr><tr>	 
			     <td width="20"> </td>			  
			     <td width="150">Otros</td>			     			     
			     <td width="250"> '.ValorRadioExFisico("ef29",$row['ef29']).' </td>			       
			     <td width="20"> </td>
			  </tr><tr>	 
			     <td width="20"> </td>			  
			     <td width="150"></td>			     			     
			     <td width="250"></td>			     
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
				 <td width="150"> <b>Diagnosticos Medicos </b> <td>
				 <td width="20"> Primero </td>  
				 <td width="120">
				 
<!-- ************************************************************************************************ -->
<!-- AJAX AUTOSUGGEST SCRIPT -->
<script type="text/javascript" src="lib/ajax_framework11.js"></script>
<style type="text/css">
#search-wrap11 input{font-size:13px; text-transform:Capitalize; background-color:#D6F0FE; border-style:groove;}
#res11{width:100px; border:solid 1px #DEDEDE; display:none;}
#res11 ul, #res11 li{padding:0; margin:0; border:0; list-style:none; background:#F6F6F6;}
#res11 li {border-top:solid 1px #DEDEDE; background:#CEEAF5;}
#res11 li a{display:block; padding:2px; text-decoration:none; color:#000000; font-weight:bold; font-size:10px; font-family:Verdana;}
#res11 li a small{display:block; text-decoration:none; color:#999999; font-weight:normal; font-size:9px;font-family:Verdana;}
#res11 li a:hover{background:#FFFFFF;}
#res11 ul {padding:4px;}
</style>
<div id="search-wrap11">
<input name="codcie" id="search-q11" type="text" onkeyup="javascript:autosuggest11();" maxlength="12" size="15" autocomplete="off" tabindex="13" value="'.$row['codcie'].'"/>
<div id="res11"></div>
</div>
<!-- AJAX AUTOSUGGEST SCRIPT -->
<!-- ************************************************************************************************ -->
			 
				 <td>				 

				 <td width="20"> Segundo </td>  
				 <td width="120">
				 
<!-- ************************************************************************************************ -->
<!-- AJAX AUTOSUGGEST SCRIPT -->
<script type="text/javascript" src="lib/ajax_framework12.js"></script>
<style type="text/css">
#search-wrap12 input{font-size:13px; text-transform:Capitalize; background-color:#D6F0FE; border-style:groove;}
#res12{width:100px; border:solid 1px #DEDEDE; display:none;}
#res12 ul, #res12 li{padding:0; margin:0; border:0; list-style:none; background:#F6F6F6;}
#res12 li {border-top:solid 1px #DEDEDE; background:#CEEAF5;}
#res12 li a{display:block; padding:2px; text-decoration:none; color:#000000; font-weight:bold; font-size:10px; font-family:Verdana;}
#res12 li a small{display:block; text-decoration:none; color:#999999; font-weight:normal; font-size:9px;font-family:Verdana;}
#res12 li a:hover{background:#FFFFFF;}
#res12 ul {padding:4px;}
</style>
<div id="search-wrap12">
<input name="codcie2" id="search-q12" type="text" onkeyup="javascript:autosuggest12();" maxlength="12" size="15" autocomplete="off" tabindex="13" value="'.$row['codcie'].'"/>
<div id="res12"></div>
</div>
<!-- AJAX AUTOSUGGEST SCRIPT -->
<!-- ************************************************************************************************ -->
			 
				 <td>				 

				 <td width="20"> Tercero </td>  
				 <td width="120">
				 
<!-- ************************************************************************************************ -->
<!-- AJAX AUTOSUGGEST SCRIPT -->
<script type="text/javascript" src="lib/ajax_framework13.js"></script>
<style type="text/css">
#search-wrap13 input{font-size:13px; text-transform:Capitalize; background-color:#D6F0FE; border-style:groove;}
#res13{width:100px; border:solid 1px #DEDEDE; display:none;}
#res13 ul, #res13 li{padding:0; margin:0; border:0; list-style:none; background:#F6F6F6;}
#res13 li {border-top:solid 1px #DEDEDE; background:#CEEAF5;}
#res13 li a{display:block; padding:2px; text-decoration:none; color:#000000; font-weight:bold; font-size:10px; font-family:Verdana;}
#res13 li a small{display:block; text-decoration:none; color:#999999; font-weight:normal; font-size:9px;font-family:Verdana;}
#res13 li a:hover{background:#FFFFFF;}
#res13 ul {padding:4px;}
</style>
<div id="search-wrap13">
<input name="codcie3" id="search-q13" type="text" onkeyup="javascript:autosuggest13();" maxlength="12" size="15" autocomplete="off" tabindex="13" value="'.$row['codcie3'].'"/>
<div id="res13"></div>
</div>
<!-- AJAX AUTOSUGGEST SCRIPT -->
<!-- ************************************************************************************************ -->
			 
				 <td>				 



				 <td width="40"> <input title="Guardar Historia" alt="Guardar Historia" src="images/iconos/guardar.png" type="image" width="32" height="32" border="0"> </td> 
				 <td width="40"> 
				    <a href="#top" title="Ir Arriba"><img src="images/iconos/top.png" width="24" height="24" border="0"></a> </td> 				 
    		     <td width="8"> </td>
			   </tr>	 			   
			</table>		
            <br>
            <table width="100%">
	           <tr class="CabezoteTabla"> 
			     <td width="10"> </td>
			     <td width="37"> <img src="images/iconos/faq.png" width="32" height="32" border="0"> </td>
				 <td width="160"> <b>Concepto del Profesional</b> <td>
				 <td width="490">'.$clase->CrearCombo("conceptomedid","conceptomed","descripcion","conceptomedid",$row['conceptomedid'],"S","codigo").'
				 <br> Si no esta en la Lista, Digite : <input type="text" name="nconceptopro" size="70">  
				 <td>				 
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
			</table></div><br><br><br><br>';  //onclick="location.href=\'?opcion=eliminar&amp;id='.$row['ciudadid']
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
	 $expuesto5  = strtoupper($_POST['expuesto5']);
	 $expuesto6  = strtoupper($_POST['expuesto6']);
	 $expuesto7  = strtoupper($_POST['expuesto7']);
	 $expuesto8  = strtoupper($_POST['expuesto8']);
	 $expuesto9  = strtoupper($_POST['expuesto9']);
	 $expuesto10 = strtoupper($_POST['expuesto10']);
	 $expuesto11 = strtoupper($_POST['expuesto11']);
	 $expuesto12 = strtoupper($_POST['expuesto12']);
	 $expuesto13 = strtoupper($_POST['expuesto13']);
	 $expuesto14 = strtoupper($_POST['expuesto14']);
	 $expuesto15 = strtoupper($_POST['expuesto15']);
	 $expuesto16 = strtoupper($_POST['expuesto16']);
	 	 	 
     $vsql = "INSERT INTO anteocupacionales (historiaid,fecdesde,fechasta,empresa,ocupacion,tiempo,riesgos,expuesto1,expuesto2,expuesto3,expuesto4,expuesto5,expuesto6,expuesto7,expuesto8,expuesto9,expuesto10,
expuesto11,expuesto12,expuesto13,expuesto14,expuesto15,expuesto16,creador,momento) values(".$id.",'".$fecdesde."','".$fechasta."','".$empresa."','".$ocupacion."','".$tiempo."','".$riesgos."','".$expuesto1."','".$expuesto2."','".$expuesto3."','".$expuesto4."','".$expuesto5."','".$expuesto6."','".$expuesto7."','".$expuesto8."','".$expuesto9."','".$expuesto10."','".$expuesto11."','".$expuesto12."','".$expuesto13."','".$expuesto14."','".$expuesto15."','".$expuesto16."','".$_SESSION['USERNAME']."',CURRENT_TIMESTAMP)";

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
	   $diahoy = "00/00/0000";
	   
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
				  <input type="checkbox" name="expuesto1" value="CHECKED"> Iluminacion 
				  <input type="checkbox" name="expuesto2" value="CHECKED"> Radiaciones
				  <input type="checkbox" name="expuesto3" value="CHECKED"> Ruido
				  <input type="checkbox" name="expuesto4" value="CHECKED"> Temperaturas
				</td>
			  </tr>
			  <tr>	  
			    <td width="50"> </td>
				<td>
				  <input type="checkbox" name="expuesto5" value="CHECKED"> Vibraciones
				  <input type="checkbox" name="expuesto6" value="CHECKED"> Gases
				  <input type="checkbox" name="expuesto7" value="CHECKED"> Humos
				  <input type="checkbox" name="expuesto8" value="CHECKED"> Polvos
				  <input type="checkbox" name="expuesto9" value="CHECKED"> Liquidos
				</td>
			  </tr>
			  <tr>	  
			    <td width="50"> </td>
				<td>				  
				  <input type="checkbox" name="expuesto10" value="CHECKED"> Vapores
				  <input type="checkbox" name="expuesto11" value="CHECKED"> Fibras
				  <input type="checkbox" name="expuesto12" value="CHECKED"> Cargas
				  <input type="checkbox" name="expuesto13" value="CHECKED"> Movimientos Repetit.
				</td>
			  </tr>
			  <tr>	  
			    <td width="50"> </td>
				<td>			
				  <input type="checkbox" name="expuesto14" value="CHECKED"> Biologicos
				  <input type="checkbox" name="expuesto15" value="CHECKED"> PsicoSociales
				  <input type="checkbox" name="expuesto16" value="CHECKED"> De Seguridad				  		   				  				  			  				
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
				     Fecha : <input type="text" name="fecha" size="10" value="00/00/0000">
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
    $vsql = "SELECT HC.* , T1.nombre NombrePaciente , T2.nombre NombreMedico , TX.nombre Empresa 
    	     FROM historiacli HC 
	         INNER JOIN terceros T1 ON (HC.teridpaciente = T1.terid) 
	         LEFT JOIN terceros T2 ON (HC.teridprof = T2.terid)
             LEFT JOIN documentos DX ON (DX.docuid = HC.docuid)
             LEFT JOIN contratos CX ON (DX.contratoid = CX.contratoid)
             LEFT JOIN terceros TX ON (CX.terid = TX.terid)
             WHERE HC.tipoexamen like '%".$criterio."%' OR TX.nombre like '%".$criterio."%' OR T1.nit like '%".$criterio."%' OR T1.nombre like '%".$criterio."%' OR T2.nit like '%".$criterio."%' OR T2.nombre like '%".$criterio."%' 
		     OR HC.conceptomed like '%".$criterio."%' ORDER BY HC.momento DESC Limit 0,".$_SESSION["NUMREGISTROSXCONSULTA"];

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
	if($opcion == "cerrar")
	{
	    // Si es un medico - la Marca como vista por él
        if($_SESSION['ROL'] == "MED")
        {	
           $vsql2 = "UPDATE historiacli SET usuariocierra = '".$_SESSION['USERNAME']."' , momento3 = CURRENT_TIMESTAMP WHERE historiaid=".$id; 
           $clase->EjecutarSQL($vsql2);
        }

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
     	$vsql = "SELECT HC.* , T1.nombre NombrePaciente , T2.nombre NombreMedico , TX.nombre Empresa 
    	         FROM historiacli HC 
		         INNER JOIN terceros T1 ON (HC.teridpaciente = T1.terid) 
		         LEFT JOIN terceros T2 ON (HC.teridprof = T2.terid)
                 LEFT JOIN documentos DX ON (DX.docuid = HC.docuid)
                 LEFT JOIN contratos CX ON (DX.contratoid = CX.contratoid)
                 LEFT JOIN terceros TX ON (CX.terid = TX.terid)
                 WHERE HC.estado='A' ORDER BY HC.momento DESC Limit 0,".$_SESSION["NUMREGISTROSXCONSULTA"];

	$_SESSION['SQL_HISTORIAS'] = $vsql;
	header("Location: historiacli.php");
  }

  /////////////////////////////////////////  
  if($opcion == "solohoy")
  {
    $fechahoy = date("Y-m-d");
    $criterio = $_POST['criterio'];
   	$vsql = "SELECT HC.* , T1.nombre NombrePaciente , T2.nombre NombreMedico , TX.nombre Empresa 
    	     FROM historiacli HC 
		     INNER JOIN terceros T1 ON (HC.teridpaciente = T1.terid) 
		     LEFT JOIN terceros T2 ON (HC.teridprof = T2.terid)
             LEFT JOIN documentos DX ON (DX.docuid = HC.docuid)
             LEFT JOIN contratos CX ON (DX.contratoid = CX.contratoid)
             LEFT JOIN terceros TX ON (CX.terid = TX.terid)
             WHERE HC.momento LIKE '".$fechahoy."%' Limit 0,".$_SESSION["NUMREGISTROSXCONSULTA"];
	$_SESSION['SQL_HISTORIAS'] = $vsql;
	header("Location: historiacli.php");
  }

  /////////////////////////////////////////  
  if($opcion == "")
  {
     $teridprof = $clase->BDLockup($_SESSION['USERNAME'],'terceros','username','terid');
	 $cont = $clase->HeaderBlanco("Historias Clinicas");
	 $cont.='<table width="100%">
	           <tr class="CabezoteTabla"> 
			     <td width="10"> </td>
			     <td width="37"> <img src="images/iconos/historias.png" width="32" height="32" border="0"> </td>
				 <td width="750"> Historias Clinias. Usuario <b>'.$_SESSION['USERNAME'].'</b><td>
				 <td width="27"> <a href="?opcion=nuevo"> <img src="images/icononuevo.png" border="0"> </a> </td>
				 <form action="?opcion=encontrar" method="POST" name="x">
				 <td> <input type="text" name="criterio" size="20" placeholder="Criterio a Buscar" tabindex="1" id="default"> </td>
				 <td> <input type="submit" value="Encontrar"> </td> 
				 <td width="25" align="center"> <a href="?opcion=solohoy" title="Solo Historias de Hoy"> <img src="images/calendario.png" border="0"> </a> </td>
				 <td width="25" align="center"> <a href="?opcion=soloabiertas" title="Solo Historias Abiertas"> 
				 <img src="images/servicios.png" border="0" width="14" height="16"> </a> </td>';

	 if($_SESSION['SQL_HISTORIAS'] != "")
         $cont.='<td width="25"> <a href="?opcion=nofiltro"> <img src="images/nofiltro.png"> </a> </td>'; 

		$cont.=' </form>
		         <td width="25" align="center"> <a href="principal.php" title="Menu Principal"> <img src="images/iconos/principal.png" border="0" width="32" height="32"> </a></td>
    		     <td width="10"> </td>
			   </tr>	 			   
			 </table> ';	
	
	
    $vsql = $_SESSION['SQL_HISTORIAS'];
	if($vsql == "")
	{
    	$vsql = "SELECT HC.* , T1.nombre NombrePaciente , T2.nombre NombreMedico , TX.nombre Empresa 
    	         FROM historiacli HC 
		         INNER JOIN terceros T1 ON (HC.teridpaciente = T1.terid) 
		         LEFT JOIN terceros T2 ON (HC.teridprof = T2.terid)
                 LEFT JOIN documentos DX ON (DX.docuid = HC.docuid)
                 LEFT JOIN contratos CX ON (DX.contratoid = CX.contratoid)
                 LEFT JOIN terceros TX ON (CX.terid = TX.terid)";
	    
		if($teridprof != "")	
		   $vsql.= " WHERE HC.teridprof = ".$teridprof;	
		     
		$vsql.= " ORDER BY momento DESC limit 0,".$_SESSION["NUMREGISTROSXCONSULTA"];
    }

	$conex  = $clase->Conectar();
    $result = mysql_query($vsql,$conex);

	 $cont.='<div style="overflow:auto; height:650px;width:1200px;">
	          <script type="text/javascript" src="lib/sorttable.js"></script>
	           <table width="100%" class="sortable">
	            <tr class="TituloTabla"> 
			     <td width="5"> </td>
			     <td width="20">Es</td>			     
			     <td width="90"> Fecha | Hora </td>
				 <td width="180"> Nombre del Paciente </td>
				 <td width="150"> Empresa </td>
				 <td width="150"> Asignada a </td>				 
				 <td width="50"> Examen </td>			
				 <td width="80"> Vista por </td>				 
				 <td width="80"> Cerrada por </td>				 
				 <td width="20"><img src="images/iconoimprimir.png" border="0"></td>
				 <td width="20"><img src="images/funciones.png" border="0"></td>
				 <td width="20"><img src="images/seleccion.png" border="0"></td>		 				 
			   </tr>';	
    $i = 0;
    while($row = mysql_fetch_array($result)) 
	{
	     /// Momento en que el Medico Vió la Historia Clinica
	     if($row['usuariomed']!="")
	        $medvisto = '<font color="green">'.$row['usuariomed'].'</font><br>'.substr($row['momento2'],8,2).'/'.substr($row['momento2'],5,2).'/'.substr($row['momento2'],2,2).' '.substr($row['momento2'],11,5); 
         else
         	$medvisto = '';

         /// Momento en que el Medico Cerró la Historia Clinica
	     if($row['usuariocierra']!="")
	        $medcierra = '<font color="red">'.$row['usuariocierra'].'</font><br>'.substr($row['momento3'],8,2).'/'.substr($row['momento3'],5,2).'/'.substr($row['momento3'],2,2).' '.substr($row['momento3'],11,5); 
         else
         	$medcierra = '';

	     $i++;
		 if($i%2 == 0)
		   $cont.='<tr class="TablaDocsPar">';
		 else
		   $cont.='<tr class="TablaDocsImPar">';		 
		          
		 $cont.=' <td width="5"> </td>
				  <td width="20"> '.IconoEstado($row['estado']).' </td>
				  <td width="90"> '.substr($row['momento'],8,2).'/'.substr($row['momento'],5,2).'/'.substr($row['momento'],2,2).
				                  '&nbsp;&nbsp;&nbsp;<font color="blue">'.substr($row['momento'],11,5).' </td>
				  <td width="180"> '.substr($row['NombrePaciente'],0,32).' </td>
				  <td width="150"> <b>'.substr($row['Empresa'],0,21).' </td>
				  <td width="150"> <font color="gray">'.substr($row['NombreMedico'],0,22).' </td>
				  <td width="50"> <font color="blue"> '.$row['tipoexamen'].' </td>				  
				  <td width="60"> '.$medvisto.'</td>
				  <td width="60"> '.$medcierra.' </td>				  
				  <td width="20"> <a href="historiacli.php?opcion=menuhc&id='.$row['historiaid'].'" rel="facebox"> <img src="images/iconoimprimir.png" border="0"> </td>				  
				  <td width="20"> <a href="?opcion=cambiar&amp;id='.$row['historiaid'].'" title="Modificar Historia" rel="facebox"> <img src="images/funciones.png" border="0"> </td>';
		  if(($row['estado'] != 'C')||($_SESSION['USERNAME'] == 'ADMINISTRADOR')||($_SESSION['USERNAME'] == 'SAID')) 	  
				  $cont.='<td width="25"> <a href="?opcion=detalles&amp;id='.$row['historiaid'].'" title="Diligenciar Historia"> <img src="images/seleccion.png" border="0"> </td>';
				  
		 $cont.='</tr>';
	}
	$cont.='</table></div>
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
  echo $cont; 
  
  ////////////////////////////////
  ////////////////////////////////

  //////////////////////////////////////////////////////////////////////////////   
  function ValorRadioExFisico($nombreRad,$valor)
  {	
	$cont='<input type="text" size="110" maxlenght="150" name="'.$nombreRad.'" value="'.$valor.'">';
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