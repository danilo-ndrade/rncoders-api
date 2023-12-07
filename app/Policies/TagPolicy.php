<?php

namespace App\Policies;

use App\Models\Tag;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TagPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): Response
    {
        return $user->hasPermissionTo('ver-todas-as-tags')
            ? Response::allow()
            : Response::deny('Você não tem permissão para visualizar todos os tags');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Tag $model): Response
    {
        return ($user->hasPermissionTo('ver-todas-as-tags') || ($user->hasPermissionTo('ver-tags') && ($user->id === $model->user_id)))
            ? Response::allow()
            : Response::deny('Você não tem permissão para visualizar este tag');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): Response
    {
        return $user->hasPermissionTo('criar-tags')
            ? Response::allow()
            : Response::deny('Você não tem permissão para criar tags');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, User $model): Response
    {
        return ($user->hasPermissionTo('editar-todas-as-tags') || ($user->hasPermissionTo('editar-tags') && ($user->id === $model->user_id)))
            ? Response::allow()
            : Response::deny('Você não tem permissão para atualizar este tag');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Tag $model): Response
    {
        return ($user->hasPermissionTo('excluir-todas-as-tags') || ($user->hasPermissionTo('excluir-tags') && ($user->id === $model->user_id)))
            ? Response::allow()
            : Response::deny('Você não tem permissão para deletar este tag');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Tag $model): Response
    {
        return ($user->hasPermissionTo('restore-all-users') || ($user->hasPermissionTo('restore-user') && ($user->id === $model->user_id)))
            ? Response::allow()
            : Response::deny('Você não tem permissão para restaurar este tag');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, User $model): Response
    {
        return ($user->hasPermissionTo('force-delete-all-users') || ($user->hasPermissionTo('force-delete-user') && ($user->id === $model->user_id)))
            ? Response::allow()
            : Response::deny('Você não tem permissão para deletar permanentemente este tag');
    }
}
