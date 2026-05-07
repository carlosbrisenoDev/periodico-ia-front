<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class filesfrompath extends Model
{
    protected $guarded = [];
    protected $table = "filesfrompath";

    public function documento(){
        return $this->hasOne("\App\documento", "id", "document_id");
    }
}
