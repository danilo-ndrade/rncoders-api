<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): Response
    {
        return $user->hasPermissionTo('view-all-users')
            ? Response::allow()
            : Response::deny('Você não tem permissão para visualizar todos os usuários');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, User $model): Response
    {
        return ($user->hasPermissionTo('view-all-users') || ($user->hasPermissionTo('view-user') && ($user->id === $model->user_id)))
            ? Response::allow()
            : Response::deny('Você não tem permissão para visualizar este usuário');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): Response
    {
        return $user->hasPermissionTo('create-user')
            ? Response::allow()
            : Response::deny('Você não tem permissão para criar usuários');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, User $model): Response
    {
        return ($user->hasPermissionTo('update-all-users') || ($user->hasPermissionTo('update-user') && ($user->id === $model->user_id)))
            ? Response::allow()
            : Response::deny('Você não tem permissão para atualizar este usuário');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, User $model): Response
    {
        return ($user->hasPermissionTo('delete-all-users') || ($user->hasPermissionTo('delete-user') && ($user->id === $model->user_id)))
            ? Response::allow()
            : Response::deny('Você não tem permissão para deletar este usuário');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, User $model): Response
    {
        return ($user->hasPermissionTo('restore-all-users') || ($user->hasPermissionTo('restore-user') && ($user->id === $model->user_id)))
            ? Response::allow()
            : Response::deny('Você não tem permissão para restaurar este usuário');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, User $model): Response
    {
        return ($user->hasPermissionTo('force-delete-all-users') || ($user->hasPermissionTo('force-delete-user') && ($user->id === $model->user_id)))
            ? Response::allow()
            : Response::deny('Você não tem permissão para deletar permanentemente este usuário');
    }
}
