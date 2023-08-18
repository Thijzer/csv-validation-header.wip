<?php

namespace Misery\Component\AttributeFormatter;

use Misery\Component\Common\Utils\ValueFormatter;

class MultiValuePresenterFormatter implements PropertyFormatterInterface, RequiresContextInterface
{
    /**
     * The Value that needs formatting supplied with context if needed
     *
     * @param $value
     * @param array $context
     * @return string|array
     */
    public function format($value, array $context = [])
    {
        if ($context['current-attribute-type'] === 'pim_catalog_multiselect' && empty($value)) {
            return '';
        }

        if (empty($value)) {
            return $value;
        }

        if (is_array($value) && isset($value['format'])) {
            return ValueFormatter::format($value['format'], $value);
        }

        if (is_array($value) && !isset($value['format']) && isset($context['value-separator'])) {
            $value = implode($context['value-separator'], $value);
        }

        if (is_string($value) && isset($context['format'])) {
            return ValueFormatter::format($context['format'], ['value' => $value]);
        }

        return $value;
    }

    /**
     * Check what values are supported
     * @param string $type
     * @return bool
     */
    public function supports(string $type): bool
    {
        // we support all types
        return true;
    }

    public function requires(array $context): bool
    {
        return true;
    }
}