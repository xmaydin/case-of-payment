<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\SubscriptionRequest;
use App\Http\Requests\UnsubscriptionRequest;
use App\Repository\Eloquent\PaymentServiceRepository;
use Illuminate\Support\Str;

class ServiceController extends Controller
{
    public function __construct(PaymentServiceRepository $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * Abonelik Başlatma.
     * Abone id generate et. Client'a gönder.
     * Her abone 1 aktif aboneliğe sahip olacak.
     *
     * @param SubscriptionRequest $request
     */
    public function subscription(SubscriptionRequest $request)
    {
        $user = $request->user();

        if (!$user)
            return $this->paymentService->sendError(message: 'Herhangi bir kullanıcı bulunamadı.', code: 400);

        if ($this->paymentService->checkStatus($user))
            return $this->sendError(message: 'Bu kullanıcıya ait aktif abonelik bulunmaktadır.', code: 400);

        $validated = collect($request->validated());

        // Gönderilecek zorunlu parametreler
        $parameters = $validated->merge([
            'platform' => 'web',
            'subscriberCountry' => 'US',
            'subscriberId' => Str::uuid(),
            'subscriberIpAddress' => $request->ip(),
            'subscriberEmail' => $request->user()->email,
            'subscriberPhoneNumber' => $request->user()->phone_number,
            'redirectUrl' => env('PAYMENT_CALLBACK_URL', 'http://zotlo.test/payment-callback')
        ]);

        return $this->paymentService->subscription($parameters, $request->user());
    }

    /**
     * Abonelik durum sorgulama
     *
     * @param $subscriberId
     * @return \Illuminate\Http\JsonResponse
     */
    public function subscriptionStatus($subscriberId)
    {
        $user = $this->paymentService->relationWhere('subscriptions', ['subscriber_id' => $subscriberId]);

        // Kullanıcı kontrol
        if (!$user)
            return $this->paymentService->sendError(message: 'Herhangi bir kullanıcı bulunamadı.', code: 400);

        // Abonelik Durum Sorgulama
        if (!$user->subscriptions->first())
            return $this->paymentService->sendError(message: 'Kullanıcı abonelik profili bulunamadı.');

        return $this->paymentService->subscriptionStatus($user->subscriptions->first());
    }

    /**
     * Abonelik iptal
     *
     * @param UnsubscriptionRequest $request
     * @return \Illuminate\Http\JsonResponse|void
     */
    public function unSubscription(UnsubscriptionRequest $request)
    {
        $user = $this->paymentService->relationFirstWhere('subscriptions', [
            'subscriber_id' => $request->subscriberId,
        ]);

        if (!$user)
            return $this->paymentService->sendError(message: 'Herhangi bir kullanıcı bulunamadı.', code: 400);

        if (!$user->subscriptions->first())
            return $this->paymentService->sendError(message: 'Kullanıcı abonelik profili bulunamadı.');

        if ('inactive' == $user->subscriptions->first()->status)
            return $this->paymentService->sendResponse(message: 'Aboneliğiniz başarıyla iptal edildi.', data: $user->subscriptions->first()->toArray());

        $validated = collect($request->validated());

        // Gönderilecek zorunlu parametreler
        $parameters = $validated->merge([
            'packageId' => $user->subscription->package
        ]);

        return $this->paymentService->unsubscription($parameters, $user);
    }

    /**
     * @param $subscriberId
     * @return \Illuminate\Http\JsonResponse|void
     */
    public function savedCardList($subscriberId)
    {
        $user = $this->paymentService->relationFirstWhere('subscriptions', [
            'subscriber_id' => $subscriberId,
        ]);

        if (!$user)
            return $this->paymentService->sendError(message: 'Herhangi bir kullanıcı bulunamadı.', code: 400);

        if (!$user->subscriptions->first())
            return $this->paymentService->sendError(message: 'Kullanıcı abonelik profili bulunamadı.');

        $query = ['subscriberId' => $user->subscriptions->first()->subscriber_id];

        return $this->paymentService->savedCardList($query);
    }
}
