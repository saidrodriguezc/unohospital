<?php
  /// Cargo el JSON en la URL Remota
  $json = file_get_contents('http://181.143.221.66:8080/dropos/wsjson/usuarios/?id=24');   
  //$json = file_get_contents('http://181.143.221.66:8080/dropos/wsjson/usuarios/');   
  
  /// Lo decodifico
  $data = json_decode($json);
  
  /// Queda almacenado en la variable $data - Lo imprimo
  var_dump($data);
?>
