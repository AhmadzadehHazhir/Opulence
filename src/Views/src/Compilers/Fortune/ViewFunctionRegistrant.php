<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Views\Compilers\Fortune;

/**
 * Defines the registrant that creates the built-in functions
 */
final class ViewFunctionRegistrant
{
    /**
     * Registers the built-in view functions
     *
     * @param ITranspiler $transpiler The transpiler to register to
     */
    public function registerViewFunctions(ITranspiler $transpiler): void
    {
        // Register the charset function
        $transpiler->registerViewFunction('charset', fn ($charset) => '<meta charset="' . $charset . '">');
        // Register the CSS function
        $transpiler->registerViewFunction('css', function ($paths) {
            $callback = fn ($path) => '<link href="' . $path . '" rel="stylesheet">';

            return implode("\n", array_map($callback, (array)$paths));
        });
        // Register the favicon function
        $transpiler->registerViewFunction('favicon', fn ($path) => '<link href="' . $path . '" rel="shortcut icon">');
        // Register the HTTP-equiv function
        $transpiler->registerViewFunction('httpEquiv', function ($name, $value) {
            return '<meta http-equiv="' . htmlentities($name) . '" content="' . htmlentities((string)$value) . '">';
        });
        // Registers the HTTP method hidden input
        $transpiler->registerViewFunction('httpMethodInput', function ($httpMethod) {
            return sprintf(
                '<input type="hidden" name="_method" value="%s">',
                $httpMethod
            );
        });
        // Register the meta description function
        $transpiler->registerViewFunction('metaDescription', function ($metaDescription) {
            return '<meta name="description" content="' . htmlentities($metaDescription) . '">';
        });
        // Register the meta keywords function
        $transpiler->registerViewFunction('metaKeywords', function (array $metaKeywords) {
            return '<meta name="keywords" content="' . implode(',', array_map('htmlentities', $metaKeywords)) . '">';
        });
        // Register the page title function
        $transpiler->registerViewFunction('pageTitle', fn ($title) => '<title>' . htmlentities($title) . '</title>');
        // Register the script function
        $transpiler->registerViewFunction('script', function ($paths, $type = 'text/javascript') {
            $callback = function ($path) use ($type) {
                return '<script type="' . $type . '" src="' . $path . '"></script>';
            };

            return implode(PHP_EOL, array_map($callback, (array)$paths));
        });
    }
}