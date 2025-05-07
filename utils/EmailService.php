<?php
require_once __DIR__ . '/../config/Config.php';

class EmailService {
    // Send email using SendGrid API
    public function sendEmail($to_email, $to_name, $subject, $html_content) {
        // SendGrid API endpoint
        $url = 'https://api.sendgrid.com/v3/mail/send';
        
        // Prepare email data
        $data = [
            'personalizations' => [
                [
                    'to' => [
                        [
                            'email' => $to_email,
                            'name' => $to_name
                        ]
                    ]
                ]
            ],
            'from' => [
                'email' => Config::SENDGRID_FROM_EMAIL,
                'name' => Config::SENDGRID_FROM_NAME
            ],
            'subject' => $subject,
            'content' => [
                [
                    'type' => 'text/html',
                    'value' => $html_content
                ]
            ]
        ];
        
        // Convert data to JSON
        $json_data = json_encode($data);
        
        // Initialize cURL
        $ch = curl_init($url);
        
        // Set cURL options
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . Config::SENDGRID_API_KEY,
            'Content-Type: application/json'
        ]);
        
        // Execute cURL request
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        // Close cURL
        curl_close($ch);
        
        // Return success or failure
        return $http_code >= 200 && $http_code < 300;
    }
}
?>