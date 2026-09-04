<?php

// Load API parameters from JSON configuration
$configFile = __DIR__ . '/config.json';

if (file_exists($configFile)) {
    $config   = json_decode(file_get_contents($configFile), true);
    $apiToken = $config["api_token"] ?? "ce7dec7661f500ee7aa06ba2b1a60d0f8509836f";
    $url      = $config["api_endpoint"] ?? "https://www.iprogsms.com/api/v1/sms_messages";
    $timeout  = (int)($config["timeout"] ?? 30);
} else {
    $apiToken = "ce7dec7661f500ee7aa06ba2b1a60d0f8509836f";
    $url      = "https://www.iprogsms.com/api/v1/sms_messages";
    $timeout  = 30;
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    die("Invalid request method.");
}

$number  = trim($_POST["number"] ?? "");
$message = trim($_POST["message"] ?? "");

if (empty($number) || empty($message)) {
    die("Mobile number and message are required fields.");
}

// Format Phone Number to Standard 639XXXXXXXXX
$number = preg_replace("/[\s\-\(\)]/", "", $number);

if (strlen($number) === 10 && substr($number, 0, 1) === "9") {
    $number = "63" . $number;
} elseif (substr($number, 0, 2) === "09") {
    $number = "63" . substr($number, 1);
} elseif (substr($number, 0, 3) === "+63") {
    $number = "63" . substr($number, 3);
}

if (!preg_match("/^639\d{9}$/", $number)) {
    die("Invalid Philippine mobile number format.");
}

// Prepare Payload
$data = [
    "api_token"    => $apiToken,
    "phone_number" => $number,
    "message"      => $message
];

// Execute cURL Request
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Content-Type: application/json",
    "Accept: application/json"
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);

$response = curl_exec($ch);

function safeDisplay($value) {
    if (is_array($value) || is_object($value)) {
        return htmlspecialchars(json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    }
    return htmlspecialchars((string)$value);
}

if ($response === false) {
    $error = curl_error($ch);
    curl_close($ch);
    die("<div style='font-family:sans-serif; padding:40px; text-align:center;'><h3 style='color:red;'>Connection Failure</h3><p>".htmlspecialchars($error)."</p><a href='index.html'>← Return</a></div>");
}

curl_close($ch);
$result = json_decode($response, true);
$status = is_array($result) ? ($result["status"] ?? null) : null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ISUFST SMS - Dispatch Result</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body style="display: flex; align-items: center; justify-content: center; min-height: 100vh;">

    <div class="card" style="max-width: 520px; width: 90%; text-align: center;">

        <?php if ((string)$status === "200"): ?>
            <div style="font-size: 48px; margin-bottom: 12px;">✅</div>
            <h2 style="font-size: 22px; font-weight: 800; margin-bottom: 8px;">Broadcast Dispatched</h2>
            <p style="color: var(--text-muted); font-size: 14px; margin-bottom: 24px;">The announcement has been transmitted to the gateway.</p>

            <div style="background: #f8fafc; border: 1px solid var(--border-color); padding: 16px; border-radius: 10px; text-align: left; font-size: 14px; margin-bottom: 24px;">
                <div style="margin-bottom: 8px;"><strong>Recipient:</strong> +<?= safeDisplay($number) ?></div>
                <div><strong>Message:</strong> <?= safeDisplay($message) ?></div>
            </div>

            <a href="index.html" class="btn-submit" style="text-decoration: none;">← Return to Console</a>
        <?php else: ?>
            <div style="font-size: 48px; margin-bottom: 12px;">❌</div>
            <h2 style="font-size: 22px; font-weight: 800; margin-bottom: 8px; color: #ef4444;">Dispatch Failed</h2>
            <p style="color: var(--text-muted); font-size: 14px; margin-bottom: 24px;">Gateway server returned an error error.</p>

            <div style="background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; padding: 16px; border-radius: 10px; text-align: left; font-size: 13px; overflow-x: auto; margin-bottom: 24px;">
                <pre style="margin: 0; white-space: pre-wrap;"><?= safeDisplay($result ?? $response) ?></pre>
            </div>

            <a href="index.html" class="btn-submit" style="background: #ef4444; text-decoration: none;">← Try Again</a>
        <?php endif; ?>

    </div>

</body>
</html>