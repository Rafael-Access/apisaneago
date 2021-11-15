<?php
    function requisitarApi($params){
        $dir = $_SERVER['DOCUMENT_ROOT']."/ctemp";
        $path = tempnam(sys_get_temp_dir(), 'MyTempCookie');
        $cookie_file_path = $path."/cookie.txt";
        // $url = 'http://prod.saneago.com.br/prt/';
        $url = 'http://homolog.saneago.com.br/prt/';
        // $url = '172.16.1.171/prt/';
        // $token="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJjaGF2ZXNEZUFjZXNzbyI6WyJFQUNTMDAxfENBSUUiLCJCQVBTMDAxfEMqKioiLCJGR0NWMDA3fEMqKioiLCJFQ09TMDE5fEMqKioiLCJFQ09TMDA2fEMqKioiLCJFQ09TMDAxfENBSUUiLCJFQ09TMDAyfEMqKioiLCJFR1dTMDAxfEMqKioiLCJFQ09TMDAzfENBSSoiLCJFQ09TMDA0fEMqKioiLCJFQ09TMDIxfEMqKioiLCJNUFNTMDAxfEMqKioiLCJNUFNTMDAyfEMqKioiLCJFQ09TMDE2fEMqKioiLCJFQ09TMDA1fEMqKioiLCJFQ09TMDE0fEMqKioiLCJFQ09TMDE3fEMqKioiLCJFQ09TMDA4fEMqKioiLCJHUE1TMDAxfEMqKioiLCJFQ09TMDA3fENBSSoiLCJFQ09TMDIwfEMqKioiLCJFQ09TMDEwfEMqKioiLCJFQ09TMDExfEMqKioiLCJNUFNTMDAzfEMqKioiLCJFQ09TMDEyfEMqKioiLCJFQ09TMDEzfEMqKioiLCJFQUNTMDAyfCoqSSoiLCJNU1NTMDAxfEMqKioiLCJNU1NTMDAyfEMqKioiLCJPU0xTMDAxfEMqKioiLCJFQ09TMDE1fEMqKioiLCJFQ09TMDA5fEMqKioiLCJFQ09TMDE4fEMqKioiLCJNUFNTMDA0fEMqKioiXSwiZXhwIjoxNjM2ODI3NTI2LCJ1c2VybmFtZSI6Ik1TSTAwMTEifQ.VEXzze1bJNSgjKbThsqCv0AjPgbMmFYWe4JnlJUtdyc";
        if(isset($_POST['token'])){
            $getToken = $_POST['token'];
        }
        $header = array();
        $header[] = 'Content-type: application/json';
        // $header[] = 'Content-type: application/x-www-form-urlencoded';
        if($params['url']!='ws/login'){
            $header[] = 'token: '.(string) $getToken;
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.0; en-US; rv:1.7.12) Gecko/20050915 Firefox/1.0.7");
        // curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT , 120 );
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        if($params['metodo']=='POST'){
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_POST, 1);
            $body = json_encode($params['params']);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
            curl_setopt($ch, CURLOPT_URL, $url.$params['url']);
            // echo '<h5>Envio:';
            // var_dump(array('body'=>$body, 'url'=>$url.$params['url'], 'header'=>$header));
            // echo '</h5>';
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
        if ($retorno === false)
        {
            // throw new Exception('Curl error: ' . curl_error($crl));
            print_r('Curl error: ' . curl_error($ch));
        }
        curl_close($ch);       
        return $retorno;
    }

    $getTelefone="";
    $getUrl = '';
    $getMatricula = '';
    $getSenha = '';
    if(isset($_POST['url'])){
        $getUrl = $_POST['url'];
    }
    if(isset($_POST['matricula'])){
        $getMatricula = $_POST['matricula'];
    }
    if(isset($_POST['senha'])){
        $getSenha = $_POST['senha'];
    }
    if(isset($_POST['telefone'])){
        $getTelefone = $_POST['telefone'];
    }
    if(isset($_POST['numConta'])){
        $getNumConta = $_POST['numConta'];
    }
    if(isset($_POST['protocolo'])){
        $getProtocolo = $_POST['protocolo'];
    }
    
    // gera token
    $params = array(
        'login' => [
            "url" => $getUrl,
            "metodo" => 'POST',
            "header" => '',
            "params" => ["matricula" => $getMatricula, "senha" => $getSenha],
            "resposta" => "token",
            "status" => ''
        ],
        'getProtocolo' => [
            "url" => 'ws/eac/protocolo',
            "metodo" => 'POST',
            "header" => 'token',
            "params" => ["telefone" => $getTelefone],
            "resposta" => "protocolo",
            "status" => 'Código de status: 201 Created'
        ],
        'checkFaltaAgua' => [
            "url" => 'ws/GPM/conta/{numConta}/ConsultarFaltaDagua?protocolo={protocolo}',
            "metodo" => 'GET',
            "header" => 'token',
            "params" => ["numConta" => $getNumConta, "protocolo" => $getProtocolo],
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
            "params" => ["numConta" => $getNumConta, "protocolo" =>  $getProtocolo],
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
            "params" => ["numConta" => $getNumConta, "protocolo" => $getProtocolo],
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
            "params" => ["numConta" => $getNumConta,  "protocolo" => $getProtocolo],
            "resposta" => 'A sua solicitação de religação foi registrada com sucesso.',
            "status" => 'Código de status: 200 Ok'
        ]
    );    
    // echo "<pre>";
    $request = $_POST['request'];
    // $get = requisitarApi($params['login']);
    $get = requisitarApi($params[$request]);
    echo $get;
  
