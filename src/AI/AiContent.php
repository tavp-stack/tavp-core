<?php

declare(strict_types=1);

namespace Tavp\Core\AI;

/**
 * AI Content — optimize, translate, improve content.
 */
class AiContent
{
    public function __construct(private AiManager $ai)
    {
    }

    /**
     * Improve content quality.
     */
    public function improve(string $content): string
    {
        return $this->ai->complete("Improve this content:\n\n{$content}");
    }

    /**
     * Translate content.
     */
    public function translate(string $content, string $targetLanguage): string
    {
        return $this->ai->complete("Translate to {$targetLanguage}:\n\n{$content}");
    }

    /**
     * Shorten content.
     */
    public function shorten(string $content, int $targetWords = 100): string
    {
        return $this->ai->complete("Shorten to {$targetWords} words:\n\n{$content}");
    }

    /**
     * Generate SEO meta.
     */
    public function seoMeta(string $content): array
    {
        $result = $this->ai->complete(
            "Generate SEO title and meta description for this content. Return JSON with 'title' and 'description' keys:\n\n{$content}"
        );

        return json_decode($result, true) ?? [
            'title' => substr($content, 0, 60),
            'description' => substr($content, 0, 160),
        ];
    }

    /**
     * Generate social media post.
     */
    public function socialPost(string $content, string $platform = 'twitter'): string
    {
        $limits = [
            'twitter' => 280,
            'linkedin' => 3000,
            'facebook' => 63206,
        ];

        $limit = $limits[$platform] ?? 280;
        return $this->ai->complete("Create a {$platform} post (max {$limit} chars):\n\n{$content}");
    }
}
