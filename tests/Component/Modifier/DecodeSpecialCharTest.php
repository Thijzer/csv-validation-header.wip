<?php

namespace Tests\Misery\Component\Modifier;

use Misery\Component\Modifier\DecodeSpecialChar;
use PHPUnit\Framework\TestCase;

class DecodeSpecialCharTest extends TestCase
{
    function test_it_should_decode_all_chars_and_quotes(): void
    {
        $modifier = new DecodeSpecialChar();

        $this->assertEquals('Test&Test', $modifier->modify('Test&amp;Test'));
        $this->assertEquals('§èé&çà', $modifier->modify('&sect;&egrave;&eacute;&amp;&ccedil;&agrave;'));

        $this->assertEquals('Albert Einstein said: \'E=MC²\'',
            $modifier->modify('Albert Einstein said: &#039;E=MC&sup2;&#039;'));

        $this->assertEquals('My name is Øyvind Åsane. I\'m Norwegian.',
            $modifier->modify('My name is &Oslash;yvind &Aring;sane. I&#039;m Norwegian.'));

        $this->assertEquals('I\'ll "walk" the <b>dog</b> now',
            $modifier->modify('I\'ll &quot;walk&quot; the &lt;b&gt;dog&lt;/b&gt; now'));
    }

    function test_it_should_decode_all_chars_and_double_quotes(): void
    {
        $modifier = new DecodeSpecialChar();

        $this->assertEquals('Albert Einstein said: "E=MC²"',
            $modifier->modify('Albert Einstein said: &quot;E=MC&sup2;&quot;', ENT_COMPAT));

        $this->assertEquals('My name is Øyvind Åsane. I&#039;m Norwegian.',
            $modifier->modify('My name is &Oslash;yvind &Aring;sane. I&#039;m Norwegian.', ENT_COMPAT));
    }

    function test_it_should_decode_all_chars_and_no_quotes(): void
    {
        $modifier = new DecodeSpecialChar();

        $this->assertEquals('Albert Einstein said: &quot;E=MC²&quot;',
            $modifier->modify('Albert Einstein said: &quot;E=MC&sup2;&quot;', ENT_NOQUOTES));

        $this->assertEquals('My name is Øyvind Åsane. I&#039;m Norwegian.',
            $modifier->modify('My name is &Oslash;yvind &Aring;sane. I&#039;m Norwegian.', ENT_NOQUOTES));
    }
}