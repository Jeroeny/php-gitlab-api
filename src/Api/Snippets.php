<?php

declare(strict_types=1);

namespace Gitlab\Api;

final class Snippets extends ApiBase
{
    /**
     * @return mixed
     */
    public function all(int $project_id)
    {
        return $this->get($this->getProjectPath($project_id, 'snippets'));
    }

    /**
     * @return mixed
     */
    public function show(int $project_id, int $snippet_id)
    {
        return $this->get($this->getProjectPath($project_id, 'snippets/' . $this->encodePath((string)$snippet_id)));
    }

    /**
     * @return mixed
     */
    public function create(int $project_id, string $title, string $filename, string $code, string $visibility)
    {
        return $this->post($this->getProjectPath($project_id, 'snippets'), [
            'title'      => $title,
            'file_name'  => $filename,
            'code'       => $code,
            'visibility' => $visibility,
        ]);
    }

    /**
     * @param mixed[] $params
     *
     * @return mixed
     */
    public function update(int $project_id, int $snippet_id, array $params)
    {
        return $this->put($this->getProjectPath($project_id, 'snippets/' . $this->encodePath((string)$snippet_id)), $params);
    }

    public function content(int $project_id, int $snippet_id): string
    {
        return $this->get($this->getProjectPath($project_id, 'snippets/' . $this->encodePath((string)$snippet_id) . '/raw'));
    }

    /**
     * @return mixed
     */
    public function remove(int $project_id, int $snippet_id)
    {
        return $this->delete($this->getProjectPath($project_id, 'snippets/' . $this->encodePath((string)$snippet_id)));
    }

    /**
     * @return mixed
     */
    public function awardEmoji(int $project_id, int $snippet_id)
    {
        return $this->get($this->getProjectPath($project_id, 'snippets/' . $this->encodePath((string)$snippet_id) . '/award_emoji'));
    }
}
