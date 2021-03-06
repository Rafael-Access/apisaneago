<?php
    function requisitarApi($paramsApi){
        $dir = $_SERVER['DOCUMENT_ROOT']."/ctemp";
        $path = tempnam(sys_get_temp_dir(), 'MyTempCookie');
        $cookie_file_path = $path."/cookie.txt";
        $url = 'http://prod.saneago.com.br/prt/';
        $url = 'http://homolog.saneago.com.br/prt/';
        $url = 'http://prod.saneago.com.br/prt/';
        // $url = '172.16.1.171/prt/';
        $token = "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJjaGF2ZXNEZUFjZXNzbyI6WyJCQVBTMDAxfENBSUUiLCJFQUNTMDAxfENBSUUiLCJFQUNTMDAyfENBSUUiLCJFQ09TMDAxfENBSUUiLCJFQ09TMDAyfENBSUUiLCJFQ09TMDAzfENBSUUiLCJFQ09TMDA0fENBSUUiLCJFQ09TMDA1fENBSUUiLCJFQ09TMDA2fENBSUUiLCJFQ09TMDA3fENBSUUiLCJFQ09TMDA4fENBSUUiLCJFQ09TMDA5fENBSUUiLCJFQ09TMDEwfENBSUUiLCJFQ09TMDExfENBSUUiLCJFQ09TMDEyfENBSUUiLCJFQ09TMDEzfENBSUUiLCJFQ09TMDE0fENBSUUiLCJFQ09TMDE1fENBSUUiLCJFQ09TMDE2fENBSUUiLCJFQ09TMDE3fENBSUUiLCJFQ09TMDE4fENBSUUiLCJFQ09TMDE5fENBSUUiLCJFQ09TMDIwfENBSUUiLCJFQ09TMDIxfEMqKioiLCJFR1dTMDAxfENBSUUiLCJHUE1TMDAxfENBSUUiLCJNUFNTMDAxfENBSUUiLCJNUFNTMDAyfENBSUUiLCJNUFNTMDAzfENBSUUiLCJNUFNTMDA0fENBSUUiLCJNU1NTMDAxfENBSUUiLCJNU1NTMDAyfENBSUUiLCJPU0xTMDAxfENBSUUiXSwiZXhwIjoxNjM3MDcyMDIwLCJ1c2VybmFtZSI6Ik1TSTAwMTEifQ.e5owh4wuwBw_G37z786xbDtdEe1auqEW9q9a5PXkY_Q";
       
        $header = array();
        $header[] = 'Authorization: '.$token;
        $header[] = 'Content-type: application/json';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);      
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT , 260 );
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        if($paramsApi['metodo']=='POST'){
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_POST, 1);
            $body = json_encode($paramsApi['params']);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
            curl_setopt($ch, CURLOPT_URL, $url.$paramsApi['url']);
            var_dump($body);
        }
        if($paramsApi['metodo']=='GET'){
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
            foreach($paramsApi['params'] as $k => $v){
                $paramsApi['url'] = str_replace('{'.$k.'}', $v, $paramsApi['url']);
            }
            var_dump($token);
            echo "<br>";
            var_dump($url.$paramsApi['url']);
            curl_setopt($ch, CURLOPT_URL, $url.$paramsApi['url']);
        }
        curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file_path);
        // Check HTTP status code
       
        $retorno = curl_exec($ch);
        if (!curl_errno($ch)) {
            switch ($http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE)) {
            case 200:  # OK
                break;
            default:
                echo 'Unexpected HTTP code: ', $http_code, "\n";
            }
        }
        return json_decode($retorno);
    }

    // gera token
    $paramsApi = array(
        'login' => [
            "url" => 'ws/login',
            "metodo" => 'POST',
            "header" => '',
            "params" => ["matricula" => "MSI0011", "senha" => "Ura#908173"],
            "resposta" => "token",
            "status" => ''
        ],
        'getProtocolo' => [
            "url" => 'ws/eac/protocolo/',
            "metodo" => 'POST',
            "header" => 'token',
            "params" => ["telefone" => ""],
            "resposta" => "protocolo",
            "status" => 'C??digo de status: 201 Created'
        ],
        'checkFaltaAgua' => [
            "url" => 'ws/gpm/conta/{numConta}/ConsultarFaltaDagua?protocolo={protocolo}',
            "metodo" => 'GET',
            "header" => 'token',
            "params" => ["numConta" => "2442850", "protocolo" => "2021030079970"],
            "resposta" => [
                "DataHoraProgramada" => 'timestamp',
                'DataHoraNormalizacao' => 'timestamp'
            ],
            "status" => 'C??digo de status: 200 Ok'
        ],
        'checkDebitos' => [
            "url" => 'ws/eco/conta/{numConta}/ConsultarDebitosAbertos?protocolo={protocolo}',
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
            "status" => 'C??digo de status: 200 Ok'
        ],
        'checkReligacao' => [
            "url" => 'ws/eco/conta/{numConta}/ValidarSolicitacaoReligacao?protocolo={protocolo}',
            "metodo" => 'GET',
            "header" => 'token',
            "params" => ["numConta" => "", "protocolo" => ""],
            "resposta" => [
                "valorReligacaoNormal" => 'decimal',
                'valorReligacaoUrgente' => 'decimal'               
            ],
            "status" => 'C??digo de status: 200 Ok'
        ],
        'solicitaReligacao' => [
            "url" => 'ws/eco/conta/{numConta}/SolicitarReligacao?protocolo={protocolo}',
            "metodo" => 'GET',
            "header" => 'token',
            "params" => ["numConta" => "", "protocolo" => ""],
            "resposta" => 'A sua solicita????o de religa????o foi registrada com sucesso.',
            "status" => 'C??digo de status: 200 Ok'
        ]
    );
    
    echo "<pre>";
    $get = requisitarApi($paramsApi['login']);
    var_dump($get);
