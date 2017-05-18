<?php

namespace Statamic\StaticCaching;

use Statamic\API\File;
use Statamic\API\Path;
use Statamic\API\Config;
use Statamic\API\Folder;
use Illuminate\Http\Request;
use League\Flysystem\FileNotFoundException;

class FileCacher extends AbstractCacher
{
    /**
     * @var string
     */
    private $cache_path;

    /**
     * Cache a page
     *
     * @param \Illuminate\Http\Request $request     Request associated with the page to be cached
     * @param string                   $content     The response content to be cached
     * @param null|int                 $expiration  Length of time to cache for, in minutes.
     *                                              Null to use the configured default.
     */
    public function cachePage(Request $request, $content, $expiration = null)
    {
        $url = $this->getUrl($request);

        if ($this->isExcluded($url)) {
            return;
        }

        $content = $this->normalizeContent($content);

        $path = $this->getFilePath($request);

        if (! $this->writeFile($path, $content)) {
            return;
        }

        $this->cacheUrl($this->makeHash($url), $url);
    }

    /**
     * Write the cache file to disk.
     *
     * @param string $path     The path to the file.
     * @param string $content  The content of the file.
     * @return bool            True if written, false if not.
     */
    private function writeFile($path, $content)
    {
        @mkdir(dirname($path), 0777, true);

        // Create the file handle. We use the "c" mode which will avoid writing an
        // empty file if we abort when checking the lock status in the next step.
        $handle = fopen($path, 'c');

        // Attempt to obtain the lock for a the file. If the file is already locked, then we'll
        // abort right here since another process is in the middle of writing the file. Since
        // file locks are only advisory, we'll have to manually check and prevent writing.
        if (! flock($handle, LOCK_EX | LOCK_NB)) {
            return false;
        }

        fwrite($handle, $content);
        chmod($path, 0777);

        // Hold the file lock for a moment to prevent other processes from trying to write the same file.
        sleep(Config::get('caching.static_caching_lock_hold_length', 0));

        fclose($handle);

        return true;
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return string
     */
    public function getCachedPage(Request $request)
    {
        // This method doesn't get used when using file-based static caching.
        // The html file will get served before PHP even gets a chance.
    }

    /**
     * Flush out the entire static cache
     *
     * @return void
     */
    public function flush()
    {
        foreach (Folder::getFilesRecursively($this->cachePath()) as $path) {
            File::delete($path);
        }

        Folder::deleteEmptySubfolders($this->cachePath());

        $this->flushUrls();
    }

    /**
     * Invalidate a URL
     *
     * @param string $url
     * @return void
     */
    public function invalidateUrl($url)
    {
        if (! $key = $this->getUrls()->flip()->get($url)) {
            // URL doesn't exist, nothing to invalidate.
            return;
        }

        try {
            File::delete(Path::assemble($this->cachePath(), $url, 'index.html'));
        } catch (FileNotFoundException $e) {
            //
        }

        $this->forgetUrl($key);
    }

    /**
     * Get the path where static files are stored
     *
     * @return string
     */
    private function cachePath()
    {
        if ($this->cache_path) {
            return $this->cache_path;
        }

        return $this->cache_path = Config::get('caching.static_caching_file_path');

    }

    /**
     * Get the path to the cached file.
     *
     * @param Request $request
     * @return string
     */
    private function getFilePath(Request $request)
    {
        return Path::makeFull($this->cachePath() . '/' . $request->path() . '/index.html');
    }
}
