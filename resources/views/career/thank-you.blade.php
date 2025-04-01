<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta http-equiv="refresh" content="3;url=https://lemoninfosys.com">
    <title>Application Submitted Successfully</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            text-align: center;
        }
        .success-message {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .redirect-notice {
            margin: 20px 0;
            font-style: italic;
            color: #666;
        }
        .manual-link {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        .manual-link:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>
    <div class="success-message">
        ✅ {{ session('success', 'Your application has been submitted successfully.') }}
    </div>

    <div class="redirect-notice">
        You will be automatically redirected to lemoninfosys.com in 3 seconds...
    </div>

    <a href="https://lemoninfosys.com" class="manual-link">
        Click here if you are not redirected automatically
    </a>

    <script>
        // Fallback in case meta refresh doesn't work
        setTimeout(function() {
            window.location.href = "https://lemoninfosys.com";
        }, 3000);
    </script>
</body>
</html>