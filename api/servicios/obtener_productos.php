<?php
	
	$objBD = new clsBD;
	$objConfiguracion = new clsConfiguracion;
	$objUtilidades = new clsUtilidades;
	$objConsultaSQL = new clsConsultaSQL();

	if(defined('TOKEN_WEBSERVICE')){

		if(constant('TOKEN_WEBSERVICE')==$objConfiguracion->obtenerTokenWebservices()){	

			//Recogemos los parámetros que lleguen por POST (raw y form-data)
			$parametrosRecibidos = $objUtilidades->obtenerParametrosPOST();

			//CONSTRUIMOS LA CONSULTA
			$objConsultaSQL->addCampoSelect('producto.id','id');
			$objConsultaSQL->addCampoSelect('producto.marca','marca');
			$objConsultaSQL->addCampoSelect('producto.modelo','modelo');
			$objConsultaSQL->addCampoSelect('producto.stock','stock');
			$objConsultaSQL->addCampoSelect('producto.precio','precio');
			$objConsultaSQL->addTablaFrom('producto');
			$objConsultaSQL->addCampoOrderby("producto.marca","asc");
			$objConsultaSQL->addCampoOrderby("producto.modelo","asc");

			//CONSTRUIMOS LIMIT
			if(array_key_exists("fila_inicial", $parametrosRecibidos) && array_key_exists("numero_filas", $parametrosRecibidos)){
				$objConsultaSQL->establecerLimitInferior($parametrosRecibidos['fila_inicial']-1);
				$objConsultaSQL->establecerLimitFilas($parametrosRecibidos['numero_filas']);
			}

			//CONSTRUIMOS FILTRADOS
			if(array_key_exists("id", $parametrosRecibidos))
				$objConsultaSQL->addCondicionWhere("producto.id"," = '".$parametrosRecibidos['id']."' ");

			if(array_key_exists("marca", $parametrosRecibidos))
				$objConsultaSQL->addCondicionWhere("producto.marca"," like '%".$parametrosRecibidos['marca']."%' ", clsConstantes::$AND);

			if(array_key_exists("modelo", $parametrosRecibidos))
				$objConsultaSQL->addCondicionWhere("producto.modelo"," like '%".$parametrosRecibidos['modelo']."%' ", clsConstantes::$AND);


			//DEBUG. SOLO DESCOMENTAR SI QUERÉIS VER LA CONSULTA QUE SE EJECUTA
			//AL DESCOMENTAR, NO EJECUTARÁ LA CONSULTA, SOLO LA MOSTRARÁ
		    //echo $objConsultaSQL->obtenerConsultaSQL();die();

			//EJECUTAMOS LA CONSULTA
			$resultado= $objBD->ejecutarConsulta($objConsultaSQL->obtenerConsultaSQL()); 

			//Comprobamos si el resultado contiene filas
			if (!$resultado) {
					$response['code'] = 0;				
				    $response['status'] = $api_response_code[ $response['code'] ]['HTTP Response'];
			        $response['message'] = $api_response_code[$response['code'] ]['Message'];
			    	$response['numero_filas']=0;
					$response['data']=array(
			       	 		'resultado' => 'error_servidor_bd',
							'datos' => array()
							
			        );					
			}
			else{  
				//Si la consulta se ha podido ejecutar  			
				//Si no se devuelven filas
				if (mysqli_num_rows($resultado) == 0){
					$response['code'] = 1;				
				   	$response['status'] = $api_response_code[ $response['code'] ]['HTTP Response'];
					$response['message'] = $api_response_code[$response['code'] ]['Message'];
			    	$response['numero_filas']=0;
					$response['data']=array(
						'resultado' => 'sin_resultados',
						'datos' => array()
					);
				}
				else{
					//Si se devuelven filas
					$response['code'] = 1;				
				   	$response['status'] = $api_response_code[ $response['code'] ]['HTTP Response'];
					$response['message'] = $api_response_code[$response['code'] ]['Message'];
			    	$response['numero_filas']=mysqli_num_rows($resultado);
					$response['data']=array(
						'resultado' => 'ok',
						'datos' => array()
					);
					$indice=0;
					while ($fila = mysqli_fetch_assoc($resultado)) {
						$response['data']['datos'][$indice]= 
							array(
								"id" => $fila["id"],
								"marca" => $fila["marca"],
								"modelo" => $fila["modelo"],
								"stock" => $fila["stock"],
								"precio" => $fila["precio"],
                            );	
						$indice++;					
					}
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