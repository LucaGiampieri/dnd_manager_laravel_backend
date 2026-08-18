<?php

namespace App\Models;

use App\Models\Concerns\HasSourceReferences;
use Illuminate\Database\Eloquent\Model;

class Subclass extends Model
{
    use HasSourceReferences;
}
