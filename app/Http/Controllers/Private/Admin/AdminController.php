<?php

namespace App\Http\Controllers\Private\Admin;

use App\Http\Controllers\Controller;
use Inertia\Inertia;

/**
 * Base Controller for Admin Resources.
 *
 * This controller serves as a base for resources that manage a specific model.
 * It provides helper methods to:
 * - Render standardized views (Show, Edit).
 * - Handle common redirect patterns.
 */
abstract class AdminController extends Controller
{
    /**
     * Helper to render the SHOW view.
     */
    protected function renderShow($model, string $viewPath, string $variableName)
    {
        // $this->authorize('view', $model); // Uncomment if Policy exists

        // Attempt to load user relation if it exists, for context
        if (method_exists($model, 'user')) {
            $model->load('user.roles');
        } elseif (method_exists($model, 'owner')) {
             $model->load('owner.roles');
        }

        return Inertia::render($viewPath . '/Show', [
            $variableName => $model, // Pass full model or use DataResource
        ]);
    }

    /**
     * Helper to render the EDIT view.
     */
    protected function renderEdit($model, string $viewPath, string $variableName, array $extra = [])
    {
        // $this->authorize('update', $model);

        return Inertia::render($viewPath . '/Edit', array_merge([
            $variableName => $model,
        ], $extra));
    }

    /**
     * Helper to update user and redirect.
     */
    protected function redirectAfterUpdate(string $routePrefix, string $successMessage = 'Registro actualizado correctamente.')
    {
        return redirect()->route($routePrefix . '.index')
            ->with('success', $successMessage);
    }

    /**
     * Helper to store user and redirect.
     */
    protected function redirectAfterStore(string $routePrefix, string $successMessage = 'Registro creado correctamente.')
    {
        return redirect()->route($routePrefix . '.index')
            ->with('success', $successMessage);
    }
}