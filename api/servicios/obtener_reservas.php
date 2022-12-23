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
			$objConsultaSQL->addCampoSelect('reserva.id','id_reserva');
			$objConsultaSQL->addCampoSelect('reserva.fecha_hora_inicio','fecha_hora_inicio');
			$objConsultaSQL->addCampoSelect('reserva.fecha_hora_fin','fecha_hora_fin');
			$objConsultaSQL->addCampoSelect('reserva.id_usuario','id_usuario');
			$objConsultaSQL->addCampoSelect('usuario.login','login_usuario');
            $objConsultaSQL->addCampoSelect('usuario.nombre','nombre_usuario');
			$objConsultaSQL->addCampoSelect('usuario.apellidos','apellidos_usuario');
			$objConsultaSQL->addCampoSelect('reserva.id_zona_comun','id_zona_comun');
            $objConsultaSQL->addCampoSelect('zona_comun.nombre','nombre_zona_comun');
			$objConsultaSQL->addTablaFrom('reserva');
			
            $objConsultaSQL->addTablaInnerJoin("usuario", "usuario.id = reserva.id_usuario");
            $objConsultaSQL->addTablaInnerJoin("zona_comun", "zona_comun.id = reserva.id_zona_comun");

			$objConsultaSQL->addCampoOrderby("zona_comun.nombre","asc");

			//CONSTRUIMOS LIMIT
			if(array_key_exists("fila_inicial", $parametrosRecibidos) && array_key_exists("numero_filas", $parametrosRecibidos)){
				$objConsultaSQL->establecerLimitInferior($parametrosRecibidos['fila_inicial']-1);
				$objConsultaSQL->establecerLimitFilas($parametrosRecibidos['numero_filas']);
			}

			//CONSTRUIMOS FILTRADOS
			if(array_key_exists("fecha_hora_inicio", $parametrosRecibidos))
				$objConsultaSQL->addCondicionWhere("reserva.fecha_hora_inicio"," >= '".$parametrosRecibidos['fecha_hora_inicio']."' ");
            
            if(array_key_exists("fecha_hora_fin", $parametrosRecibidos))
				$objConsultaSQL->addCondicionWhere("reserva.fecha_hora_fin"," <= '".$parametrosRecibidos['fecha_hora_fin']."' ");

			if(array_key_exists("id_comunidad", $parametrosRecibidos))
				$objConsultaSQL->addCondicionWhere("zona_comun.id_comunidad"," = '".$parametrosRecibidos['id_comunidad_comunidad']."' ", clsConstantes::$AND);


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
								"id_reserva" => $fila["id_reserva"],
								"fecha_hora_inicio" => $fila["fecha_hora_inicio"],
								"fecha_hora_fin" => $fila["fecha_hora_fin"],
								"id_usuario" => $fila["id_usuario"],
								"login_usuario" => $fila["login_usuario"],
								"nombre_usuario" => $fila["nombre_usuario"],
								"apellidos_usuario" => $fila["apellidos_usuario"],
								"id_zona_comun" => $fila["id_zona_comun"],
								"nombre_zona_comun" => $fila["nombre_zona_comun"]
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