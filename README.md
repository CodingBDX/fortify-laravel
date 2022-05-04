<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400"></a></p>

<p align="center">
<a href="https://travis-ci.org/laravel/framework"><img src="https://travis-ci.org/laravel/framework.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## Auth fortify

composer require laravel/fortify

php artisan vendor:publish --provider="Laravel\Fortify\FortifyServiceProvider"

php artisan migrate  (ne pas oublier de configurer le fichier .env pour relier a la table)
```
** Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique(User::class),
            ], **
```
permet de choisir les options de validitée, par exemple on impose que le name soit un string est qu'il est une valeur max de 255 caractere
on remarque comment il fonctionne dans le dossier app/actions

permet l'authentification a deux facteurs!

`config/fortify on peut voir au niveau feature toute les options que ce sert fortify notamment confirmation d'email et double authentification

** Pour continuer la configuration, il faut indiquer le service fortify dans notre dossier app configuration **

`App\Providers\FortifyServiceProvider::class,`


** il faut indiquer dans le service fortify dans la function boot, la vu ou l'on veut utiliser fortify

on lui indique la vu sur laquelle agire 

Fortify::loginView(function () {
        return view('auth.login');
    });


et pour le register, on imbrique la vue register
```php
    Fortify::registerView(function () {
        return view('auth.register');
    });
```
pour modifier les field du register
App\Actions\Fortify\CreateNewUser
    puis si on veut le systeme de reset de password

        Fortify::requestPasswordResetLinkView(function () {
        return view('auth.forgot-password');
    });

## les views
il faut maintenant creer les vues que nous avons indiquées
login.blade.php, et register.blade.php

nous pouvons biensur recuperer les functions de auth notamment le user name, id, etc..

{{auth::user()->name}}

toujours utiliser dans le controller ou route, le middleware pour signaler qui a acces auth ou guest par exemple
## configuration framework css
on peut utiliser n'importe qu'elle framework css avec fortify!
installation de tailwindcss `npm install tailwindcss`

ensuite dans le fichier ressources css/app.js

@tailwind base;
@tailwind components;
@tailwind utilities;

la commande npx tailwindcss init permet de creer le fichier tailwind.config.js

et pour finir dans le fichier webpack, il faut indiquer que nous utilisons le plugin tailwindcss pour compiler `npm install && npm run watch`

pour relier le css creer un link:css et dans source {{ asset('css/app.css') }} 


la redirection dans un logout on redirige vers la page principale

```php
    $this->app->instance(LogoutResponse::class, new class implements LogoutResponse {
        public function toResponse($request)
        {
            return redirect('/');
        }

```


```php

    Fortify::confirmPasswordView(function () {
        return view('auth.confirm-password');
    });

```

Permet de confirmer le password

```php
$request->user()->twoFactorQrCodeSvg();
```
2 facteurs d'authentifications et le code d'appel dans la function boot de fortify

```php

    Fortify::twoFactorChallengeView(function () {
        return view('auth.two-factor-challenge');
    });

    ```

```php

    Fortify::verifyEmailView(function () {
        return view('auth.verify-email');
    });

    ```

permet de verifier votre mail, dans la view on indique le link

``php
@if (session('status') == 'verification-link-sent')
    <div class="mb-4 font-medium text-sm text-green-600">
        A new email verification link has been emailed to you!
    </div>
@endif
``




## hashing
la method hash permet de creer et encrypter un nouveau password
`hash::make

$request->user()->fill([
            'password' => Hash::make($request->newPassword)
        ])->save();
        
on request un nouveau mot de passe
il est possible de définir le niveau de l'algorythme avec les options suivantes

$hashed = Hash::make('password', [
    'memory' => 1024,
    'time' => 2,
    'threads' => 2,
]);

nous pouvons utiliser la function php crypt pour plus de securite

```      
 if (CRYPT_BLOWFISH == 1) {
    $cryptoon = crypt($hashed, 'wx');
}
dd($cryptoon);
```
<!-- laz ou a compiler webpack le css! -->
