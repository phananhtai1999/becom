<?php

namespace App\Models;

use App\Abstracts\AbstractModel;
use Baum\NestedSet\Node as WorksAsNestedSet;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class ArticleCategory extends AbstractModel
{
    use HasFactory, WorksAsNestedSet, SoftDeletes, HasTranslations;

    /**
     * @var string
     */
    protected $table = "article_categories";

    /**
     * @var string
     */
    protected $primaryKey = 'uuid';

    /**
     * Column name which stores reference to parent's node.
     *
     * @var string
     */
    protected $parentColumnName = 'parent_uuid';

    /**
     * Column name for the left index.
     *
     * @var string
     */
    protected $leftColumnName = 'left';

    /**
     * Column name for the right index.
     *
     * @var string
     */
    protected $rightColumnName = 'right';

    /**
     * Column name for the depth field.
     *
     * @var string
     */
    protected $depthColumnName = 'depth';

    public $translatable = ['title'];

    /**
     * @var string[]
     */
    protected $fillable = [
        'image',
        'slug',
        'parent_uuid',
        'user_uuid',
        'publish_status',
        'title'
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
        'user_uuid' => 'integer',
        'parent_uuid' => 'integer',
        'title' => 'array'
    ];

    /**
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_uuid', 'uuid');
    }

    /**
     * @return BelongsTo
     */
    public function parentArticleCategory()
    {
        return $this->belongsTo(__CLASS__, 'parent_uuid');
    }

    /**
     * @return Collection
     */
    public function childrenArticleCategory()
    {
        return $this->children()->orderBy('created_at', 'DESC')->get();
    }

}
