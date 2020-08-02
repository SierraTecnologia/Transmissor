<?php

// @todo

namespace Transmissor\Models;

use App\Contants\Tables;
use Illuminate\Database\Eloquent\Collection;
use App\Models\Model;

use Population\Manipule\Entities\NotificationEntity;
use Population\Manipule\Builders\NotificationBuilder;

/**
 * Class Notification.
 *
 * @property int id
 * @property string value
 * @property Collection posts
 * @package  App\Models
 */
class Notification extends Model
{
    public $table = "notifications";

    public $primaryKey = "id";

    public $timestamps = true;

    public $fillable = [
        'user_id',
        'flag',
        'uuid',
        'title',
        'details',
        'is_read',
    ];

    public $rules = [
        'title' => 'required',
        'details' => 'required',
        'flag' => 'required',
        'user_id' => 'required',
    ];
    // /**
    //  * @inheritdoc
    //  */
    // public $timestamps = false;

    // /**
    //  * @inheritdoc
    //  */
    // protected $fillable = [
    //     'value',
    // ];

    // public function types()
    // {
    //     return [
    //         'newSubscription' => [
    //             'title' => 'Novo inscrito',
    //             'text' => '{name} se cadastrou no seu site!',
    //             'url' => '/admin/subscription/'
    //         ],
    //         'newMessage' => [
    //             'title' => 'Nova Mensagem',
    //             'text' => '{name} te enviou uma mensagem!',
    //             'url' => '/messages/{id}'
    //         ],
    //     ];
    // }

    // /**
    //  * Gera uma Nova Notificação para o Alvo
    //  *
    //  * @param  [type] $target
    //  * @param  [type] $typeNotification
    //  * @param  array  $data
    //  * @return void
    //  */
    // public static function generate($target, $typeNotification, $data = [])
    // {
    //     // @todo
    //     // self::create([

    //     // ]);
    // }

    // /**
    //  * @inheritdoc
    //  */
    // public static function boot()
    // {
    //     parent::boot();

    //     static::deleting(
    //         function (self $tag) {
    //             $tag->posts()->detach();
    //         }
    //     );
    // }

    // /**
    //  * @inheritdoc
    //  */
    // public function newEloquentBuilder($query): NotificationBuilder
    // {
    //     return new NotificationBuilder($query);
    // }

    // /**
    //  * @inheritdoc
    //  */
    // public function newQuery(): NotificationBuilder
    // {
    //     return parent::newQuery();
    // }

    // /**
    //  * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
    //  */
    // public function posts()
    // {
    //     return $this->belongsToMany(Post::class, Tables::TABLE_POSTS_TAGS);
    // }

    // /**
    //  * Setter for the 'value' attribute.
    //  *
    //  * @param  string $value
    //  * @return $this
    //  */
    // public function setValueAttribute(string $value)
    // {
    //     $this->attributes['value'] = trim(str_replace(' ', '_', strtolower($value)));

    //     return $this;
    // }

    // /**
    //  * @return NotificationEntity
    //  */
    // public function toEntity(): NotificationEntity
    // {
    //     return new NotificationEntity([
    //         'id' => $this->id,
    //         'value' => $this->value,
    //     ]);
    // }
}
