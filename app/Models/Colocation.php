<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Colocation extends Model
{
    use HasFactory;

    protected $fillable = ['nom', 'status', 'owner_id'];

 
   public function owner()
   {
     return $this->belongsTo(User::class , 'owner_id');
   }

   public function member()
   {
     return $this->belongsToMany(User::class)->withPivot('joined_at' , 'role' , 'left_at');
   }

   public function categories()
   {
     return $this->hasMany(Category::class);
   }

   public function expenses()
   {
     return $this->hasMany(Expense::class);
   }

   public function invitations()
   {
     return $this->hasMany(Invitation::class);
   }

   public function payments()
   {
     return $this->hasMany(Payment::class);
   }

   public function getActiveMembers()
   {
     return $this->member()
           ->wherePivot('left_at', null)
           ->get();
   }

   public function isMemberActive($userId)
   {
     return $this->member()
           ->where('user_id', $userId)
           ->wherePivot('left_at', null)
           ->exists();
   }

   /**
    * Calculate balance for a given member (positive means credit, negative means owes)
    */
   public function balanceForUser($userId)
   {
       $activeMembers = $this->getActiveMembers();
       $expenses = $this->expenses;
       $totalAmount = $expenses->sum('amount');
       $memberCount = $activeMembers->count();
       $share = $memberCount > 0 ? $totalAmount / $memberCount : 0;

       $paid = $expenses->where('payer_id', $userId)->sum('amount');
       return $paid - $share;
   }

}
