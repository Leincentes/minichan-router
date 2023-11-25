<?php
declare(strict_types=1);
namespace MinichanRouter\Router\Interfaces;

interface IServices {
    // public function getDocumentRoot(): string;

    // public function getRemoteAddr(): string;

    // public function getRemotePort(): int;

    // public function getServerSoftware(): string;

    // public function getServerProtocol(): string;

    // public function getServerName(): string;

    public function getServerPort(): string;

    // public function getRequestUri(): string;

    public function getRequestMethod(): string;

    // public function getScriptName(): string;

    // public function getScriptFilename(): string;

    // public function getPathInfo(): string;

    // public function getPhpSelf(): string;

    public function getHttpHost(): string;

    // public function getHttpConnection(): string;

    // public function getHttpCacheControl(): string;

    // public function getHttpSecChUa(): string;

    // public function getHttpSecChUaMobile(): string;

    // public function getHttpSecChUaPlatform(): string;

    // public function getHttpUpgradeInsecureRequests(): string;

    // public function getHttpUserAgent(): string;

    // public function getHttpAccept(): string;

    // public function getHttpSecFetchSite(): string;

    // public function getHttpSecFetchMode(): string;

    // public function getHttpSecFetchUser(): string;

    // public function getHttpSecFetchDest(): string;

    // public function getHttpAcceptEncoding(): string;

    // public function getHttpAcceptLanguage(): string;

    // public function getRequestTimeFloat(): float;

    // public function getRequestTime(): int;
}