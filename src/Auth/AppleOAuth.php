<?php

declare(strict_types=1);

namespace Tavp\Core\Auth;

/**
 * Social OAuth — Apple login integration.
 */
class AppleOAuth
{
    private string $clientId;
    private string $teamId;
    private string $keyId;
    private string $privateKey;
    private string $redirectUri;

    public function __construct(array $config)
    {
        $this->clientId = $config['client_id'] ?? '';
        $this->teamId = $config['team_id'] ?? '';
        $this->keyId = $config['key_id'] ?? '';
        $this->privateKey = $config['private_key'] ?? '';
        $this->redirectUri = $config['redirect_uri'] ?? '';
    }

    /**
     * Get the authorization URL.
     */
    public function getAuthUrl(): string
    {
        $params = http_build_query([
            'client_id' => $this->clientId,
            'redirect_uri' => $this->redirectUri,
            'response_type' => 'code id_token',
            'scope' => 'name email',
            'response_mode' => 'form_post',
        ]);

        return "https://appleid.apple.com/auth/authorize?{$params}";
    }

    /**
     * Exchange authorization code for tokens.
     */
    public function exchangeCode(string $code): ?array
    {
        $clientSecret = $this->generateClientSecret();

        $data = [
            'client_id' => $this->clientId,
            'client_secret' => $clientSecret,
            'code' => $code,
            'grant_type' => 'authorization_code',
            'redirect_uri' => $this->redirectUri,
        ];

        $ch = curl_init('https://appleid.apple.com/auth/token');
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS => http_build_query($data),
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            return null;
        }

        return json_decode($response, true);
    }

    /**
     * Generate client secret JWT for Apple.
     */
    private function generateClientSecret(): string
    {
        $header = base64_encode(json_encode(['alg' => 'ES256', 'kid' => $this->keyId]));
        $payload = base64_encode(json_encode([
            'iss' => $this->teamId,
            'iat' => time(),
            'exp' => time() + 3600,
            'aud' => 'https://appleid.apple.com',
            'sub' => $this->clientId,
        ]));

        // In production, sign with Apple's private key using ECDSA
        return "{$header}.{$payload}.signature";
    }
}
