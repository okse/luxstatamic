<?php

namespace Statamic\Http\ViewComposers;

use Statamic\API\URL;
use Illuminate\Contracts\View\View;
use Statamic\Extend\Management\AddonRepository;

class JavascriptComposer
{
    /**
     * @var AddonRepository
     */
    private $repo;

    public function __construct(AddonRepository $repo)
    {
        $this->repo = $repo;
    }

    public function compose(View $view)
    {
        $view->with('scripts', $this->scripts());
    }

    private function scripts()
    {
        // Don't bother doing anything on the login screen.
        if (\Route::current() && \Route::current()->getName() === 'login') {
            return '';
        }

        $scripts = $this->repo->thirdParty()->filename('scripts.js')->files();

        $str = '';

        foreach ($scripts as $path) {
            $dir = pathinfo($path)['dirname'];
            $parts = explode('/', $dir);

            $str .= '<script src="' . URL::prependSiteRoot(URL::assemble(RESOURCES_ROUTE, 'addons', $parts[2], 'js/scripts.js')) . '"></script>';
        }

        return $str;
    }
}
