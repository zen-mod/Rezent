<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Build extends Model
{
    protected $fillable = ['commit_hash', 'branch', 'successful'];

    public function displayHash()
    {
        return substr($this->commit_hash, 0, 7);
    }
}
