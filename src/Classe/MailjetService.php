<?php

namespace App\Classe;

use Mailjet\Client;
use Mailjet\Resources;

class MailjetService
{
    protected $publicKey;
    protected $secretKey;

    public function __construct(
        string $publicKey,
        string $secretKey
    ) {
        $this->secretKey = $secretKey;
        $this->publicKey = $publicKey;
    }

    /**
     * Envoi des email via l'api Mailjet
     */
    public function send(
        string $to_email,
        string $to_name,
        string $subject,
        array $variables
    ) {
        $mj = new Client($this->publicKey, $this->secretKey, true, ['version' => 'v3.1']);
        $body = [
            'Messages' => [
                [
                    'From' => [
                        'Email' => "immersioncanyon@gmail.com",
                        'Name' => "Le Gorille"
                    ],
                    'To' => [
                        [
                            'Email' => $to_email,
                            'Name' => $to_name
                        ]
                    ],
                    'TemplateID' => 3813450,
                    'TemplateLanguage' => true,
                    'Subject' => $subject,
                    'Variables' => $variables
                ]
            ]
        ];
        $response = $mj->post(Resources::$Email, ['body' => $body]);
        $response->success() && dump($response->getData());
    }
}
