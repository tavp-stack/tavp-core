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
        $q = htmlspecialchars($data['question'], ENT_QUOTES, 'UTF-8');
        $t = htmlspecialchars($data['token'], ENT_QUOTES, 'UTF-8');

        return '<div class="tavp-captcha captcha-math bg-white rounded-2xl p-4 shadow-ambient border border-gray-100"' . $extra . '>
            <label class="block text-sm font-semibold text-[#1F2937] mb-2">' . $q . '</label>
            <input type="hidden" name="captcha_token" value="' . $t . '">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-base text-[#e6c446]">psychology</span>
                <input type="number" name="captcha_answer" required
                    class="w-full rounded-xl bg-[#F9FAFB] px-4 py-2.5 text-sm text-[#1F2937] outline-none transition-all duration-200 focus:bg-white focus:ring-2 focus:ring-[#e6c446]"
                    placeholder="Jawaban"
                    autocomplete="off"
                    min="0" max="999">
            </div>
        </div>';
    }

    private function renderPuzzle(array $data, array $attrs): string
    {
        $extra = $this->buildAttributes($attrs);
        $q = htmlspecialchars($data['question'], ENT_QUOTES, 'UTF-8');
        $t = htmlspecialchars($data['token'], ENT_QUOTES, 'UTF-8');

        $html = '<div class="tavp-captcha captcha-puzzle bg-white rounded-2xl p-4 shadow-ambient border border-gray-100"' . $extra . '>
            <label class="block text-sm font-semibold text-[#1F2937] mb-3 flex items-center gap-2">
                <span class="material-symbols-outlined text-base text-[#e6c446]">live_help</span>
                ' . $q . '
            </label>
            <input type="hidden" name="captcha_token" value="' . $t . '">';

        if (!empty($data['options'])) {
            $html .= '<div class="flex flex-wrap gap-2">';

            foreach ($data['options'] as $i => $option) {
                $esc = htmlspecialchars((string) $option, ENT_QUOTES, 'UTF-8');
                $id = 'captcha_opt_' . $i . '_' . substr($data['token'], 0, 8);
                $checked = $i === 0 ? ' checked' : '';

                $html .= '<input type="radio" name="captcha_answer" id="' . $id . '" value="' . $esc . '"'
                    . $checked . ' class="hidden peer">
                    <label for="' . $id . '"
                        class="px-4 py-2 rounded-xl text-sm font-medium text-[#1F2937] bg-[#F9FAFB] border border-gray-200 cursor-pointer transition-all duration-200 peer-checked:bg-[#e6c446] peer-checked:text-white peer-checked:border-[#e6c446] hover:bg-gray-100">'
                        . $esc . '</label>';
            }

            $html .= '</div>';
        } else {
            $html .= '<input type="text" name="captcha_answer" required
                class="w-full rounded-xl bg-[#F9FAFB] px-4 py-2.5 text-sm text-[#1F2937] outline-none transition-all duration-200 focus:bg-white focus:ring-2 focus:ring-[#e6c446]" autocomplete="off">';
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

        return '<div class="tavp-captcha captcha-slider bg-white rounded-2xl p-4 shadow-ambient border border-gray-100"' . $extra . '>
            <label class="block text-sm font-semibold text-[#1F2937] mb-3 flex items-center gap-2 justify-between">
                <span class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-base text-[#e6c446]">swipe</span>
                    Geser slice ke posisi yang tepat
                </span>
                <button type="button" onclick="location.reload()" class="p-1 rounded-lg hover:bg-gray-100 transition-colors" title="Refresh captcha">
                    <span class="material-symbols-outlined text-base text-gray-400">refresh</span>
                </button>
            </label>
            <div class="relative" id="' . $id . '-container">
                <img src="' . $bg . '" alt="Captcha" style="width:' . $w . 'px;height:' . $h . 'px;max-width:100%;border-radius:12px;display:block;user-select:none">
                <img src="' . $slice . '" alt="Slice"
                    style="position:absolute;top:0;left:0;width:' . $sw . 'px;height:' . $h . 'px;pointer-events:none;image-rendering:pixelated;border-radius:4px"
                    id="' . $id . '-slice">
                <div class="mt-3 px-1">
                    <input type="range" name="captcha_answer" id="' . $id . '-slider"
                        min="0" max="' . ($w - $sw) . '" value="0"
                        class="w-full h-2 rounded-full appearance-none cursor-pointer bg-gray-200 accent-[#e6c446]"
                        oninput="document.getElementById(\'' . $id . '-slice\').style.left=this.value+\'px\'">
                    <div class="flex justify-between text-[10px] text-gray-400 mt-0.5">
                        <span>Geser</span>
                        <span>Lepaskan</span>
                    </div>
                </div>
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
