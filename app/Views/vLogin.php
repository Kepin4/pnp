<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Sign In</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                background-color: #A5A5A5;
                margin: 0;
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100vh;
            }
            .container {
                width: 360px;
                background: #fff;
                border-radius: 8px;
                box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
                overflow: hidden;
                padding: 20px;
                box-sizing: border-box;
            }
            h2 {
                margin-top: 0;
                text-align: center;
                color: #333;
            }
            input {
                width: 100%;
                padding: 12px;
                margin: 8px 0;
                border: 1px solid #ddd;
                border-radius: 4px;
                box-sizing: border-box;
            }
            button {
                width: 100%;
                padding: 12px;
                background-color: #007bff;
                border: none;
                border-radius: 4px;
                color: white;
                font-size: 16px;
                cursor: pointer;
                transition: background-color 0.3s;
            }
            button:hover {
                background-color: #0056b3;
            }
            .message {
                text-align: center;
                margin-top: 15px;
                font-size: 14px;
            }
            .message a {
                color: #007bff;
                text-decoration: none;
            }
            .message a:hover {
                text-decoration: underline;
            }
            .toast {
                visibility: hidden;
                min-width: 250px;
                background-color: #333;
                color: #fff;
                text-align: center;
                border-radius: 2px;
                padding: 16px;
                position: fixed;
                z-index: 1;
                left: 50%;
                top: 30px;
                font-size: 17px;
                opacity: 0;
                transition: opacity 0.5s, top 0.5s;
                box-shadow: 0px 0px 15px 3px rgba(255, 255, 255, 0.3);
                transform: translateX(-50%);
            }
            .toast.show {
                visibility: visible;
                opacity: 1;
                top: 50px;
            }
        </style>
    </head>
    <body>
        <div id="toast" class="toast"></div>
        <div class="container">
            <h2>Sign In</h2>
            <form action="<?= base_url("../CLogin/pLogin") ?>" method="post">
                <input type="text" placeholder="Username..." name="txtUsername" required>
                <input type="password" placeholder="Password..." name="txtPassword" required>
                <button type="submit">Sign In</button>
            </form>
        </div>
    </body>
</html>

<?php if(session('LoginError') != ''){ ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            showToast("<?= session('LoginError'); ?>");
        });
    </script>
<?php } ?>

<script>
    function showToast(message) {
        var toast = document.getElementById("toast");
        toast.innerHTML = message;
        toast.className = "toast show";
        setTimeout(function() { toast.className = toast.className.replace("show", ""); }, 3000);
    }
</script>