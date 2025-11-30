<?php

namespace App\Policies;

use App\Models\FeedbackForm;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class FeedbackFormPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['student', 'faculty', 'staff']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, FeedbackForm $feedbackForm): bool
    {
        return $user->id === $feedbackForm->user_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->role === 'student';
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, FeedbackForm $feedbackForm): bool
    {
        return $user->id === $feedbackForm->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, FeedbackForm $feedbackForm): bool
    {
        return $user->id === $feedbackForm->user_id && $user->role === 'student';
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, FeedbackForm $feedbackForm): bool
    {
        return $user->id === $feedbackForm->user_id;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, FeedbackForm $feedbackForm): bool
    {
        return $user->id === $feedbackForm->user_id;
    }
} 