<?php

namespace App\Rules;

use App\Services\DomainService;
use App\Services\UserService;
use Illuminate\Contracts\Validation\Rule;

class InviteRule implements Rule
{
    private $domain;

    /**
     * @param $domain
     */
    public function __construct($domain)
    {
        $this->domain = $domain;
    }

    /**
     * @param $attribute
     * @param $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $domain = optional((new DomainService())->findOneWhere([['name', $this->domain]]))->name;
        $email = $value . '@' . $domain;
        $user = (new UserService())->findOneWhere([['email', $email]]);

        if ($user)
        {
            return false;
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The :attribute has already been taken.';
    }
}
