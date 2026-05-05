```
project/
├── config.php
├── composer.json
├── migrate.php
├── migrations/
│   ├── 001_create_mood_diary_tables.php
│   └── 002_add_users_and_auth.php
├── public/
│   ├── index.php
│   └── styles.css
├── src/
│   ├── helpers.php
│   ├── Core/
│   │   ├── Database.php
│   │   ├── Csrf.php
│   │   └── Middleware.php
│   ├── Repository/
│   │   ├── MoodEntryRepository.php
│   │   └── UserRepository.php
│   ├── Service/
│   │   ├── AuthService.php
│   │   └── MoodEntryFormHandler.php
│   └── Validator/
│       ├── AuthValidator.php
│       └── MoodEntryValidator.php
└── templates_twig/
    ├── layout.twig
    ├── home.twig
    ├── error.twig
    ├── auth/
    │   ├── login.twig
    │   └── register.twig
    ├── entries/
    │   ├── list.twig
    │   └── form.twig
    └── admin/
        └── users.twig
```




```
users
- id
- username
- email
- password_hash
- role
- created_at
- updated_at

moods
- id
- code
- title
- icon

entries
- id
- user_id
- mood_id
- title
- mood_date
- energy_level
- note
- author
- tags
- created_at
- updated_at
```