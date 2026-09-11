<?php

declare(strict_types=1);

namespace Risan\OAuth1\Request;

use InvalidArgumentException;

final class ParameterList
{
    /** @param list<array{0: string, 1: string}> $pairs */
    private function __construct(private array $pairs)
    {
    }

    public static function empty(): self
    {
        return new self([]);
    }

    public static function fromQueryString(string $query): self
    {
        if ($query === '') {
            return self::empty();
        }

        $pairs = [];
        foreach (explode('&', $query) as $field) {
            [$name, $value] = array_pad(explode('=', $field, 2), 2, '');
            $pairs[] = [urldecode($name), urldecode($value)];
        }

        return new self($pairs);
    }

    /** @param array<array-key, mixed> $parameters */
    public static function fromArray(array $parameters): self
    {
        $pairs = [];

        if (array_is_list($parameters) && $parameters !== []) {
            foreach ($parameters as $pair) {
                if (! is_array($pair) || array_keys($pair) !== [0, 1]) {
                    throw new InvalidArgumentException('OAuth parameters must be an associative scalar map or a list of [name, value] pairs.');
                }
                $pairs[] = [self::stringValue($pair[0]), self::stringValue($pair[1])];
            }

            return new self($pairs);
        }

        foreach ($parameters as $name => $value) {
            $pairs[] = [(string) $name, self::stringValue($value)];
        }

        return new self($pairs);
    }

    public function merge(self ...$lists): self
    {
        $pairs = $this->pairs;
        foreach ($lists as $list) {
            array_push($pairs, ...$list->pairs);
        }

        return new self($pairs);
    }

    /** @return list<array{0: string, 1: string}> */
    public function pairs(): array
    {
        return $this->pairs;
    }

    /** @return list<array{0: string, 1: string}> */
    public function normalizedPairs(): array
    {
        $pairs = array_values(array_filter($this->pairs, static fn (array $pair): bool => $pair[0] !== 'oauth_signature'));
        $pairs = array_map(static fn (array $pair): array => [rawurlencode($pair[0]), rawurlencode($pair[1])], $pairs);
        usort($pairs, static fn (array $left, array $right): int => $left[0] <=> $right[0] ?: $left[1] <=> $right[1]);

        return $pairs;
    }

    public function toQueryString(): string
    {
        return implode('&', array_map(static fn (array $pair): string => rawurlencode($pair[0]).'='.rawurlencode($pair[1]), $this->pairs));
    }

    private static function stringValue(mixed $value): string
    {
        if ($value instanceof \Stringable) {
            return (string) $value;
        }

        if (is_string($value) || is_int($value) || is_float($value) || is_bool($value)) {
            return match (true) {
                $value === true => '1',
                $value === false => '0',
                default => (string) $value,
            };
        }

        throw new InvalidArgumentException('OAuth parameter names and values must be scalar. Use an empty string for an empty value and [name, value] pairs for repeated names.');
    }
}
