<?php
require __DIR__ . '/../../vendor/autoload.php';
$secret = 'whsec_testsecret';
$payload = json_encode([
    'id' => 'evt_test_1',
    'type' => 'payment_intent.succeeded',
    'data' => ['object' => ['id' => 'pi_test_456', 'amount' => 2000, 'currency' => 'usd', 'metadata' => ['user_id' => 1]]]
]);
$t = time();
$signed = hash_hmac('sha256', $t . '.' . $payload, $secret);
$sigHeader = "t={$t},v1={$signed}";
try {
    $event = \Stripe\Webhook::constructEvent($payload, $sigHeader, $secret);
    echo "Verified via SDK. Event type: " . ($event->type ?? '(none)') . PHP_EOL;
    var_dump($event->data->object);
} catch (Exception $e) {
    echo "SDK verification failed: " . $e->getMessage() . PHP_EOL;
}
