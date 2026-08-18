<?php

namespace App\Models;

use App\Models\Concerns\HasSourceReferences;
use Illuminate\Database\Eloquent\Model;

class Spell extends Model
{
    use HasSourceReferences;
}
