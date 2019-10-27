<?php

declare(strict_types=1);

namespace Gitlab\Api;

use Psr\Http\Message\StreamInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use function array_diff;
use function is_array;

final class Jobs extends ApiBase
{
    public const SCOPE_CREATED  = 'created';
    public const SCOPE_PENDING  = 'pending';
    public const SCOPE_RUNNING  = 'running';
    public const SCOPE_FAILED   = 'failed';
    public const SCOPE_SUCCESS  = 'success';
    public const SCOPE_CANCELED = 'canceled';
    public const SCOPE_SKIPPED  = 'skipped';
    public const SCOPE_MANUAL   = 'manual';

    /**
     * @param int|string $project_id
     * @param mixed[]    $parameters scope      The scope of jobs to show, one or array of: created, pending, running, failed,
     *                               success, canceled, skipped, manual; showing all jobs if none provided.
     *
     * @return mixed
     */
    public function all($project_id, array $parameters = [])
    {
        $resolver = $this->createOptionsResolver();

        return $this->get('projects/' . $this->encodePath((string)$project_id) . '/jobs', $resolver->resolve($parameters));
    }

    /**
     * @param string[] $parameters Containing: scope The scope of jobs to show, one or array of: created, pending, running, failed,
     *                             success, canceled, skipped, manual; showing all jobs if none provided.
     *
     * @return mixed
     */
    public function pipelineJobs(int $project_id, int $pipeline_id, array $parameters = [])
    {
        $resolver = $this->createOptionsResolver();

        return $this->get(
            $this->getProjectPath($project_id, 'pipelines/') . $this->encodePath((string)$pipeline_id) . '/jobs',
            $resolver->resolve($parameters)
        );
    }

    /**
     * @param int|string $project_id
     *
     * @return mixed
     */
    public function show($project_id, int $job_id)
    {
        return $this->get('projects/' . $this->encodePath((string)$project_id) . '/jobs/' . $this->encodePath((string)$job_id));
    }

    /**
     * @param int|string $project_id
     */
    public function artifacts($project_id, int $job_id): StreamInterface
    {
        return $this->getAsResponse('projects/' . $this->encodePath((string)$project_id) . '/jobs/' . $this->encodePath((string)$job_id) . '/artifacts')->getBody();
    }

    /**
     * @param int|string $project_id
     */
    public function artifactsByRefName($project_id, string $ref_name, string $job_name): StreamInterface
    {
        return $this->getAsResponse('projects/' . $this->encodePath((string)$project_id) . '/jobs/artifacts/' . $this->encodePath((string)$ref_name) . '/download', [
            'job' => $this->encodePath((string)$job_name),
        ])->getBody();
    }

    public function artifactByRefName(
        int $project_id,
        string $ref_name,
        string $job_name,
        string $artifact_path
    ): StreamInterface {
        return $this->getAsResponse('projects/' . $this->encodePath((string)$project_id) . '/jobs/artifacts/' . $this->encodePath((string)$ref_name) . '/raw/' . $this->encodePath((string)$artifact_path), [
            'job' => $this->encodePath((string)$job_name),
        ])->getBody();
    }

    /**
     * @param int|string $project_id
     */
    public function trace($project_id, int $job_id): string
    {
        return $this->get('projects/' . $this->encodePath((string)$project_id) . '/jobs/' . $this->encodePath((string)$job_id) . '/trace');
    }

    /**
     * @param int|string $project_id
     *
     * @return mixed
     */
    public function cancel($project_id, int $job_id)
    {
        return $this->post('projects/' . $this->encodePath((string)$project_id) . '/jobs/' . $this->encodePath((string)$job_id) . '/cancel');
    }

    /**
     * @param int|string $project_id
     *
     * @return mixed
     */
    public function retry($project_id, int $job_id)
    {
        return $this->post('projects/' . $this->encodePath((string)$project_id) . '/jobs/' . $this->encodePath((string)$job_id) . '/retry');
    }

    /**
     * @param int|string $project_id
     *
     * @return mixed
     */
    public function erase($project_id, int $job_id)
    {
        return $this->post('projects/' . $this->encodePath((string)$project_id) . '/jobs/' . $this->encodePath((string)$job_id) . '/erase');
    }

    /**
     * @param int|string $project_id
     *
     * @return mixed
     */
    public function keepArtifacts($project_id, int $job_id)
    {
        return $this->post('projects/' . $this->encodePath((string)$project_id) . '/jobs/' . $this->encodePath((string)$job_id) . '/artifacts/keep');
    }

    /**
     * @param int|string $project_id
     *
     * @return mixed
     */
    public function play($project_id, int $job_id)
    {
        return $this->post('projects/' . $this->encodePath((string)$project_id) . '/jobs/' . $this->encodePath((string)$job_id) . '/play');
    }

    /**
     * {@inheritdoc}
     */
    protected function createOptionsResolver(): OptionsResolver
    {
        $allowedScopeValues = [
            self::SCOPE_CANCELED,
            self::SCOPE_CREATED,
            self::SCOPE_FAILED,
            self::SCOPE_MANUAL,
            self::SCOPE_PENDING,
            self::SCOPE_RUNNING,
            self::SCOPE_SKIPPED,
            self::SCOPE_SUCCESS,
        ];

        $resolver = parent::createOptionsResolver();
        $resolver->setDefined('scope')
            ->setAllowedTypes('scope', ['string', 'array'])
            ->setAllowedValues('scope', $allowedScopeValues)
            ->addAllowedValues('scope', static function ($value) use ($allowedScopeValues) {
                return is_array($value) && empty(array_diff($value, $allowedScopeValues));
            })
            ->setNormalizer('scope', static function (OptionsResolver $resolver, $value) {
                return (array)$value;
            });

        return $resolver;
    }
}
