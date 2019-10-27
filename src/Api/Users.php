<?php

declare(strict_types=1);

namespace Gitlab\Api;

use DateTimeInterface;
use Symfony\Component\OptionsResolver\Options;

final class Users extends ApiBase
{
    /**
     * @param mixed[] $parameters (
     *
     * @var string             $search         Search for user by email or username.
     * @var string             $username       Lookup for user by username.
     * @var bool               $external       Search for external users only.
     * @var string             $extern_uid     Lookup for users by external uid.
     * @var string             $provider       Lookup for users by provider.
     * @var \DateTimeInterface $created_before Return users created before the given time (inclusive).
     * @var \DateTimeInterface $created_after  Return users created after the given time (inclusive).
     * @var bool               $active         Return only active users. It does not support filtering inactive users.
     * @var bool               $blocked        Return only blocked users. It does not support filtering non-blocked users.
     * )
     *
     * @return mixed
     */
    public function all(array $parameters = [])
    {
        $resolver           = $this->createOptionsResolver();
        $datetimeNormalizer = static function (Options $resolver, DateTimeInterface $value) {
            return $value->format('c');
        };

        $resolver->setDefined('search');
        $resolver->setDefined('username');
        $resolver->setDefined('external')
            ->setAllowedTypes('external', 'bool');
        $resolver->setDefined('extern_uid');
        $resolver->setDefined('provider');
        $resolver->setDefined('created_before')
            ->setAllowedTypes('created_before', DateTimeInterface::class)
            ->setNormalizer('created_before', $datetimeNormalizer);
        $resolver->setDefined('created_after')
            ->setAllowedTypes('created_after', DateTimeInterface::class)
            ->setNormalizer('created_after', $datetimeNormalizer);
        $resolver->setDefined('active')
            ->setAllowedTypes('active', 'bool')
            ->setAllowedValues('active', true);
        $resolver->setDefined('blocked')
            ->setAllowedTypes('blocked', 'bool')
            ->setAllowedValues('blocked', true);

        return $this->get('users', $resolver->resolve($parameters));
    }

    /**
     * @return mixed
     */
    public function show(int $id)
    {
        return $this->get('users/' . $this->encodePath((string)$id));
    }

    /**
     * @return mixed
     */
    public function usersProjects(int $id)
    {
        return $this->get('users/' . $this->encodePath((string)$id) . '/projects');
    }

    /**
     * @return mixed
     */
    public function user()
    {
        return $this->get('user');
    }

    /**
     * @param mixed[] $params
     *
     * @return mixed
     */
    public function create(string $email, string $password, array $params = [])
    {
        $params['email']    = $email;
        $params['password'] = $password;

        return $this->post('users', $params);
    }

    /**
     * @param mixed[] $params
     * @param mixed[] $files
     *
     * @return mixed
     */
    public function update(int $id, array $params, array $files = [])
    {
        return $this->put('users/' . $this->encodePath((string)$id), $params, [], $files);
    }

    /**
     * @return mixed
     */
    public function remove(int $id)
    {
        return $this->delete('users/' . $this->encodePath((string)$id));
    }

    /**
     * @return mixed
     */
    public function block(int $id)
    {
        return $this->post('users/' . $this->encodePath((string)$id) . '/block');
    }

    /**
     * @return mixed
     */
    public function unblock(int $id)
    {
        return $this->post('users/' . $this->encodePath((string)$id) . '/unblock');
    }

    /**
     * @return mixed
     */
    public function session(string $emailOrUsername, string $password)
    {
        return $this->post('session', [
            'login' => $emailOrUsername,
            'email' => $emailOrUsername,
            'password' => $password,
        ]);
    }

    /**
     * @return mixed
     */
    public function login(string $email, string $password)
    {
        return $this->session($email, $password);
    }

    /**
     * @return mixed
     */
    public function me()
    {
        return $this->get('user');
    }

    /**
     * @return mixed
     */
    public function keys()
    {
        return $this->get('user/keys');
    }

    /**
     * @return mixed
     */
    public function key(int $id)
    {
        return $this->get('user/keys/' . $this->encodePath((string)$id));
    }

    /**
     * @return mixed
     */
    public function createKey(string $title, string $key)
    {
        return $this->post('user/keys', [
            'title' => $title,
            'key' => $key,
        ]);
    }

    /**
     * @return mixed
     */
    public function removeKey(int $id)
    {
        return $this->delete('user/keys/' . $this->encodePath((string)$id));
    }

    /**
     * @return mixed
     */
    public function userKeys(int $user_id)
    {
        return $this->get('users/' . $this->encodePath((string)$user_id) . '/keys');
    }

    /**
     * @return mixed
     */
    public function userKey(int $user_id, int $key_id)
    {
        return $this->get('users/' . $this->encodePath((string)$user_id) . '/keys/' . $this->encodePath((string)$key_id));
    }

    /**
     * @return mixed
     */
    public function createKeyForUser(int $user_id, string $title, string $key)
    {
        return $this->post('users/' . $this->encodePath((string)$user_id) . '/keys', [
            'title' => $title,
            'key' => $key,
        ]);
    }

    /**
     * @return mixed
     */
    public function removeUserKey(int $user_id, int $key_id)
    {
        return $this->delete('users/' . $this->encodePath((string)$user_id) . '/keys/' . $this->encodePath((string)$key_id));
    }

    /**
     * @return mixed
     */
    public function emails()
    {
        return $this->get('user/emails');
    }

    /**
     * @return mixed
     */
    public function email(int $id)
    {
        return $this->get('user/emails/' . $this->encodePath((string)$id));
    }

    /**
     * @return mixed
     */
    public function userEmails(int $user_id)
    {
        return $this->get('users/' . $this->encodePath((string)$user_id) . '/emails');
    }

    /**
     * @return mixed
     */
    public function createEmailForUser(int $user_id, string $email, bool $skip_confirmation = false)
    {
        return $this->post('users/' . $this->encodePath((string)$user_id) . '/emails', [
            'email' => $email,
            'skip_confirmation' => $skip_confirmation,
        ]);
    }

    /**
     * @return mixed
     */
    public function removeUserEmail(int $user_id, int $email_id)
    {
        return $this->delete('users/' . $this->encodePath((string)$user_id) . '/emails/' . $this->encodePath((string)$email_id));
    }

    /**
     * @param mixed[] $params
     *
     * @return mixed
     */
    public function userImpersonationTokens(int $user_id, array $params = [])
    {
        $resolver = $this->createOptionsResolver();

        $resolver->setDefined('state')
            ->setAllowedValues('state', ['all', 'active', 'inactive']);

        return $this->get('users/' . $this->encodePath((string)$user_id) . '/impersonation_tokens', $resolver->resolve($params));
    }

    /**
     * @return mixed
     */
    public function userImpersonationToken(int $user_id, int $impersonation_token_id)
    {
        return $this->get('users/' . $this->encodePath((string)$user_id) . '/impersonation_tokens/' . $this->encodePath((string)$impersonation_token_id));
    }

    /**
     * @param mixed[] $scopes
     * @param null    $expires_at
     *
     * @return mixed
     */
    public function createImpersonationToken(int $user_id, string $name, array $scopes, $expires_at = null)
    {
        return $this->post('users/' . $this->encodePath((string)$user_id) . '/impersonation_tokens', [
            'name' => $name,
            'scopes' => $scopes,
            'expires_at' => $expires_at,
        ]);
    }

    /**
     * @return mixed
     */
    public function removeImpersonationToken(int $user_id, int $impersonation_token_id)
    {
        return $this->delete('users/' . $this->encodePath((string)$user_id) . '/impersonation_tokens/' . $this->encodePath((string)$impersonation_token_id));
    }
}
