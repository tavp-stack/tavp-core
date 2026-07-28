<?php

declare(strict_types=1);

namespace Tavp\Core\Security;

class ImageSliderCaptcha implements CaptchaInterface
{
    private const TTL = 120;

    private const IMG_WIDTH = 320;

    private const IMG_HEIGHT = 160;

    private const SLICE_WIDTH = 40;

    private const TOLERANCE = 15;

    public function generate(): array
    {
        $this->ensureGdLoaded();

        $x = random_int(self::SLICE_WIDTH + 10, self::IMG_WIDTH - self::SLICE_WIDTH - 10);

        $fullImage = $this->createBackground();

        $sliceImage = $this->extractSlice($fullImage, $x);

        $token = bin2hex(random_bytes(16));

        $_SESSION['_captcha'][$token] = [
            'answer' => (string) $x,
            'type' => 'slider',
            'tolerance' => self::TOLERANCE,
            'expires_at' => time() + self::TTL,
        ];

        ob_start();
        imagepng($fullImage);
        $bgData = base64_encode(ob_get_clean());
        imagedestroy($fullImage);

        ob_start();
        imagepng($sliceImage);
        $sliceData = base64_encode(ob_get_clean());
        imagedestroy($sliceImage);

        return [
            'token' => $token,
            'background' => 'data:image/png;base64,' . $bgData,
            'slice' => 'data:image/png;base64,' . $sliceData,
            'width' => self::IMG_WIDTH,
            'height' => self::IMG_HEIGHT,
            'sliceWidth' => self::SLICE_WIDTH,
            'type' => 'slider',
        ];
    }

    public function verify(string|int $answer, string $token): bool
    {
        $this->cleanup();

        if (!isset($_SESSION['_captcha'][$token])) {
            return false;
        }

        $data = $_SESSION['_captcha'][$token];

        if ($data['type'] !== 'slider' || time() > $data['expires_at']) {
            unset($_SESSION['_captcha'][$token]);
            return false;
        }

        $actual = (int) $answer;
        $expected = (int) $data['answer'];
        $tolerance = (int) ($data['tolerance'] ?? self::TOLERANCE);

        $valid = abs($actual - $expected) <= $tolerance;

        unset($_SESSION['_captcha'][$token]);

        return $valid;
    }

    private function createBackground(): \GdImage
    {
        $img = imagecreatetruecolor(self::IMG_WIDTH, self::IMG_HEIGHT);

        $bg = imagecolorallocate($img, random_int(220, 255), random_int(220, 255), random_int(220, 255));
        imagefill($img, 0, 0, $bg);

        $shapes = random_int(4, 8);

        for ($i = 0; $i < $shapes; $i++) {
            $color = imagecolorallocate($img, random_int(30, 200), random_int(30, 200), random_int(30, 200));
            $shape = random_int(0, 2);

            switch ($shape) {
                case 0:
                    imagefilledellipse(
                        $img,
                        random_int(10, self::IMG_WIDTH - 10),
                        random_int(10, self::IMG_HEIGHT - 10),
                        random_int(20, 60),
                        random_int(20, 60),
                        $color
                    );
                    break;
                case 1:
                    imagefilledrectangle(
                        $img,
                        random_int(0, self::IMG_WIDTH - 40),
                        random_int(0, self::IMG_HEIGHT - 40),
                        random_int(20, self::IMG_WIDTH),
                        random_int(20, self::IMG_HEIGHT),
                        $color
                    );
                    break;
                case 2:
                    $x1 = random_int(0, self::IMG_WIDTH);
                    $y1 = random_int(0, self::IMG_HEIGHT);
                    $x2 = random_int(0, self::IMG_WIDTH);
                    $y2 = random_int(0, self::IMG_HEIGHT);
                    imageline($img, $x1, $y1, $x2, $y2, $color);
                    break;
            }
        }

        return $img;
    }

    private function extractSlice(\GdImage $source, int $x): \GdImage
    {
        $slice = imagecreatetruecolor(self::SLICE_WIDTH, self::IMG_HEIGHT);

        imagealphablending($slice, false);
        imagesavealpha($slice, true);
        $transparent = imagecolorallocatealpha($slice, 0, 0, 0, 127);
        imagefill($slice, 0, 0, $transparent);

        imagecopy($slice, $source, 0, 0, $x, 0, self::SLICE_WIDTH, self::IMG_HEIGHT);

        return $slice;
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

    private function ensureGdLoaded(): void
    {
        if (!extension_loaded('gd')) {
            throw new \RuntimeException('GD extension is required for ImageSliderCaptcha.');
        }
    }
}
