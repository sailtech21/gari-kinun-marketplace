<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Google Login</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .container {
            text-align: center;
            background: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
        }
        .success {
            color: #10b981;
        }
        .error {
            color: #ef4444;
        }
        .loader {
            border: 3px solid #f3f4f6;
            border-top: 3px solid #667eea;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 0 auto 20px;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="container">
        @if($success)
            <div class="loader"></div>
            <h2 class="success">✓ Login Successful!</h2>
            <p>Redirecting...</p>
        @else
            <h2 class="error">✗ Login Failed</h2>
            <p>{{ $error ?? 'An error occurred' }}</p>
            <p><small>You can close this window.</small></p>
        @endif
    </div>

    <script>
        @if($success)
            // Send success message to parent window
            if (window.opener) {
                window.opener.postMessage({
                    type: 'GOOGLE_AUTH_SUCCESS',
                    token: @json($token),
                    user: @json($user)
                }, '*');
                
                // Close popup after short delay
                setTimeout(() => {
                    window.close();
                }, 1000);
            } else {
                // If no opener, redirect to main app
                setTimeout(() => {
                    window.location.href = '/';
                }, 2000);
            }
        @else
            // Send error message to parent window
            if (window.opener) {
                window.opener.postMessage({
                    type: 'GOOGLE_AUTH_ERROR',
                    error: @json($error ?? 'Authentication failed')
                }, '*');
                
                setTimeout(() => {
                    window.close();
                }, 3000);
            }
        @endif
    </script>
</body>
</html>
