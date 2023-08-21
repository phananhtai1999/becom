<?php

namespace App\Console\Commands;

use App\Models\Article;
use App\Models\Role;
use App\Services\ArticleService;
use App\Services\ConfigService;
use App\Services\UserService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class AutoDeleteDraftArticleOfEditor extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'delete:draft-articles';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Auto delete draft articles of editor';

    /**
     * @var ArticleService
     */
    protected $articleService;

    /**
     * @var UserService
     */
    protected $userService;

    /**
     * @var ConfigService
     */
    protected $configService;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(
        ArticleService $articleService,
        UserService    $userService,
        ConfigService  $configService
    )
    {
        $this->articleService = $articleService;
        $this->userService = $userService;
        $this->configService = $configService;
        parent::__construct();
    }

    /**
     * @return void
     */
    public function handle()
    {
        //Get All Draft Articles
        $draftArticles = $this->articleService->findAllWhere([['publish_status', Article::DRAFT_PUBLISH_STATUS]]);
        foreach ($draftArticles as $article) {
            //Check role user
            $roleAdminAndRoot = optional(optional(optional($this->userService->findOneWhere([['uuid', $article->user_uuid]]))->roles)->whereIn('slug', [Role::ADMIN_ROOT, Role::ROLE_ROOT]))->count();
            $config = $this->configService->findConfigByKey('time_allowed_view_articles_of_editor');
            //Delete Article role editor
            if (!$roleAdminAndRoot && $config) {
                if (Carbon::now()->subDays($config->value) > $article->updated_at) {
                    $article->delete();
                }
            }
        }
    }
}
