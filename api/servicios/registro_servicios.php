<?php

    //AQUI TENEMOS QUE REGISTRAR CADA UNO DE LOS SERVICIOS 
    switch ($servicio) {
        //================================================
        //INICIOS DE SESION
        //================================================
        case 'login':
            include_once('servicios/login.php');
            break;                   
        //================================================
        //ALTAS
        //================================================ 
        case 'alta_usuario':
            include_once('servicios/alta_usuario.php');
            break;  
        case 'registrar_usuario':
                include_once('servicios/registrar_usuario.php');
                break;      
        case 'alta_comunidad':
            include_once('servicios/alta_comunidad.php');
            break;                
        case 'alta_producto':
            include_once('servicios/alta_producto.php');
            break;   
        //================================================
        //MODIFICACIONES
        //================================================ 
        case 'modificar_comunidad':
            include_once('servicios/modificar_comunidad.php');
            break;
        //================================================     
        //CONSULTAS
        //================================================    
        case 'obtener_comunidades':
            include_once('servicios/obtener_comunidades.php');
            break;    
        case 'obtener_reservas':
            include_once('servicios/obtener_reservas.php');
            break;               
        case 'obtener_productos':
            include_once('servicios/obtener_productos.php');
            break;              
        //================================================     
        //ELIMINACIONES     
        //================================================     
        case 'eliminar_usuario':
            include_once('servicios/eliminar_usuario.php');
            break;
        case 'eliminar_producto':
            include_once('servicios/eliminar_producto.php');
            break;                
        //TEST DE ESTADO    
        case 'ping':
                include_once('servicios/ping.php');    
                break;
            default:
                $response['code'] = 5;
                $response['status'] = $api_response_code[ $response['code'] ]['HTTP Response'];
                $response['data'] = $api_response_code[$response['code']]['Message'];
                break;
        }

?>