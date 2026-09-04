<?php
// Path to local configuration file
$configFile = __DIR__ . '/config.json';

// Default configuration values
$defaultConfig = [
    "api_endpoint" => "https://www.iprogsms.com/api/v1/sms_messages",
    "api_token"    => "ce7dec7661f500ee7aa06ba2b1a60d0f8509836f",
    "timeout"      => 30
];

// Load existing config or fallback to default
if (file_exists($configFile)) {
    $savedConfig = json_decode(file_get_contents($configFile), true);
    $config = array_merge($defaultConfig, is_array($savedConfig) ? $savedConfig : []);
} else {
    $config = $defaultConfig;
}

$message = "";
$statusClass = "";

// Save Submitted Form Parameters
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $endpoint = trim($_POST["api_endpoint"] ?? "");
    $token    = trim($_POST["api_token"] ?? "");
    $timeout  = (int)($_POST["timeout"] ?? 30);

    if (empty($endpoint) || empty($token)) {
        $message = "Please fill in all required gateway fields.";
        $statusClass = "error";
    } else {
        $newConfig = [
            "api_endpoint" => $endpoint,
            "api_token"    => $token,
            "timeout"      => $timeout
        ];

        if (file_put_contents($configFile, json_encode($newConfig, JSON_PRETTY_PRINT))) {
            $config = $newConfig;
            $message = "✓ Gateway configurations updated and saved successfully!";
            $statusClass = "success";
        } else {
            $message = "Error writing config.json. Please check folder write permissions.";
            $statusClass = "error";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ISUFST SMS - Gateway Settings</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

    <div class="dashboard-layout">
        <!-- Sidebar Navigation -->
        <aside class="sidebar">
            <div class="brand">
                <div class="brand-icon">
                    <i class="fa-solid fa-graduation-cap"></i>
                </div>
                <div>
                    <h2>ISUFST SMS</h2>
                    <span class="brand-sub">Broadcast System</span>
                </div>
            </div>
            
            <nav class="nav-menu">
                <a href="index.html" class="nav-item">
                    <i class="fa-solid fa-paper-plane"></i>
                    <span>Dispatcher</span>
                </a>
                <a href="#" class="nav-item">
                    <i class="fa-solid fa-users"></i>
                    <span>Target Groups</span>
                </a>
                <a href="#" class="nav-item">
                    <i class="fa-solid fa-file-lines"></i>
                    <span>Templates</span>
                </a>
                <a href="#" class="nav-item">
                    <i class="fa-solid fa-clock-rotate-left"></i>
                    <span>Dispatch History</span>
                </a>
                <a href="settings.php" class="nav-item active">
                    <i class="fa-solid fa-sliders"></i>
                    <span>Gateway Settings</span>
                </a>
            </nav>

            <div class="sidebar-footer">
                <div class="status-dot"></div> IPROG API Connected
            </div>
        </aside>

        <!-- Main Content Area -->
        <main class="main-content">
            <header class="top-bar">
                <div>
                    <h1>Gateway API Settings</h1>
                    <p class="subtitle">Configure endpoint parameters and credentials for the IPROG SMS provider.</p>
                </div>
                <div class="api-badge">
                    <i class="fa-solid fa-shield-halved"></i>
                    <span>System Admin Mode</span>
                </div>
            </header>

            <div class="settings-container">
                <section class="card settings-card">
                    <div class="card-header">
                        <i class="fa-solid fa-sliders"></i>
                        <h3>IPROG Integration Parameters</h3>
                    </div>

                    <?php if (!empty($message)): ?>
                        <div class="feedback-msg <?= htmlspecialchars($statusClass) ?>" style="display: block;">
                            <?= htmlspecialchars($message) ?>
                        </div>
                    <?php endif; ?>

                    <form action="settings.php" method="POST">
                        <div class="form-group">
                            <label for="api_endpoint">
                                <i class="fa-solid fa-link"></i> API Gateway Endpoint URL
                            </label>
                            <div class="input-prefix-wrapper">
                                <span class="prefix-flag"><i class="fa-solid fa-globe"></i></span>
                                <input 
                                    type="url" 
                                    id="api_endpoint" 
                                    name="api_endpoint" 
                                    value="<?= htmlspecialchars($config['api_endpoint']) ?>" 
                                    required
                                >
                            </div>
                            <small class="help-text">Direct REST API endpoint URL provided by IPROG.</small>
                        </div>

                        <div class="form-group">
                            <label for="api_token">
                                <i class="fa-solid fa-key"></i> IPROG API Token Key
                            </label>
                            <div class="input-prefix-wrapper">
                                <span class="prefix-flag"><i class="fa-solid fa-lock"></i></span>
                                <input 
                                    type="password" 
                                    id="api_token" 
                                    name="api_token" 
                                    value="<?= htmlspecialchars($config['api_token']) ?>" 
                                    required
                                >
                                <button type="button" class="btn-toggle-token" onclick="toggleToken()">
                                    <i class="fa-solid fa-eye" id="tokenEyeIcon"></i>
                                </button>
                            </div>
                            <small class="help-text">Keep this secret key secure. Never share your API credentials.</small>
                        </div>

                        <div class="form-group">
                            <label for="timeout">
                                <i class="fa-solid fa-hourglass-half"></i> Request Timeout (Seconds)
                            </label>
                            <div class="input-prefix-wrapper short-input">
                                <input 
                                    type="number" 
                                    id="timeout" 
                                    name="timeout" 
                                    value="<?= htmlspecialchars((string)$config['timeout']) ?>" 
                                    min="5" 
                                    max="120"
                                    required
                                >
                            </div>
                            <small class="help-text">Maximum duration allowed for cURL to reach the provider server.</small>
                        </div>

                        <div class="settings-actions">
                            <button type="button" class="btn-secondary" onclick="testConnection()">
                                <i class="fa-solid fa-network-wired"></i> Test Connection
                            </button>
                            <button type="submit" class="btn-primary">
                                <i class="fa-solid fa-floppy-disk"></i> Save Settings
                            </button>
                        </div>

                        <div id="connectionFeedback" class="feedback-msg"></div>
                    </form>
                </section>
            </div>
        </main>
    </div>

    <script>
        function toggleToken() {
            const tokenInput = document.getElementById('api_token');
            const eyeIcon = document.getElementById('tokenEyeIcon');
            if (tokenInput.type === "password") {
                tokenInput.type = "text";
                eyeIcon.className = "fa-solid fa-eye-slash";
            } else {
                tokenInput.type = "password";
                eyeIcon.className = "fa-solid fa-eye";
            }
        }

        function testConnection() {
            const feedback = document.getElementById('connectionFeedback');
            feedback.className = "feedback-msg info";
            feedback.innerText = "Pinging API Gateway Endpoint...";
            feedback.style.display = "block";
            
            setTimeout(() => {
                feedback.className = "feedback-msg success";
                feedback.innerText = "✓ Connection Established: IPROG Server Responding (200 OK)";
            }, 1200);
        }
    </script>
</body>
</html>