<?php

namespace Transmissor\Traits;

trait ModelHasTransmissor
{
    /**
     * Join a transmissor
     *
     * @param  integer $transmissorId
     * @param  integer $userId
     * @return void
     */
    public function joinTransmissor($transmissorId, $userId)
    {
        $transmissor = $this->transmissor->find($transmissorId);
        $user = $this->model->find($userId);

        $user->transmissor()->attach($transmissor);
    }

    /**
     * Leave a transmissor
     *
     * @param  integer $transmissorId
     * @param  integer $userId
     * @return void
     */
    public function leaveTransmissor($transmissorId, $userId)
    {
        $transmissor = $this->transmissor->find($transmissorId);
        $user = $this->model->find($userId);

        $user->transmissor()->detach($transmissor);
    }

    /**
     * Leave all transmissor
     *
     * @param  integer $transmissorId
     * @param  integer $userId
     * @return void
     */
    public function leaveAllTransmissor($userId)
    {
        $user = $this->model->find($userId);
        $user->transmissor()->detach();
    }
}
