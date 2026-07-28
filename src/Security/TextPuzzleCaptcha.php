<?php

declare(strict_types=1);

namespace Tavp\Core\Security;

class TextPuzzleCaptcha implements CaptchaInterface
{
    private const TTL = 120;

    public function generate(): array
    {
        $puzzles = $this->getPuzzles();
        $puzzle = $puzzles[array_rand($puzzles)];

        $token = bin2hex(random_bytes(16));

        $_SESSION['_captcha'][$token] = [
            'answer' => (string) $puzzle['answer'],
            'type' => 'puzzle',
            'expires_at' => time() + self::TTL,
        ];

        return [
            'token' => $token,
            'question' => $puzzle['question'],
            'options' => $puzzle['options'] ?? null,
            'type' => 'puzzle',
        ];
    }

    public function verify(string|int $answer, string $token): bool
    {
        $this->cleanup();

        if (!isset($_SESSION['_captcha'][$token])) {
            return false;
        }

        $data = $_SESSION['_captcha'][$token];

        if ($data['type'] !== 'puzzle' || time() > $data['expires_at']) {
            unset($_SESSION['_captcha'][$token]);
            return false;
        }

        $valid = hash_equals($data['answer'], trim($answer));

        unset($_SESSION['_captcha'][$token]);

        return $valid;
    }

    private function getPuzzles(): array
    {
        return [
            [
                'question' => 'Pilih angka GENAP dari berikut: 3, 8, 5, 7',
                'options' => ['3', '8', '5', '7'],
                'answer' => '8',
            ],
            [
                'question' => 'Pilih angka GANJIL dari berikut: 2, 6, 9, 4',
                'options' => ['2', '6', '9', '4'],
                'answer' => '9',
            ],
            [
                'question' => 'Pilih HURUF HIDUP (vokal): B, A, C, D',
                'options' => ['B', 'A', 'C', 'D'],
                'answer' => 'A',
            ],
            [
                'question' => 'Pilih HURUF MATI (konsonan): A, E, I, K',
                'options' => ['A', 'E', 'I', 'K'],
                'answer' => 'K',
            ],
            [
                'question' => 'Berapakah hasil dari 2 + 3 × 2? (kerjakan perkalian dulu)',
                'options' => ['10', '8', '7', '12'],
                'answer' => '8',
            ],
            [
                'question' => 'Angka berapakah yang TIDAK termasuk bilangan prima? 2, 3, 4, 5',
                'options' => ['2', '3', '4', '5'],
                'answer' => '4',
            ],
            [
                'question' => 'Warna apakah yang TIDAK ada di pelangi?',
                'options' => ['Merah', 'Hijau', 'Hitam', 'Biru'],
                'answer' => 'Hitam',
            ],
            [
                'question' => 'Ibukota Indonesia adalah?',
                'options' => ['Jakarta', 'Bandung', 'Surabaya', 'Medan'],
                'answer' => 'Jakarta',
            ],
            [
                'question' => 'Hewan apakah yang bisa terbang?',
                'options' => ['Kucing', 'Anjing', 'Elang', 'Ikan'],
                'answer' => 'Elang',
            ],
            [
                'question' => 'Berapakah akar kuadrat dari 25?',
                'options' => ['4', '5', '6', '7'],
                'answer' => '5',
            ],
        ];
    }

    private function cleanup(): void
    {
        if (!isset($_SESSION['_captcha']) || !is_array($_SESSION['_captcha'])) {
            return;
        }

        $now = time();

        foreach ($_SESSION['_captcha'] as $token => $data) {
            if ($now > ($data['expires_at'] ?? 0)) {
                unset($_SESSION['_captcha'][$token]);
            }
        }
    }
}
