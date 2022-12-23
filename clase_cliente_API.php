<?

    class clase_cliente_API{
        private $URLAPI = "http://localhost/api/";
        private $nombreServicioAPI = '';    
        private $arrayDatosJSON = array();
        private $datosJSON = '';
        private $cliente = null;

        public function setURLAPI($URLAPI){
            $this->URLAPI = $URLAPI;
            $this->cliente = curl_init($this->URLAPI.'?servicio='.$this->nombreServicioAPI);
        }

        public function setNombreServicioAPI($nombreServicioAPI){
            $this->nombreServicioAPI = $nombreServicioAPI;
            //Creamos el  cURL resource
            $this->cliente = curl_init($this->URLAPI.'?servicio='.$this->nombreServicioAPI);
        }

        public function setDatosJSON($arrayDatosJSON){
            $this->arrayDatosJSON = $arrayDatosJSON;
            $this->datosJSON = json_encode($arrayDatosJSON);
        }

        public function ejecutar(){
            try{
                $JSONdata = $this->datosJSON;
    
                //Añadimos como POST los datos en formato JSON a enviar al servicio
                curl_setopt($this->cliente, CURLOPT_POSTFIELDS, $JSONdata);
            
                //Establecemos el content type a application/json (Los datos serán JSON)
                curl_setopt($this->cliente, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
            
                //configuramos el cliente para que retorne datos
                curl_setopt($this->cliente, CURLOPT_RETURNTRANSFER, true);
            
                //ejecutamos la petición POST
                return curl_exec($this->cliente);
            }catch(Exception $e){
                $response=[];
                $response['code'] = 10;
                $response['status'] = "500";	
                $response['message'] = "ERROR: ".$e->getMessage();
                $response['data'] = array(
                                            'resultado' => 'no_ok',
                                            'id'=> 0
                );
                return json_encode($response);
            }finally{
                curl_close($this->cliente);
            }
        
        }

    }