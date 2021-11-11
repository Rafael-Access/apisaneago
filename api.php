<?php
    function requisitarApi($params){
        $dir = $_SERVER['DOCUMENT_ROOT']."/ctemp";
        $path = tempnam(sys_get_temp_dir(), 'MyTempCookie');
        $cookie_file_path = $path."/cookie.txt";
        $urlprod = 'http://prod.saneago.com.br/prt/';
        $url = 'http://homolog.saneago.com.br/prt/';
        // $url = '172.16.1.171/prt/';
        // $token = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJuYW1lIjoiQWNjZXNzIENvbnRhY3QiLCJpYXQiOjE1MTYyMzkwMjJ9.kLCEA4WlmIBQ61SWWmE-AHWS5S2_ETWhRORvm2Nx00Q";
       
        $header = array();
        // $header[] = 'Authorization: '.$token;
        $header[] = 'Content-type: application/json';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);      
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT , 260 );
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        if($params['metodo']=='POST'){
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_POST, 1);
            $body = json_encode($params['params']);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
            curl_setopt($ch, CURLOPT_URL, $url.$params['url']);
            var_dump($body);
        }
        if($params['metodo']=='GET'){
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
            foreach($params['params'] as $k => $v){
                $params['url'] = str_replace('{'.$k.'}', $v, $params['url']);
            }
            curl_setopt($ch, CURLOPT_URL, $url.$params['url']);
        }
        curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file_path);

        $retorno = curl_exec($ch);
        return json_decode($retorno);
    }

    // gera token
    $params = array(
        'login' => [
            "url" => 'ws/login',
            "metodo" => 'POST',
            "header" => '',
            "params" => ["matricula" => "MSI0011", "senha" => "Ura#908173"],
            "resposta" => "token",
            "status" => ''
        ],
        'getProtocolo' => [
            "url" => 'ws/EAC/protocolo/',
            "metodo" => 'POST',
            "header" => 'token',
            "params" => ["telefone" => ""],
            "resposta" => "protocolo",
            "status" => 'Código de status: 201 Created'
        ],
        'checkFaltaAgua' => [
            "url" => 'ws/GPM/conta/{numConta}/ConsultarFaltaDagua?protocolo={protocolo}',
            "metodo" => 'GET',
            "header" => 'token',
            "params" => ["numConta" => "", "protocolo" => ""],
            "resposta" => [
                "DataHoraProgramada" => 'timestamp',
                'DataHoraNormalizacao' => 'timestamp'
            ],
            "status" => 'Código de status: 200 Ok'
        ],
        'checkDebitos' => [
            "url" => 'ws/ECO/conta/{numConta}/ConsultarDebitosAbertos?protocolo={protocolo}',
            "metodo" => 'GET',
            "header" => 'token',
            "params" => ["numConta" => "", "protocolo" => ""],
            "resposta" => [
                "valorDebito" => 'decimal',
                'qtdeFaturas' => 'numerico',
                'CpfCnp' => 'numerico',
                'valorDebitoCliente' => 'decimal',
                'qtdeFaturasCliente' => 'numerico'
            ],
            "status" => 'Código de status: 200 Ok'
        ],
        'checkReligacao' => [
            "url" => 'ws/ECO/conta/{numConta}/ValidarSolicitacaoReligacao?protocolo={protocolo}',
            "metodo" => 'GET',
            "header" => 'token',
            "params" => ["numConta" => "", "protocolo" => ""],
            "resposta" => [
                "valorReligacaoNormal" => 'decimal',
                'valorReligacaoUrgente' => 'decimal'               
            ],
            "status" => 'Código de status: 200 Ok'
        ],
        'solicitaReligacao' => [
            "url" => 'ws/ECO/conta/{numConta}/SolicitarReligacao?protocolo={protocolo}',
            "metodo" => 'GET',
            "header" => 'token',
            "params" => ["numConta" => "", "protocolo" => ""],
            "resposta" => 'A sua solicitação de religação foi registrada com sucesso.',
            "status" => 'Código de status: 200 Ok'
        ]
    );
    
    echo "<pre>";
    $get = requisitarApi($params['login']);
    var_dump($get);
