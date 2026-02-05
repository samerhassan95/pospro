<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Business;
use App\Models\User;
use App\Library\Moyasar;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;

class MoyasarIntegrationTest extends TestCase
{
    use RefreshDatabase;

    private $business;
    private $user;
    private $moyasar;

    protected function setUp(): void
    {
        parent::setUp();
        
        // إنشاء بيانات اختبار
        $this->business = Business::factory()->create([
            'moyasar_setting' => json_encode([
                'publishable_key' => 'pk_test_vcFUHJGEzPBIBWkwUyOlUhXN',
                'secret_key' => 'sk_test_kovrMB0mupbQkIQUXyoUHgLy',
                'environment' => 'test'
            ])
        ]);

        $this->user = User::factory()->create([
            'business_id' => $this->business->id
        ]);

        $this->moyasar = new Moyasar();
    }

    /** @test */
    public function it_can_create_payment_for_sale()
    {
        // محاكاة استجابة ميسر الناجحة
        Http::fake([
            'api.moyasar.com/v1/payments' => Http::response([
                'id' => 'pay_test_123456',
                'status' => 'paid',
                'amount' => 10000,
                'currency' => 'SAR',
                'source' => [
                    'type' => 'creditcard',
                    'name' => 'Test User',
                    'number' => '4111'
                ]
            ], 200)
        ]);

        $this->actingAs($this->user);

        $response = $this->post(route('business.moyasar.process-payment'), [
            'payment_type' => 'sale',
            'reference_id' => 1,
            'amount' => 100.00,
            'currency' => 'SAR',
            'description' => 'Test Sale Payment'
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'payment_id' => 'pay_test_123456'
        ]);
    }

    /** @test */
    public function it_handles_failed_payments_gracefully()
    {
        Http::fake([
            'api.moyasar.com/v1/payments' => Http::response([
                'type' => 'validation_error',
                'message' => 'Your card was declined'
            ], 400)
        ]);

        $this->actingAs($this->user);

        $response = $this->post(route('business.moyasar.process-payment'), [
            'payment_type' => 'sale',
            'reference_id' => 1,
            'amount' => 100.00,
            'currency' => 'SAR'
        ]);

        $response->assertStatus(400);
        $response->assertJson([
            'success' => false,
            'message' => 'Your card was declined'
        ]);
    }

    /** @test */
    public function it_validates_moyasar_settings()
    {
        $this->actingAs($this->user);

        // اختبار حفظ إعدادات صحيحة
        $response = $this->post(route('business.moyasar.settings.store'), [
            'publishable_key' => 'pk_test_valid_key',
            'secret_key' => 'sk_test_valid_key',
            'environment' => 'test'
        ]);

        $response->assertStatus(200);
        
        // التحقق من حفظ الإعدادات في قاعدة البيانات
        $this->business->refresh();
        $settings = json_decode($this->business->moyasar_setting, true);
        
        $this->assertEquals('pk_test_valid_key', $settings['publishable_key']);
        $this->assertEquals('test', $settings['environment']);
    }

    /** @test */
    public function it_encrypts_sensitive_data()
    {
        $this->actingAs($this->user);

        $response = $this->post(route('business.moyasar.settings.store'), [
            'publishable_key' => 'pk_test_sensitive',
            'secret_key' => 'sk_test_sensitive',
            'environment' => 'test'
        ]);

        $this->business->refresh();
        $settings = json_decode($this->business->moyasar_setting, true);

        // التأكد من أن المفتاح السري مشفر
        $this->assertNotEquals('sk_test_sensitive', $settings['secret_key']);
        $this->assertStringContainsString('eyJ', $settings['secret_key']); // Base64 encoded
    }

    /** @test */
    public function it_can_verify_payment_status()
    {
        Http::fake([
            'api.moyasar.com/v1/payments/pay_test_123456' => Http::response([
                'id' => 'pay_test_123456',
                'status' => 'paid',
                'amount' => 10000,
                'currency' => 'SAR'
            ], 200)
        ]);

        $result = $this->moyasar->verifyPayment('pay_test_123456', $this->business);

        $this->assertTrue($result['success']);
        $this->assertEquals('paid', $result['data']['status']);
    }

    /** @test */
    public function it_handles_different_payment_types()
    {
        $paymentTypes = ['sale', 'purchase', 'due_collection'];

        foreach ($paymentTypes as $type) {
            Http::fake([
                'api.moyasar.com/v1/payments' => Http::response([
                    'id' => "pay_test_{$type}_123",
                    'status' => 'paid',
                    'amount' => 5000,
                    'currency' => 'SAR'
                ], 200)
            ]);

            $this->actingAs($this->user);

            $response = $this->post(route('business.moyasar.process-payment'), [
                'payment_type' => $type,
                'reference_id' => 1,
                'amount' => 50.00,
                'currency' => 'SAR'
            ]);

            $response->assertStatus(200);
            $this->assertStringContains($type, $response->json('payment_id'));
        }
    }

    /** @test */
    public function it_supports_multiple_currencies()
    {
        $currencies = ['SAR', 'USD', 'EUR', 'AED'];

        foreach ($currencies as $currency) {
            Http::fake([
                'api.moyasar.com/v1/payments' => Http::response([
                    'id' => 'pay_test_currency_123',
                    'status' => 'paid',
                    'amount' => 10000,
                    'currency' => $currency
                ], 200)
            ]);

            $this->actingAs($this->user);

            $response = $this->post(route('business.moyasar.process-payment'), [
                'payment_type' => 'sale',
                'reference_id' => 1,
                'amount' => 100.00,
                'currency' => $currency
            ]);

            $response->assertStatus(200);
        }
    }

    /** @test */
    public function it_logs_payment_activities()
    {
        Http::fake([
            'api.moyasar.com/v1/payments' => Http::response([
                'id' => 'pay_test_logging_123',
                'status' => 'paid',
                'amount' => 10000,
                'currency' => 'SAR'
            ], 200)
        ]);

        $this->actingAs($this->user);

        // تفعيل تسجيل السجلات
        \Log::shouldReceive('info')
            ->once()
            ->with('Moyasar payment created successfully', \Mockery::type('array'));

        $response = $this->post(route('business.moyasar.process-payment'), [
            'payment_type' => 'sale',
            'reference_id' => 1,
            'amount' => 100.00,
            'currency' => 'SAR'
        ]);

        $response->assertStatus(200);
    }
}