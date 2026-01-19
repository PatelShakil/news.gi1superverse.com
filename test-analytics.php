<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analytics Test</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }

        .test-box {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .status {
            padding: 10px;
            border-radius: 4px;
            margin: 10px 0;
        }

        .success {
            background: #d4edda;
            color: #155724;
        }

        .error {
            background: #f8d7da;
            color: #721c24;
        }

        .info {
            background: #d1ecf1;
            color: #0c5460;
        }

        button {
            background: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
        }

        button:hover {
            background: #0056b3;
        }

        pre {
            background: #f8f9fa;
            padding: 10px;
            border-radius: 4px;
            overflow-x: auto;
        }
    </style>
</head>

<body>
    <h1>🧪 Analytics Configuration Test</h1>

    <div class="test-box">
        <h2>Environment Detection</h2>
        <div id="env-info"></div>
    </div>

    <div class="test-box">
        <h2>Global Variables</h2>
        <div id="var-info"></div>
    </div>

    <div class="test-box">
        <h2>Analytics Test</h2>
        <button onclick="testAnalytics()">Test Analytics Tracking</button>
        <div id="analytics-result"></div>
    </div>

    <div class="test-box">
        <h2>Console Logs</h2>
        <pre id="console-logs"></pre>
    </div>

    <?php
    require_once __DIR__ . '/includes/config.php';
    ?>

    <script>
        // Set test variables
        window.newsId = 1;
        window.apiBaseUrl = '<?= API_BASE_URL ?>';

        // Display environment info
        document.getElementById('env-info').innerHTML = `
            <div class="info">
                <strong>Environment:</strong> <?= IS_LOCAL_ENV ? 'LOCAL (XAMPP)' : 'PRODUCTION' ?><br>
                <strong>Site URL:</strong> <?= SITE_URL ?><br>
                <strong>API Base URL:</strong> <?= API_BASE_URL ?><br>
                <strong>Base Path:</strong> <?= BASE_PATH ?: '(empty)' ?><br>
                <strong>Error Reporting:</strong> <?= IS_LOCAL_ENV ? 'ON' : 'OFF' ?>
            </div>
        `;

        // Display global variables
        document.getElementById('var-info').innerHTML = `
            <div class="info">
                <strong>window.newsId:</strong> ${window.newsId}<br>
                <strong>window.apiBaseUrl:</strong> ${window.apiBaseUrl}
            </div>
        `;

        // Override console.log to capture logs
        const logs = [];
        const originalLog = console.log;
        const originalWarn = console.warn;
        const originalError = console.error;

        console.log = function (...args) {
            logs.push(['LOG', ...args]);
            updateLogs();
            originalLog.apply(console, args);
        };

        console.warn = function (...args) {
            logs.push(['WARN', ...args]);
            updateLogs();
            originalWarn.apply(console, args);
        };

        console.error = function (...args) {
            logs.push(['ERROR', ...args]);
            updateLogs();
            originalError.apply(console, args);
        };

        function updateLogs() {
            const logsEl = document.getElementById('console-logs');
            logsEl.textContent = logs.map(log => log.join(' ')).join('\n');
        }

        function testAnalytics() {
            const resultEl = document.getElementById('analytics-result');
            resultEl.innerHTML = '<div class="info">Testing... Please check console and logs below.</div>';

            // Call trackAnalytics if available
            if (typeof trackAnalytics === 'function') {
                trackAnalytics();
            } else {
                // Manual test
                const newsId = window.newsId;
                const apiBaseUrl = window.apiBaseUrl;

                console.log('Tracking analytics for newsId:', newsId);

                if (!newsId) {
                    console.warn('Analytics tracking skipped: newsId is not defined');
                    resultEl.innerHTML = '<div class="error">❌ newsId is not defined</div>';
                    return;
                }

                if (!apiBaseUrl) {
                    console.warn('Analytics tracking skipped: apiBaseUrl is not defined');
                    resultEl.innerHTML = '<div class="error">❌ apiBaseUrl is not defined</div>';
                    return;
                }

                const analyticsData = {
                    ip_address: '',
                    user_agent: navigator.userAgent,
                    device_type: 'desktop',
                    browser: 'Chrome',
                    operating_system: 'Windows',
                    source: document.referrer || 'direct',
                    session_id: 'test_session',
                    utm_source: null,
                    utm_medium: null,
                    utm_campaign: null,
                    utm_content: null,
                };

                fetch(`${apiBaseUrl}/news/analytics/${newsId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(analyticsData)
                })
                    .then(response => {
                        console.log('Response status:', response.status);
                        return response.json();
                    })
                    .then(data => {
                        console.log('Analytics tracked successfully:', data);
                        resultEl.innerHTML = '<div class="success">✅ Analytics tracked successfully! Check console for details.</div>';
                    })
                    .catch(error => {
                        console.error('Error tracking analytics:', error);
                        resultEl.innerHTML = `<div class="error">❌ Error: ${error.message}</div>`;
                    });
            }
        }

        // Auto-test on load
        console.log('=== Analytics Test Page Loaded ===');
        console.log('Environment:', '<?= IS_LOCAL_ENV ? 'LOCAL' : 'PRODUCTION' ?>');
        console.log('newsId:', window.newsId);
        console.log('apiBaseUrl:', window.apiBaseUrl);
    </script>

    <script src="assets/js/analytics.js"></script>
</body>

</html>