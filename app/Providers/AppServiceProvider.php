<?php

namespace App\Providers;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        parent::register();

        $this->registerRequestSignatureValidation();
    }

    /**
     * Register the "hasValidSignature" macro on the request.
     *
     * @return void
     */
    public function registerRequestSignatureValidation()
    {
        /**
         * Determine if the signature from the given request matches the URL.
         *
         * @param Request $request
         * @param  bool  $absolute
         * @return bool
         */
        Request::macro('hasCorrectSignature', function ($absolute = true) {
            /**
             * @var $this Request
             */
            $url = $absolute ? $this->url() : '/'.$this->path();

            $original = rtrim($url.'?'.Arr::query(
                    Arr::except($this->query(), 'signature')
                ), '?');

            // Here we allow the user to validate the hash values for a different URL than the current.
            if($this->filled('validate_for_path')) {
                $original = str_replace(
                    $this->path(),
                    ltrim($this->input('validate_for_path'), '/'),
                    $original
                );
            }

            $key = $this->input('id', null);

            $signature = hash_hmac('sha256', $original, $key);

            return hash_equals($signature, (string) $this->query('signature', ''));
        });

        /**
         * Determine if the expires timestamp from the given request is not from the past.
         *
         * @param Request $request
         * @return bool
         */
        Request::macro('signatureHasNotExpired', function () {
            /**
             * @var $this Request
             */
            $expires = $this->query('expires');

            return ! ($expires && Carbon::now()->getTimestamp() > $expires);
        });

        /**
         * Verify the Signature on the URL is valid
         */
        Request::macro('hasValidSignature', function ($absolute = true) {
            /**
             * @var $this Request
             */
            return $this->hasCorrectSignature($this, $absolute)
                && $this->signatureHasNotExpired($this);
        });
    }
}
