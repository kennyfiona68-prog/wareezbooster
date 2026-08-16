<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

$ordersFile = __DIR__ . '/orders.json';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) $input = $_POST;
    
    $orders = [];
    if (file_exists($ordersFile)) {
        $orders = json_decode(file_get_contents($ordersFile), true) ?? [];
    }
    
    $newOrder = [
        'id' => 'WB-' . rand(100000, 999999),
        'package_name' => $input['package_name'] ?? '',
        'qty' => $input['qty'] ?? 0,
        'price' => $input['price'] ?? 0,
        'link' => $input['link'] ?? '',
        'whatsapp' => $input['whatsapp'] ?? '',
        'status' => 'pending_payment',
        'proof' => $input['proof'] ?? '',
        'created_at' => date('Y-m-d H:i:s'),
        'api_order_id' => null,
        'api_status' => null
    ];
    
    $orders[] = $newOrder;
    file_put_contents($ordersFile, json_encode($orders, JSON_PRETTY_PRINT));
    
    echo json_encode(['success' => true, 'order' => $newOrder]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $orders = [];
    if (file_exists($ordersFile)) {
        $orders = json_decode(file_get_contents($ordersFile), true) ?? [];
    }
    // newest first
    $orders = array_reverse($orders);
    echo json_encode($orders);
    exit;
}
