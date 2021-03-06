# Laravel PHP Framework

![vk_music](https://user-images.githubusercontent.com/10347617/40197720-f17eaee4-5a1c-11e8-9245-b3ae96d7bb17.png)

[![Total Downloads](https://poser.pugx.org/laravel/framework/d/total.svg)](https://packagist.org/packages/laravel/framework)
[![Latest Stable Version](https://poser.pugx.org/laravel/framework/v/stable.svg)](https://packagist.org/packages/laravel/framework)
[![Latest Unstable Version](https://poser.pugx.org/laravel/framework/v/unstable.svg)](https://packagist.org/packages/laravel/framework)
[![License](https://poser.pugx.org/laravel/framework/license.svg)](https://github.com/laravel/framework/blob/master/LICENSE.md)

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable, creative experience to be truly fulfilling. Laravel attempts to take the pain out of development by easing common tasks used in the majority of web projects, such as authentication, routing, sessions, queueing, and caching.

Laravel is accessible, yet powerful, providing tools needed for large, robust applications. A superb inversion of control container, expressive migration system, and tightly integrated unit testing support give you the tools you need to build any application with which you are tasked.

## Install

Run commands:

    composer install
    npm install
    php artisan migrate
    
Next:
 - go to https://vk.com/dev
 - open "My apps" >> "Create an Application"
 - Input your app name
 - Platform: "Website"
 - Input your web-address and base domain
 - Click "Connect site"
 - Next, get code on your phone to activate a new app
 - Open settings page.

Next, copy "Application ID" to "config/vk.php" >> "client_id"
and "Secure key" to "config/vk.php" >> "client_secret"

Replace "config/vk.php": "redirect_uri" to your link from "Authorized redirect URI" field in "VK Dev".

![2017-01-23 23-41-23 edit application - musaver - google chrome](https://cloud.githubusercontent.com/assets/10347617/22208238/8783a4f2-e1c5-11e6-8402-25afaaff2476.jpg)

Profit!
