<?php

declare(strict_types=1);

namespace Orchid\Press\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Orchid\Platform\Dashboard;
use Orchid\Platform\Models\User;
use Orchid\Platform\Traits\AttachTrait;
use Orchid\Platform\Traits\LogsActivityTrait;

class Comment extends Model
{
    use AttachTrait,
        LogsActivityTrait;

    /**
     * @var string
     */
    protected $table = 'comments';

    /**
     * @var array
     */
    protected $fillable = [
        'post_id',
        'user_id',
        'parent_id',
        'content',
        'approved',
    ];

    /**
     * @var array
     */
    protected $casts = [
        'post_id'   => 'integer',
        'user_id'   => 'integer',
        'parent_id' => 'integer',
        'approved'  => 'boolean',
    ];

    /**
     * @var string
     */
    protected static $logAttributes = ['*'];

    /**
     * Find a comment by post ID.
     *
     * @param $postId
     *
     * @return mixed
     */
    public static function findByPostId($postId)
    {
        $instance = new static;

        return $instance->where('post_id', $postId)->get();
    }

    /**
     * Post relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function post(): BelongsTo
    {
        return $this->belongsTo(Dashboard::model(Post::class), 'post_id');
    }

    /**
     * Original relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function original(): BelongsTo
    {
        return $this->belongsTo(static::class, 'parent_id');
    }

    /**
     * Replies relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function replies(): HasMany
    {
        return $this->hasMany(static::class, 'parent_id');
    }

    /**
     * Verify if the current comment is approved.
     *
     * @return bool
     */
    public function isApproved(): bool
    {
        return $this->attributes['approved'] === 1 || $this->attributes['approved'] === true;
    }

    /**
     * Verify if the current comment is a reply from another comment.
     *
     * @return bool
     */
    public function isReply(): bool
    {
        return $this->attributes['parent_id'] > 0;
    }

    /**
     * Verify if the current comment has replies.
     *
     * @return bool
     */
    public function hasReplies(): bool
    {
        return count($this->replies) > 0;
    }

    /**
     *   Author relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(Dashboard::model(User::class), 'user_id');
    }

    /**
     * Where clause for only approved comments.
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('approved', 1);
    }
}
