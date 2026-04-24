<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
/**
 * AuthorizesRequests menyediakan method-method otorisasi:
 *  - $this->authorize('view', $model) => cek Policy, lempar 403 jika gagal
 *  - $this->authorizeForUser($user, ...) => cek untuk user tertentu
 *  - $this->can('view', $model) => return bool, tidak lempar exception
 */
abstract class Controller
{
    use AuthorizesRequests;
}
