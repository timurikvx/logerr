<?php

namespace App\Services\Teams;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class TeamEntity extends Model
{


    public function id(): int
    {
        return $this->id;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function guid(): string
    {
        return $this->guid;
    }

    public function creator(): User
    {
        return User::find($this->creator);
    }

    public function created(): \DateTime
    {
        return new \DateTime($this->created_at);
    }

    public function updated(): \DateTime
    {
        return new \DateTime($this->updated_at);
    }

}
