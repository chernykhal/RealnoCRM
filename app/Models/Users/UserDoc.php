<?php

namespace App\Models\Users;

use App\Models\Users\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;


/**
 * App\Models\Users\UserDoc
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\UserDoc newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\UserDoc newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\UserDoc query()
 * @mixin \Eloquent
 * @property int $id
 * @property string $name
 * @property string $filename
 * @property string $doc_url
 * @property int|null $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\UserDoc whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\UserDoc whereDocUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\UserDoc whereFilename($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\UserDoc whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\UserDoc whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\UserDoc whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\UserDoc whereUserId($value)
 * @property-read \App\Models\Users\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\UserDoc filter($frd)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\UserDoc filterDocument($userId)
 * @property-read int|null $version_count
 * @property-read int|null $versions_count
 * @property-read \App\Models\Users\File $file
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Users\File[] $versions
 */
class UserDoc extends Model
{

    protected $table = 'user_docs';

    protected $primaryKey = 'id';

    protected $fillable = [
        'name',
        'user_id',
    ];

    protected $dates = [
        'created_at',
    ];

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return route('documents.show', $this);
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return \Illuminate\Support\Carbon|null
     */
    public function getCreatedAt(): ?\Illuminate\Support\Carbon
    {
        return $this->created_at;
    }

    /**
     * @return string
     */
    public function getFilename(): string
    {
        return $this->filename;
    }

    /**
     * @param string $filename
     */
    public function setFilename(string $filename): void
    {
        $this->filename = $filename;
    }

    /**
     * @return string
     */
    public function getDocUrl(): string
    {
        return $this->doc_url;
    }

    /**
     * @param string $doc_url
     */
    public function setDocUrl(string $doc_url): void
    {
        $this->doc_url = $doc_url;
    }

    /**
     * @return int
     */
    public function getNextVersionId(): int
    {
        return $this->versions()->count() + 1;
    }

    /**
     * @return int|null
     */
    public function getUserId(): ?int
    {
        return $this->user_id;
    }

    /**
     * @param int|null $user_id
     */
    public function setUserId(?int $user_id): void
    {
        $this->user_id = $user_id;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return MorphMany
     */
    public function versions(): MorphMany
    {
        return $this->morphMany(File::class, 'fileable');
    }

    /**
     * @return string
     */
    public function getNextVersionName(): string
    {
        return '/users/' . $this->getUserId() . '/documents/' . $this->getKey() . '/' . $this->getNextVersionId();
    }

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return MorphOne
     */
    public function file(): MorphOne
    {
        return $this->morphOne(File::class, 'fileable');
    }

    public function scopeFilterDocument(Builder $query, int $userId): Builder
    {
        return $query->where(function (Builder $query) use ($userId): Builder {
            return $query->orWhere('user_id', $userId);
        });
    }


    public function scopeFilter(Builder $query, array $frd): Builder
    {
        array_filter($frd);
        foreach ($frd as $key => $value) {
            if (null === $value) {
                continue;
            }
            switch ($key) {
                case 'search':
                    {
                        $query->where(function (Builder $query) use ($value): Builder {
                            return $query->orWhere('user_id', 'like', '%' . $value . '%')
                                ->orWhere('name', 'like', '%' . $value . '%')
                                ->orWhere('id', 'like', '%' . $value . '%');
                        });
                    }
                    break;
            }
        }
        return $query;
    }

}
