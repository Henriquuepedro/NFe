<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \Firebase\JWT\JWT;

class TokenJWT extends Controller
{
    const KEY = 'h7u6m2w3'; // chave
 
    /**
     * Geracao de um novo token jwt
     */
    public static function encode(array $options)
    { 
        return JWT::encode($options['userdata'], self::KEY);
    }
 
    /**
     * Decodifica token jwt
     */
    public static function decode($jwt)
    {
        return JWT::decode($jwt, self::KEY, ['HS256']);
    }
}
