<!DOCTYPE html>
<html >
<head>
  <meta charset="UTF-8">
  <title>Login - Clinico</title>
  <link rel='stylesheet prefetch' href='http://fonts.googleapis.com/css?family=Open+Sans:600'>
    <link rel="stylesheet" href="css/style.css">

</head>
<body onload="document.forms['x']['usuario'].focus()">
  <div class="login-wrap">
	<div class="login-html">
		<input id="tab-1" type="radio" name="tab" class="sign-in" checked><label for="tab-1" class="tab">Ingreso</label>
		<input id="tab-2" type="radio" name="tab" class="sign-up"><label for="tab-2" class="tab">Salud Empresarial IPS</label>
		<div class="login-form">
			<div class="sign-in-htm">
			  <form action="usuarios.php?opcion=login" method="POST" name="x">

				<div class="group">
					<label for="user" class="label">Usuario</label>
					<input id="usuario" name="usuario" type="text" class="input">
				</div>
				<div class="group">
					<label for="pass" class="label">Clave</label>
					<input id="clave" name="clave" type="password" class="input" data-type="password">
				</div>
				<div class="group">
					<input id="check" type="checkbox" class="check" checked>
					<label for="check"><span class="icon"></span> Recordarme </label>
				</div>
				<div class="group">
					<input type="submit" class="button" value="Ingreso Seguro">
				</div>
				<div class="hr"></div>
			</div>
	
			</div>
		</div>
	</div>
</div>
<br><br> 
<div class="pie-swc"> 
		 Un Producto <b><a href="http://www.swebcolombia.com" target="_blank">Sistemas y Soluciones Web de Colombia</a></b>	
</div>
		 
</body>
</html>
