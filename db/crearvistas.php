<?PHP
  session_start(); 
  include("../lib/Sistema.php");
  $clase = new Sistema();

  $vsql = "CREATE VIEW v_menu_usuario AS 
		   select u.usuid AS usuarioid,u.username AS username,u.activo AS activo,m.id AS id,m.orden AS orden,m.descripcion AS descripcion,m.link AS link,m.tipolink AS 						tipolink,mu.permitido AS permitido 
from ((usuarios u join menuxusuario mu on((u.usuid = mu.usuarioid))) join menu m on((m.id = mu.menuid))) order by u.username,m.orden";
  $clase->EjecutarSQL($vsql);

  header("Location: ../index.php");

?> 