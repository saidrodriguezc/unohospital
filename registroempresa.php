<?PHP
  include("lib/Sistema.php");
  $clase = new Sistema(); 
  
      
  $accion = $_POST['accion'];
  if(isset($accion))
  {
     $alias     = strtoupper($_POST['alias']);
	 $nombre    = strtoupper($_POST['nombre']);
	 $nit       = strtoupper($_POST['nit']);	 
	 $direccion = strtoupper($_POST['direccion']);	 	 
	 $telefonos = strtoupper($_POST['telefonos']);	 	 	 
	 $percon    = strtoupper($_POST['percon']);	 	 	 
	 $ciudaddig = strtoupper($_POST['ciudad']);	 	 	 	 	 
	 $ubicacion = $_POST['ubicacion'];	 	 	 	 
	 $email     = strtolower($_POST['email']);	 	 	 	 	 	 

	 $clase = new Sistema();
     $ciudad    = $clase->BDLockup($ciudaddig,"ciudades","codigo","codigo"); 
	 
	 if($ciudad != "")
	 {
	   $vsql = "INSERT INTO empresas(alias,nombre,dni,direccion,ubicacion,telefonos,percontacto,email,estadoid,ciudad) ";
	   $vsql.= "VALUES('".$alias."','".$nombre."','".$nit."','".$direccion."','".$ubicacion."','".$telefonos."','".$percon."','".$email."','PA','".$ciudad."')";
	 
	   $creado = $clase->EjecutarSQL($vsql);
	   
	   if($creado == 1)
	      header("Location:pages/empresacreada.php");
	   else
	      echo' <script language="Javascript">
		         alert("Error al Crear la Empresa");
         	    </script>';  
	 }
	 else{
	    echo' <script language="Javascript">
		         alert("Ciduad NO Válida");
            	 location.href=\'registroempresa.php\';
				</script>';
				 		
	 }			
  }
  
   /////////////////////////////////////////////////////////  
   // Pagina por Defecto 
   $cont = $clase->Header("S","W"); ;
   
   $cont.='<script language="Javascript">
		  function ValidarDatos(){ 
          	if (document.x.alias.value.length==0){ 
          	 alert("El Alias de la Empresa es Obligatorio") 
         	 document.x.alias.focus(); 
      	     exit;
   	        }   
          	if (document.x.nombre.value.length==0){ 
          	 alert("Debe digitar el Nombre de la Empresa") 
         	 document.x.nombre.focus(); 
      	     exit;
   	        }   
			if (document.x.nit.value.length==0){ 
          	 alert("El NIT o Cedula es Obligatorio") 
         	 document.x.nit.focus(); 
      	     exit;
   	        }			
			if (document.x.percon.value.length==0){ 
          	 alert("Debe Digitar una Persona de Contacto") 
         	 document.x.percon.focus(); 
      	     exit;
   	        }			
			if (document.x.ciudad.value.length==0){ 
          	 alert("Debe Elegir una Ciudad") 
         	 document.x.ciudad.focus(); 
      	     exit;
   	        }			
			if (document.x.email.value.length==0){ 
          	 alert("Debe Digitar un Correo Electronico") 
         	 document.x.email.focus(); 
      	     exit;
   	        }
			else{
			  if (document.x.email.value.length<6){
			   alert("Correo Electronico No Valido") 
         	   document.x.email.focus(); 
      	       exit;
			  }			
			}
			
		   document.x.submit(); 
		  }	
		  </script>   
	 <form action="registroempresa.php" method="POST" name="x">
	 <input type="hidden" name="accion" value="crear">
	 <br><br><center>
     <h2>Registro de Empresas Nuevas</h2>
	 <br><br>
	 <table width="700" align="center">
	   <tr>
	     <td width="90">Alias : </td>
	     <td> <input type="text" maxlength="12" size="12" name="alias"> </td>
		 <td width="65"></td>
	     <td>Nombre Empresa : <label id="nombre"> </label> </td>
	     <td> <input type="text" maxlength="40" size="35" name="nombre"> </td>
	   </tr>
	   <tr>
	     <td width="90">N.I.T. : </td>
	     <td> <input type="text" maxlength="12" size="12" name="nit"> </td>
		 <td width="65"></td>
	     <td>Direccion : </td>
	     <td> <input type="text" maxlength="30" size="35" name="direccion"> </td>
	   </tr>
	   <tr>
	     <td width="90">Telefonos : </td>
	     <td> <input type="text" maxlength="12" size="12" name="telefonos"> </td>
		 <td width="65"></td>
	     <td>Persona Contacto : </td>
	     <td> <input type="text" maxlength="30" size="35" name="percon"> </td>
	   </tr>
	   <tr>
	     <td width="90">Ciudad : </td>
	     <td> 
		 
		 <!-- AJAX AUTOSUGGEST SCRIPT -->
<script type="text/javascript" src="lib/ajax_framework1.js"></script>
<style type="text/css">
#search-wrap1 input{font-size:13px; text-transform:Capitalize; background-color:#D6F0FE; border-style:groove;}
#res1{width:150px; border:solid 1px #DEDEDE; display:none;}
#res1 ul, #res1 li{padding:0; margin:0; border:0; list-style:none; background:#F6F6F6;}
#res1 li {border-top:solid 1px #DEDEDE; background:#CEEAF5;}
#res1 li a{display:block; padding:2px; text-decoration:none; color:#000000; font-weight:bold; font-size:10px; font-family:Verdana;}
#res1 li a small{display:block; text-decoration:none; color:#999999; font-weight:normal; font-size:9px;font-family:Verdana;}
#res1 li a:hover{background:#FFFFFF;}
#res1 ul {padding:4px;}
</style>
<div id="search-wrap1">
<input name="ciudad" id="search-q1" type="text" onkeyup="javascript:autosuggest1();" maxlength="12" size="12" autocomplete="off"/>
<div id="res1"></div>
</div>

		 </td>
		 <td width="65"></td>
	     <td>E-mail Contacto : </td>
	     <td> <input type="text" maxlength="30" size="35" name="email" placeholder="correo@servidor"> </td>
	   </tr>
	    <tr>
	     <td width="90"> Ubicacion : </td>
	     <td> 
		      <input type="radio" name="ubicacion" value="Colombia" checked> <img src="images/banderacol.png" border="0"> 
	          <input type="radio" name="ubicacion" value="Exterior"> <img src="images/banderaotros.png" border="0"> 
	     </td>
	     <td> </td>
	     <td> </td>
	   </tr>
	   
	   
	 </table>
     <center>
	  <br><br><br>
	   <input type="button" value="Registrar Empresa" class="Boton" onClick="javascript:ValidarDatos();"> 
  	   <input type="reset" value="Cancelar" class="Boton"> 
  	  <br><br><br><br> 
	   <font size="1"><a href="index.php">Iniciar Sesion</a></font>
	 </center> 	 
  </form>';
  
  $cont.= $clase->PiePagina();
  echo $cont;
  
?>  