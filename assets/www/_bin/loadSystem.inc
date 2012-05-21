<?
/*
# Remote MySQL Server
$hostname = "dbmy0038.whservidor.com";
$banco 	  = "needa_4";
$username = "needa_4";
$senha    = "D18042011B";
*/

# Local MySQL Server
$hostname = "dbmy0016.whservidor.com";
$banco 	  = "needa_7";
$username = "needa_7";
$senha    = "needa3674";

#Abre a conexão
$conn = mysql_connect($hostname, $username, $senha) or die ('#INFO: Estamos passando por uma breve atualiza&ccedil;&atildeo do sistema. Tente novamente mais tarde.');

if (mysql_error()) {
   die('Could not connect: ' . mysql_error());
}
/*
# Carregando session
ob_start();

session_start();
*/
#Abre o banco de dados
$db = mysql_select_db($banco);

?>
