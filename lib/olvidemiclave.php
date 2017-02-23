<?PHP
   include("lib/Sistema.php");
   $clase = new Sistema();    
   
   
   $opcion = $_GET['opcion'];
   if(isset($opcion)){
      $emaildig  = strtoupper($_POST['email']);
	  header("Location: pages/claverestaurada.php");
   }


   // Pagina por Defecto 
   $cont = $clase->Header("S","W"); ;
   
   $cont.='<script language="Javascript">
		   function ValidarDatos(){
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
	 <form action="olvidemiclave.php?opcion=reset" method="POST" name="x">
	 <br><br> <center>
     <h2>Recuperacion de Contrase&ntilde;as</h2>
	 <br><br>
	 <table width="330" align="center">
        <tr>
	     <td> Correo Electr&oacute;nico : </td>
	     <td> <input type="text" maxlength="55" size="25" name="email" placeholder="correo@servidor"> </td>
  	    </tr>
	 </table>
     <center>
	  <br><br><br>
	   <input type="button" value="Recuperar Mi Clave" class="Boton" onClick="javascript:ValidarDatos();"> 
	  <br><br><br><br> 
	   <font size="1"><a href="index.php">Iniciar Sesion</a></font>
	 </center> 	 
  </form>';
  
   $cont.= $clase->PiePagina();
   echo $cont;
?>   