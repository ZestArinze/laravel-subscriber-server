<?php

namespace App\Utils;

class SecurityUtils {

    /**
     * @param string $mac HMAC
     */
    public static function isValidPublisherHashMac($mac) {
        return $mac === SecurityUtils::getPublisherHashMac();
    }

    /**
     * get the MMAC for publisher
     */
    public static function getPublisherHashMac(): string {
        
        return base64_encode(hash_hmac(
            'sha256', 
            config('webhook.publisher_client_id'), 
            config('webhook.publisher_client_secret'), 
            true
        ));    
    }
}