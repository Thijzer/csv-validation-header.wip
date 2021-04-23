<?php

namespace Misery\Component\Common\Client;

use Misery\Component\Common\Generator\UrlGenerator;

class ApiClient
{
    private $handle;
    /** @var UrlGenerator */
    private $urlGenerator;
    /** @var AuthenticatedAccount */
    private $authenticatedAccount;

    public function __construct(string $domain)
    {
        $this->urlGenerator = new UrlGenerator($domain);
    }

    public function authorize(ApiClientAccountInterface $account): void
    {
        if (null === $this->handle) {
            $this->handle = \curl_init();
            //curl_setopt($this->handle, CURLOPT_SSL_VERIFYHOST, FALSE);
            //curl_setopt($this->handle, CURLOPT_SSL_VERIFYPEER, FALSE);
            $this->authenticatedAccount = $account->authorize($this);
        }
    }

    /**
     * A GET HTTP VERB
     */
    public function get(string $endpoint, array $params = []): self
    {
        if ($this->authenticatedAccount instanceof AuthenticatedAccount) {
            $this->authenticatedAccount->useToken($this);
        }

        \curl_setopt($this->handle, CURLOPT_URL, $this->generateUrl($endpoint, $params));

        return $this;
    }

    /**
     * A POST HTTP VERB
     * $postData is a structured entity array that will be encoded to json
     */
    public function post(string $endpoint, array $postData): self
    {
        if ($this->authenticatedAccount instanceof AuthenticatedAccount) {
            $this->authenticatedAccount->useToken($this);
        }

        \curl_setopt($this->handle, CURLOPT_URL, $endpoint);
        \curl_setopt($this->handle, CURLOPT_POST, true);
        \curl_setopt($this->handle, CURLOPT_POSTFIELDS, \json_encode($postData));

        return $this;
    }

    /**
     * HTTP PATCH VERB That supports a multi patch insert
     */
    public function multiPatch(string $endpoint, array $dataSet): self
    {
        if ($this->authenticatedAccount instanceof AuthenticatedAccount) {
            $this->authenticatedAccount->useToken($this);
        }

        $patchData = "";
        foreach($dataSet as $item) {
            $patchData .= json_encode($item)."\n";
        }

        \curl_setopt($this->handle, CURLOPT_URL, $endpoint);
        \curl_setopt($this->handle, CURLOPT_CUSTOMREQUEST, "PATCH");
        \curl_setopt($this->handle, CURLOPT_POSTFIELDS, $patchData);

        return $this;
    }

    /**
     * HTTP PATCH VERB
     */
    public function patch(string $endpoint, array $patchData): self
    {
        if ($this->authenticatedAccount instanceof AuthenticatedAccount) {
            $this->authenticatedAccount->useToken($this);
        }

        \curl_setopt($this->handle, CURLOPT_URL, $endpoint);
        \curl_setopt($this->handle, CURLOPT_CUSTOMREQUEST, "PATCH");
        \curl_setopt($this->handle, CURLOPT_POSTFIELDS, \json_encode($patchData));

        return $this;
    }

    /**
     * Set HTTP Headers
     */
    public function setHeaders(array $headerData): self
    {
        \curl_setopt($this->handle, CURLOPT_HTTPHEADER, $headerData);

        return $this;
    }

    /**
     * Returns a Usable API response
     */
    public function getResponse(): ApiResponse
    {
        \curl_setopt($this->handle, CURLOPT_HEADER, false);
        \curl_setopt($this->handle, CURLOPT_RETURNTRANSFER, true);

        // obtain response
        $content = \curl_exec($this->handle);
        if (!$content) {
            throw new \RuntimeException(curl_error($this->handle), curl_errno($this->handle));
        }

        return ApiResponse::create(
            \json_decode($content, true),
            \curl_getinfo($this->handle, CURLINFO_HTTP_CODE)
        );
    }

    public function clear(): void
    {
        \curl_setopt($this->handle, \CURLOPT_HEADERFUNCTION, null);
        \curl_setopt($this->handle, \CURLOPT_READFUNCTION, null);
        \curl_setopt($this->handle, \CURLOPT_WRITEFUNCTION, null);
        \curl_setopt($this->handle, \CURLOPT_PROGRESSFUNCTION, null);
        \curl_reset($this->handle);
    }

    public function close(): void
    {
        if ($this->handle) {
            $this->clear();
            \curl_close($this->handle);
            $this->handle = null;
        }
    }

    public function __destruct()
    {
        $this->close();
    }

    public function getUrlGenerator(): UrlGenerator
    {
        return $this->urlGenerator;
    }
}
