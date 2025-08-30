<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WebSocket Test - PnP Project</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
        }

        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
        }

        .card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .status {
            padding: 10px;
            border-radius: 4px;
            margin: 10px 0;
            font-weight: bold;
        }

        .connected {
            background-color: #d4edda;
            color: #155724;
        }

        .disconnected {
            background-color: #f8d7da;
            color: #721c24;
        }

        .connecting {
            background-color: #fff3cd;
            color: #856404;
        }

        .signal-buttons {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 10px;
            margin: 20px 0;
        }

        .test-section {
            border: 1px solid #dee2e6;
            border-radius: 4px;
            padding: 15px;
            margin: 15px 0;
        }

        .test-section h3 {
            margin-top: 0;
            color: #495057;
        }

        .log {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            padding: 10px;
            height: 300px;
            overflow-y: auto;
            font-family: monospace;
            font-size: 12px;
            margin: 10px 0;
        }

        .navbar {
            background-color: #fff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container">
            <a class="navbar-brand" href="<?= base_url('/CBase') ?>">
                <i class="fas fa-arrow-left me-2"></i>
                Back to Dashboard
            </a>
            <div class="navbar-nav ms-auto">
                <span class="navbar-text me-3">
                    <i class="fa fa-user me-1"></i>
                    <?= session('username') ?>
                </span>
                <a href="<?= base_url('/CLogin/Logout') ?>" class="btn btn-outline-danger btn-sm">
                    <i class="fa fa-sign-out me-1"></i>
                    Logout
                </a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="card">
            <h1><i class="fas fa-plug me-2"></i>WebSocket Test - PnP Project</h1>
            <p class="text-muted">This page tests the WebSocket functionality implemented for your CodeIgniter 4 project.</p>

            <div class="test-section">
                <h3>Connection Status</h3>
                <div id="status" class="status disconnected">Disconnected</div>
                <button id="connectBtn" onclick="connectWebSocket()" class="btn btn-primary me-2">Connect</button>
                <button id="disconnectBtn" onclick="disconnectWebSocket()" disabled class="btn btn-secondary">Disconnect</button>
            </div>

            <div class="test-section">
                <h3>Send Signals</h3>
                <p>Test the <code>sendSignal()</code> function with different signal types:</p>
                <div class="signal-buttons">
                    <button onclick="testSendSignal('ReportIncome')" id="sendBtn1" disabled class="btn btn-outline-primary">Send 'ReportIncome'</button>
                    <button onclick="testSendSignal('SignalRequest')" id="sendBtn2" disabled class="btn btn-outline-primary">Send 'Signal Request'</button>
                </div>
            </div>

            <div class="test-section">
                <h3>Receive Signals</h3>
                <p>This section demonstrates the <code>receiveSignal()</code> function:</p>
                <div id="receivedSignals"></div>
            </div>

            <div class="test-section">
                <h3>Activity Log</h3>
                <div id="log" class="log"></div>
                <button onclick="clearLog()" class="btn btn-secondary">Clear Log</button>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // WebSocket Client Implementation
        class WebSocketClient {
            constructor() {
                this.socket = null;
                this.isConnected = false;
                this.clientId = null;
                this.reconnectAttempts = 0;
                this.maxReconnectAttempts = 5;
                this.reconnectDelay = 3000;
            }

            connect() {
                try {
                    const protocol = window.location.protocol === 'https:' ? 'wss:' : 'ws:';
                    let hostname = window.location.hostname;

                    // Handle cases where hostname might be empty or localhost variations
                    if (!hostname || hostname === '') {
                        hostname = 'localhost';
                    }

                    const wsUrl = `${protocol}//${hostname}:8081`;

                    this.log(`Connecting to WebSocket server: ${wsUrl}`);
                    this.updateStatus('connecting', 'Connecting...');

                    this.socket = new WebSocket(wsUrl);

                    this.socket.onopen = () => {
                        this.log('WebSocket connected successfully');
                        this.isConnected = true;
                        this.reconnectAttempts = 0;
                        this.updateStatus('connected', 'Connected');
                        this.updateButtons();
                    };

                    this.socket.onmessage = (event) => {
                        try {
                            const message = JSON.parse(event.data);
                            this.handleMessage(message);
                        } catch (error) {
                            this.log(`Error parsing WebSocket message: ${error.message}`);
                        }
                    };

                    this.socket.onclose = () => {
                        this.log('WebSocket disconnected');
                        this.isConnected = false;
                        this.clientId = null;
                        this.updateStatus('disconnected', 'Disconnected');
                        this.updateButtons();
                    };

                    this.socket.onerror = (error) => {
                        this.log(`WebSocket error: ${error}`);
                        this.updateStatus('disconnected', 'Connection Error');
                    };

                } catch (error) {
                    this.log(`Failed to create WebSocket connection: ${error.message}`);
                    this.updateStatus('disconnected', 'Connection Failed');
                }
            }

            handleMessage(message) {
                this.log(`Received message: ${JSON.stringify(message)}`);

                switch (message.type) {
                    case 'welcome':
                        this.clientId = message.clientId;
                        this.log(`WebSocket client ID: ${this.clientId}`);
                        break;

                    case 'signal':
                        this.log(`Signal received: ${message.signal}`);
                        // Trigger signal event for testing
                        window.dispatchEvent(new CustomEvent('websocket-signal', {
                            detail: {
                                signal: message.signal,
                                data: message.data || {},
                                timestamp: message.timestamp,
                                fromClientId: message.fromClientId
                            }
                        }));
                        this.displayReceivedSignal(message);
                        break;

                    case 'error':
                        this.log(`WebSocket server error: ${message.message}`);
                        break;

                    default:
                        this.log(`Unknown message type: ${message.type}`);
                }
            }

            sendSignal(signalType, data = {}) {
                if (!this.isConnected || !this.socket) {
                    this.log('WebSocket not connected. Cannot send signal.');
                    return false;
                }

                const message = {
                    type: 'signal',
                    signal: signalType,
                    data: data
                };

                try {
                    this.socket.send(JSON.stringify(message));
                    this.log(`Signal sent: ${signalType} with data: ${JSON.stringify(data)}`);
                    return true;
                } catch (error) {
                    this.log(`Failed to send signal: ${error.message}`);
                    return false;
                }
            }

            disconnect() {
                if (this.socket) {
                    this.socket.close();
                }
            }

            log(message) {
                const timestamp = new Date().toLocaleTimeString();
                const logElement = document.getElementById('log');
                if (logElement) {
                    logElement.innerHTML += `[${timestamp}] ${message}\n`;
                    logElement.scrollTop = logElement.scrollHeight;
                }
            }

            updateStatus(type, message) {
                const statusElement = document.getElementById('status');
                if (statusElement) {
                    statusElement.className = `status ${type}`;
                    statusElement.textContent = message;
                }
            }

            updateButtons() {
                const connectBtn = document.getElementById('connectBtn');
                const disconnectBtn = document.getElementById('disconnectBtn');
                const sendButtons = ['sendBtn1', 'sendBtn2', 'sendBtn3', 'sendBtn4'];

                if (connectBtn) connectBtn.disabled = this.isConnected;
                if (disconnectBtn) disconnectBtn.disabled = !this.isConnected;

                sendButtons.forEach(btnId => {
                    const btn = document.getElementById(btnId);
                    if (btn) btn.disabled = !this.isConnected;
                });
            }

            displayReceivedSignal(message) {
                const container = document.getElementById('receivedSignals');
                if (container) {
                    const signalDiv = document.createElement('div');
                    signalDiv.style.cssText = 'background: #e7f3ff; border: 1px solid #b3d9ff; padding: 10px; margin: 5px 0; border-radius: 4px;';
                    signalDiv.innerHTML = `
                        <strong>Signal:</strong> ${message.signal}<br>
                        <strong>From:</strong> ${message.fromClientId}<br>
                        <strong>Time:</strong> ${new Date(message.timestamp).toLocaleTimeString()}<br>
                        <strong>Data:</strong> ${JSON.stringify(message.data || {})}
                    `;
                    container.appendChild(signalDiv);

                    // Keep only last 5 signals
                    while (container.children.length > 5) {
                        container.removeChild(container.firstChild);
                    }
                }
            }
        }

        // Global WebSocket client instance
        let wsClient = null;

        // Initialize when DOM is loaded
        document.addEventListener('DOMContentLoaded', function() {
            wsClient = new WebSocketClient();
            setupSignalReceivers();

            // Log initialization
            if (wsClient) {
                wsClient.log('WebSocket Test Page initialized');
            }
        });

        // Global functions for testing
        function connectWebSocket() {
            if (wsClient) {
                wsClient.connect();
            } else {
                console.error('WebSocket client not initialized');
            }
        }

        function disconnectWebSocket() {
            if (wsClient) {
                wsClient.disconnect();
            } else {
                console.error('WebSocket client not initialized');
            }
        }

        function testSendSignal(signalType) {
            if (wsClient) {
                const testData = {
                    testId: Math.floor(Math.random() * 1000),
                    timestamp: new Date().toISOString(),
                    source: 'test-page'
                };
                wsClient.sendSignal(signalType, testData);
            } else {
                console.error('WebSocket client not initialized');
            }
        }

        function clearLog() {
            const logElement = document.getElementById('log');
            if (logElement) {
                logElement.innerHTML = '';
            }
        }

        // Global sendSignal function (same as in vMenu.php)
        function sendSignal(signalType, data = {}) {
            if (wsClient) {
                return wsClient.sendSignal(signalType, data);
            } else {
                console.error('WebSocket client not initialized');
                return false;
            }
        }

        // receiveSignal function (same as in vIncomeReport.php)
        function receiveSignal(signalType, callback) {
            window.addEventListener('websocket-signal', function(event) {
                const signalData = event.detail;

                if (signalData.signal === signalType) {
                    console.log(`Received signal: ${signalType}`);
                    if (typeof callback === 'function') {
                        callback(signalData.data);
                    } else {
                        console.warn('Callback is not a function for signal:', signalType);
                    }
                }
            });

            console.log(`Signal receiver registered for: ${signalType}`);
        }

        // Set up signal receivers for testing
        function setupSignalReceivers() {
            receiveSignal('ReportIncome', function(data) {
                if (wsClient) {
                    wsClient.log(`receiveSignal callback triggered for 'ReportIncome' with data: ${JSON.stringify(data)}`);
                }
            });

            receiveSignal('RefreshIncomeReport', function(data) {
                if (wsClient) {
                    wsClient.log(`receiveSignal callback triggered for 'RefreshIncomeReport' with data: ${JSON.stringify(data)}`);
                }
            });

            receiveSignal('TestSignal', function(data) {
                if (wsClient) {
                    wsClient.log(`receiveSignal callback triggered for 'TestSignal' with data: ${JSON.stringify(data)}`);
                }
            });
        }
    </script>


    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            connectWebSocket()
        });
    </script>
</body>

</html>