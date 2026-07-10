<?php

declare(strict_types=1);

namespace Tavp\Core\Community;

/**
 * Community manager — guidelines, code of conduct, contribution tools.
 */
class CommunityManager
{
    /**
     * Get contribution guidelines.
     */
    public function getGuidelines(): string
    {
        return <<<MARKDOWN
# Contributing to TAVP Stack

## How to Contribute

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## Code Style

- Follow PSR-12 coding standards
- Use PHP 8.3+ features
- Write meaningful commit messages
- Add tests for new features

## Reporting Issues

- Use GitHub Issues
- Include reproduction steps
- Include PHP version and OS
- Include error messages

## Code of Conduct

- Be respectful
- Be inclusive
- Be constructive
- Be collaborative
MARKDOWN;
    }

    /**
     * Get issue templates.
     */
    public function getIssueTemplates(): array
    {
        return [
            'bug_report' => [
                'name' => 'Bug Report',
                'about' => 'Report a bug',
                'labels' => ['bug'],
                'template' => '## Bug Description\n\n## Steps to Reproduce\n\n## Expected Behavior\n\n## Actual Behavior\n\n## Environment\n',
            ],
            'feature_request' => [
                'name' => 'Feature Request',
                'about' => 'Suggest a feature',
                'labels' => ['enhancement'],
                'template' => '## Description\n\n## Use Case\n\n## Proposed Solution\n',
            ],
            'module_request' => [
                'name' => 'Module Request',
                'about' => 'Request a new module',
                'labels' => ['module'],
                'template' => '## Module Name\n\n## Description\n\n## Use Case\n',
            ],
        ];
    }

    /**
     * Get pull request template.
     */
    public function getPrTemplate(): string
    {
        return <<<MARKDOWN
## Description

## Related Issue

## Type of Change

- [ ] Bug fix
- [ ] New feature
- [ ] Breaking change
- [ ] Documentation update

## Testing

- [ ] Unit tests pass
- [ ] Feature tests pass
- [ ] Manual testing completed

## Checklist

- [ ] Code follows style guidelines
- [ ] Self-review completed
- [ ] Documentation updated
- [ ] No breaking changes
MARKDOWN;
    }
}
