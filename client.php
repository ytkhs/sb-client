<?php

require_once 'vendor/autoload.php';

class SBClient
{

    private static SBClient $instance;

    protected function __construct()
    {
        $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
        $dotenv->load();
    }

    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new SBClient();
        }

        return self::$instance;
    }

    public static function getDevices()
    {
        return self::makeRequest('/devices');
    }

    public static function getDeviceStatus($deviceId)
    {
        $path = sprintf('/devices/%s/status', $deviceId);
        return self::makeRequest($path);
    }

    private static function makeRequest($path = '/', $method = 'GET')
    {
        $response = file_get_contents($_ENV['SWITCHBOT_ENDPOINT'] . $path, false, self::createContext($method));
        return json_decode($response, true);
    }

    private static function createContext($method, array $data = [])
    {
        $token = $_ENV['SWITCHBOT_TOKEN'];
        $secret = $_ENV['SWITCHBOT_SECRET'];
        $nonce = self::guidv4();
        $timestamp = time() * 1000;

        return stream_context_create([
            'http' => [
                'method' => $method,
                'header' =>  [
                    'Content-Type: application/json',
                    'Authorization: ' . $token,
                    'sign: ' . strtoupper(base64_encode(hash_hmac('sha256', mb_convert_encoding($token . $timestamp . $nonce, 'UTF-8'), $secret, true))),
                    'nonce: ' . $nonce,
                    't: ' . $timestamp
                ],
                'content' => json_encode($data)
            ]
        ]);
    }

    private static function guidv4($data = null)
    {
        // Generate 16 bytes (128 bits) of random data or use the data passed into the function.
        $data = $data ?? random_bytes(16);
        assert(strlen($data) == 16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);

        // Output the 36 character UUID.
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}