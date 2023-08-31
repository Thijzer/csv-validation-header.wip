<?php

namespace Tests\Misery\Component\Action;

use Misery\Component\Action\CopyAction;
use Misery\Component\Action\StatementAction;
use PHPUnit\Framework\TestCase;

class StatementActionTest extends TestCase
{
    public function test_it_should_do_a_statement_with_copy_action_and_key_pair_list(): void
    {
        $format = new StatementAction();

        $item = [
            'brand' => 'louis',
            'new_brand' => '',
            'description' => 'LV',
            'sku' => '1',
        ];

        $format->setOptions([
            'when' => [
                'field' => 'brand',
                'operator' => 'IN_LIST',
                'context' => [
                    'list' => [
                        'louis' => null,
                        'nike' => null,
                        'reebok' => null,
                    ],
                ],
            ],
            'then'  => [
                'action' => 'copy',
                'from' => 'brand',
                'to' => 'new_brand',
            ],
        ]);

        $this->assertEquals([
            'brand' => 'louis',
            'new_brand' => 'louis',
            'description' => 'LV',
            'sku' => '1',
        ], $format->apply($item));
    }

    public function test_it_should_do_a_statement_with_copy_action_and_list(): void
    {
        $format = new StatementAction();

        $item = [
            'brand' => 'louis',
            'new_brand' => '',
            'description' => 'LV',
            'sku' => '1',
        ];

        $format->setOptions([
            'when' => [
                'field' => 'brand',
                'operator' => 'IN_LIST',
                'context' => [
                    'list' => [
                        'louis',
                        'nike',
                        'reebok',
                    ],
                ],
            ],
            'then'  => [
                'action' => 'copy',
                'from' => 'brand',
                'to' => 'new_brand',
            ],
        ]);

        $this->assertEquals([
            'brand' => 'louis',
            'new_brand' => 'louis',
            'description' => 'LV',
            'sku' => '1',
        ], $format->apply($item));
    }

    public function test_it_should_do_a_statement_with_set_action_with_list(): void
    {
        $format = new StatementAction();

        $item = [
            'brand' => 'louis',
            'description' => 'LV',
            'sku' => '1',
        ];

        $format->setOptions([
            'when' => [
                'field' => 'brand',
                'operator' => 'IN_LIST',
                'context' => [
                    'list' => [
                        'louis',
                        'nike',
                        'reebok',
                    ],
                ],
            ],
            'then'  => [
                'field' => 'brand',
                'state' => 'Louis',
            ],
        ]);

        $this->assertEquals([
            'brand' => 'Louis',
            'description' => 'LV',
            'sku' => '1',
        ], $format->apply($item));
    }

    public function test_it_should_do_a_statement_with_set_action(): void
    {
        $format = new StatementAction();

        $item = [
            'brand' => 'louis',
            'description' => 'LV',
            'sku' => '1',
        ];

        $format->setOptions([
            'when' => [
                'field' => 'brand',
                'operator' => 'EQUALS',
                'state' => 'louis',
            ],
            'then'  => [
                'field' => 'brand',
                'state' => 'Louis',
            ],
        ]);

        $this->assertEquals([
            'brand' => 'Louis',
            'description' => 'LV',
            'sku' => '1',
        ], $format->apply($item));
    }

    public function test_it_should_do_a_statement_with_key_value_set_action(): void
    {
        $format = new StatementAction();

        $item = [
            'brand' => 'louis',
            'description' => 'LV',
            'sku' => '1',
        ];

        $format->setOptions([
            'when' => [
                'field' => 'brand',
                'operator' => 'EQUALS',
                'state' => 'louis',
            ],
            'then' => [
                'brand' => 'Louis',
            ],
        ]);

        $this->assertEquals([
            'brand' => 'Louis',
            'description' => 'LV',
            'sku' => '1',
        ], $format->apply($item));
    }
}