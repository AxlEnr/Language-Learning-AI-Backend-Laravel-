# Language Learning API

AI-powered language learning backend built with Laravel 12 and Laravel Sail.

## Requirements

- Docker
- Docker Compose

## Setup

1. Copy environment file:
```bash
cp .env.example .env
```

2. Configure your OpenAI API key in `.env`:
```
OPENAI_API_KEY=your-api-key-here
```

> **Note**: For a free alternative, you can use OpenRouter or other OpenAI-compatible APIs by changing the `OPENAI_MODEL` and base URL in the AIService.

3. Start the containers:
```bash
./vendor/bin/sail up -d
```

4. Install dependencies:
```bash
./vendor/bin/sail composer install
```

5. Generate application key:
```bash
./vendor/bin/sail artisan key:generate
```

6. Run migrations and seeders:
```bash
./vendor/bin/sail artisan migrate --seed
```

## API Endpoints

### Authentication
- `POST /api/v1/auth/register` - Register new user
- `POST /api/v1/auth/login` - Login
- `POST /api/v1/auth/logout` - Logout (authenticated)
- `GET /api/v1/auth/user` - Get current user (authenticated)

### Modules & Lessons
- `GET /api/v1/modules` - List all modules
- `GET /api/v1/modules/{id}` - Get module details
- `GET /api/v1/lessons/{id}` - Get lesson details

### Progress
- `GET /api/v1/progress` - Get progress overview
- `POST /api/v1/progress/start` - Start a lesson
- `POST /api/v1/progress/answer` - Submit an answer
- `POST /api/v1/progress/lessons/{lessonId}/complete` - Complete a lesson

### AI
- `POST /api/v1/ai/conversations` - Start AI conversation
- `GET /api/v1/ai/conversations` - List conversations
- `GET /api/v1/ai/conversations/{id}` - Get conversation with messages
- `POST /api/v1/ai/conversations/{id}/messages` - Send message
- `POST /api/v1/ai/exercises/generate` - Generate AI exercise
- `GET /api/v1/ai/recommendations/lesson` - Get lesson recommendation

### Vocabulary
- `GET /api/v1/vocabulary/review` - Get words due for review
- `POST /api/v1/vocabulary/review/{userWordId}` - Review a word
- `POST /api/v1/vocabulary/add/{wordId}` - Add word to review list
- `GET /api/v1/vocabulary/mastered` - Get mastered words
- `GET /api/v1/vocabulary/progress` - Get vocabulary progress

### User
- `PUT /api/v1/user/profile` - Update profile
- `GET /api/v1/user/skills` - Get user skills
- `GET /api/v1/user/stats` - Get user stats

### Other
- `GET /api/v1/dashboard` - Get dashboard data
- `GET /api/v1/languages` - List languages
- `GET /api/v1/languages/{id}` - Get language details

## Architecture

- **Models**: Eloquent models for all entities
- **Services**: Business logic separated into services
- **Requests**: Form requests for validation
- **Resources**: API resources for consistent responses
- **Enums**: Typed enums for statuses and types
- **Rules**: Custom validation rules

## Services

- `AIService` - OpenAI integration for conversations and feedback
- `ProgressionService` - Lesson progress and answer evaluation
- `VocabularyService` - Spaced repetition system
- `AdaptiveService` - Personalized learning recommendations

## Free AI Options

To use a free AI provider, modify the `AIService` base URL:

```php
// Use OpenRouter (has free tier)
protected string $baseUrl = 'https://openrouter.ai/api/v1';

// Use Ollama (local, completely free)
protected string $baseUrl = 'http://host.docker.internal:11434/v1';
```

Then set the appropriate model in `.env`:
```
OPENAI_MODEL=meta-llama/llama-3.1-8b-instruct:free
```
