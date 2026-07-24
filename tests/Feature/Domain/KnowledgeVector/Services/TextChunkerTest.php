<?php

use App\Domain\KnowledgeVector\Services\TextChunker;

beforeEach(function () {
    $this->chunker = app(TextChunker::class);
});

it('returns an empty array for empty text', function () {
    expect($this->chunker->chunk(''))->toBe([]);
});

it('returns a single chunk for short text', function () {
    $short = 'Hello world this is a short text';

    $chunks = $this->chunker->chunk($short);

    expect($chunks)->toHaveCount(1);
    expect($chunks[0])->toBe($short);
});

it('splits long text into multiple chunks', function () {
    $words = array_fill(0, 1000, 'word');
    $longText = implode(' ', $words);

    $chunks = $this->chunker->chunk($longText);

    expect(count($chunks))->toBeGreaterThan(1);
});

it('returns chunks with metadata', function () {
    $text = 'This is a test document for chunking. '.str_repeat('Hello world. ', 200);

    $result = $this->chunker->chunkWithMetadata($text);

    expect($result)->toBeArray();
    expect($result[0])->toHaveKeys(['content', 'chunk_index', 'token_count']);
    expect($result[0]['chunk_index'])->toBe(0);
    expect($result[1]['chunk_index'])->toBe(1);
});

it('counts tokens based on word count', function () {
    $text = 'one two three four';

    $tokens = $this->chunker->countTokens($text);

    expect($tokens)->toBeGreaterThanOrEqual(4);
});

it('handles text with special characters', function () {
    $text = 'Hello, world! This is a test (with special chars).';

    $chunks = $this->chunker->chunk($text);

    expect($chunks)->toHaveCount(1);
    expect($chunks[0])->toContain('Hello');
});

it('respects custom chunk size', function () {
    $chunker = new TextChunker(chunkSize: 128, overlap: 16);
    $words = array_fill(0, 500, 'word');
    $longText = implode(' ', $words);

    $chunks = $chunker->chunk($longText);

    expect(count($chunks))->toBeGreaterThan(1);
    expect(count($chunks))->toBeGreaterThan(
        count(app(TextChunker::class)->chunk($longText))
    );
});
