const WebSocket = require('ws');

// Create WebSocket server on port 8081
const wss = new WebSocket.Server({ 
    port: 8081,
    perMessageDeflate: false
});

// Store connected clients
const clients = new Map();
let clientIdCounter = 0;

console.log('WebSocket server started on port 8081');
console.log('Server ready to accept connections...');

wss.on('connection', function connection(ws, req) {
    // Generate unique client ID
    const clientId = `client_${++clientIdCounter}_${Date.now()}`;
    clients.set(clientId, ws);
    
    console.log(`Client connected: ${clientId} (Total clients: ${clients.size})`);
    
    // Send welcome message
    const welcomeMessage = {
        type: 'welcome',
        message: 'Connected to WebSocket server',
        clientId: clientId,
        timestamp: new Date().toISOString()
    };
    
    ws.send(JSON.stringify(welcomeMessage));
    
    // Handle incoming messages
    ws.on('message', function incoming(data) {
        try {
            const message = JSON.parse(data.toString());
            console.log(`Message from ${clientId}:`, message);
            
            // Add client info to message
            message.fromClientId = clientId;
            message.timestamp = new Date().toISOString();
            
            // Broadcast message to all connected clients
            broadcast(JSON.stringify(message), clientId);
            
        } catch (error) {
            console.error('Error parsing message:', error);
            ws.send(JSON.stringify({
                type: 'error',
                message: 'Invalid message format',
                timestamp: new Date().toISOString()
            }));
        }
    });
    
    // Handle client disconnect
    ws.on('close', function close() {
        clients.delete(clientId);
        console.log(`Client disconnected: ${clientId} (Total clients: ${clients.size})`);
    });
    
    // Handle errors
    ws.on('error', function error(err) {
        console.error(`WebSocket error for ${clientId}:`, err);
        clients.delete(clientId);
    });
});

// Broadcast message to all connected clients
function broadcast(message, excludeClientId = null) {
    let sentCount = 0;
    
    clients.forEach((client, clientId) => {
        if (client.readyState === WebSocket.OPEN && clientId !== excludeClientId) {
            try {
                client.send(message);
                sentCount++;
            } catch (error) {
                console.error(`Error sending to ${clientId}:`, error);
                clients.delete(clientId);
            }
        }
    });
    
    console.log(`Message broadcasted to ${sentCount} clients`);
}

// Handle server shutdown gracefully
process.on('SIGTERM', () => {
    console.log('Received SIGTERM, shutting down gracefully...');
    wss.close(() => {
        console.log('WebSocket server closed');
        process.exit(0);
    });
});

process.on('SIGINT', () => {
    console.log('Received SIGINT, shutting down gracefully...');
    wss.close(() => {
        console.log('WebSocket server closed');
        process.exit(0);
    });
});

// Log server status every 30 seconds
setInterval(() => {
    console.log(`Server status: ${clients.size} connected clients`);
}, 30000);
