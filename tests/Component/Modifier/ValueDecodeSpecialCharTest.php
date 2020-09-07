<?php

namespace Tests\Misery\Component\Modifier;

use Misery\Component\Modifier\ValueDecodeSpecialChar;
use PHPUnit\Framework\TestCase;

class ValueDecodeSpecialCharTest extends TestCase
{
    function test_it_should_snake_case_a_value(): void
    {
        $modifier = new ValueDecodeSpecialChar();

        $this->assertEquals('Test&Test', $modifier->modify('Test&amp;Test'));
        $this->assertEquals('§èé&çà', $modifier->modify('&sect;&egrave;&eacute;&amp;&ccedil;&agrave;'));

        $this->assertEquals('Albert Einstein said: \'E=MC²\'',
            $modifier->modify('Albert Einstein said: &#039;E=MC&sup2;&#039;'));

        $this->assertEquals('My name is Øyvind Åsane. I\'m Norwegian.',
            $modifier->modify('My name is &Oslash;yvind &Aring;sane. I&#039;m Norwegian.'));

        $this->assertEquals('I\'ll "walk" the <b>dog</b> now',
            $modifier->modify('I\'ll &quot;walk&quot; the &lt;b&gt;dog&lt;/b&gt; now'));
    }
}