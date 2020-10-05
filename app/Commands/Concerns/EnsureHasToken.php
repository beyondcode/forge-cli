<?php

namespace App\Commands\Concerns;

trait EnsureHasToken
{
    protected function hasToken()
    {
        return config('forge.token') !== null && config('forge.token') !== '';
    }

    protected function ensureHasToken()
    {
        if (! $this->hasToken()) {
            $this->error('You have not configured your Forge API token yet. Please call "forge login" first.');
            return false;
        }

        return true;
    }
}
