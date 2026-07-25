<?php

namespace App\Domain\AIEngine\Enums;

use Laravel\Ai\Enums\Lab;

enum AIProvider: string
{
    case OpenAI = 'openai';
    case Anthropic = 'anthropic';
    case Gemini = 'gemini';
    case Azure = 'azure';
    case DeepSeek = 'deepseek';
    case Groq = 'groq';
    case Mistral = 'mistral';
    case xAI = 'xai';
    case Ollama = 'ollama';
    case OpenRouter = 'openrouter';
    case Cohere = 'cohere';
    case Jina = 'jina';
    case VoyageAI = 'voyageai';
    case ElevenLabs = 'eleven';
    case Bedrock = 'bedrock';
    case OpenAiCompatible = 'openai-compatible';

    public function toLab(): Lab
    {
        return match ($this) {
            self::OpenAI => Lab::OpenAI,
            self::Anthropic => Lab::Anthropic,
            self::Gemini => Lab::Gemini,
            self::Azure => Lab::Azure,
            self::DeepSeek => Lab::DeepSeek,
            self::Groq => Lab::Groq,
            self::Mistral => Lab::Mistral,
            self::xAI => Lab::xAI,
            self::Ollama => Lab::Ollama,
            self::OpenRouter => Lab::OpenRouter,
            self::Cohere => Lab::Cohere,
            self::Jina => Lab::Jina,
            self::VoyageAI => Lab::VoyageAI,
            self::ElevenLabs => Lab::ElevenLabs,
            self::Bedrock => Lab::Bedrock,
            self::OpenAiCompatible => Lab::OpenAiCompatible,
        };
    }

    public function label(): string
    {
        return match ($this) {
            self::OpenAiCompatible => 'OpenAI Compatible',
            default => $this->name,
        };
    }
}
