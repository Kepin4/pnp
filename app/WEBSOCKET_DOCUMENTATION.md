# WebSocket Implementation Documentation

## Table of Contents
1. [Project Overview](#project-overview)
2. [Server Implementation Options](#server-implementation-options)
3. [Client Implementation](#client-implementation)
4. [CodeIgniter 4 Integration](#codeigniter-4-integration)
5. [Configuration Guide](#configuration-guide)
6. [Deployment to Another CI4 Project](#deployment-to-another-ci4-project)
7. [Customization for Non-Chat Applications](#customization-for-non-chat-applications)
8. [Troubleshooting](#troubleshooting)

---

## Project Overview

This WebSocket implementation provides real-time bidirectional communication between clients and server. The project includes:

- **Two server options**: Node.js (recommended) and PHP implementations
- **HTML/JavaScript client**: Complete WebSocket client with connection management
- **CodeIgniter 4 integration**: Routes and controller for serving the client
- **Broadcasting capability**: Messages sent to all connected clients
- **Connection management**: Automatic client tracking and cleanup

**Key Features:**
- Real-time messaging
- Client connection tracking
- Automatic reconnection handling
- Message broadcasting
- Clean disconnection handling
- Error management

---

## Server Implementation Options

### Option 1: Node.js WebSocket Server (Recommended)

**File:** `websocket-server.js`

**Dependencies:**
```json
{
  "dependencies": {
    "ws": "^8.14.2"
  }
}
```

**Key Features:**
- Runs on port 8081
- Uses the `ws` library for WebSocket handling
- Automatic client ID generation
- Message broadcasting to all connected clients
- Graceful shutdown handling

**Starting the server:**
```bash
npm install
npm start
# or
node websocket-server.js
```

**Server Structure:**
```javascript
const WebSocket = require('ws');
const wss = new WebSocket.Server({ port: 8081 });

// Client management
const clients = new Map();

// Connection handling
wss.on('connection', function connection(ws, req) {
    // Client registration and message handling
});
```

### Option 2: PHP WebSocket Server

**File:** `websocket-server.php`

**Dependencies:**
- ReactPHP (requires Composer)
- PHP 7.4+ recommended

**Key Features:**
- Pure PHP implementation
- WebSocket handshake handling
- Frame encoding/decoding
- Client connection management

**Starting the server:**
```bash
composer install
php websocket-server.php
```

**Note:** The Node.js implementation is recommended for production use due to better performance and stability.

---

## Client Implementation

**File:** `public/websocket-client.html`

### Client Features

1. **Connection Management**
   - Connect/Disconnect buttons
   - Connection status indicator
   - Automatic reconnection handling

2. **Message Handling**
   - Send messages via input field or Enter key
   - Receive and display messages from all clients
   - System message notifications

3. **UI Components**
   - Status indicator (Connected/Disconnected/Connecting)
   - Message history display
   - Input controls

### JavaScript WebSocket Client Class

```javascript
class WebSocketClient {
    constructor() {
        this.socket = null;
        this.isConnected = false;
        this.clientId = null;
    }
    
    connect() {
        this.socket = new WebSocket('ws://localhost:8081');
        // Event handlers for onopen, onmessage, onclose, onerror
    }
}
```

### Message Protocol

The client and server communicate using JSON messages:

```javascript
// Welcome message (server to client)
{
    "type": "welcome",
    "message": "Connected to WebSocket server",
    "clientId": "unique_client_id"
}

// Chat message (bidirectional)
{
    "type": "message",
    "clientId": "sender_client_id",
    "message": "message content",
    "timestamp": "2024-01-01 12:00:00"
}
```

---

## CodeIgniter 4 Integration

### Controller Setup

**File:** `app/Controllers/WebSocket.php`

```php
<?php

namespace App\Controllers;

class WebSocket extends BaseController
{
    public function index()
    {
        return view('websocket_client');
    }
    
    public function client()
    {
        // Serve the HTML file directly
        $filePath = FCPATH . 'websocket-client.html';
        
        if (file_exists($filePath)) {
            return file_get_contents($filePath);
        }
        
        return view('errors/html/error_404');
    }
}
```

### Routes Configuration

**File:** `app/Config/Routes.php`

```php
<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('/websocket', 'WebSocket::client');
$routes->get('/websocket/client', 'WebSocket::client');
```

---

## Configuration Guide

### CodeIgniter 4 Base URL Configuration

**File:** `app/Config/App.php`

```php
<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class App extends BaseConfig
{
    /**
     * Base Site URL
     * 
     * For WebSocket implementation, ensure this matches your development/production environment
     */
    public string $baseURL = 'http://localhost:8080/';
    
    // For production, use your actual domain:
    // public string $baseURL = 'https://yourdomain.com/';
    
    // For different ports:
    // public string $baseURL = 'http://localhost:3000/';
}
```

### WebSocket Server Configuration

**Important Port Configuration:**
- **WebSocket Server Port:** 8081 (configurable in server files)
- **CI4 Application Port:** 8080 (or as configured in baseURL)
- **Client Connection:** Must match the WebSocket server port

**In the client HTML file, update the WebSocket connection URL:**
```javascript
// For local development
this.socket = new WebSocket('ws://localhost:8081');

// For production (replace with your domain)
this.socket = new WebSocket('ws://yourdomain.com:8081');

// For secure connections (WSS)
this.socket = new WebSocket('wss://yourdomain.com:8081');
```

### Environment-Specific Configuration

Create environment-specific configurations:

**Development:**
```javascript
const WEBSOCKET_URL = 'ws://localhost:8081';
```

**Production:**
```javascript
const WEBSOCKET_URL = 'wss://yourdomain.com:8081';
```

---

## Deployment to Another CI4 Project

### Step 1: Copy Required Files

Copy these files to your new CI4 project:

```
├── websocket-server.js          # Node.js WebSocket server
├── websocket-server.php         # PHP WebSocket server (optional)
├── package.json                 # Node.js dependencies
├── public/websocket-client.html # WebSocket client
├── app/Controllers/WebSocket.php # CI4 controller
```

### Step 2: Install Dependencies

```bash
# For Node.js server
npm install

# For PHP server (if using)
composer require react/socket react/stream
```

### Step 3: Update Routes

Add to your `app/Config/Routes.php`:

```php
$routes->get('/websocket', 'WebSocket::client');
$routes->get('/websocket/client', 'WebSocket::client');
```

### Step 4: Configure Base URL

Update `app/Config/App.php`:

```php
public string $baseURL = 'http://localhost:8080/'; // Your CI4 app URL
```

### Step 5: Update WebSocket Connection URL

In `public/websocket-client.html`, update the WebSocket connection:

```javascript
// Update this line in the connect() method
this.socket = new WebSocket('ws://localhost:8081'); // Your WebSocket server URL
```

### Step 6: Start the WebSocket Server

```bash
# Start the WebSocket server
node websocket-server.js

# Start your CI4 application
php spark serve --port=8080
```

### Step 7: Access the Application

- **CI4 Application:** `http://localhost:8080`
- **WebSocket Client:** `http://localhost:8080/websocket`

---

## Customization for Non-Chat Applications

### Real-Time Notifications System

```javascript
// Server-side: Send notification to specific client
const notificationMessage = {
    type: 'notification',
    title: 'New Order',
    message: 'You have received a new order #12345',
    timestamp: new Date().toISOString(),
    priority: 'high'
};

// Send to specific client
if (clients.has(targetClientId)) {
    clients.get(targetClientId).send(JSON.stringify(notificationMessage));
}
```

### Live Data Updates

```javascript
// Server-side: Broadcast data updates
const dataUpdate = {
    type: 'data_update',
    entity: 'products',
    action: 'update',
    data: {
        id: 123,
        name: 'Updated Product Name',
        price: 99.99
    },
    timestamp: new Date().toISOString()
};

broadcast(JSON.stringify(dataUpdate));
```

### Status Monitoring

```javascript
// Server-side: System status updates
const statusUpdate = {
    type: 'status_update',
    system: 'payment_gateway',
    status: 'online',
    message: 'Payment gateway is operational',
    timestamp: new Date().toISOString()
};

broadcast(JSON.stringify(statusUpdate));
```

### Client-Side Message Handling for Custom Applications

```javascript
handleMessage(data) {
    try {
        const message = JSON.parse(data);
        
        switch (message.type) {
            case 'notification':
                this.showNotification(message.title, message.message, message.priority);
                break;
                
            case 'data_update':
                this.updateDataTable(message.entity, message.action, message.data);
                break;
                
            case 'status_update':
                this.updateSystemStatus(message.system, message.status, message.message);
                break;
                
            case 'user_activity':
                this.updateUserActivity(message.userId, message.activity);
                break;
                
            default:
                console.log('Unknown message type:', message);
        }
    } catch (error) {
        console.error('Failed to parse message:', error);
    }
}
```

### Integration with CI4 Models

```php
<?php

namespace App\Controllers;

use App\Models\NotificationModel;

class WebSocket extends BaseController
{
    public function sendNotification()
    {
        $notificationModel = new NotificationModel();
        
        // Save notification to database
        $notificationData = [
            'user_id' => $this->request->getPost('user_id'),
            'title' => $this->request->getPost('title'),
            'message' => $this->request->getPost('message'),
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        $notificationModel->insert($notificationData);
        
        // Send real-time notification via WebSocket
        // This would require a WebSocket client in PHP or API call to Node.js server
        
        return $this->response->setJSON(['status' => 'success']);
    }
}
```

---

## Troubleshooting

### Common Issues and Solutions

#### 1. Connection Refused Error

**Problem:** `WebSocket connection to 'ws://localhost:8081/' failed`

**Solutions:**
- Ensure WebSocket server is running: `node websocket-server.js`
- Check if port 8081 is available
- Verify firewall settings
- Check if another process is using port 8081

#### 2. CORS Issues

**Problem:** Cross-origin WebSocket connection blocked

**Solution:** Add CORS headers to your CI4 application:

```php
// In app/Config/Filters.php
public array $globals = [
    'before' => [
        'cors',
    ],
];
```

#### 3. WebSocket Server Not Starting

**Problem:** Node.js server fails to start

**Solutions:**
- Check Node.js version: `node --version` (requires Node.js 12+)
- Install dependencies: `npm install`
- Check for port conflicts: `netstat -an | grep 8081`

#### 4. Messages Not Broadcasting

**Problem:** Messages sent but not received by other clients

**Solutions:**
- Check client connection status
- Verify message format (must be valid JSON)
- Check server logs for errors
- Ensure clients are properly registered

#### 5. CI4 Routes Not Working

**Problem:** `/websocket` route returns 404

**Solutions:**
- Clear CI4 routes cache: `php spark cache:clear`
- Check Routes.php syntax
- Verify controller namespace and class name
- Check file permissions

### Debug Mode

Enable debug logging in the WebSocket server:

```javascript
// Add to websocket-server.js
const DEBUG = true;

function debugLog(message) {
    if (DEBUG) {
        console.log(`[DEBUG] ${new Date().toISOString()}: ${message}`);
    }
}

// Use throughout the code
debugLog(`Client ${clientId} connected`);
debugLog(`Broadcasting message: ${message}`);
```

### Performance Monitoring

Monitor WebSocket performance:

```javascript
// Add connection metrics
let connectionCount = 0;
let messageCount = 0;

wss.on('connection', function connection(ws) {
    connectionCount++;
    console.log(`Total connections: ${connectionCount}`);
    
    ws.on('message', function incoming(data) {
        messageCount++;
        console.log(`Total messages processed: ${messageCount}`);
    });
    
    ws.on('close', function close() {
        connectionCount--;
        console.log(`Total connections: ${connectionCount}`);
    });
});
```

---

## Security Considerations

### 1. Authentication

Implement authentication before WebSocket connection:

```javascript
// Client-side: Send auth token
const authToken = localStorage.getItem('auth_token');
this.socket = new WebSocket(`ws://localhost:8081?token=${authToken}`);
```

```javascript
// Server-side: Validate token
wss.on('connection', function connection(ws, req) {
    const url = new URL(req.url, 'http://localhost:8081');
    const token = url.searchParams.get('token');
    
    if (!validateToken(token)) {
        ws.close(1008, 'Invalid authentication');
        return;
    }
    
    // Continue with connection setup
});
```

### 2. Rate Limiting

Implement message rate limiting:

```javascript
const clientRateLimits = new Map();

ws.on('message', function incoming(data) {
    const now = Date.now();
    const clientLimit = clientRateLimits.get(clientId) || { count: 0, resetTime: now + 60000 };
    
    if (now > clientLimit.resetTime) {
        clientLimit.count = 0;
        clientLimit.resetTime = now + 60000;
    }
    
    if (clientLimit.count >= 100) { // 100 messages per minute
        ws.send(JSON.stringify({ type: 'error', message: 'Rate limit exceeded' }));
        return;
    }
    
    clientLimit.count++;
    clientRateLimits.set(clientId, clientLimit);
    
    // Process message
});
```

### 3. Input Validation

Always validate incoming messages:

```javascript
function validateMessage(data) {
    try {
        const message = JSON.parse(data);
        
        // Check required fields
        if (!message.type || typeof message.type !== 'string') {
            return false;
        }
        
        // Validate message length
        if (message.message && message.message.length > 1000) {
            return false;
        }
        
        return true;
    } catch (error) {
        return false;
    }
}
```

---

## Production Deployment

### 1. Process Management

Use PM2 for production deployment:

```bash
npm install -g pm2

# Start WebSocket server with PM2
pm2 start websocket-server.js --name "websocket-server"

# Monitor
pm2 status
pm2 logs websocket-server
```

### 2. Reverse Proxy (Nginx)

Configure Nginx for WebSocket proxy:

```nginx
server {
    listen 80;
    server_name yourdomain.com;
    
    location /websocket {
        proxy_pass http://localhost:8081;
        proxy_http_version 1.1;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection "upgrade";
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
    }
}
```

### 3. SSL/TLS Configuration

For secure WebSocket connections (WSS):

```nginx
server {
    listen 443 ssl;
    server_name yourdomain.com;
    
    ssl_certificate /path/to/certificate.crt;
    ssl_certificate_key /path/to/private.key;
    
    location /websocket {
        proxy_pass http://localhost:8081;
        proxy_http_version 1.1;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection "upgrade";
        # ... other headers
    }
}
```

Update client connection:
```javascript
this.socket = new WebSocket('wss://yourdomain.com/websocket');
```

---

This documentation provides a complete guide for implementing and deploying WebSocket functionality in CodeIgniter 4 projects. The implementation is flexible and can be adapted for various real-time applications beyond chat systems.
