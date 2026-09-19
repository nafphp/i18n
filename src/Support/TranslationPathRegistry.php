<?php

declare(strict_types=1);

namespace Naf\I18n\Support;

use InvalidArgumentException;
use LogicException;

/**
 * Directories a plugin offers translations from.
 *
 * The translator reads these in order and the application's own directory last,
 * so a host translation always wins over a package's. Every change bumps the
 * revision, which is what tells a loaded translator its data is stale.
 */
class TranslationPathRegistry
{
    /** @var array<string, array{id: string, directory: string, index: int}> */
    private array $paths = [];

    private int $revision = 0;

    /**
     * @param string $id        Stable id, namespaced like the package it belongs to
     * @param string $directory Absolute directory holding <locale>.json files
     * @param int    $index     Read order, ascending; ties read by id
     * @param bool   $replace   Whether an existing id may be replaced
     */
    public function add(string $id, string $directory, int $index = 100, bool $replace = false): void
    {
        if (trim($id) === '') {
            throw new InvalidArgumentException('A translation path needs a non-empty id.');
        }

        if (isset($this->paths[$id]) && !$replace) {
            throw new LogicException(
                'Translation path "' . $id . '" is already registered. Pass replace: true to replace it.',
            );
        }

        $this->paths[$id] = [
            'id'        => $id,
            'directory' => rtrim($directory, '/\\'),
            'index'     => $index,
        ];
        $this->revision++;
    }

    /**
     * Registered directories, ordered by index and id
     *
     * @return array<string, string> Id to directory
     */
    public function all(): array
    {
        $ordered = $this->paths;

        uasort(
            $ordered,
            static fn(array $left, array $right) => $left['index'] <=> $right['index']
                ?: strcmp($left['id'], $right['id']),
        );

        return array_map(static fn(array $path) => $path['directory'], $ordered);
    }

    /**
     * @param string $id Registered id
     *
     * @return bool True when a directory was removed
     */
    public function remove(string $id): bool
    {
        if (!isset($this->paths[$id])) {
            return false;
        }

        unset($this->paths[$id]);
        $this->revision++;

        return true;
    }

    /** Changes whenever a directory is added or removed. */
    public function revision(): int
    {
        return $this->revision;
    }
}
