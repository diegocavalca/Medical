<?
header('Content-Type: text/html; charset=utf-8');
include('loadSystem.inc');

$json =  utf8_encode(strip_tags($_POST['req']));

//Requisi磯 JSON
$json = json_decode($json, true);

if (isset($json["OBJECT"])){

  if ($json["OBJECT"] == 'LOGIN'){

	$retornoJson = array();
	$CODMSG     = -1;
	$MENSAGEM   = '';
	$CODUSUARIO = 0;
	$sessao_id  = '';

    $strSQL = 'SELECT
					*
			   FROM
					usuario as u
			   WHERE
					UPPER(u.USERNAME) = UPPER("'.$json["USER"].'");';
					
    $rsLogin = mysql_query($strSQL);
    $linhaUsu  = mysql_num_rows($rsLogin);
	  
	  if ($linhaUsu > 0){
	  
	     $rowLogin = mysql_fetch_array($rsLogin);
		 
		 if ($json["PASSWORD"] == $rowLogin["SENHA"]){

		    $CODMSG    				 = 0;
		    $MENSAGEM  				 = 'Acesso liberado!';			  
			  
			//Cria a Session
			$sessao_timeout = 43200; // 3600 seconds = 60 minutes = 1 hour
			ini_set('session.gc_maxlifetime', $sessao_timeout);
	      	ini_set('session.auto_start', 1);
			
	      	session_start();
			$sessao_id = session_id();
	
			$_SESSION["SESSION_ID"] = $sessao_id;
			$_SESSION["ID"] 		= $rowLogin["K_USUARIO"];
			$_SESSION["USERNAME"]   = $rowLogin["USERNAME"];
			$_SESSION["NOME"] 		= $rowLogin["NOME"];
			$_SESSION["SENHA"]      = $rowLogin["SENHA"];
			
			//Criando cookie's do usuario com o USUARIO/SENHA/CONTA
			if ( $json["REMEMBER"] == "T" ) {
			   setcookie("USER", strtolower($rowLogin["USERNAME"]), time()+(60*60*24*365));
			   setcookie("PASSWORD", $json["PASSWORD"], time()+(60*60*24*365));
			}else{
			   // Configura a data de expirar para uma hora atr᳍
			   setcookie("USER", "XXXX", time() - 3600);		
			   setcookie("PASSWORD", "", time() - 3600);		
			}
			
			/*
	        $strSQL    = 'select distinct(codempresa) from acesso where usuario = '.$rowUsuario["CODIGO"];
	        $rsAcesso  = mysql_query($strSQL,$conEmpresa);
	        $linAcesso = mysql_num_rows($rsAcesso);

        	if ($linAcesso > 0){
  			  $empresa_id = 1;
	          $CODUSUARIO = $rowUsuario["CODIGO"];
		      $CODMSG     = 0;
		      $MENSAGEM   = 'Acesso liberado!';
			}else{
		      $CODMSG     = 4;
		      $MENSAGEM   = 'UsuᲩo sem acesso liberado!';
			}
			*/

		 }else{
	      	$CODMSG   = 3;
	      	$MENSAGEM = utf8_encode('Senha n&atilde;o confere!');
		 }

	 }else{
	    $CODMSG   = 2;
	    $MENSAGEM = utf8_encode('Usu&aacute;rio n&atilde;o cadastrado.');
	 }

	}else{
	  $CODMSG   = 1;
	  $MENSAGEM = utf8_encode('Objeto n&atilde;o reconhecido!');
	}


	$retornoJson["CODMSG"]    = $CODMSG;
	$retornoJson["MENSAGEM"]  = utf8_encode($MENSAGEM);
	$retornoJson["SESSAOPHP"] = $sessao_id;	
	
	//$retornoJson["PASS_X"]   = $_COOKIE["PASSWORD"];
 
    echo(json_encode($retornoJson));

  }

?>