<?
  // inicia a sessão
  session_start();
 
  // primeiro destruímos os dados associados à sessão
  $_SESSION = array();

  // finalmente destruimos a sessão
  session_destroy();
  
  header("Location: ../");
  exit;
  
?>
