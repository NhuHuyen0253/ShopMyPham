<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\RequestException;

class GhnService
{
    protected string $baseUrl;
    protected string $token;
    protected string $shopId;
    protected string $fromDistrictId;
    protected string $fromWardCode;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('services.ghn.base_url'), '/');
        $this->token = (string) config('services.ghn.token');
        $this->shopId = (string) config('services.ghn.shop_id');
        $this->fromDistrictId = (string) config('services.ghn.from_district_id');
        $this->fromWardCode = (string) config('services.ghn.from_ward_code');
    }

    protected function client()
    {
        return Http::timeout(20)
            ->acceptJson()
            ->withHeaders([
                'Token' => $this->token,
                'ShopId' => $this->shopId,
                'Content-Type' => 'application/json',
            ]);
    }

    public function getProvinces(): array
    {
        $res = $this->client()->get($this->baseUrl . '/master-data/province')->throw();
        return $res->json('data', []);
    }

    public function getDistricts(int $provinceId): array
    {
        $res = $this->client()->post($this->baseUrl . '/master-data/district', [
            'province_id' => $provinceId,
        ])->throw();

        return $res->json('data', []);
    }

    public function getWards(int $districtId): array
    {
        $res = $this->client()->post($this->baseUrl . '/master-data/ward', [
            'district_id' => $districtId,
        ])->throw();

        return $res->json('data', []);
    }

    public function getAvailableServices(int $toDistrictId): array
    {
        $res = $this->client()->post($this->baseUrl . '/v2/shipping-order/available-services', [
            'shop_id' => (int) $this->shopId,
            'from_district' => (int) $this->fromDistrictId,
            'to_district' => $toDistrictId,
        ])->throw();

        return $res->json('data', []);
    }

    public function calculateFee(array $payload): array
    {
        $res = $this->client()->post($this->baseUrl . '/v2/shipping-order/fee', $payload)->throw();
        return $res->json('data', []);
    }

    public function defaultFromDistrictId(): int
    {
        return (int) $this->fromDistrictId;
    }

    public function defaultFromWardCode(): string
    {
        return $this->fromWardCode;
    }

    public function shopId(): int
    {
        return (int) $this->shopId;
    }
}