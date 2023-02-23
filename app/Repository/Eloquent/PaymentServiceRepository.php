<?php

namespace App\Repository\Eloquent;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use \App\Services\Client as ApiClient;
use Illuminate\Support\Facades\Log;

class PaymentServiceRepository extends BaseRepository
{
    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function subscription($params, User $user): JsonResponse
    {
        // Abonelik başlatma
        $client = new ApiClient();

        $response = $client->setData($params)->subscriptionRequest();

        if (!$response['status'])
            return $this->sendError(message: $response['message']['meta']['errorMessage']);

        try {
            $subscription = $user->subscription()->create([
                'user_id' => $user->id,
                'subscriber_id' => $response['data']['result']['profile']['subscriberId'],
                'status' => $response['data']['result']['profile']['realStatus'],
                'package' => $response['data']['result']['profile']['package'],
                'expire_date' => $response['data']['result']['profile']['expireDate'],
                'renewal_date' => $response['data']['result']['profile']['expireDate']
            ]);

            return $this->sendResponse(message: 'Ödeme işlemi başarılı', data: ['result' => $subscription]);
        } catch (\Exception $exception) {

            Log::critical('An error has occurred when create payment', (array)$exception);

            return $this->sendError(message: 'Ödeme işlemi sırasında bir hata oluştu.', code: 500);
        }
    }

    /**
     * @param $subscriberId
     * @return JsonResponse
     */
    public function subscriptionStatus($sub)
    {
        // Abonelik başlatma
        $client = new ApiClient();
        $params = [
            'subscriberId' => $sub->subscriber_id,
            'packageId' => $sub->package
        ];

        $response = $client->setData($params)->getSubscriptionProfile();

        if (!$response['status'])
            return $this->sendError(message: $response['message']['meta']['errorMessage']);

        try {
            if ($sub->status != $response['data']['result']['profile']['realStatus']) {
                $sub->status = 'inactive';
                $sub->cancellation_date = $response['data']['result']['profile']['cancellation']['date'];
            }else{
                $sub->status = 'active';
                $sub->cancellation_date = null;
            }

            $sub->expire_date = $response['data']['result']['profile']['expireDate'];
            $sub->renewal_date = $response['data']['result']['profile']['renewalDate'];

            $sub->save();

        } catch (\Exception $exception) {
            Log::critical('An error has occurred when create payment', (array)$exception);

            return $this->sendError(message: 'Abonelik durum güncelleme işlemi sırasında bir hata oluştu', code: 500);
        }

        return $this->sendResponse(message: 'Güncel abonelik durumu.', data: $sub->toArray());
    }

    /**
     * @param $params
     * @param User $user
     * @return JsonResponse|void
     */
    public function unsubscription($params, User $user)
    {
        // Aktif abonelik var mı?
        if (!$this->checkStatus($user))
            return $this->sendError(message: 'Kullanıcı abonelik profili bulunamadı.');

        // Abonelik başlatma
        $client = new ApiClient();

        $response = $client->setData($params)->unsubscriptionRequest();

        if (!$response['status'])
            return $this->sendError(message: $response['message']['meta']['errorMessage']);

        try {
            $item = $user->subscriptions->first();
            $item->status = 'inactive';
            $item->cancellation_date = $response['data']['result']['profile']['cancellation']['date'];

            if (isset($params['force']))
                $item->expire_date = $response['data']['result']['profile']['expireDate'];

            $item->save();

            return $this->sendResponse(message: $response['data']['result']['cancellationStatus']['message'], data: $item->toArray());

        } catch (\Exception $exception) {

            Log::critical('An error has occurred when create payment', (array)$exception);

            return $this->sendError(message: 'Abonelik iptal işlemi sırasında bir hata oluştu', code: 500);
        }
    }

    public function savedCardList($query)
    {
        $client = new ApiClient();

        $response = $client->setData($query)->savedCardListRequest();

        if (!$response['status'])
            return $this->sendError(message: $response['message']['meta']['errorMessage']);

        return $this->sendResponse(message: 'Kayıtlı kart listesi.', data: $response['data']['result']['cardList']);
    }

    /**
     * @param User $user
     * @return bool
     */
    public function checkStatus(User $user)
    {
        if ('inactive' == $user->subscription_status['status'])
            return false;

        return true;
    }
}
