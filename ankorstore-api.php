<?php

class ankorstoreApi {
    private $client;
    private $secret;
    private $token;

    function __construct($client, $secret) {
        $this->client = $client;
        $this->secret = $secret;
        $this->token = $this->auth();
    }

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    private function auth() {
        $url = 'https://www.ankorstore.com/oauth/token';

        $header = array (
            "accept: application/json",
            "content-type: application/x-www-form-urlencoded"
            );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_ENCODING, "");
        curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=client_credentials&client_id=".$this->client."&client_secret=".$this->secret."&scope=*");
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

        $result =  curl_exec($ch);
        $err = curl_error($ch);

        curl_close($ch);

        if ($err) {
            return "cURL Error #:" . $err;
        } 
        else {
            $token = json_decode($result, false);
            //echo $token->access_token;
            return $token->access_token;
        }

    }

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // Returns all orders, can be filtered by retailer ID or by order status //////////////////////////////////////////
    public function listOrders() {
        $url = 'https://www.ankorstore.com/api/v1/orders';

        $curl = curl_init();

        curl_setopt_array($curl, [
        CURLOPT_URL => "https://www.ankorstore.com/api/v1/orders?page%5Blimit%5D=900",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => [
            "Accept: application/vnd.api+json",
            "Content-Type: application/json",
            "Authorization: Bearer " . $this->token
        ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            return "cURL Error #:" . $err;
        } else {
            $data = json_decode($response, false);
            //print_r($data->data[1]);
            return $data->data;
        }
    }

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    public function printOrdersTable($data) {
        $n = 1;
        echo 'ORDERS NUMBER: ' . count($data);
        echo '<br><br>';
        echo '<table>';
        echo '<th> Nº </th>';
        echo '<th> Order ID </th>';
        echo '<th> Status </th>';
        echo '<th> Date created </th>';

        foreach($data as $order) {
            echo '<tr>';
            echo '<td>' . $n . '</td>';
            echo '<td>' . $order->id . '</td>';
            echo '<td>' . $order->attributes->status . '</td>';
            echo '<td>' . $order->attributes->createdAt . '</td>';
            echo '</tr>';
            $n++;
        }
        echo '</table>';
    }

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    public function getOrder($order_id) {
        $curl = curl_init();

        curl_setopt_array($curl, [
        CURLOPT_URL => "https://www.ankorstore.com/api/v1/orders/".$order_id."?include=retailer%2CbillingItems",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => [
            "Accept: application/vnd.api+json",
            "Content-Type: application/json",
            "Authorization: Bearer " . $this->token
        ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            return "cURL Error #:" . $err;
        } 
        else {
            $data = json_decode($response, false);
            //print_r($data->data);
            return $data->data;
        }
    }

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    public function acceptOrder($order_id) {
        $curl = curl_init();

        curl_setopt_array($curl, [
        CURLOPT_URL => "https://www.ankorstore.com/api/v1/orders/".$order_id."/accept",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => "",
        CURLOPT_HTTPHEADER => [
            "Accept: ",
            "Content-Type: application/json",
            "Authorization: Bearer " . $this->token
        ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            return "cURL Error #:" . $err;
        } 
        else {
            //echo $response;
            return $response;
        }
    }

}

?>