<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'ACME Donation Platform') }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #333;
        }
        .container {
            background: white;
            border-radius: 20px;
            padding: 60px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            text-align: center;
            max-width: 600px;
        }
        h1 {
            font-size: 2.5rem;
            color: #667eea;
            margin-bottom: 20px;
        }
        p {
            font-size: 1.1rem;
            color: #666;
            margin-bottom: 10px;
        }
        .badge {
            display: inline-block;
            background: #667eea;
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 0.9rem;
            margin: 20px 5px 0;
        }
        .links {
            margin-top: 30px;
        }
        .links a {
            display: inline-block;
            margin: 10px;
            padding: 12px 24px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.3s;
        }
        .links a:hover {
            background: #764ba2;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>{{ config('app.name', 'ACME Donation Platform') }}</h1>
        <p>Welcome to your donation platform</p>
        <p>Backend is running successfully</p>

        <div class="links">
            <a href="http://localhost:5173" target="_blank">Frontend (Vue.js)</a>
            <a href="/up">Health Check</a>
        </div>

        <div>
            <span class="badge">Laravel {{ app()->version() }}</span>
            <span class="badge">PHP {{ PHP_VERSION }}</span>
        </div>
    </div>
</body>
</html>
