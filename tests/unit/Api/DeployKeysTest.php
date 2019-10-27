<?php

declare(strict_types=1);

namespace Gitlab\Tests\Api;

use Gitlab\Api\DeployKeys;

final class DeployKeysTest extends TestCase
{
    /** @var DeployKeys */
    protected $api;

    /**
     * @param array<string, string|int> $expected
     *
     * @dataProvider getMultipleDeployKeysData
     */
    public function testShouldGetAllDeployKeys(array $expected): void
    {
        $this->setResponseBody($expected);
        $this->assertEquals($expected, $this->api->all(['page' => 2, 'per_page' => 5]));
    }

    public function getMultipleDeployKeysData(): array
    {
        return [
            [
                [
                    'id' => 1,
                    'title' => 'Public key',
                    'key' => 'ssh-rsa AAAAB3NzaC1yc2EAAAABJQAAAIEAiPWx6WM4lhHNedGfBpPJNPpZ7yKu+dnn1SJejgt4596k6YjzGGphH2TUxwKzxcKDKKezwkpfnxPkSMkuEspGRt/aZZ9wa++Oi7Qkr8prgHc4soW6NUlfDzpvZK2H5E7eQaSeP3SAwGmQKUFHCddNaP0L+hM7zhFNzjFvpaMgJw0=',
                    'created_at' => '2013-10-02T10:12:29Z',
                ],
            ],
            [
                [
                    'id' => 3,
                    'title' => 'Another Public key',
                    'key' => 'ssh-rsa AAAAB3NzaC1yc2EAAAABJQAAAIEAiPWx6WM4lhHNedGfBpPJNPpZ7yKu+dnn1SJejgt4596k6YjzGGphH2TUxwKzxcKDKKezwkpfnxPkSMkuEspGRt/aZZ9wa++Oi7Qkr8prgHc4soW6NUlfDzpvZK2H5E7eQaSeP3SAwGmQKUFHCddNaP0L+hM7zhFNzjFvpaMgJw0=',
                    'created_at' => '2013-10-02T11:12:29Z',
                ],
            ],
        ];
    }

    protected function getApiClass(): string
    {
        return DeployKeys::class;
    }
}
