# WebSocket Deployment Guide for Plesk

This guide provides step-by-step instructions for deploying the WebSocket functionality on a Plesk hosting environment.

## Table of Contents
1. [Prerequisites](#prerequisites)
2. [Development Setup](#development-setup)
3. [Plesk Production Deployment](#plesk-production-deployment)
4. [Testing the Implementation](#testing-the-implementation)
5. [Troubleshooting](#troubleshooting)
6. [Usage Examples](#usage-examples)

---

## Prerequisites

### Development Environment
- Node.js 14+ installed
- npm or yarn package manager
- CodeIgniter 4 application running
- Access to terminal/command prompt

### Plesk Environment
- Plesk Panel with Node.js support enabled
- SSH access to the server
- Domain configured in Plesk
- Port 8081 available (or alternative port)

---

## Development Setup

### 1. Install Dependencies

```bash
# Navigate to your project directory
cd /path/to/your/project

# Install WebSocket dependencies
npm install

# This will install:
# - ws: WebSocket library for Node.js
```

### 2. Start WebSocket Server (Development)

```bash
# Option 1: Start WebSocket server directly
npm run websocket

# Option 2: Start with auto-restart (development)
npm run websocket-dev

# Option 3: Start manually
node websocket-server.js
```

### 3. Start CodeIgniter Application

```bash
# Start CI4 development server
npm start
# or
php spark serve --port=8080
```

### 4. Test Local Setup

1. Open your browser to `http://localhost:8080`
2. Open browser console (F12)
3. Navigate to any page with the menu (vMenu.php is included)
4. Check console for WebSocket connection messages
5. Test sending a signal: `sendSignal('ReportIncome')`
6. Navigate to Income Report page to see if it receives the signal

---

## Plesk Production Deployment

### Step 1: Upload Files to Plesk

Upload these files to your domain's root directory in Plesk:

```
your-domain.com/
├── websocket-server.js
├── package.json
├── ecosystem.config.js
├── app/
│   ├── Views/
│   │   ├── vMenu.php (updated)
│   │   └── vIncomeReport.php (updated)
│   └── ... (other CI4 files)
└── ... (other project files)
```

### Step 2: Configure Node.js in Plesk

1. **Log into Plesk Panel**
2. **Go to your domain**
3. **Click on "Node.js"**
4. **Configure Node.js settings:**
   - **Node.js version:** Select latest stable (14+ recommended)
   - **Document root:** `/httpdocs` (or your web root)
   - **Application root:** `/httpdocs` (same as document root)
   - **Application startup file:** `websocket-server.js`
   - **Custom environment variables:**
     ```
     NODE_ENV=production
     PORT=8081
     ```

### Step 3: Install Dependencies in Plesk

1. **In Plesk Node.js interface:**
   - Click **"NPM Install"**
   - Wait for dependencies to install
   - Verify `ws` package is installed

2. **Alternative via SSH:**
   ```bash
   cd /var/www/vhosts/your-domain.com/httpdocs
   npm install
   ```

### Step 4: Configure PM2 (Recommended)

If your Plesk supports PM2:

```bash
# SSH into your server
ssh user@your-server.com

# Navigate to your project
cd /var/www/vhosts/your-domain.com/httpdocs

# Install PM2 globally (if not installed)
npm install -g pm2

# Start WebSocket server with PM2
pm2 start ecosystem.config.js --env production

# Save PM2 configuration
pm2 save

# Setup PM2 to start on boot
pm2 startup
```

### Step 5: Configure Firewall/Ports

Ensure port 8081 is open:

1. **In Plesk:**
   - Go to **Tools & Settings**
   - Click **Firewall**
   - Add rule to allow port 8081

2. **Via command line:**
   ```bash
   # For iptables
   iptables -A INPUT -p tcp --dport 8081 -j ACCEPT
   
   # For ufw
   ufw allow 8081
   ```

### Step 6: Update WebSocket URL for Production

The WebSocket client in `vMenu.php` automatically detects the environment:

```javascript
// This code automatically adapts to your domain
const protocol = window.location.protocol === 'https:' ? 'wss:' : 'ws:';
const hostname = window.location.hostname;
const wsUrl = `${protocol}//${hostname}:8081`;
```

For HTTPS sites, ensure you have SSL certificate covering the WebSocket port or use a reverse proxy.

### Step 7: Configure Reverse Proxy (Optional but Recommended)

For production HTTPS sites, configure Nginx reverse proxy:

1. **Create Nginx configuration:**
   ```nginx
   # In your domain's Nginx configuration
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
   ```

2. **Update client connection:**
   ```javascript
   // In vMenu.php, modify the WebSocket URL
   const wsUrl = `${protocol}//${hostname}/websocket`;
   ```

---

## Testing the Implementation

### 1. Verify WebSocket Server is Running

```bash
# Check if process is running
ps aux | grep websocket-server

# Check if port is listening
netstat -tlnp | grep 8081

# Check PM2 status (if using PM2)
pm2 status
```

### 2. Test WebSocket Connection

1. **Open your website in browser**
2. **Open browser console (F12)**
3. **Look for connection messages:**
   ```
   Connecting to WebSocket server: ws://your-domain.com:8081
   WebSocket connected
   WebSocket client ID: client_1_1234567890
   ```

### 3. Test Signal Sending

1. **In browser console, test sending signal:**
   ```javascript
   sendSignal('ReportIncome');
   ```

2. **Expected console output:**
   ```
   Signal sent: ReportIncome {}
   ```

### 4. Test Signal Receiving

1. **Navigate to Income Report page**
2. **In another browser tab/window, send signal:**
   ```javascript
   sendSignal('ReportIncome');
   ```
3. **Check Income Report page console for:**
   ```
   Received WebSocket signal: {signal: "ReportIncome", ...}
   Received ReportIncome signal - refreshing data
   Refreshing Income Report data...
   ```

---

## Usage Examples

### Basic Usage

**Send a signal from any page:**
```javascript
// Send simple signal
sendSignal('ReportIncome');

// Send signal with data
sendSignal('ReportIncome', { 
    userId: 123, 
    action: 'refresh' 
});
```

**Receive signals in vIncomeReport.php:**
```javascript
// Method 1: Using receiveSignal function
receiveSignal('ReportIncome', function(data) {
    console.log('Signal received with data:', data);
    refreshData();
});

// Method 2: The signal is automatically handled by the existing code
// No additional code needed - it will automatically refresh the page
```

### Advanced Usage

**Send signals from PHP backend:**
You can integrate WebSocket signals into your PHP controllers:

```php
// In your controller, after updating data
public function updateIncomeData() {
    // Your data update logic here
    
    // Trigger WebSocket signal via HTTP request to Node.js server
    $this->triggerWebSocketSignal('ReportIncome');
}

private function triggerWebSocketSignal($signal) {
    // You can implement this by making HTTP request to a Node.js endpoint
    // or by using a message queue system
}
```

### Multiple Signal Types

```javascript
// Send different types of signals
sendSignal('ReportIncome');        // Refresh income report
sendSignal('ReportPlacement');     // Refresh placement report  
sendSignal('UpdateNotifications'); // Update notification counts
sendSignal('RefreshDashboard');    // Refresh dashboard data
```

---

## Troubleshooting

### Common Issues

#### 1. WebSocket Connection Failed
**Error:** `WebSocket connection to 'ws://domain.com:8081/' failed`

**Solutions:**
- Check if WebSocket server is running: `pm2 status`
- Verify port 8081 is open in firewall
- Check server logs: `pm2 logs websocket-server`
- Restart WebSocket server: `pm2 restart websocket-server`

#### 2. Port Already in Use
**Error:** `Error: listen EADDRINUSE: address already in use :::8081`

**Solutions:**
- Find process using port: `lsof -i :8081`
- Kill existing process: `kill -9 <PID>`
- Use different port in `websocket-server.js` and update client

#### 3. SSL/HTTPS Issues
**Error:** Mixed content warnings on HTTPS sites

**Solutions:**
- Use WSS instead of WS for HTTPS sites
- Configure reverse proxy with SSL termination
- Ensure SSL certificate covers WebSocket port

#### 4. PM2 Not Starting on Boot
**Solutions:**
```bash
# Generate startup script
pm2 startup

# Save current PM2 processes
pm2 save

# Test startup script
sudo systemctl status pm2-user
```

### Debugging

#### Enable Debug Logging
Modify `websocket-server.js` to enable debug mode:

```javascript
// Add at the top of websocket-server.js
const DEBUG = true;

function debugLog(message) {
    if (DEBUG) {
        console.log(`[DEBUG] ${new Date().toISOString()}: ${message}`);
    }
}
```

#### Check Logs
```bash
# PM2 logs
pm2 logs websocket-server

# System logs
tail -f /var/log/messages | grep websocket

# Plesk logs
tail -f /var/www/vhosts/system/your-domain.com/logs/error_log
```

---

## Performance Considerations

### Resource Usage
- **Memory:** ~50-100MB per WebSocket server instance
- **CPU:** Minimal for signaling (not chat)
- **Connections:** Can handle 1000+ concurrent connections

### Scaling
For high-traffic sites:
1. Use multiple WebSocket server instances
2. Implement load balancing
3. Consider Redis for message broadcasting
4. Monitor connection counts and memory usage

### Security
1. Implement rate limiting for message sending
2. Validate message content
3. Consider authentication for sensitive signals
4. Use WSS (secure WebSocket) for production

---

## Maintenance

### Regular Tasks
```bash
# Check server status
pm2 status

# View logs
pm2 logs websocket-server --lines 50

# Restart if needed
pm2 restart websocket-server

# Update dependencies
npm update
```

### Monitoring
Set up monitoring for:
- WebSocket server uptime
- Connection counts
- Memory usage
- Error rates

---

This completes the WebSocket implementation for your CodeIgniter 4 project. The system is now ready for both development and production use with Plesk hosting.
