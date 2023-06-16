This is a Task API, that allows users to register, login and then Create a new task, Update an existing task, Retrieve a specific task by ID, Retrieve all tasks created by the logged in user. Making use of ACL to assign roles and give Permissons.

* Create a new Env file with Mail Properties and Database
* composer install 
* composer require spatie/laravel-permission
* php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
* php artisan db:seed --class=Permissonseeder
* php artisan migrate

