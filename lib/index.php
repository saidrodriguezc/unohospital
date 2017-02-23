<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link rel="stylesheet" href="css/estilo.css" type="text/css">
<link rel="shortcut icon" type="image/x-icon" href="favicon.ico" />
<title>ELGUIA Clinico</title>
</head>
<body onload="document.forms['x']['usuario'].focus()">

<!-- Encabezado Logo y Datos de Session -->
<table class="EncabezadoIndex" align="center">
 <tr valign="top">
   <td width="500"> </td>
   <td class="MenuEncabezado">

   </td>      
 </tr>
</table>

<!-- Contenido Central del Sistema -->
<table border="1" class="ContenidoIndex" align="center" width="950"> 
 <tr valign="middle">
  <td width="10%">
  <td width="65%" align="center">  <br><br>   
    <img src="images/indexcaracteristicas.jpg" border="0">
  <br><br><br>
  </td>
  <td align="center" width="25%">

	<table class="TablaIndex" align="center"  bgcolor="#E8E8E8" width="280">
	 <tr>
	  <td align="center"> 
		  <br><br>
		  <h2> Ingreso al Sistema</h2>
		  <br> <center>
		  <form action="usuarios.php?opcion=login" method="POST" name="x">
		  <table class="TablaLogin" align="center">
		   <tr>
		     <td><b>Usuario
			 <br><input type="text" name="usuario" value="<?PHP  echo $_COOKIE["ult_username_1uno"]; ?>" class="CajaLogin"> <br><br> </td>
		   </tr>
		   <tr>
		     <td><b>Contrase&ntilde;a
			 <br><input type="password" name="clave" class="CajaLogin"> <br><br> </td>
		   </tr>
		   <tr>
		     <td align="center"> <input title="Ingresar al Sistema" alt="Ingresar al Sistema" src="images/btnlogin.png" type="image"> </td>
		   </tr>
		  </table>
		  <br><br>
		  </form>
  		  <center>
		  <a href="usuarios.php?opcion=olvidemiclave">Olvide Mi Contraseña</a> |
		  <a href="usuarios.php?opcion=ayudaingreso">Problemas para Acceder</a>
          <br><br><br>
	  </td>
	 </tr> 
	</table>   

  
  </td>  
 </tr>
</table>

<!-- Pie de pagina -->
<table class="Pie" align="center">
 <tr>
   <td align="center"> 
 		  <br><br>
		  <a href="http://www.conelguia.com" target="_blank">Comprar ELGUIA Clinico</a> |  
		  <a href="http://conelguia.com/clinico.php" target="_blank">Terminos y Condiciones</a> |  		  
		  <a href="http://conelguia.com/helpdesk/" target="_blank">Ayuda</a> 
     <br><br>
     <b>1Uno.co</b>
     Derechos Reservados	 
	 <br><br>
   </td>
 </tr>
</table>

</body>
</html>
