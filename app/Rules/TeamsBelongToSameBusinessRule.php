<?php

namespace App\Rules;

use App\Models\Team;
use Illuminate\Contracts\Validation\Rule;

class TeamsBelongToSameBusinessRule implements Rule
{
    protected $teamUuid;

    public function __construct($teamUuid)
    {
        $this->teamUuid = $teamUuid;
    }

    public function passes($attribute, $value)
    {
        $parentTeam = Team::where('uuid', $this->teamUuid)->first();
        $childTeam = Team::where('uuid', $value)->first();
        if ($parentTeam->business->first()->uuid == $childTeam->business->first()->uuid) {

            return true;
        }

        return false;
    }

    public function message()
    {
        return 'The team and child teams must belong to the same business.';
    }
}
