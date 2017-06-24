<?php
namespace MyApp\Model;

use Illuminate\Database\Eloquent;

class Task extends Eloquent\Model
{
    protected $fillable = ['title'];
}
