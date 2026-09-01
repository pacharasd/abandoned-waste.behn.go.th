<?php
@mkdir(__DIR__ . '/../public/assets/images', 0777, true);

$urls = [
    'https://upload.wikimedia.org/wikipedia/th/f/f6/Seal_Nonthaburi_Town.png',
    'https://upload.wikimedia.org/wikipedia/commons/thumb/c/cb/Seal_Nonthaburi.png/600px-Seal_Nonthaburi.png',
    'https://nakornnont.go.th/images/content/logo-139-1/logo.png'
];

$dest = __DIR__ . '/../public/assets/images/nonthaburi-logo.png';

$context = stream_context_create([
    'http' => [
        'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
    ],
    'ssl' => [
        'verify_peer' => false,
        'verify_peer_name' => false
    ]
]);

$success = false;
foreach ($urls as $url) {
    echo "Trying $url ...\n";
    $content = @file_get_contents($url, false, $context);
    if ($content && strlen($content) > 1000) {
        file_put_contents($dest, $content);
        echo "Successfully downloaded to $dest (" . strlen($content) . " bytes)\n";
        $success = true;
        break;
    }
}

if (!$success) {
    echo "Could not download directly.\n";
}
