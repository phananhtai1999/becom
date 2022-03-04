<?php

namespace App\Abstracts;

use Illuminate\Database\Eloquent\Concerns\HasAttributes;
use Illuminate\Database\Eloquent\Model;

abstract class AbstractModel extends Model
{
    use HasAttributes;
}
