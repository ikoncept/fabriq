<?php

namespace Ikoncept\Fabriq\Services;

use Aws\Credentials\Credentials;
use Aws\Lambda\LambdaClient;
use Ikoncept\Fabriq\Exceptions\LambdaException;

class LambdaService
{
    public static function call(string $function, array $payload): array
    {
        $accessKey = config('fabriq.aws_lambda_access_key');
        $secretKey = config('fabriq.aws_lambda_secret_key');
        $credentials = new Credentials($accessKey, $secretKey);

        $client = new LambdaClient([
            'credentials' => $credentials,
            'region' => 'eu-north-1',
            'version' => 'latest',
        ]);

        $result = $client->invoke([
            'FunctionName' => $function,
            'InvocationType' => 'RequestResponse',
            'Payload' => json_encode($payload),
        ]);

        $response = json_decode($result->get('Payload')->getContents(), true);

        if (array_key_exists('errorType', $response)) {
            logger()->error('LambdaError '.$response['errorMessage'], $response['trace']);

            throw new LambdaException($response['errorMessage']);
        }

        return $response;
    }

    /**
     * Build function.
     *
     * @param  string  $function
     * @param  array  $payload
     * @return mixed
     */
    public static function callAsync(string $function, array $payload)
    {
        $accessKey = config('fabriq.aws_lambda_access_key');
        $secretKey = config('fabriq.aws_lambda_secret_key');
        $credentials = new Credentials($accessKey, $secretKey);

        $credentials = new Credentials($accessKey, $secretKey);

        $client = new LambdaClient([
            'credentials' => $credentials,
            'region' => 'eu-north-1',
            'version' => 'latest',
        ]);

        return $client->invokeAsync([
            'FunctionName' => $function,
            'InvocationType' => 'RequestResponse',
            'Payload' => json_encode($payload),
        ]);
    }
}
