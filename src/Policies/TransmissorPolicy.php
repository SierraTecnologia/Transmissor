<?php

namespace Transmissor\Policies;

use App\Models\User;

/**
 * Class TransmissorPolicy.
 *
 * @package Finder\Http\Policies
 */
class TransmissorPolicy
{
    /**
     * Create a transmissor.
     *
     * @param  User   $authUser
     * @param  string $transmissorClass
     * @return bool
     */
    public function create(User $authUser, string $transmissorClass)
    {
        if ($authUser->toEntity()->isAdministrator()) {
            return true;
        }

        return false;
    }

    /**
     * Get a transmissor.
     *
     * @param  User  $authUser
     * @param  mixed $transmissor
     * @return bool
     */
    public function get(User $authUser, $transmissor)
    {
        return $this->hasAccessToTransmissor($authUser, $transmissor);
    }

    /**
     * Determine if an authenticated user has access to a transmissor.
     *
     * @param  User $authUser
     * @param  $transmissor
     * @return bool
     */
    private function hasAccessToTransmissor(User $authUser, $transmissor): bool
    {
        if ($authUser->toEntity()->isAdministrator()) {
            return true;
        }

        if ($transmissor instanceof User && $authUser->id === optional($transmissor)->id) {
            return true;
        }

        if ($authUser->id === optional($transmissor)->created_by_user_id) {
            return true;
        }

        return false;
    }

    /**
     * Update a transmissor.
     *
     * @param  User  $authUser
     * @param  mixed $transmissor
     * @return bool
     */
    public function update(User $authUser, $transmissor)
    {
        return $this->hasAccessToTransmissor($authUser, $transmissor);
    }

    /**
     * Delete a transmissor.
     *
     * @param  User  $authUser
     * @param  mixed $transmissor
     * @return bool
     */
    public function delete(User $authUser, $transmissor)
    {
        return $this->hasAccessToTransmissor($authUser, $transmissor);
    }
}
