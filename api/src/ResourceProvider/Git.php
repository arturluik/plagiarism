<?php

namespace eu\luige\plagiarism\resourceprovider;

use eu\luige\plagiarismresources\File;
use eu\luige\plagiarismresources\Resource;
use GitWrapper\GitWrapper;

class Git extends ResourceProvider
{

    /** @var  string */
    private $tempFolder;

    /**
     * Validate request payload. Make sure all parameters exist.
     * If something is wrong, return error message
     * @param string $payload
     * @return bool
     */
    public function validatePayload(string $payload)
    {
        // TODO: Implement validatePayload() method.
    }

    /**
     * Get ResourceProvider name
     * (displayed in UI)
     * @return string
     */
    public function getName()
    {
        return 'GIT-1.0';
    }

    /**
     * Fetch all resources
     *
     * @param $payload
     * @return Resource[]
     */
    public function getResources($payload)
    {
        $this->cloneAll($payload);
        return $this->traverse($this->getTempFolder());
    }

    public function traverse($path)
    {
        $resources = [];
        if (is_dir($path)) {
            foreach (array_diff(scandir($path), ['.', '..', '.git']) as $file) {
                $resources = array_merge($resources, $this->traverse($path . '/' . $file));
            }
        } else if (is_file($path)) {
            $resources[] = new File($path);
        }
        return $resources;
    }


    public function cloneAll($payload)
    {
        // Support only one git url without array
        if (!is_array($payload['clone'])) {
            $payload['clone'] = [$payload['clone']];
        }

        foreach ($payload['clone'] as $repository) {
            $toDir = $this->getTempFolder() . '/' . str_replace('.git', '', basename($repository));
            switch (mb_strtolower($payload['authMethod'])) {
                case 'password':
                    $this->cloneRepositoryWithPassword($repository, $payload['username'], $payload['password'], $toDir);
                    break;
                case 'noauth':
                    $this->cloneRepositoryWithoutAuthentication($repository, $toDir);
                    break;
                case 'privateKey':
                    $this->cloneRepositoryWithSsh($repository, $payload['privateKey'], $toDir);
                    break;
                default:
                    $this->logger->error("Unknown authMethod", ['payload' => $payload]);
            }
        }
    }

    private function getTempFolder()
    {
        if (!$this->tempFolder) {
            $this->tempFolder = $this->config['temp_folder'] . '/' . uniqid('git_');
            mkdir($this->tempFolder, 0777, true);
        }
        return $this->tempFolder;
    }

    private function cloneRepositoryWithSsh($repository, $privateKey, $toDir)
    {
        try {
            $privateKeyFile = $this->config['temp_folder'] . '/' . uniqid('key_');
            file_put_contents($privateKeyFile, $privateKey);
            $gitWrapper = new GitWrapper();
            $gitWrapper->setPrivateKey($privateKeyFile);
            $gitWrapper->cloneRepository($repository, $toDir);
        } catch (\Exception $e) {
            $this->logger->error("Git clone failed for repository $repository", ['error' => $e]);
        } finally {
            unlink($privateKeyFile);
        }
    }

    private function cloneRepositoryWithoutAuthentication($repository, $toDir)
    {
        $gitWrapper = new GitWrapper();
        $gitWrapper->cloneRepository($repository, $toDir);
    }

    private function cloneRepositoryWithPassword($repository, $username, $password, $toDir)
    {
        $gitWrapper = new GitWrapper();
        $gitWrapper->cloneRepository($this->addCredentialsToGitUrl($username, $password, $repository), $toDir);
    }

    private function addCredentialsToGitUrl($username, $password, $gitUrl)
    {
        $domain = parse_url($gitUrl, PHP_URL_HOST);
        return str_replace($domain, urlencode($username) . ':' . urlencode($password) . "@$domain", $gitUrl);
    }
}