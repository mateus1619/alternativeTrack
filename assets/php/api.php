<?php

header('Content-Type: application/json; charset=utf-8');

sleep(0);
set_time_limit(10);


$number = $_POST['lista'];

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, 'https://graphql.intelipost.com.br/');
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, '{"operationName":null,"variables":{"orderHash":"19911","clientId":"19911","orderNumber":"'.$number.'"},"query":"query ($clientId: ID, $orderNumber: String, $orderHash: String) {\\n  trackingStatus(clientId: $clientId, orderNumber: $orderNumber, orderHash: $orderHash) {\\n    client {\\n      logo_url\\n      locale\\n      id\\n    }\\n    order {\\n      order_number\\n      sales_order_number\\n    }\\n    tracking {\\n      status\\n      status_label\\n      last_status_with_error\\n      tracking_codes\\n      estimated_delivery_date\\n      estimated_delivery_date_lp\\n      micro_state {\\n        id\\n        name\\n        description\\n      }\\n      show_detail\\n      history {\\n        event_date\\n        status_label\\n        provider_message\\n        micro_state {\\n          id\\n          description\\n          name\\n        }\\n        is_warning\\n      }\\n    }\\n    logistic_provider {\\n      name\\n      delivery_method {\\n        name\\n        logo_url\\n      }\\n      live_tracking_url\\n    }\\n    sender {\\n      address {\\n        city\\n        zip_code\\n        state_code\\n      }\\n    }\\n    end_customer {\\n      address {\\n        city\\n        zip_code\\n        state\\n      }\\n    }\\n    shipment_order_sub_type\\n    shipment_order_type\\n  }\\n}\\n"}');
curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');

$headers = array();
$headers[] = 'Host: graphql.intelipost.com.br';
$headers[] = 'Sec-Ch-Ua: \".Not/A)Brand\";v=\"99\", \"Google Chrome\";v=\"103\", \"Chromium\";v=\"103\"';
$headers[] = 'Accept: */*';
$headers[] = 'Content-Type: application/json';
$headers[] = 'Sec-Ch-Ua-Mobile: ?0';
$headers[] = 'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/103.0.0.0 Safari/537.36';
$headers[] = 'Sec-Ch-Ua-Platform: \"Windows\"';
$headers[] = 'Origin: https://status.ondeestameupedido.com';
$headers[] = 'Sec-Fetch-Site: cross-site';
$headers[] = 'Sec-Fetch-Mode: cors';
$headers[] = 'Sec-Fetch-Dest: empty';
$headers[] = 'Accept-Language: pt-BR,pt;q=0.9';
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
$result = curl_exec($ch);
curl_close($ch);


if(strpos($result, 'Número do pedido desconhecido')) {
    header("Status: 404 Not Found");
    $resultado = [
        "situacao" => "Pedido em processamento ou não existe!",
    ];
    return;
    print_r(json_encode($resultado, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
}

$decod = json_decode($result, true);

$history = $decod['data']['trackingStatus']['tracking']['history'];
$result_array_json = [];

for($i=1; $i < sizeof($history); ++$i) {
    # Extraindo Dados #
    $dataHora = $decod['data']['trackingStatus']['tracking']['history'][$i - 1]['event_date'];
    $descricao = $decod['data']['trackingStatus']['tracking']['history'][$i - 1]['micro_state']['description'];

    # Formatando o Horário 'str_replace()' #
    $horaFormat = str_replace('T', " ", $dataHora);
    $horaFormat = str_replace('+00:00', " ", $horaFormat);
    $horaFormat = str_replace('-', "/", $horaFormat);

    # Excluindo os caracteres :00 #
    $regex = '/[0-9]{4}\/[0-9]{2}\/[0-9]{2} [0-9]{2}:[0-9]{2}/';
    preg_match_all($regex, $horaFormat, $matches);

    if( !empty($matches) ) {
        foreach($matches[0] as $match) {
            # Resultado final #
            header("HTTP/1.1 200 OK");
            $resultado = [
                "datahora" => "$match",
                "situacao" => "$descricao",
        ];
            $result_array_json[] = $resultado;
        }
    }
}

echo json_encode(["livetrack" => $result_array_json], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

