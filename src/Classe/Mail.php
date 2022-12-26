<?php

namespace App\Classe;

use Mailjet\Resources;

class Mail
{
    public function send($to_email, $to_name, $subjet, $content)
    {
        $mj = new \Mailjet\Client(getenv('MAILJET_PUBLIC_KEY'), getenv('MAILJET_SECRET_KEY'),true,['version' => 'v3.1']);
        $body = [
            'Messages' => [
                [
                    'From' => [
                        'Email' => "jonathandevelopper971@gmail.com",
                        'Name' => "La Boutique française"
                    ],
                    'To' => [
                        [
                            'Email' => $to_email,
                            'Name' => $to_name
                        ]
                    ],
                    'TemplateID' => 4406445,
                    'TemplateLanguage' => true,
                    'Subject' => $subjet,
                    'Variables' => [
                        'content' => $content // c'est ici qu'on va envoyer le message personnaliser dans le template
                    ],
                ]
            ]
        ];

        $response = $mj->post(Resources::$Email, ['body' => $body]);
        $response->success() /*&& dump($response->getData())*/;  
    }
}