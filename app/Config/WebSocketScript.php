<script>
    // WebSocket Client for Signal Sending
    class WebSocketSignalClient {
        constructor() {
            this.socket = null;
            this.isConnected = false;
            this.clientId = null;
        }

        init() {
            try {
                const protocol = window.location.protocol === "https:" ? "wss:" : "ws:";
                let hostname = window.location.hostname;

                if (!hostname || hostname === "") {
                    hostname = "localhost";
                }

                const wsUrl = `${protocol}//${hostname}:8081`;

                this.socket = new WebSocket(wsUrl);

                this.socket.onopen = () => {
                    this.isConnected = true;
                    console.log("WebSocket connected successfully");
                };

                this.socket.onmessage = (event) => {
                    try {
                        const message = JSON.parse(event.data);
                        if (message.type === "welcome") {
                            this.clientId = message.clientId;
                            console.log(`WebSocket client ID: ${this.clientId}`);
                        }
                    } catch (error) {
                        console.error("Error parsing WebSocket message:", error.message);
                    }
                };

                this.socket.onclose = () => {
                    this.isConnected = false;
                    this.clientId = null;
                    console.log("WebSocket disconnected");
                };

                this.socket.onerror = (error) => {
                    console.error("WebSocket error:", error);
                };

            } catch (error) {
                console.error("Failed to create WebSocket connection:", error.message);
            }
        }

        sendSignalPhp(signalType, data = {}) {
            if (!this.isConnected || !this.socket) {
                console.error("WebSocket not connected. Cannot send signal.");
                return false;
            }

            const message = {
                type: "signal",
                signal: signalType,
                data: data
            };

            try {
                this.socket.send(JSON.stringify(message));
                console.log(`Signal sent: ${signalType} with data:`, data);
                return true;
            } catch (error) {
                console.error("Failed to send signal:", error.message);
                return false;
            }
        }
    }

    // Global WebSocket client instance
    let wsSignalClient = null;

    // Initialize WebSocket client
    document.addEventListener("DOMContentLoaded", function() {
        wsSignalClient = new WebSocketSignalClient();
        wsSignalClient.init();
    });

    // Global function to send signals
    function sendSignalPhp(signalType, data = {
        testId: Math.floor(Math.random() * 1000),
        timestamp: new Date().toISOString(),
        source: 'test-page'
    }) {

        if (wsSignalClient) {
            return wsSignalClient.sendSignalPhp(signalType, data);
        } else {
            console.error("WebSocket client not initialized");
            return false;
        }
    }
</script>