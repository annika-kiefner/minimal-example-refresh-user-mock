<?php

namespace App\Security;

class ExternalApiClient
{
    public function requestForUserRefresh(User $user): User
    {
        /**
         * Here would be some request to an external api, that we do not want to perform in tests.
         * By mocking the ExternalApiClient, this method should really never be called as-is!
         */
        throw new \Exception('Oops, the actual ExternalApiClient was called during testing!
            We did not want to allow this!'
        );
    }
}