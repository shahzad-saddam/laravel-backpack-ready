# Installation

```
$ git clone git@github.com:shahzad-saddam/laravel-backpack-ready.git your-project-directory-name

$ cd your-project-directory-name # replace with project directory name

$ cp .env.example .env # and then set your database configurations
$ composer install # to install all packages
$ php artisan key:generate

$ mkdir public/uploads #create a public/uploads directory

$ php artisan migrate

$ php artisan jwt:secret

$ php artisan migrate --path=vendor/backpack/langfilemanager/src/database/migrations
$ php artisan db:seed --class="Backpack\LangFileManager\database\seeds\LanguageTableSeeder"
```



# Usage

Steps to generate a crud 
### STEP 1. create migration and run it
```
$ php artisan make:migration:schema create_tags_table --model=0 --schema="name:string:unique"
$ php artisan migrate
```

### STEP 2. create a model, a request and a controller for the admin panel
```
$ php artisan backpack:crud tag #use singular, not plural
```

### STEP 3. manually add this to your admin.php routes file:
```
CRUD::resource('tag', 'TagCrudController'); 
```

