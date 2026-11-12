<?php
session_start();
require_once '../../config/database.php';
require_once '../../includes/functions.php';

header('Content-Type: application/json');

$response = ['success' => false, 'documents' => null];

$vendorId = $_GET['vendor_id'] ?? 0;

if (!$vendorId) {
    echo json_encode($response);
    exit;
}

$db = new Database();
$vendor = $db->fetch("SELECT verification_documents FROM vendor_profiles WHERE id = ?", [$vendorId]);

if ($vendor && !empty($vendor['verification_documents'])) {
    $response['success'] = true;
    $response['documents'] = json_decode($vendor['verification_documents'], true);
}

echo json_encode($response);