<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Usuario extends Authenticatable
{
    use Notifiable;

    protected $table = 'usuario';

    protected $fillable = ['email',  'password'];

    protected $hidden = ['password',  'remember_token', 'fk_cliente_natural', 'fk_cliente_juridico', 'fk_empleado', 'fk_rol'];
}
