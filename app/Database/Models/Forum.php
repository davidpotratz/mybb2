<?php
/**
 * Forum model class.
 *
 * @author    MyBB Group
 * @version   2.0.0
 * @package   mybb/core
 * @license   http://www.mybb.com/licenses/bsd3 BSD-3
 */

namespace MyBB\Core\Database\Models;

use Kalnoy\Nestedset\Node;
use McCool\LaravelAutoPresenter\HasPresenter;
use MyBB\Core\Content\ContentInterface;
use MyBB\Core\Moderation\Moderations\CloseableInterface;
use MyBB\Core\Moderation\Moderations\StickableInterface;
use MyBB\Core\Permissions\Interfaces\InheritPermissionInterface;
use MyBB\Core\Permissions\Traits\InheritPermissionableTrait;
use MyBB\Core\Presenters\ForumPresenter;

/**
 * @property int id
 */
class Forum extends Node implements HasPresenter, InheritPermissionInterface, CloseableInterface, StickableInterface, ContentInterface
{
    use InheritPermissionableTrait;

    /**
     * Nested set column IDs.
     */
    const LFT = 'left_id';
    const RGT = 'right_id';
    const PARENT_ID = 'parent_id';

    // @codingStandardsIgnoreStart
    /**
     * Indicates if the model should be timestamped.
     *
     * @var boolean
     */
    public $timestamps = false;

    // @codingStandardsIgnoreEnd

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'forums';
    /**
     * The relations to eager load on every query.
     *
     * @var array
     */
    protected $with = [];
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['left_id', 'right_id', 'parent_id'];

    /**
     * @var array
     */
    protected $casts = [
        'id' => 'int',
    ];

    /**
     * Get the presenter class.
     *
     * @return string
     */
    public function getPresenterClass()
    {
        return ForumPresenter::class;
    }

    /**
     * Find a model by its primary key.
     *
     * @param  mixed $id
     * @param  array $columns
     *
     * @return \Illuminate\Support\Collection|static|null
     */
    public static function find($id, $columns = ['*'])
    {
        return static::query()->find($id, $columns);
    }

    /**
     * A forum contains many topics.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function topics()
    {
        return $this->hasMany(\MyBB\Core\Database\Models\Topic::class);
    }

    /**
     * A forum contains many posts, through its topics.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function posts()
    {
        return $this->hasManyThrough(\MyBB\Core\Database\Models\Post::class, \MyBB\Core\Database\Models\Topic::class);
    }

    /**
     * A forum has a single last post.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function lastPost()
    {
        return $this->hasOne(\MyBB\Core\Database\Models\Post::class, 'id', 'last_post_id');
    }

    /**
     * A forum has a single last post author.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function lastPostAuthor()
    {
        return $this->hasOne(\MyBB\Core\Database\Models\User::class, 'id', 'last_post_user_id');
    }

    /**
     * @return bool|int
     */
    public function close()
    {
        return $this->update(['closed' => 1]);
    }

    /**
     * @return bool|int
     */
    public function open()
    {
        return $this->update(['closed' => 0]);
    }

    /**
     * @return bool|int
     */
    public function stick()
    {
        return $this->update(['sticky' => 1]);
    }

    /**
     * @return bool|int
     */
    public function unstick()
    {
        return $this->update(['sticky' => 0]);
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return 'forum';
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return route('forums.show', ['id' => $this->id, 'slug' => $this->slug]);
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }
}
