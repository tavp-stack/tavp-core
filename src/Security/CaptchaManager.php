<?php

declare(strict_types=1);

namespace Tavp\Core\Security;

class CaptchaManager
{
    private const DRIVERS = [
        'math' => MathCaptcha::class,
        'puzzle' => TextPuzzleCaptcha::class,
        'slider' => ImageSliderCaptcha::class,
    ];

    public function make(string $type): CaptchaInterface
    {
        if (!isset(self::DRIVERS[$type])) {
            throw new \InvalidArgumentException("Unknown captcha type: {$type}");
        }

        $class = self::DRIVERS[$type];

        return new $class();
    }

    public function generate(string $type): array
    {
        return $this->make($type)->generate();
    }

    public function verify(string|int $answer, string $token): bool
    {
        $this->cleanup();

        if (!isset($_SESSION['_captcha'][$token])) {
            return false;
        }

        $type = $_SESSION['_captcha'][$token]['type'] ?? null;

        if ($type === null) {
            return false;
        }

        $driver = $this->make($type);

        return $driver->verify($answer, $token);
    }

    public function render(string $type, array $attributes = []): string
    {
        $data = $this->generate($type);
        $view = match ($type) {
            'math' => $this->renderMath($data, $attributes),
            'puzzle' => $this->renderPuzzle($data, $attributes),
            'slider' => $this->renderSlider($data, $attributes),
            default => throw new \InvalidArgumentException("No renderer for: {$type}"),
        };

        return $view;
    }

    private function renderMath(array $data, array $attrs): string
    {
        $extra = $this->buildAttributes($attrs);

        return '<div class="tavp-captcha captcha-math"' . $extra . '>
            <label class="captcha-question">' . htmlspecialchars($data['question'], ENT_QUOTES, 'UTF-8') . '</label>
            <input type="hidden" name="captcha_token" value="' . htmlspecialchars($data['token'], ENT_QUOTES, 'UTF-8') . '">
            <input type="number" name="captcha_answer" required
                class="captcha-input"
                placeholder="Jawaban"
                autocomplete="off"
                min="0" max="999">
        </div>';
    }

    private function renderPuzzle(array $data, array $attrs): string
    {
        $extra = $this->buildAttributes($attrs);

        $html = '<div class="tavp-captcha captcha-puzzle"' . $extra . '>
            <label class="captcha-question">' . htmlspecialchars($data['question'], ENT_QUOTES, 'UTF-8') . '</label>
            <input type="hidden" name="captcha_token" value="' . htmlspecialchars($data['token'], ENT_QUOTES, 'UTF-8') . '">';

        if (!empty($data['options'])) {
            $html .= '<div class="captcha-options">';

            foreach ($data['options'] as $i => $option) {
                $id = 'captcha_opt_' . $i;
                $html .= '<input type="radio" name="captcha_answer" id="' . $id
                    . '" value="' . htmlspecialchars((string) $option, ENT_QUOTES, 'UTF-8') . '"'
                    . ($i === 0 ? ' checked' : '') . '>
                    <label for="' . $id . '">' . htmlspecialchars((string) $option, ENT_QUOTES, 'UTF-8') . '</label>';
            }

            $html .= '</div>';
        } else {
            $html .= '<input type="text" name="captcha_answer" required class="captcha-input" autocomplete="off">';
        }

        $html .= '</div>';

        return $html;
    }

    private function renderSlider(array $data, array $attrs): string
    {
        $extra = $this->buildAttributes($attrs);
        $token = htmlspecialchars($data['token'], ENT_QUOTES, 'UTF-8');
        $bg = htmlspecialchars($data['background'], ENT_QUOTES, 'UTF-8');
        $slice = htmlspecialchars($data['slice'], ENT_QUOTES, 'UTF-8');
        $w = (int) $data['width'];
        $sw = (int) $data['sliceWidth'];
        $h = (int) $data['height'];

        $id = 'tavp-slider-' . $token;

        return '<div class="tavp-captcha captcha-slider"' . $extra . '>
            <div class="slider-container" id="' . $id . '-container" style="position:relative;width:' . $w . 'px;height:' . ($h + 50) . 'px">
                <img src="' . $bg . '" alt="Captcha" style="width:' . $w . 'px;height:' . $h . 'px;border-radius:8px;display:block;user-select:none">
                <img src="' . $slice . '" alt="Slice"
                    style="position:absolute;top:0;left:0;width:' . $sw . 'px;height:' . $h . 'px;pointer-events:none;image-rendering:pixelated"
                    id="' . $id . '-slice">
                <input type="range" name="captcha_answer" id="' . $id . '-slider"
                    min="0" max="' . ($w - $sw) . '" value="0"
                    style="width:100%;margin-top:8px"
                    oninput="document.getElementById(\'' . $id . '-slice\').style.left=this.value+\'px\'">
            </div>
            <input type="hidden" name="captcha_token" value="' . $token . '">
        </div>';
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

    private function buildAttributes(array $attrs): string
    {
        $parts = [];

        foreach ($attrs as $key => $value) {
            $parts[] = htmlspecialchars((string) $key, ENT_QUOTES, 'UTF-8')
                . '="' . htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8') . '"';
        }

        return $parts ? ' ' . implode(' ', $parts) : '';
    }
}
