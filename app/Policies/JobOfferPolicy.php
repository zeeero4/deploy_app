<?php

namespace App\Policies;

use App\Models\JobOffer;
use App\Models\User;

class JobOfferPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user)
    {
        return isset($user->company);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, JobOffer $job_offer)
    {
        return $user->id === $job_offer->company->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, JobOffer $job_offer)
    {
        return $user->id === $job_offer->company->user_id;
    }
}
