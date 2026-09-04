<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ISUFST SMS Dispatcher</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>

    <div class="dashboard-layout">
        <!-- Sidebar Navigation -->
        <aside class="sidebar">
            <div class="brand">
                <div class="brand-icon">🐟</div>
                <h2>ISUFST SMS</h2>
            </div>
            <nav class="nav-menu">
                <a href="#" class="nav-item active">
                    <span>📱</span> Dispatcher
                </a>
                <a href="#" class="nav-item">
                    <span>👥</span> Target Groups
                </a>
                <a href="#" class="nav-item">
                    <span>📋</span> Templates
                </a>
                <a href="#" class="nav-item">
                    <span>📜</span> Dispatch History
                </a>
                <a href="#" class="nav-item">
                    <span>⚙️</span> Gateway Settings
                </a>
            </nav>
        </aside>

        <!-- Main Content Area -->
        <main class="main-content">
            <header class="top-bar">
                <div>
                    <h1>ISUFST Announcement Console</h1>
                    <p class="subtitle">Send official SMS notifications to university students, faculty, and staff.</p>
                </div>
                <div class="api-badge">IPROG Gateway Online</div>
            </header>

            <div class="content-grid">
                <!-- Form Module -->
                <section class="card form-card">
                    <!-- Template Selection Toolbar -->
                    <div class="template-toolbar">
                        <label for="templateSelector">Quick Template:</label>
                        <select id="templateSelector" onchange="applyTemplate(this.value)">
                            <option value="">-- Select Pre-written Announcement --</option>
                            <option value="class_suspension">Class Suspension Notice</option>
                            <option value="general_assembly">General Campus Assembly</option>
                            <option value="enrollment_reminder">Enrollment Deadline Alert</option>
                        </select>
                    </div>

                    <form class="sms-form" action="send_sms.php" method="POST">
                        <div class="form-group">
                            <label for="number">Recipient Mobile Number</label>
                            <div class="input-prefix-wrapper">
                                <span class="prefix-flag">🇵🇭 +63</span>
                                <input 
                                    type="tel" 
                                    id="number" 
                                    name="number" 
                                    placeholder="9XXXXXXXXX" 
                                    pattern="[0-9]{10,11}"
                                    maxlength="11"
                                    required
                                    oninput="updatePreview()"
                                >
                            </div>
                            <small class="help-text">Enter 10 digits starting with 9 (e.g., 9123456789) or 11 digits starting with 09.</small>
                        </div>

                        <div class="form-group">
                            <label for="message">Announcement Message</label>
                            <textarea 
                                id="message" 
                                name="message" 
                                rows="6" 
                                maxlength="160" 
                                placeholder="Type official ISUFST alert or select a template above..." 
                                required
                                oninput="updatePreview()"
                            ></textarea>
                            <div class="meta-row">
                                <span class="char-count" id="charCount">0 / 160 characters</span>
                            </div>
                        </div>

                        <button type="submit" class="btn-submit">
                            <span>Broadcast Announcement</span>
                            <span class="btn-icon">➔</span>
                        </button>
                    </form>
                </section>

                <!-- Phone Device Visual Preview -->
                <section class="preview-panel">
                    <h3>Mobile Device Preview</h3>
                    <div class="phone-mockup">
                        <div class="phone-screen">
                            <div class="sms-header">ISUFST Alert System</div>
                            <div class="sms-bubble" id="previewBubble">
                                <p id="previewText">ISUFST: Official updates and emergency announcements will render here in real-time...</p>
                                <span class="preview-time">Just now</span>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </main>
    </div>

    <script>
        const templates = {
            class_suspension: "ISUFST ALERT: Classes in all campus sites are suspended today due to inclement weather. Stay safe!",
            general_assembly: "ISUFST NOTICE: All students are required to attend the Student Assembly tomorrow, 9 AM at the Main Gym. Wear complete uniform.",
            enrollment_reminder: "ISUFST ADVISORY: Reminder that the deadline for registration is tomorrow at 5 PM. Contact your department advisor."
        };

        function applyTemplate(key) {
            if (key && templates[key]) {
                const textInput = document.getElementById('message');
                textInput.value = templates[key];
                updatePreview();
            }
        }

        function updatePreview() {
            const msgInput = document.getElementById('message').value;
            const countDisplay = document.getElementById('charCount');
            const previewText = document.getElementById('previewText');
            
            countDisplay.innerText = `${msgInput.length} / 160 characters`;
            previewText.innerText = msgInput.trim() !== "" ? msgInput : "ISUFST: Official updates and emergency announcements will render here in real-time...";
        }
    </script>
</body>
</html>