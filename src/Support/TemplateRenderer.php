<?php

declare(strict_types=1);

namespace Velt\Cli\Support;

use RuntimeException;

final class TemplateRenderer
{
    public function __construct(private readonly string $templatesPath)
    {
    }

    /**
     * @param array<string, string> $variables
     */
    public function render(string $template, array $variables): string
    {
        $path = $this->templatesPath . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $template);

        if (!is_file($path)) {
            throw new RuntimeException(sprintf('Template not found: %s', $template));
        }

        $contents = (string) file_get_contents($path);

        foreach ($variables as $key => $value) {
            $contents = str_replace('{' . $key . '}', $value, $contents);
        }

        return $contents;
    }
}
