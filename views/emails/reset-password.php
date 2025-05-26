<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Réinitialisation de votre mot de passe</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <h2 style="color: #2c3e50;">Bonjour <?php echo htmlspecialchars($studentName); ?>,</h2>
        
        <p>Vous avez demandé la réinitialisation de votre mot de passe.</p>
        
        <p>Pour réinitialiser votre mot de passe, cliquez sur le lien ci-dessous :</p>
        
        <div style="text-align: center; margin: 30px 0;">
            <a href="<?php echo htmlspecialchars($resetLink); ?>" 
               style="background-color: #3498db; color: white; padding: 12px 24px; text-decoration: none; border-radius: 4px;">
                Réinitialiser mon mot de passe
            </a>
        </div>
        
        <p>Ce lien expirera dans 1 heure pour des raisons de sécurité.</p>
        
        <p>Si vous n'avez pas demandé cette réinitialisation, vous pouvez ignorer cet email.</p>
        
        <hr style="border: 1px solid #eee; margin: 20px 0;">
        
        <p style="color: #7f8c8d; font-size: 12px;">
            Cet email a été envoyé automatiquement, merci de ne pas y répondre.
        </p>
    </div>
</body>
</html> 