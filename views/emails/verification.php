<?php
// Template pour l'email de vérification
// Les variables $studentName et $verificationLink sont passées depuis le contrôleur
?>
<div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; background-color: #ffffff;'>
    <!-- En-tête avec image -->
    <div style='background-color: #1a365d; padding: 20px; text-align: center;'>
        <img src='<?php echo Config::BASE_URL; ?>../public/images/images.png' 
             alt='Bibliothèque' 
             style='width: 100%; max-width: 600px; height: auto; border-radius: 8px;'>
    </div>
    
    <!-- Contenu principal -->
    <div style='padding: 30px; background-color: #f8fafc;'>
        <h1 style='color: #1a365d; text-align: center; margin-bottom: 20px;'>
            Bienvenue dans votre nouvelle aventure littéraire! 📚
        </h1>
        
        <p style='font-size: 16px; line-height: 1.6; color: #2d3748;'>
            Bonjour <strong><?php echo $studentName; ?></strong>,
        </p>
        
        <p style='font-size: 16px; line-height: 1.6; color: #2d3748;'>
            Nous sommes ravis de vous accueillir dans notre bibliothèque numérique! Votre inscription est presque terminée. 
            Il ne vous reste plus qu'une étape pour accéder à :
        </p>
        
        <ul style='font-size: 16px; line-height: 1.6; color: #2d3748;'>
            <li>✨ Des milliers de livres à portée de clic</li>
            <li>📖 Des ressources pédagogiques exclusives</li>
            <li>🔍 Un système de recherche avancé</li>
            <li>📱 Une interface intuitive et moderne</li>
        </ul>
        
        <!-- Bouton d'activation -->
        <div style='text-align: center; margin: 30px 0;'>
            <a href='<?php echo $verificationLink; ?>' 
               style='background-color: #2563eb; 
                      color: white; 
                      padding: 15px 30px; 
                      text-decoration: none; 
                      border-radius: 8px;
                      font-size: 16px;
                      font-weight: bold;
                      display: inline-block;
                      box-shadow: 0 4px 6px rgba(37, 99, 235, 0.2);
                      transition: all 0.3s ease;'>
                Activer mon compte maintenant
            </a>
        </div>
        
        <p style='font-size: 14px; color: #4a5568; text-align: center;'>
            Ce lien est valable pendant 24 heures
        </p>
        
        <!-- Lien alternatif -->
        <div style='background-color: #edf2f7; padding: 15px; border-radius: 8px; margin-top: 20px;'>
            <p style='font-size: 14px; color: #4a5568; margin: 0;'>
                Si le bouton ne fonctionne pas, copiez et collez ce lien dans votre navigateur :<br>
                <span style='color: #2563eb; word-break: break-all;'><?php echo $verificationLink; ?></span>
            </p>
        </div>
    </div>
    
    <!-- Pied de page -->
    <div style='background-color: #1a365d; color: white; padding: 20px; text-align: center;'>
        <p style='margin: 0; font-size: 14px;'>
            Cordialement,<br>
            <strong>L'équipe de la bibliothèque</strong>
        </p>
        <p style='margin: 10px 0 0 0; font-size: 12px;'>
            Si vous n'avez pas demandé cette inscription, veuillez ignorer cet email.
        </p>
    </div>
</div>