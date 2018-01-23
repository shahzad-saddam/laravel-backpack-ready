<code>
  git clone git@github.com:shahzad-saddam/laravel--backpack-ready.git <br />
  cp .env.example .env # and then set your database configurations<br />
  composer install # to install all packages<br />
  php artisan key:generate<br />
  mkdir public/uploads #create a public/uploads directory<br />
  php artisan migrate<br />
  php artisan migrate --path=vendor/backpack/langfilemanager/src/database/migrations<br />
  php artisan db:seed --class="Backpack\LangFileManager\database\seeds\LanguageTableSeeder"<br />
</code>

