# Lifesaving Rankings Parser
[![build](https://github.com/rubenvanerk/lifesavingrankings-parser/workflows/build/badge.svg)](https://github.com/rubenvanerk/lifesavingrankings-parser/actions?query=workflow%3Abuild)

This is a continuation of the [competition-parser](https://github.com/rubenvanerk/competition-parser) project with a user-friendly frontend.

## Prerequisites

To use this project, you need a LifesavingRankings database to export the results to. 
Use the [setup instructions](https://github.com/rubenvanerk/lifesavingrankings#setup) from the LifesavingRankings project. 

## Installation

```bash
cp .env.example .env
```

Edit `.env` to match your environment. 

```bash
composer install  
php artisan migrate
php artisan db:seed
npm install
npm run dev
php artisan serve
```

To compile assets use `npm run dev` or use `npm run watch` to automatically rebuild assets whenever you change a file.

`npm run watch` also spawns a browsersync that automatically refreshes after a change.

Currently, there's no way to register a new user. For now, the best way is using tinker: `php artisan tinker`  
And then edit and paste this code:
```php
App\User::create([
    'email' => 'user@example.com',
    'name' => 'Name',
    'password' => Hash::make('your-password'),
]);
```
