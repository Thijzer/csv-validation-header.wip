<?php

namespace Tests\Misery\Component\Action;

use Misery\Component\Action\ModifierAction;
use Misery\Component\Common\Registry\Registry;
use Misery\Component\Modifier\ReplaceCharacterModifier;
use PHPUnit\Framework\TestCase;

class ModifierActionTest extends TestCase
{
    public function test_it_should_modify_multi_key_values_action(): void
    {
        $registry = new Registry('modifier');
        $modifier = new ReplaceCharacterModifier();
        $registry->register($modifier::NAME, $modifier);
        $format = new ModifierAction($registry);

        $format->setOptions(
            [
                'modifier' => $modifier::NAME,
                'keys' => 'description,short_description',
                'characters' => ['á' => 'a', 'é' => 'e'],
            ]
        );

        $item = [
            'description' => [
                'nl_BE' => 'áááááábéééééé',
                'fr_BE' => 'áááááábéééééé',
            ],
            'short_description' => [
                'nl_BE' => 'áán ná be ééglé sam max.',
                'fr_BE' => 'áán ná be ééglé sam max FR',
            ],
            'another_description' => [
                'nl_BE' => 'áááááábéééééé',
                'fr_BE' => 'áááááábéééééé',
            ],
            'sku' => '1',
        ];

        $expected = [
            'description' => [
                'nl_BE' => 'aaaaaabeeeeee',
                'fr_BE' => 'aaaaaabeeeeee',
            ],
            'short_description' => [
                'nl_BE' => 'aan na be eegle sam max.',
                'fr_BE' => 'aan na be eegle sam max FR',
            ],
            'another_description' => [
                'nl_BE' => 'áááááábéééééé',
                'fr_BE' => 'áááááábéééééé',
            ],
            'sku' => '1',
        ];

        $this->assertEquals($expected, $format->apply($item));
    }

    public function test_it_should_modify_single_key_values_action(): void
    {
        $registry = new Registry('modifier');
        $modifier = new ReplaceCharacterModifier();
        $registry->register($modifier::NAME, $modifier);
        $format = new ModifierAction($registry);

        $format->setOptions(
            [
                'modifier' => $modifier::NAME,
                'keys' => 'short_description',
                'characters' => ['á' => 'a', 'é' => 'e'],
            ]
        );

        $item = [
            'description' => [
                'nl_BE' => 'áááááábéééééé',
                'fr_BE' => 'áááááábéééééé',
            ],
            'short_description' => [
                'nl_BE' => 'áán ná be ééglé sam max.',
                'fr_BE' => 'áán ná be ééglé sam max FR',
            ],
            'another_description' => [
                'nl_BE' => 'áááááábéééééé',
                'fr_BE' => 'áááááábéééééé',
            ],
            'sku' => '1',
        ];

        $expected = [
            'description' => [
                'nl_BE' => 'áááááábéééééé',
                'fr_BE' => 'áááááábéééééé',
            ],
            'short_description' => [
                'nl_BE' => 'aan na be eegle sam max.',
                'fr_BE' => 'aan na be eegle sam max FR',
            ],
            'another_description' => [
                'nl_BE' => 'áááááábéééééé',
                'fr_BE' => 'áááááábéééééé',
            ],
            'sku' => '1',
        ];

        $this->assertEquals($expected, $format->apply($item));
    }
}