<?php

namespace RiseTechApps\ViewSuite;

use Illuminate\View\FileViewFinder;
use InvalidArgumentException;

class ThemedViewFinder extends FileViewFinder
{
    /**
     * {@inheritdoc}
     */
    protected function findNamespacedView($name)
    {
        [$namespace, $view] = $this->parseNamespaceSegments($name);

        if ($namespace === 'view-suite') {
            $segments = explode('.', $view);
            $folder = array_shift($segments);

            if ($folder && in_array($folder, ['emails', 'errors'], true)) {
                $viewName = implode('.', $segments);

                if ($viewName !== '') {
                    $themeKey = $folder === 'emails' ? 'email' : 'error';
                    $theme = config("view-suite.theme.{$themeKey}", 'default');
                    $paths = $this->hints[$namespace] ?? [];

                    if (! empty($paths)) {
                        try {
                            return $this->findInPaths("{$folder}.{$theme}.{$viewName}", $paths);
                        } catch (InvalidArgumentException $exception) {
                            if ($theme !== 'default') {
                                try {
                                    return $this->findInPaths("{$folder}.default.{$viewName}", $paths);
                                } catch (InvalidArgumentException $fallbackException) {
                                    // Let the parent resolver throw the exception below
                                }
                            }
                        }
                    }
                }
            }
        }

        return parent::findNamespacedView($name);
    }
}
