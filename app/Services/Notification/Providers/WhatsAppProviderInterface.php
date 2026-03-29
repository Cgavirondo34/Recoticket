<?php

namespace App\Services\Notification\Providers;

interface WhatsAppProviderInterface
{
    /**
     * Send a WhatsApp message to a phone number.
     *
     * @param  string  $phone  E.164 format e.g. +541112345678
     * @param  string  $message  Plain text message body
     * @return array{success: bool, provider_message_id: ?string, error: ?string}
     */
    public function send(string $phone, string $message): array;
}
