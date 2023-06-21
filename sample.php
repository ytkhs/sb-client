<?php
require_once 'client.php';

$client = SBClient::getInstance();

dump($devices = $client->getDevices());
foreach($devices['body']['deviceList'] as $device) {
    dump($client->getDeviceStatus($device['deviceId']));
}
foreach($devices['body']['infraredRemoteList'] as $device) {
    dump($client->getDeviceStatus($device['deviceId']));
}