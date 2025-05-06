<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Compte approuvé</title>
    <style>
        body {
            font-family: 'Poppins', 'Source Sans Pro', 'Lato', sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #F1F9FF;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
        }
        .header {
            text-align: center;
            padding: 20px 0;
            border-bottom: 1px solid #eaeaea;
        }
        .logo {
            max-width: 150px;
            margin-bottom: 15px;
        }
        .content {
            padding: 30px 20px;
            text-align: center;
        }
        .title {
            font-size: 22px;
            font-weight: 600;
            margin-bottom: 20px;
            color: #333;
        }
        .description {
            font-size: 16px;
            margin-bottom: 30px;
            color: #555;
        }
        .button {
            display: inline-block;
            padding: 12px 24px;
            background-color: #0072bc;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: 500;
            margin-bottom: 20px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        .button:hover {
            background-color: #005a95;
        }
        .footer {
            text-align: center;
            padding: 20px;
            font-size: 14px;
            color: #777;
            border-top: 1px solid #eaeaea;
        }
        .status {
            display: inline-block;
            padding: 8px 16px;
            background-color: #d4edda;
            color: #155724;
            border-radius: 50px;
            font-weight: 500;
            margin-bottom: 25px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="{{ asset('assets/img/dash/logo.png') }}" alt="StartupStudio Coach Logo" class="logo">
        </div>
        
        <div class="content">
            <div class="status">Compte approuvé</div>
            <h1 class="title">Félicitations {{ $user->name }} !</h1>
            <p class="description">Votre compte a été approuvé par notre équipe d'administration. Vous pouvez maintenant vous connecter et accéder à toutes les fonctionnalités de notre plateforme.</p>
            
            <a href="{{ $loginUrl ?? url(route('login')) }}" class="button">Se connecter</a>
            
            <p>Nous sommes ravis de vous compter parmi notre communauté et nous espérons que notre plateforme répondra à toutes vos attentes.</p>
        </div>
        
        <div class="footer">
            <p>&copy; {{ date('Y') }} StartupStudio Coach. Tous droits réservés.</p>
        </div>
    </div>
</body>
</html> 