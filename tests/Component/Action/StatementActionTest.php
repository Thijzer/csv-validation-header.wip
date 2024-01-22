<?php

namespace Tests\Misery\Component\Action;

use Misery\Component\Action\CopyAction;
use Misery\Component\Action\StatementAction;
use Misery\Component\Common\Pipeline\Exception\SkipPipeLineException;
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

    public function test_it_should_skip_when_char_length_is_greater_then_check_value(): void
    {
        $this->expectException(SkipPipeLineException::class);

        $format = new StatementAction();

        $item = [
            'fr_values' => [
                'skcsveysvmtpkiddxdjwugeayqvruotxzgffqwkrkhbxjldyfxfqfmzwakqdixikxxprbfpscldluxibczlalgfxkaitnikgyrlkvweoeahctrjjkdgkeoxfnmfjmmyancrldbupzxvvvsgcdwiphlxhwqslhybzzegsyuuyxqgvesciybykosbozznteeaxctgqbgfkyzoveopmkhwkezzliegitxkokrbpjyiatroopyspndjnhgznvfnyyiultqivpeg',
            ],
            'sku' => '1',
        ];

        $format->setOptions([
            'when' => [
                'field' => 'fr_values',
                'operator' => 'CHARLEN_GT',
                'context' => [
                    'char_len' => 255
                ],
            ],
            'then' => [
                'action' => 'skip',
            ],
        ]);
        $format->apply($item);
    }

    public function test_it_should_NOT_skip_when_char_length_is_greater_then_check_value(): void
    {
        $format = new StatementAction();

        $item = [
            'fr_values' => [
                "110 V AC : 50 Hz, puissance d'appel 5,0 VA, puissance de maintien 3,7 VA;;110 V AC : 60 Hz, puissance d'appel 5,0 VA, puissance de maintien 3,7 VA;;230 V AC : 50 Hz, puissance d'appel 5,0 VA, puissance de maintien 3,7 VA;;230 V AC : 60 Hz, ",
            ],
            'sku' => '1',
        ];

        $format->setOptions([
            'when' => [
                'field' => 'fr_values',
                'operator' => 'CHARLEN_GT',
                'context' => [
                    'char_len' => 255
                ],
            ],
            'then' => [
                'action' => 'skip',
            ],
        ]);
        $format->apply($item);

        $this->assertEquals($item, $format->apply($item));
    }
}