<?php

namespace Misery\Component\Common\Pipeline;

use Misery\Component\Common\Pipeline\Exception\InvalidItemException;
use Misery\Component\Common\Pipeline\Exception\SkipPipeLineException;

class Pipeline
{
    /** @var PipeReaderInterface */
    private $in;
    /** @var PipeWriterInterface */
    private $invalid;
    /** @var PipeInterface[] */
    private $lines = [];
    /** @var PipeWriterInterface[] */
    private $out = [];

    public function input(PipeReaderInterface $reader): self
    {
        $this->in = $reader;

        return $this;
    }

    public function line(PipeInterface $pipe): self
    {
        $this->lines[] = $pipe;

        return $this;
    }

    public function invalid(PipeWriterInterface $writer): self
    {
        $this->invalid = $writer;

        return $this;
    }

    public function output(PipeWriterInterface $writer): self
    {
        $this->out[] = $writer;

        return $this;
    }

    public function run(int $amount = -1)
    {
        $i = 0;
        // looping
        while ($i !== $amount && $item = $this->in->read()) {
            $i++;
            try {
                foreach ($this->lines as $line) {
                    $item = $line->pipe($item);
                }
                foreach ($this->out as $out) {
                    $out->write($item);
                }
            } catch (SkipPipeLineException $exception) {
                continue;
            } catch (InvalidItemException $exception) {
                $this->invalid->write([
                    'line' => $i,
                    'msg' => $exception->getMessage(),
                    'item' => json_encode($exception->getInvalidItem()),
                ]);
                continue;
            }
        }
        // stopping
        foreach ($this->out as $out) {
            try {
                $out->stop();
            } catch (SkipPipeLineException $exception) {
                continue;

            } catch (InvalidItemException $exception) {
                $this->invalid->write([
                    'line' => $i,
                    'msg' => $exception->getMessage(),
                    'item' => json_encode($exception->getInvalidItem()),
                ]);
                continue;
            }
        }
    }
}