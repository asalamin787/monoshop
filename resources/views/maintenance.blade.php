<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Site Under Maintenance' }}</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
        }
        
        .maintenance-container {
            text-align: center;
            max-width: 600px;
            padding: 2rem;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            backdrop-filter: blur(10px);
            box-shadow: 0 8px 32px rgba(31, 38, 135, 0.37);
        }
        
        .maintenance-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
            display: block;
        }
        
        h1 {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            font-weight: 300;
        }
        
        p {
            font-size: 1.2rem;
            line-height: 1.6;
            margin-bottom: 2rem;
            opacity: 0.9;
        }
        
        .eta {
            background: rgba(255, 255, 255, 0.2);
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 2rem;
        }
        
        .contact-info {
            font-size: 0.9rem;
            opacity: 0.8;
        }
        
        .spinner {
            border: 4px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top: 4px solid white;
            width: 40px;
            height: 40px;
            animation: spin 2s linear infinite;
            margin: 0 auto 1rem;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        @media (max-width: 768px) {
            .maintenance-container {
                margin: 1rem;
                padding: 1.5rem;
            }
            
            h1 {
                font-size: 2rem;
            }
            
            p {
                font-size: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="maintenance-container">
        <div class="spinner"></div>
        <span class="maintenance-icon">🔧</span>
        
        <h1>{{ $title ?? 'Site Under Maintenance' }}</h1>
        
        <p>{{ $message ?? 'We are currently performing scheduled maintenance to improve your experience. Please check back soon.' }}</p>
        
        <div class="eta">
            <strong>Expected Duration:</strong> 30-60 minutes<br>
            <small>We apologize for any inconvenience this may cause.</small>
        </div>
        
        <div class="contact-info">
            <p>
                <strong>Need immediate assistance?</strong><br>
                Email us at: <a href="mailto:support@monoshop.com" style="color: #ffd700;">support@monoshop.com</a><br>
                Or follow us on social media for updates.
            </p>
        </div>
    </div>

    <script>
        // Auto-refresh every 5 minutes
        setTimeout(function() {
            window.location.reload();
        }, 300000);
    </script>
</body>
</html>
