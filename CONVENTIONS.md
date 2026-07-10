# TAVP Coding Conventions

Human Style Coding Standards — plain-text reference for developers and AI tools.
Full version: https://tavp.web.id/coding-standards.html

## Core Rule

Code is written for humans. The Glance Test: can you understand what it does in 3 seconds? If no, rewrite it.

## File Naming

| Type | Convention | Example |
|------|-----------|---------|
| PHP class | PSR-4 (class = file name) | `User.php`, `PaymentService.php` |
| Volt template | lowercase, hyphen-separated | `admin-dashboard.volt`, `user-profile.volt` |
| Migration | Sequential numbers | `001_create_users_table.php` |
| Config | lowercase | `cms.php`, `database.php` |
| JavaScript | lowercase, descriptive | `otp-form.js`, `modal.js` |

## Variable Naming — snake_case, full English words

```php
// GOOD
$user_email = 'test@example.com';
$post_count = 10;
$is_authenticated = true;
$order_list = [];

// BAD
$email = 'test@example.com';     // too short
$cnt = 10;                       // abbreviation
$auth = true;                    // unclear
$data = [];                      // meaningless
```

**Booleans MUST start with:** `is_`, `has_`, `can_`, `should_`

## Function Naming — snake_case, verb + noun

```php
// GOOD
get_user_by_email($email)
get_all_published_posts()
create_new_user($data)
update_user_profile($user, $data)
delete_post_by_id($post_id)
send_welcome_email_to_user($user)
validate_login_input($request)

// BAD
getUser($email)        // camelCase
fetch()                // no noun
processData($d)        // unclear + abbreviated
```

## Class Naming — PascalCase, full English words

| Suffix | Purpose | Example |
|--------|---------|---------|
| `Controller` | HTTP handlers | `AuthController`, `DashboardController` |
| `Service` | Business logic | `PaymentService`, `OtpService` |
| (none) | Models | `User`, `Post`, `Order` |
| `Middleware` | Request filters | `VerifyCsrfToken`, `RequireLogin` |
| `Job` | Queued tasks | `SendWelcomeEmailJob` |
| `Event` | Something that happened | `UserRegisteredEvent` |
| `Listener` | Reacts to events | `SendWelcomeEmailListener` |
| `Repository` | Data access | `UserRepository` |
| `Validator` | Validation logic | `EmailValidator` |
| `Exception` | Errors | `UserNotFoundException` |
| `ServiceProvider` | Module registration | `AuthServiceProvider` |

## Model Convention

```php
<?php

declare(strict_types=1);

namespace App\Models;

use Tavp\Core\Database\Model;
use Tavp\Core\Database\Relations;

class Post extends Model
{
    protected string $table = 'posts';
    protected string $primaryKey = 'id';

    protected array $fillable = [
        'title',
        'slug',
        'body',
        'status',
    ];

    protected array $casts = [
        'published_at' => 'datetime',
    ];

    protected bool $timestamps = true;
    protected bool $softDeletes = false;

    use Relations;
}
```

## Controller Convention

```php
<?php

declare(strict_types=1);

namespace App\Controllers;

use Tavp\Core\Http\Response;

class PostController extends BaseController
{
    public function index(): string
    {
        $posts = $this->service->get_all_published_posts();

        return $this->view('posts.index', ['posts' => $posts]);
    }

    public function show(string $slug): string|Response
    {
        $post = $this->service->get_post_by_slug($slug);

        if ($post === null) {
            return response('404 Not Found', 404);
        }

        return $this->view('posts.show', ['post' => $post]);
    }
}
```

## Route Convention

```php
<?php

declare(strict_types=1);

/** @var \Tavp\Core\Routing\Router $router */

// Resource routes
$router->get('/posts', [PostController::class, 'index']);
$router->get('/posts/{slug}', [PostController::class, 'show']);
$router->post('/posts', [PostController::class, 'store']);
$router->put('/posts/{id}', [PostController::class, 'update']);
$router->delete('/posts/{id}', [PostController::class, 'destroy']);

// Named routes with closures
$router->get('/about', function () {
    return view('pages.about');
});
```

## Volt Template Convention

```volt
{# GOOD: descriptive, readable #}
{% extends 'layouts/app.volt' %}

{% block content %}
<h1>{{ post.title }}</h1>
<div class="body">{{ post.body }}</div>

{% if post.categories is defined %}
    {% for category in post.categories %}
        <span class="tag">{{ category.name }}</span>
    {% endfor %}
{% endif %}
{% endblock %}
```

## Config Key Convention

```php
// GOOD — descriptive, namespaced
'cms' => [
    'admin' => [
        'otp_ttl_minutes' => 10,
        'allowed_emails' => [],
    ],
    'storage' => 'flatfile',
],

// BAD — vague, global
'admin_timeout' => 10,
'storage_type' => 'flatfile',
```

## Database Convention

```php
// Table names: plural, snake_case
'users', 'posts', 'content_types', 'media_library'

// Column names: snake_case
'email_verified_at', 'created_at', 'featured_image'

// Foreign keys: singular_id
'user_id', 'post_id', 'parent_id'
```

## Route Parameters

```php
// Use descriptive names
$router->get('/blog/{slug}', ...);     // GOOD
$router->get('/blog/{id}', ...);       // BAD — {id} is vague

$router->get('/admin/c/{type}/{id}/edit', ...);  // GOOD
$router->get('/admin/c/{t}/{i}/edit', ...);       // BAD — abbreviated
```

## Error Messages

```php
// GOOD — specific, actionable
'The e-mail address is not valid.'
'Post not found with slug: hello-world'
'You do not have permission to delete this post.'

// BAD — vague
'Invalid input.'
'Error.'
'Not found.'
```

## Directory Structure

```
app/
  Controllers/         # HTTP handlers
  Models/              # Database models (extend Tavp\Core\Database\Model)
  Services/            # Business logic
  Middleware/           # Request filters
config/                # Configuration files
database/migrations/   # Schema migrations (sequential numbering)
public/                # Web root (index.php, assets)
resources/views/       # Volt templates
routes/                # Route definitions
storage/               # Cache, compiled views, logs
themes/                # Frontend themes (Volt + Tailwind)
```

## AI Prompt Tip

When using AI to generate TAVP code, include this prompt:

```
Generate PHP code following TAVP Stack conventions:
- PSR-4 autoloading, snake_case functions/variables, PascalCase classes
- Models extend Tavp\Core\Database\Model with $table, $fillable, $casts
- Controllers extend BaseController, return string|Response
- Use app()->getService() for dependency injection
- Use response('content', 200) or redirect('/path') helpers
- Volt templates use {% extends %} and {% block content %}
- No abbreviations, full English words, descriptive names
```
