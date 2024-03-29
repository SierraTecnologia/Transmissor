<?php

namespace Transmissor\Factory;

use Transmissor\Models\Debt;
use Transmissor\Models\Group;
use Transmissor\Models\User;

class TransmissorFactory
{

    private $payer;
    private $amount;
    private $receiver;
    private $group;

    public function __construct(User $payer, $amount)
    {
        $this->payer = $payer;
        $this->amount = $amount;
    }

    /**
     * @return static
     */
    public function to(User $receiver): self
    {
        $this->receiver = $receiver;

        return $this;
    }

    /**
     * @return static
     */
    public function in(Group $group): self
    {
        $this->group = $group;

        return $this;
    }

    /**
     * @return void
     */
    public function save()
    {

        $this->payer->debts_to_pay()
            ->where('to_id', $this->receiver->id)
            ->where('group_id', $this->group->id)
            ->get()
            ->each(
                function (Debt $debt) {

                    $debtAmount = $debt->amount;

                    if ($this->amount < $debtAmount) {
                        $debt->amount -= $this->amount;
                        $debt->save();

                    } else {
                        $debt->delete();
                    }

                    $this->amount -= $debtAmount;

                    if ($this->amount <= 0) {
                        return false;
                    }
                }
            );

        if ($this->amount <= 0) {
            return;
        }

        $previousDebt = $this->payer->debts_to_receive()
            ->where('from_id', $this->receiver->id)
            ->where('group_id', $this->group->id)
            ->first();

        if (! $previousDebt) {
            $previousDebt = new Debt();
            $previousDebt->from_id = $this->receiver->id;
            $previousDebt->to_id = $this->payer->id;
            $previousDebt->amount = 0;
            $previousDebt->group_id = $this->group->id;
            $previousDebt->currency = $this->group->currency;
        }

        $previousDebt->amount += $this->amount;
        $previousDebt->save();
    }
}