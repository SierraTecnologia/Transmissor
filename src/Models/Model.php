<?php

namespace Transmissor\Models;

use Illuminate\Database\Eloquent\Model as EloquentModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;
use App\Sitec\Business;
use Auth;

class Model extends EloquentModel
{
    /**
     * Provides public access to get the raw attribute value from the model.
     * Used in areas where no mutations are required but performance is critical.
     *
     * @param  $key
     * @return mixed
     */
    public function getRawAttribute($key)
    {
        return parent::getAttributeFromArray($key);
    }

    public static function boot()
    {
        parent::boot();

        if (isset(self::$organizationPerspective) && self::$organizationPerspective) {

            if(!$user = Auth::user()) {
                $user = Business::getViaParamsToken();
            }

            // Reduz o nivel global a nivel de Business
            if (!$user->isAdmin() || !Auth::check()) {
                static::addGlobalScope(
                    'user', function (Builder $builder) use ($user) {
                        $builder->where(self::getTableName().'.user_id', '=', $user->id);
                    }
                );
            }

            // Reduz o nivel de business a nivel de cliente da compania 
            // @todo

            // Reduz a nivel de Pessoa ()
            // @todo
        }

    }
}