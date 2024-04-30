<?php

namespace Jmf\SpamEvaluation\Client;

use Jmf\SpamEvaluation\Shared\SpamEvaluationOutcome;
use Jmf\SpamEvaluation\Shared\SpamEvaluationRequest;
use Jmf\SpamEvaluation\Shared\SpamEvaluationResponse;
use Jmf\SpamEvaluation\Shared\SpamEvaluatorInterface;
use Override;
use Symfony\Contracts\HttpClient\HttpClientInterface;

readonly class SpamEvaluator implements SpamEvaluatorInterface
{
    public function __construct(
        private HttpClientInterface $httpClient,
        private string $baseUrl,
    ) {
    }

    #[Override]
    public function evaluate(SpamEvaluationRequest $request): SpamEvaluationResponse
    {
        $response = $this->httpClient->request(
            'POST',
            "{$this->baseUrl}/spam/evaluate",
            [
                'json' => [
                    'type'     => $request->getType()->value,
                    'language' => $request->getLanguage(),
                    'content'  => $request->getContent(),
                ],
            ]
        );

        $content = $response->toArray();

        return new SpamEvaluationResponse(
            $content['score'],
            SpamEvaluationOutcome::from($content['outcome']),
        );
    }
}
