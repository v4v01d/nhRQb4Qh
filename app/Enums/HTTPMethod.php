<?php
namespace App\Enums;

enum HTTPMethod: string
{
    case GET = 'GET';
    case PUT = 'PUT';
    case POST = 'POST';
}