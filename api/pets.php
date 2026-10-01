<?php
header('Content-Type: application/json');

$file = __DIR__ . '/../data/pets.json';

if (!file_exists($file)) {
    echo json_encode([]);
    exit;
}

$data = json_decode(file_get_contents($file), true);
$allowedTypes = ['cat', 'dog', 'hamster'];
$pets = array_filter($data ?: [], static function ($pet) use ($allowedTypes) {
    return isset($pet['type']) && in_array(strtolower($pet['type']), $allowedTypes, true);
});

echo json_encode(array_values($pets));
