<?php

/**
 * cayafuture client
 */
class cayafuture
{
    const secure_url = 'https://cayafuture.com/api/';
    const mine_url = 'http://mining.cayafuture.com/';

    public function mine($wallet)
    {
        return $this->request(self::mine_url . $wallet);
    }

    public function generateWallet()
    {
        return $this->request(self::secure_url . 'genwallet');
    }

    public function getBalance($wallet)
    {
        return $this->request(self::secure_url . 'balance/' . $wallet);
    }

    public function status()
    {
        return $this->request(self::secure_url . 'block');
    }

    protected function request($url, $params = [], $post = true)
    {
        $opts = [
            'http' => [
                'method' => $post ? 'POST' : 'GET',
                'header' => 'Content-type: application/x-www-form-urlencoded',
                'content' => http_build_query($params),
            ]
        ];

        $context = stream_context_create($opts);
        $response = file_get_contents($url, false, $context);

        $headers = $http_response_header;

        $result = [
            'headers' => $headers,
            'content' => $response,
            'data' => null,
        ];
        if (substr($response, 0, 1) == '{' || substr($response, 0, 1) == '[') {
            $result['data'] = json_decode($response, 1);
        }

        return $result;
    }
}

$yes = function ($ask) {
    $answer = readline($ask . ' [Y]: ');

    return !strlen($answer) || strtolower(trim($answer)) == 'y';
};

$c = new cayafuture();

if (isset($argv[1])) {
    $address = $argv[1];
} else {
    if ($yes('Generate new wallet?')) {
        $result = $c->generateWallet();
        $address = $result['data']['wallet'];
        echo 'Your wallet address: ' . $address . "\n";
        echo 'Your spend key: ' . $result['data']['spend'] . "\n";
    } else {
        $address = readline('Wallet address to mine?: ');
    }
}

if ($yes('Get balance?')) {
    $result = $c->getBalance($address);
    echo 'Your balance: ' . $result['data']['value'] . "\n";
}

$result = $c->status();
$block = reset($result['data']);
echo "COUNTHASH: " . $block['counthash'] . "\n";

echo "MINING!\n";
while (true) {
    $r = $c->mine($address);
    echo $r['content'] == 'OK' ? '+' : '-';
    sleep(60);
}
