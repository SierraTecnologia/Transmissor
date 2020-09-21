<?php

namespace Transmissor\Models;

use App\Contants\Tables;
use Carbon\Carbon;
use Finder\Models\Reference;
use Illuminate\Database\Eloquent\Collection;
use Population\Manipule\Builders\CommentBuilder;
use Population\Manipule\Entities\CommentEntity;
use Pedreiro\Models\Base;
use Transmissor\Models\Post;

/**
 * Class Comment.
 *
 * @property int id
 * @property string content
 * @property Collection posts
 * @package  App\Models
 */
class Comment extends Base
{
    public static $classeBuilder = CommentBuilder::class;
    /**
     * @inheritdoc
     */
    public $timestamps = false;

    /**
     * @inheritdoc
     */
    protected $fillable = [
        'commentable_id',
        'commentable_type',
        'content',
    ];


    protected $mappingProperties = array(
        /**
         * User Info
         */
        'content' => [
            'type' => 'string',
            "analyzer" => "standard",
        ],
    );
    
    /**
     * Get the owning commentable model.
     */
    public function commentable()
    {
        return $this->morphTo();
    }

    /**
     * @inheritdoc
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(
            function (self $comment) {
                $comment->posts()->detach();
            }
        );
    }

    /**
     * @inheritdoc
     */
    public function newEloquentBuilder($query): CommentBuilder
    {
        return new CommentBuilder($query);
    }

    /**
     * @inheritdoc
     */
    public function newQuery(): CommentBuilder
    {
        return parent::newQuery();
    }

    // @todo Carregar Modelo Post para Blog
    // /**
    //  * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
    //  */
    // public function posts()
    // {
    //     return $this->belongsToMany(Post::class, Tables::TABLE_POSTS_TAGS);
    // }

    /**
     * Setter for the 'content' attribute.
     *
     * @param  string $content
     * @return $this
     */
    public function setContentAttribute(string $content)
    {
        $this->attributes['content'] = trim(str_replace(' ', '_', strtolower($content)));

        return $this;
    }

    /**
     * @return CommentEntity
     */
    public function toEntity(): CommentEntity
    {
        return new CommentEntity(
            [
            'id' => $this->id,
            'content' => $this->content,
            ]
        );
    }
    
    // @todo fazer
    public static function registerCommentForProject($comment, $id, $type, $projectUrl = false)
    {
        $comment =  self::firstOrCreate(
            [
            'content' => $comment->body,
            'commentable_id' => $id,
            'commentable_type' => $type,
            'created_at' => $comment->created,
            'updated_at' => $comment->updated
            ]
        );

        if ($projectUrl) {
            if (!$reference = Reference::where(
                [
                'code' => $projectUrl
                ]
            )->first()
            ) {
                $reference = Reference::create(
                    [
                    'code' => $projectUrl,
                    'name' => $projectUrl,
                    ]
                );
            }
            if (!$comment->references()->where('reference_id', $reference->id)->first()) {
                $comment->references()->save(
                    $reference,
                    [
                        'identify' => $comment->id,
                    ]
                );
            }
        }
        return $comment;

        // foreach($comments as $comment) {
        //     var_dump($comment);
        //     Coment::firstOrCreate([
        //         'name' => $comment->name
        //     ]);
        // }
    }
    
    public function references()
    {
        return $this->morphToMany(Reference::class, 'referenceable');
    }
    
    public static function registerComents($data, $id, $type, $reference)
    {
        if ($data->total<=0) {
            return false;
        }

        foreach ($data->comments as $comment) {
            static::registerCommentForProject($comment, $id, $type, $reference);
        }
    }
    
}
