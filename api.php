<?php
    function requisitarApi($params){
        $dir = $_SERVER['DOCUMENT_ROOT']."/ctemp";
        $path = tempnam(sys_get_temp_dir(), 'MyTempCookie');
        $cookie_file_path = $path."/cookie.txt";
        $urlprod = 'http://prod.saneago.com.br/prt/';
        $url = 'http://homolog.saneago.com.br/prt/';
        // $url = '172.16.1.171/prt/';
        $token = "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJjaGF2ZXNEZUFjZXNzbyI6WyJCQVBTMDAxfENBSUUiLCJFQUNTMDAxfENBSUUiLCJFQUNTMDAyfENBSUUiLCJFQ09TMDAxfENBSUUiLCJFQ09TMDAyfENBSUUiLCJFQ09TMDAzfENBSUUiLCJFQ09TMDA0fENBSUUiLCJFQ09TMDA1fENBSUUiLCJFQ09TMDA2fENBSUUiLCJFQ09TMDA3fENBSUUiLCJFQ09TMDA4fENBSUUiLCJFQ09TMDA5fENBSUUiLCJFQ09TMDEwfENBSUUiLCJFQ09TMDExfENBSUUiLCJFQ09TMDEyfENBSUUiLCJFQ09TMDEzfENBSUUiLCJFQ09TMDE0fENBSUUiLCJFQ09TMDE1fENBSUUiLCJFQ09TMDE2fENBSUUiLCJFQ09TMDE3fENBSUUiLCJFQ09TMDE4fENBSUUiLCJFQ09TMDE5fENBSUUiLCJFQ09TMDIwfENBSUUiLCJFQ09TMDIxfEMqKioiLCJFR1dTMDAxfENBSUUiLCJHUE1TMDAxfENBSUUiLCJNUFNTMDAxfENBSUUiLCJNUFNTMDAyfENBSUUiLCJNUFNTMDAzfENBSUUiLCJNUFNTMDA0fENBSUUiLCJNU1NTMDAxfENBSUUiLCJNU1NTMDAyfENBSUUiLCJPU0xTMDAxfENBSUUiXSwiZXhwIjoxNjM2NzM2OTk3LCJ1c2VybmFtZSI6Ik1TSTAwMTEifQ.W1g6yYpnjKSRHhrbt2Jg_OWMR6CCgIn7bp_UKMZS1ac";
        $header = array();
        $header[] = 'Content-type: application/json';
        // $header[] = 'Content-type: application/x-www-form-urlencoded';
       
        $header[] = 'token: '.(string)$token;
        $ch = curl_init();
        // curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
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
            var_dump(array($body, $url.$params['url'], $header));
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
            "url" => 'ws/EAC/protocolo',
            "metodo" => 'POST',
            "header" => 'token',
            "params" => ["telefone" => "9"],
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
    $get = requisitarApi($params['getProtocolo']);
    var_dump($get);
    
