<?php /** @noinspection PhpComposerExtensionStubsInspection */

declare(strict_types=1);

namespace Gitlab\Api;

use finfo;
use Gitlab\Client;
use Gitlab\HttpClient\Message\QueryStringBuilder;
use Gitlab\HttpClient\Message\ResponseMediator;
use Http\Discovery\Psr17FactoryDiscovery;
use Http\Message\MultipartStream\MultipartStreamBuilder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use const FILEINFO_MIME_TYPE;
use function basename;
use function class_exists;
use function count;
use function fopen;
use function rawurlencode;
use function str_replace;

abstract class ApiBase implements Api
{
    /** @var Client */
    protected $client;

    /** @var StreamFactoryInterface */
    private $streamFactory;

    public function __construct(Client $client, ?StreamFactoryInterface $streamFactory = null)
    {
        $this->client        = $client;
        $this->streamFactory = $streamFactory ?: Psr17FactoryDiscovery::findStreamFactory();
    }

    public function configure(): self
    {
        return $this;
    }

    /**
     * Performs a GET query and returns the response as a PSR-7 response object.
     *
     * @param mixed[] $parameters
     * @param mixed[] $requestHeaders
     */
    protected function getAsResponse(string $path, array $parameters = [], array $requestHeaders = []): ResponseInterface
    {
        $path = $this->preparePath($path, $parameters);

        return $this->client->getHttpClient()->get($path, $requestHeaders);
    }

    /**
     * @param mixed[] $parameters
     * @param mixed[] $requestHeaders
     *
     * @return mixed
     */
    protected function get(string $path, array $parameters = [], array $requestHeaders = [])
    {
        return ResponseMediator::getContent($this->getAsResponse($path, $parameters, $requestHeaders));
    }

    /**
     * @param mixed[] $parameters
     * @param mixed[] $requestHeaders
     * @param mixed[] $files
     *
     * @return mixed
     */
    protected function post(string $path, array $parameters = [], array $requestHeaders = [], array $files = [])
    {
        $path = $this->preparePath($path);

        $body = null;
        if ($files === [] && $parameters !== []) {
            $body                           = $this->prepareBody($parameters);
            $requestHeaders['Content-Type'] = 'application/x-www-form-urlencoded';
        }
        if ($files !== []) {
            $builder = new MultipartStreamBuilder($this->streamFactory);

            foreach ($parameters as $name => $value) {
                $builder->addResource($name, $value);
            }

            foreach ($files as $name => $file) {
                $builder->addResource($name, fopen($file, 'r'), [
                    'headers' => [
                        'Content-Type' => $this->guessContentType($file),
                    ],
                    'filename' => basename($file),
                ]);
            }

            $body                           = $builder->build();
            $requestHeaders['Content-Type'] = 'multipart/form-data; boundary=' . $builder->getBoundary();
        }

        $response = $this->client->getHttpClient()->post($path, $requestHeaders, $body);

        return ResponseMediator::getContent($response);
    }

    /**
     * @param mixed[] $parameters
     * @param mixed[] $requestHeaders
     * @param mixed[] $files
     *
     * @return mixed
     */
    protected function put(string $path, array $parameters = [], array $requestHeaders = [], array $files = [])
    {
        $path = $this->preparePath($path);

        $body = null;
        if ($files === [] && $parameters !== []) {
            $body                           = $this->prepareBody($parameters);
            $requestHeaders['Content-Type'] = 'application/x-www-form-urlencoded';
        }
        if ($files !== []) {
            $builder = new MultipartStreamBuilder($this->streamFactory);

            foreach ($parameters as $name => $value) {
                $builder->addResource($name, $value);
            }

            foreach ($files as $name => $file) {
                $builder->addResource($name, fopen($file, 'r'), [
                    'headers' => [
                        'Content-Type' => $this->guessContentType($file),
                    ],
                    'filename' => basename($file),
                ]);
            }

            $body                           = $builder->build();
            $requestHeaders['Content-Type'] = 'multipart/form-data; boundary=' . $builder->getBoundary();
        }

        $response = $this->client->getHttpClient()->put($path, $requestHeaders, $body);

        return ResponseMediator::getContent($response);
    }

    /**
     * @param mixed[] $parameters
     * @param mixed[] $requestHeaders
     *
     * @return mixed
     */
    protected function delete(string $path, array $parameters = [], array $requestHeaders = [])
    {
        $path = $this->preparePath($path, $parameters);

        $response = $this->client->getHttpClient()->delete($path, $requestHeaders);

        return ResponseMediator::getContent($response);
    }

    protected function getProjectPath(int $id, string $path): string
    {
        return 'projects/' . $this->encodePath((string)$id) . '/' . $path;
    }

    protected function getGroupPath(int $id, string $path): string
    {
        return 'groups/' . $this->encodePath((string)$id) . '/' . $path;
    }

    protected function encodePath(string $path): string
    {
        $path = rawurlencode($path);

        return str_replace('.', '%2E', $path);
    }

    /**
     * Create a new OptionsResolver with page and per_page options.
     */
    protected function createOptionsResolver(): OptionsResolver
    {
        $resolver = new OptionsResolver();
        $resolver->setDefined('page')
            ->setAllowedTypes('page', 'int')
            ->setAllowedValues('page', static function ($value) {
                return $value > 0;
            });
        $resolver->setDefined('per_page')
            ->setAllowedTypes('per_page', 'int')
            ->setAllowedValues('per_page', static function ($value) {
                return $value > 0 && $value <= 100;
            });

        return $resolver;
    }

    /**
     * @param mixed[] $parameters
     */
    private function prepareBody(array $parameters = []): StreamInterface
    {
        $raw = QueryStringBuilder::build($parameters);

        return $this->streamFactory->createStream($raw);
    }

    /**
     * @param mixed[] $parameters
     */
    private function preparePath(string $path, array $parameters = []): string
    {
        if (count($parameters) > 0) {
            $path .= '?' . QueryStringBuilder::build($parameters);
        }

        return $path;
    }

    private function guessContentType(string $file): string
    {
        if (! class_exists(finfo::class, false)) {
            return 'application/octet-stream';
        }
        $finfo = new finfo(FILEINFO_MIME_TYPE);

        return $finfo->file($file) ?: '';
    }
}
