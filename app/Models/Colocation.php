<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Colocation extends Model
{

    protected $fillable = ['name', 'status', 'owner_id'];

 
   public function owner()
   {
     return $this-> belongsTo(User::class , 'owner_id');

   }

   public function member()
   {
     return $this->belongsToMany(User::class)->withPivot('joined_at' , 'role' , 'left_at');
        
   }
}
