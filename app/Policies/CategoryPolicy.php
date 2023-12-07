<?php

namespace App\Policies;

use App\Models\Category;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CategoryPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): Response
    {
        return $user->hasPermissionTo('ver-todas-as-categorias')
            ? Response::allow()
            : Response::deny('Você não tem permissão para visualizar todos os categoriass');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Category $model): Response
    {
        return ($user->hasPermissionTo('ver-todas-as-categorias') || ($user->hasPermissionTo('ver-categorias') && ($user->id === $model->user_id)))
            ? Response::allow()
            : Response::deny('Você não tem permissão para visualizar este categorias');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): Response
    {
        return $user->hasPermissionTo('criar-categorias')
            ? Response::allow()
            : Response::deny('Você não tem permissão para criar categoriass');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Category $model): Response
    {
        return ($user->hasPermissionTo('editar-todas-as-categorias') || ($user->hasPermissionTo('editar-categorias') && ($user->id === $model->user_id)))
            ? Response::allow()
            : Response::deny('Você não tem permissão para atualizar este categorias');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Category $model): Response
    {
        return ($user->hasPermissionTo('excluir-todas-as-categorias') || ($user->hasPermissionTo('excluir-categorias') && ($user->id === $model->user_id)))
            ? Response::allow()
            : Response::deny('Você não tem permissão para deletar este categorias');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Category $model): Response
    {
        return ($user->hasPermissionTo('restore-all-users') || ($user->hasPermissionTo('restore-user') && ($user->id === $model->user_id)))
            ? Response::allow()
            : Response::deny('Você não tem permissão para restaurar este categorias');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, User $model): Response
    {
        return ($user->hasPermissionTo('force-delete-all-users') || ($user->hasPermissionTo('force-delete-user') && ($user->id === $model->user_id)))
            ? Response::allow()
            : Response::deny('Você não tem permissão para deletar permanentemente este categorias');
    }
}
