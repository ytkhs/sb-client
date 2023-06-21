<?php
require_once 'client.php';

$client = SBClient::getInstance();

dump($devices = $client->getDevices());
foreach($devices['body']['deviceList'] as $device) {
    dump($client->getDeviceStatus($device['deviceId']));
}