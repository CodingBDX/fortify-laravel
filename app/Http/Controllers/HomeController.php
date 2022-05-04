<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class HomeController extends Controller
{
    public function index() {

        $hashed = Hash::make('passwordsdfgghhdfgh', [
    'memory' => 2224,
    'time' => 6,
    'threads' => 6,
]);
if (CRYPT_BLOWFISH == 1) {
    $cryptoon = crypt($hashed, 'wx');
}
dd($cryptoon);
        return view('home');
    }
}
