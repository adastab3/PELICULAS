<?php
	
	$objBD=new clsBD;
	$objConfiguracion=new clsConfiguracion;
	$objUtilidades=new clsUtilidades;

	if(defined('TOKEN_WEBSERVICE')){

		if(constant('TOKEN_WEBSERVICE')==$objConfiguracion->obtenerTokenWebservices()){	

			//VALIDAMOS PARAMETROS
			//Definimos un array con los campos obligatorios
			//Son los parámetros que tienen que lleganos por POST
			//Si tiene valor "1" además de llegarnos por POST, no pueden estar vacios
			//Si tiene valor "0" será necesario enviar el valor pero puede estar vacio
			$parametrosObligatorios=array(
              "nombre"=>"1",
              "apellidos"=>"1",              
              "email"=>"1",              
              "fecha_nacimiento"=>"1",              
              "provincia"=>"1",              
			);

			//Recogemos los parámetros que lleguen por POST (raw y form-data)
			$parametrosRecibidos = $objUtilidades->obtenerParametrosPOST();

			//Rellenamos un array con los parámetros obligatorios no rellenados
			$arrayParametrosNoValidos=$objUtilidades->validarParametrosPost($parametrosObligatorios, $parametrosRecibidos);

			//Si hay algún elemento en el array de parámetros no rellenados, los parámetros no son correctos
			if(!empty($arrayParametrosNoValidos)){
		        $response['code'] = 8;
		        $response['status'] = $api_response_code[ $response['code'] ]['HTTP Response'];
		        $response['message'] = $objUtilidades->obtenerCadenaParametros($arrayParametrosNoValidos);
				$response['data'] = $api_response_code[ $response['code'] ]['Message'];
			}
			else{				
					//Creamos el cliente
					$sentenciaInsert = "insert into cliente(
                        id, 
                        nombre, 
                        apellidos, 
                        email, 
                        fecha_nacimiento, 
                        provincia
                    ) " .
					"values (".
                    "null" . ", " .
                    "'" . $parametrosRecibidos['nombre'] . "', " . 
                    "'" . $parametrosRecibidos['apellidos'] . "', " . 
                    "'" . $parametrosRecibidos['email'] . "', " . 
                    "'" . $parametrosRecibidos['fecha_nacimiento'] . "' " . 
                    "'" . $parametrosRecibidos['provincia'] . "' " . 
                    ");";

					$resultado = $objBD->ejecutarInsert($sentenciaInsert);
					
					if($resultado){
						$response['code'] = 1;
				    	$response['status'] = $api_response_code[ $response['code'] ]['HTTP Response'];	
						$response['message'] = $api_response_code[$response['code'] ]['Message'];
						$response['data'] = array(
										       		'resultado' => 'ok',
										        	'id'=> $resultado
						);
					}else{
						$response['code'] = 10;
				    	$response['status'] = $api_response_code[ $response['code'] ]['HTTP Response'];	
						$response['message'] = $api_response_code[$response['code'] ]['Message'];
						$response['data'] = array(
										       		'resultado' => 'no_ok',
										        	'id'=> $resultado
						);
					}
			}					
		}
  		else{
			echo _("Token incorrecto");
		}
    }
    else{
		echo _("Llamada no autorizada");
	}

?>