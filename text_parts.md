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
Публичные:
?page=home
?page=login
?page=register

Auth:
?page=logout

Пользователь:
?page=list
?page=create
?page=store
?page=edit&id=1
?page=update&id=1
?page=delete

Администратор:
?page=admin_users
?page=admin_create_user
?page=admin_store_user
?page=admin_entries
?page=admin_delete_user
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