<?
/*=================================================================
							MANAGER.php
							
Ficara a cargo deste arquivo a recep磯, tratamento e retorno de 
requisi絥s ao banco de dados dentro do sistema nokade.

NOMENCLATURA:

"OBJECT" = Responsavel por direcionar o tipo de tratamento que o
         pacote de dados da requisi磯 que esta sendo enviado.

"MODE" = Especifica a a磯 que o pacote de dados ira receber
	
	...
==================================================================*/
session_start();

require("loadSystem.inc");
require_once("nkLib.php");

mysql_query("SET NAMES 'utf8'");
mysql_query('SET character_set_connection=utf8');
mysql_query('SET character_set_client=utf8');
mysql_query('SET character_set_results=utf8');

# Variaveis padrao
$retJSON = array();
$strSQL  = "";
$fields  = "";
$values  = "";
$updateValues = "";

# Verificar objeto
if(isset($_POST["OBJECT"])){
	
	if(strtoupper($_POST["OBJECT"])=="TABLE"){
		
		if(isset($_POST["MODE"])){
		
			# Verificar se ha pacotes padroes para tratamento de tabelas
			if( isset($_POST["TABLE"]) ){
				
				# Verificar modo para tratamento
				if(strtoupper($_POST["MODE"])=="INSERT"){
					
					# Verificar chave primaria
					if( isset($_POST["KEY_FIELD"]) && isset($_POST["KEY_VALUE"]) ){
						
						$fields = $_POST["KEY_FIELD"];
						$values = "'".$_POST["KEY_VALUE"]."'";
						
					}
					
					# Verificar se passou os valores/registros
					if( isset($_POST["REGISTERS"]) ){
						
						$registers = json_decode($_POST["REGISTERS"],true);

						# Verificar campos/registros e montar SQL
					    foreach($registers as $field => $value){
					       
						   # Campos
						   if ($fields == ''){
					  	      $fields = $field;
					  	   }else{
					  	      $fields .= ", ".$field;
					  	   }
						  
						   # Valores
					       if ($values == ''){
					  	      $values = '\''.validateString($value).'\'';
					  	   }else{
				    	      $values .= ', \''.validateString($value).'\'';
					  	   }
						  
					    }
						
						# Verificar campos obrigatorios
						if( isset($_POST["REQUEST_FIELDS"]) && ($_POST["REQUEST_FIELDS"]!="") ){
							
							$requestFields = explode(',',$_POST["REQUEST_FIELDS"]);
							
							foreach($requestFields as $requestField){
								
								$requestValue = "";
								
								# Campo DATA
								if( strtoupper($requestField) == "DATE_CREATE"){
									
									$dateRequest = date('Y-m-d H:i:s');
									$requestValue = '\''.$dateRequest.'\'';
									
									# Campo HORA
								}elseif( strtoupper($requestField) == "USERNAME_CREATE"){
										
										$requestValue = $requestValue = $_SESSION["USERNAME"];
										
									 }
								
								# Atribuindo valores obrigatorios
	 				  	        $fields .= ', '.$requestField;
	 			    	        $values .= ', '.$requestValue;
								
							}
							
						}
												
						$strSQL = 'insert into '.strtolower($_POST["TABLE"]).' ('.$fields.') values ('.$values.');';
						//echo $strSQL;exit;
						
					}else{
						
						$retJSON["ID_MSG"] = 6;
						$retJSON["MESSAGE"] = 'No parameter especified "FIELDS_VALUES"';
						
					}

				
				}elseif(strtoupper($_POST["MODE"])=="UPDATE"){
					
						# Verificar chave primaria
						if( isset($_POST["KEY_FIELD"]) && isset($_POST["KEY_VALUE"]) ){
					
							# Verificar se passou os valores/registros
							if( isset($_POST["REGISTERS"]) ){
								
								# Variavel q controla os parametros para alteracao
								$changeStatus = "F";
								$registers = json_decode($_POST["REGISTERS"],true);
								
								# Verificar campos/registros e montar SQL
							    foreach($registers as $field => $value){
									
									# Parametro STATUS
									if( (strtoupper($_POST["TABLE"])=="_PROFILE") && (strtoupper($field)=="STATUS") && (strip_tags($value)!="") ){
										$changeStatus = "T";
									}
							       
								   # Campos / Valores
								   if ($updateValues == ''){
							  	      $updateValues = $field.' = \''.validateString($value).'\'';
							  	   }else{
							  	      $updateValues .= ', '.$field.' = \''.validateString($value).'\'';
							  	   }
								  
							    }
								
								# Verificar campos obrigatorios
								if( isset($_POST["REQUEST_FIELDS"]) && ($_POST["REQUEST_FIELDS"]!="") ){
									
									$requestFields = explode(',',$_POST["REQUEST_FIELDS"]);
									
									foreach($requestFields as $requestField){
										
										$requestValue = "";
										
										# Campo DATA DE MODIFICACAO
										if( strtoupper($requestField) == "DATE_MODIFY"){
											
											$dateRequest = date('Y-m-d H:i:s');
											$requestValue = $dateRequest;
											
											# Campo USUARIO DE MODIFICACAO
										}elseif( strtoupper($requestField) == "USERNAME_MODIFY"){
												
												$requestValue = $_SESSION["USERNAME"];
												
											 }
										
										# Atribuindo valores obrigatorios
										$updateValues .= ', '.strtoupper($requestField).' = \''.validateString($requestValue ).'\'';
										
									}
									
								}
								
								# Query SQL UPDATE
								$strSQL = 'update '.strtolower($_POST["TABLE"]).' SET '.$updateValues.' where '.strtoupper($_POST["KEY_FIELD"]).' = '.$_POST["KEY_VALUE"];
								//echo $strSQL;exit;
								
							}else{
							
								$retJSON["ID_MSG"] = 7;
								$retJSON["MESSAGE"] = '"REGISTERS" undefined.';
								
							}
						
						}else{
							
							$retJSON["ID_MSG"] = 8;
							$retJSON["MESSAGE"] = '"KEY_FIELD/KEY_VALUE" undefined.';
							
						}
							
						
					}elseif(strtoupper($_POST["MODE"])=="DELETE"){
					
							# Verifica variavel de comparacao
							if( isset($_POST["KEY_FIELD"]) && isset($_POST["KEY_VALUE"]) && isset($_POST["OPERATOR"]) ){
							
								$strSQL = 'delete from '.$_POST["TABLE"].' where '.strtoupper($_POST["KEY_FIELD"]).' '.$_POST["OPERATOR"].' '.$_POST["KEY_VALUE"] ;
								//echo $strSQL;exit;
								
							}else{
								
								$retJSON["ID_MSG"] = 7;
								$retJSON["MESSAGE"] = '"KEY_FIELD/KEY_VALUE/OPERATOR" undefined.';
								
							}
	
						}else{
						
							$retJSON["ID_MSG"] = 5;
							$retJSON["MESSAGE"] = '"MODE" unknown.';
						
						}		
			
			}else{
				
				$retJSON["ID_MSG"] = 4;
				$retJSON["MESSAGE"] = '"TABLE" undefined';
				
			}
		
		}else{
		
			$retJSON["ID_MSG"] = 3;
			$retJSON["MESSAGE"] = '"MODE" undefined.';
			
		}
		
	}elseif(strtoupper($_POST["OBJECT"])=="PASSWORD"){
			
			if(strtoupper($_POST["MODE"])=="CHANGE"){
				
				# Verificar senha atual
				if($_POST["PASSWORD"]==$_SESSION["PASSWORD"]){
					
					$strSQL = 'update _access set PASSWORD="'.$_POST["NEW_PASSWORD"].'" where PROFILE_ID = '.$_SESSION["ID"].'';
					# Atualizando senha
					$_SESSION["PASSWORD"] = $_POST["NEW_PASSWORD"];
					
				}else{
					$retJSON["ID_MSG"] = 9;
					$retJSON["MESSAGE"] = '"PASSWORD" wrong!';
				}
			
			}else{
				$retJSON["ID_MSG"] = 3;
				$retJSON["MESSAGE"] = '"MODE" undefined.';
			}
		
		}elseif(strtoupper($_POST["OBJECT"])=="FACEBOOK"){
				
			}else{
				$retJSON["ID_MSG"] = 2;
				$retJSON["MESSAGE"] = '"OBJECT" Unknown.';
			}

}else{
	$retJSON["ID_MSG"] = 1;
	$retJSON["MESSAGE"] = '"OBJECT" undefined';
}

//echo $strSQL;exit;
######## PROCESSAR QUERY SQL Previamente montada ########
if($strSQL!=""){
	
	mysql_query($strSQL);
	
	# Verificar erro
	if(mysql_error()){
	
		$retJSON["ID_MSG"] = 999;
		$retJSON["MESSAGE"] = '#MYSQL_ERROR: '.mysql_errno().' - '.mysql_error();	
		$retJSON["SQL"] = $strSQL;
		
		# Incluir registro do erro
		mysql_query('insert into _error (PROFILE_ID,USERNAME,ERROR_ID,ERROR_MESSAGE,ERROR_SQL,DATE,HOUR) VALUES ("'.$_SESSION["ID"].'","'.$_SESSION["USERNAME"].'","'.mysql_errno().'","'.mysql_error().'","'.$strSQL.'","'.date("Y-m-d").'","'.date("H:i:s").'")');
		
	}else{
	
		$retJSON["ID_MSG"] = 0;
		$retJSON["KEY"]    = mysql_insert_id();
		
		if(strtoupper($_POST["MODE"])=="INSERT"){
		
			$retJSON["MESSAGE"] = '#SUCCESS: Insert in '.$_POST["TABLE"].'.';
			
		}elseif(strtoupper($_POST["MODE"])=="UPDATE"){
			
			$retJSON["MESSAGE"] = '#SUCCESS: Edited existing '.$_POST["TABLE"].'.';
			
		}else{
			
			$retJSON["MESSAGE"] = '#SUCCESS: Deleted old '.$_POST["TABLE"].'.';
			
		}
	
	}	
}

# Retornando para aplicacao
echo(json_encode($retJSON));

?>