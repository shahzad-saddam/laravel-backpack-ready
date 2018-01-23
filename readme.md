# Installation

```
  git clone git@github.com:shahzad-saddam/laravel--backpack-ready.git
  cp .env.example .env # and then set your database configurations
  composer install # to install all packages
  php artisan key:generate
  mkdir public/uploads #create a public/uploads directory
  php artisan migrate
  php artisan migrate --path=vendor/backpack/langfilemanager/src/database/migrations
  php artisan db:seed --class="Backpack\LangFileManager\database\seeds\LanguageTableSeeder"
```

