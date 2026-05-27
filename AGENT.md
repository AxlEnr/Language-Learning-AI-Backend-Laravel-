# Language Learning API - Agent Guide

## Overview

**Language Learning API** is an AI-powered language learning backend built with **Laravel 12 + PHP 8.4 + PostgreSQL**. It provides a RESTful JSON API for a language learning platform featuring structured courses, AI conversation practice, spaced repetition vocabulary, and adaptive learning.

## Tech Stack

| Component       | Technology                |
|-----------------|---------------------------|
| Framework       | Laravel 12.x              |
| Language        | PHP ^8.4                  |
| Database        | PostgreSQL (via Docker/Sail) |
| Auth            | Laravel Sanctum (tokens)  |
| AI              | OpenRouter API (OpenAI-compatible, gpt-4o-mini) |
| Queue/Cache     | Database driver           |
| Container       | Laravel Sail (Docker)     |

## Project Structure

```
language-learning-api/
├── app/
│   ├── Enums/                  # Backed enums (SkillType, AIRole, ProgressStatus, ExerciseType, LessonType)
│   ├── Http/
│   │   ├── Controllers/        # Thin controllers (Auth, Dashboard, Language, Module, Lesson, Progress, AI, Vocabulary, User)
│   │   ├── Requests/           # Form request validation (Register, Login, StartLesson, SubmitAnswer, etc.)
│   │   ├── Resources/          # API transformers (ModuleResource, LessonResource, ExerciseResource)
│   │   └── Middleware/
│   ├── Interfaces/             # Contracts (AIServiceInterface)
│   ├── Models/                 # 14 Eloquent models
│   ├── Rules/                  # Custom validation (AccessibleLesson, ValidExerciseAnswer)
│   ├── Services/               # Business logic
│   │   ├── AI/AIService.php
│   │   ├── Progression/ProgressionService.php
│   │   ├── Vocabulary/VocabularyService.php
│   │   └── Adaptive/AdaptiveService.php
│   ├── Traits/                 # HasUserStats trait
│   └── Providers/              # Service providers
├── config/                     # Config files (auth, sanctum, services)
├── database/
│   ├── migrations/             # 17 migration files
│   ├── seeders/                # Languages, Levels, Words, DemoContent, UserWithStats
│   └── factories/
├── routes/
│   ├── api.php                 # All API routes (prefix /api/v1)
│   └── web.php
├── docker-compose.yml          # Sail setup
└── .env.example
```

## Architecture Pattern

**Monolithic API with internal service-oriented architecture:**

```
┌─────────────────────────────────────────────────────────────┐
│  HTTP Layer (Routes → Controllers → Resources)              │
│  Validation Layer (Form Requests + Custom Rules)            │
│  ──────────────────────────────────────────────────────────┤
│  Service Layer (Business Logic)                             │
│  ┌──────────────┬───────────────┬────────────────────────┐ │
│  │ AIService    │ Progression   │ VocabularyService      │ │
│  │              │ Service       │                        │ │
│  ├──────────────┼───────────────┼────────────────────────┤ │
│  │ AdaptiveService                                          │ │
│  └─────────────────────────────────────────────────────────┘ │
│  ──────────────────────────────────────────────────────────┤
│  Model Layer (Eloquent ORM)                                  │
│  ──────────────────────────────────────────────────────────┤
│  Database (PostgreSQL, 17 tables)                           │
└─────────────────────────────────────────────────────────────┘
```

Key decisions:
- **Controllers are thin** - they delegate all logic to Services
- **Form Requests** handle all validation
- **API Resources** ensure consistent JSON response shapes
- **AIServiceInterface** allows swapping AI providers without changing consumers
- **HasUserStats trait** uses Eloquent boot events to auto-create stats on user creation

## Models & Database

### Core Entities

| Model              | Table                  | Key Fields                                                       |
|--------------------|------------------------|------------------------------------------------------------------|
| User               | users                  | name, email, password, native_language_id, target_language_id, level_id |
| Language           | languages              | code, name                                                       |
| Level              | levels                 | code (A1-C2), description                                        |
| Module             | modules                | language_id, level_id, title, description, order_index            |
| Lesson             | lessons                | module_id, title, type (LessonType enum), order_index             |
| Exercise           | exercises              | lesson_id, type (ExerciseType enum), prompt, metadata (json)      |

### Progress & Tracking

| Model              | Table                  | Key Fields                                                       |
|--------------------|------------------------|------------------------------------------------------------------|
| UserLessonProgress | user_lesson_progress   | user_id, lesson_id, status (ProgressStatus enum), score           |
| UserAnswer         | user_answers           | user_id, exercise_id, answer, is_correct, feedback                |
| UserSkill          | user_skills            | user_id, skill (SkillType enum), level, last_updated              |
| UserStats          | user_stats             | user_id, xp, streak_days, last_activity_date                      |

### Vocabulary (Spaced Repetition)

| Model              | Table                  | Key Fields                                                       |
|--------------------|------------------------|------------------------------------------------------------------|
| Word               | words                  | language_id, word, meaning, example_sentence                     |
| UserWord           | user_words             | user_id, word_id, familiarity (0-5), next_review_at               |

### AI Chat

| Model              | Table                  | Key Fields                                                       |
|--------------------|------------------------|------------------------------------------------------------------|
| AIConversation    | ai_conversations       | user_id, context (json)                                          |
| AIMessage         | ai_messages            | conversation_id, role (AIRole enum), message, metadata (json)     |

### Enums

| Enum              | Values                                                    |
|--------------------|-----------------------------------------------------------|
| LessonType        | vocabulary, grammar, listening, speaking                   |
| ExerciseType      | multiple_choice, fill_blank, translation, speaking, ai_chat |
| SkillType         | vocabulary, grammar, listening, speaking                   |
| ProgressStatus    | locked, in_progress, completed                             |
| AIRole            | user, assistant                                            |

## API Endpoints

Base URL: `/api/v1`

### Public Endpoints

| Method | Path                          | Description            |
|--------|-------------------------------|------------------------|
| POST   | /auth/register                | Register new user      |
| POST   | /auth/login                   | Login                  |
| GET    | /languages                    | List all languages     |
| GET    | /languages/{id}               | Get language details   |

### Authenticated Endpoints (Bearer token via Sanctum)

| Method | Path                                    | Description                    |
|--------|-----------------------------------------|--------------------------------|
| POST   | /auth/logout                            | Logout                         |
| GET    | /auth/user                              | Get current user               |
| GET    | /dashboard                              | Dashboard overview             |
| GET    | /modules                                | List modules (filters: language_id, level_id) |
| GET    | /modules/{id}                           | Get module with lessons        |
| GET    | /lessons/{id}                           | Get lesson with exercises      |
| GET    | /progress                               | Progress overview              |
| POST   | /progress/start                         | Start a lesson                 |
| POST   | /progress/answer                        | Submit exercise answer         |
| POST   | /progress/lessons/{lessonId}/complete   | Complete a lesson              |
| POST   | /ai/conversations                       | Start AI conversation          |
| GET    | /ai/conversations                       | List conversations             |
| GET    | /ai/conversations/{id}                  | Get conversation + messages    |
| POST   | /ai/conversations/{id}/messages         | Send message to AI             |
| POST   | /ai/exercises/generate                  | Generate AI exercise           |
| GET    | /ai/recommendations/lesson              | Get lesson recommendation      |
| GET    | /vocabulary/review                      | Words due for review           |
| POST   | /vocabulary/review/{userWordId}         | Review a word                  |
| POST   | /vocabulary/add/{wordId}                | Add word to review             |
| GET    | /vocabulary/mastered                    | Get mastered words             |
| GET    | /vocabulary/progress                    | Vocabulary progress            |
| PUT    | /user/profile                           | Update profile                 |
| GET    | /user/skills                            | Get user skills                |
| GET    | /user/stats                             | Get user stats                 |

## Services

### AIService (`app/Services/AI/AIService.php`)
- Connects to OpenRouter API (OpenAI-compatible)
- `sendMessage()` - Sends chat message to AI, stores messages in DB
- `generateExerciseFeedback()` - Gets AI feedback on exercise answers
- `generateAdaptiveExercise()` - Creates personalized AI exercises
- `evaluateTranslation()` - Evaluates translation answers (exact match)
- Uses `OPENAI_API_KEY` and `OPENAI_MODEL` from config

### ProgressionService (`app/Services/Progression/ProgressionService.php`)
- `startLesson()` - Creates progress record (in_progress)
- `submitAnswer()` - Evaluates and stores user answers
- `completeLesson()` - Marks complete, awards XP, updates streak, unlocks next
- `calculateLessonScore()` - Percentage based on correct answers

### VocabularyService (`app/Services/Vocabulary/VocabularyService.php`)
- Spaced repetition system with 6-tier familiarity (0-5)
- Intervals: 1h → 1d → 3d → 7d → 14d → 30d (mastered)
- `reviewWord()` - Updates familiarity based on correctness
- `getWordsForReview()` - Gets words due for review

### AdaptiveService (`app/Services/Adaptive/AdaptiveService.php`)
- `recommendNextLesson()` - Suggests next lesson based on weakest skill
- `updateSkillLevels()` - Recalculates all 4 skill levels from answer history
- `getWeakestSkill()` / `getStrongestSkill()` - Skill analysis
- `adjustDifficulty()` - Returns multiplier (0.8-1.2) based on recent accuracy

## Authentication (Sanctum)

1. **Register**: Creates user, initializes skills (all at 0) and stats (xp=0), returns bearer token
2. **Login**: Validates credentials, returns new Sanctum token
3. **Logout**: Deletes current access token
4. **Protected routes**: Guarded by `auth:sanctum` middleware
5. **SPA support**: Configured for `localhost:4200` (Angular dev server)

## Local Development

```bash
# Start Docker services
./vendor/bin/sail up -d

# Run migrations
./vendor/bin/sail artisan migrate

# Seed database
./vendor/bin/sail artisan db:seed

# Run tests
./vendor/bin/sail artisan test
```

## Key Conventions

- **Naming**: Controllers are singular, Models are singular PascalCase
- **Validation**: All request validation in dedicated FormRequest classes under `app/Http/Requests/`
- **Responses**: Use API Resources for consistent JSON shapes
- **Business logic**: Always in Services, never in controllers or models
- **Enums**: PHP 8.1+ backed enums with string values
- **CORS**: Configured via Sanctum for SPA on port 4200
- **Queue**: Database driver; run `sail artisan queue:work` for async jobs

## Future Improvements

- Add API versioning headers
- Implement rate limiting
- Add comprehensive test coverage (only TestCase.php exists)
- Add OpenAPI/Swagger documentation
- Consider event-driven architecture for progression events
- Add caching layer for frequently accessed data
