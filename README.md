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

```php
@if (session('status') == 'verification-link-sent')
    <div class="mb-4 font-medium text-sm text-green-600">
        A new email verification link has been emailed to you!
    </div>
@endif
```

pour eviter d'injecter des requetes malicieuses dans un method=post par exemple
dans le route, on peut taper
```php

    $token = $request->session()->token();
 
    $token = csrf_token();
 
    // ...

```
ou bien la view
@csrf

pour exclure certains chemin de la protection quand on request une URI (api)

```php

<?php
 
namespace App\Http\Middleware;
 
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;
 
class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        'stripe/*',
        'http://example.com/foo/bar',
        'http://example.com/foo/*',
    ];
}

```

## middleware interdiction
nous pouvons dans la route imbriquer un middleware pour dire qui a accès a celle ci, nous pouvons aussi charger un controller qui indique des règles non défini
```php

})->middleware(EnsureTokenIsValid::class);

```

quand nous avons besoin qu'une page soit vu par une categorie ou certaines functions ne soit pas toute accessible
```php

  })->withoutMiddleware([EnsureTokenIsValid::class]);

ou


Route::withoutMiddleware([EnsureTokenIsValid::class])->group(function () {
    Route::get('/profile', function () {
        //
    });
```





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



 if (CRYPT_BLOWFISH == 1) {
    $cryptoon = crypt($hashed, 'wx');
}
dd($cryptoon);

pour generer un password, nous pouvons creer une route

```php
Route::put('/generate/password', [FortifyController::class, 'generate']);
```

Nous permet donc de suivre la route generate est de nous retrouver dans le controller fortify à la function generate ou nous pouvons lui indiquer la function Hash::class

## fetch données avec $request
```php
$request->fullUrlWithQuery(['type' => 'phone']);
```
la function fullurlwithquery permet de recuperer les parametres dans une url ex http://me.com/send?dollard=usd?poisson=truite...
on peut donc indiquer type = poisson

pour prendre toute la request
```php
$request->collect();
```

pour fetch en particulier la 1er ligne, et le nom par exemple
```php
$name = $request->input('products.0.name');
```
ou pour toute les lignes avec un nom
```php
$names = $request->input('products.*.name');
```
pour les fichier json c'est la même chose, pour les strings
```php
$name = $request->string('name')->trim();
```

pour determiner si une valeur est presente et retourne un boolean true or false
```php
if ($request->has('name')) {
    //
}
```
du coup on peut déterminer 2 actions a faire si la valeur est presente ou non
```php
$request->whenHas('name', function ($input) {
    // The "name" value is present...
}, function () {
    // The "name" value is not present...
});
```

nous pouvons additionner des valeurs si celle ci ne sont pas disponible a la requete d'origine
```php
$request->merge(['votes' => 0]);
```
<!-- laz ou a compiler webpack le css! -->
