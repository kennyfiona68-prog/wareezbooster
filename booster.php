<?php
require '../config.php';
require '../Api.php';
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: POST, OPTIONS');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') exit;

$input = json_decode(file_get_contents('php://input'), true);

$service_id = $input['service_id'] ?? 0;
$link = $input['link'] ?? '';
$qty = $input['quantity'] ?? 0;
$orderId = $input['order_id'] ?? null;

if (!$service_id || !$link || !$qty) {
    echo json_encode(['error' => 'Missing service_id, link, quantity']);
    exit;
}

try {
    $api = new Api();
    $result = $api->order([
        'service' => (int)$service_id,
        'link' => $link,
        'quantity' => (int)$qty
    ]);

    // Update orders.json with api_order_id
    $ordersFile = __DIR__ . '/orders.json';
    if (file_exists($ordersFile) && $orderId) {
        $orders = json_decode(file_get_contents($ordersFile), true) ?? [];
        foreach ($orders as &$o) {
            if ($o['id'] === $orderId) {
                $o['api_order_id'] = $result->order ?? null;
                $o['api_response'] = $result;
                $o['status'] = 'completed';
            }
        }
        file_put_contents($ordersFile, json_encode($orders, JSON_PRETTY_PRINT));
    }

    echo json_encode(['success' => true, 'api_result' => $result]);

} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
