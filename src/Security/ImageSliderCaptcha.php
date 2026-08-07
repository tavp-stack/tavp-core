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

    /** Directory containing bundled placeholder photos (JPG). */
    private const PHOTO_DIR = __DIR__ . '/../../resources/assets/captcha';

    public function generate(): array
    {
        $this->ensureGdLoaded();

        $x = random_int(self::SLICE_WIDTH + 10, self::IMG_WIDTH - self::SLICE_WIDTH - 10);

        $fullImage = $this->createBackground();

        $slotImage = $this->drawSlot($fullImage, $x);

        $sliceImage = $this->extractSlice($fullImage, $x);

        $token = bin2hex(random_bytes(16));

        $_SESSION['_captcha'][$token] = [
            'answer' => (string) $x,
            'type' => 'slider',
            'tolerance' => self::TOLERANCE,
            'expires_at' => time() + self::TTL,
        ];

        ob_start();
        imagepng($slotImage);
        $bgData = base64_encode(ob_get_clean());
        imagedestroy($slotImage);

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
            'targetX' => $x,
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
        $photo = $this->loadPhoto();

        if ($photo !== null) {
            return $this->resizeToCanvas($photo);
        }

        return $this->createPatternBackground();
    }

    private function loadPhoto(): ?\GdImage
    {
        // Prefer PNG: it is the most universally decodable by GD across
        // runtimes (some container builds ship without JPEG/WebP support).
        foreach (['png', 'jpg', 'jpeg', 'webp'] as $ext) {
            $files = glob(self::PHOTO_DIR . "/*.{$ext}");
            if (!$files) {
                continue;
            }

            $file = $files[array_rand($files)];
            $img = @imagecreatefromstring((string) file_get_contents($file));

            if ($img !== false) {
                return $img;
            }
        }

        return null;
    }

    private function resizeToCanvas(\GdImage $src): \GdImage
    {
        $canvas = imagecreatetruecolor(self::IMG_WIDTH, self::IMG_HEIGHT);

        $srcW = imagesx($src);
        $srcH = imagesy($src);
        $ratio = max(self::IMG_WIDTH / $srcW, self::IMG_HEIGHT / $srcH);

        $newW = (int) round($srcW * $ratio);
        $newH = (int) round($srcH * $ratio);
        $resized = imagecreatetruecolor($newW, $newH);

        imagecopyresampled($resized, $src, 0, 0, 0, 0, $newW, $newH, $srcW, $srcH);
        imagecopy($canvas, $resized, (int) ((self::IMG_WIDTH - $newW) / 2), (int) ((self::IMG_HEIGHT - $newH) / 2), 0, 0, $newW, $newH);

        imagedestroy($resized);
        imagedestroy($src);

        return $canvas;
    }

    private function createPatternBackground(): \GdImage
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

    /**
     * Produce the background shown to the user, with the target slot
     * visibly hollowed out so it is obvious where the slice belongs.
     */
    private function drawSlot(\GdImage $source, int $x): \GdImage
    {
        $canvas = imagecreatetruecolor(self::IMG_WIDTH, self::IMG_HEIGHT);
        imagecopy($canvas, $source, 0, 0, 0, 0, self::IMG_WIDTH, self::IMG_HEIGHT);

        // Semi-transparent dark overlay over the target slot.
        $dark = imagecolorallocatealpha($canvas, 10, 12, 18, 60);
        imagefilledrectangle($canvas, $x, 0, $x + self::SLICE_WIDTH - 1, self::IMG_HEIGHT - 1, $dark);

        // Dashed outline to indicate exactly where the piece sits.
        $white = imagecolorallocate($canvas, 255, 255, 255);
        $style = [$white, $white, $white, $white, $white, $white, $white, $white, IMG_COLOR_TRANSPARENT, IMG_COLOR_TRANSPARENT, IMG_COLOR_TRANSPARENT, IMG_COLOR_TRANSPARENT];
        imagesetstyle($canvas, $style);
        imageline($canvas, $x, 0, $x + self::SLICE_WIDTH - 1, 0, IMG_COLOR_STYLED);
        imageline($canvas, $x, self::IMG_HEIGHT - 1, $x + self::SLICE_WIDTH - 1, self::IMG_HEIGHT - 1, IMG_COLOR_STYLED);
        imageline($canvas, $x, 0, $x, self::IMG_HEIGHT - 1, IMG_COLOR_STYLED);
        imageline($canvas, $x + self::SLICE_WIDTH - 1, 0, $x + self::SLICE_WIDTH - 1, self::IMG_HEIGHT - 1, IMG_COLOR_STYLED);

        return $canvas;
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
