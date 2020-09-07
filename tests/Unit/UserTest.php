<?php

namespace Tests\Unit;

use App\Tester;
use App\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Schema;
use Osiset\BasicShopifyAPI\ResponseAccess;
use Osiset\ShopifyApp\Contracts\ApiHelper as IApiHelper;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test */
    public function table_has_expected_columns()
    {
        $this->assertTrue(
            Schema::hasColumns((new User())->getTable(), [
                'id',
                'name',
                'email',
                'password',
                'email_verified_at',
                'shopify_grandfathered',
                'shopify_namespace',
                'shopify_freemium',
                'plan_id',
                'analytics_id',
                'chat_id',
                'shopify_gid',
                'domain',
                'shop_name',
                'contact_email',
                'city',
                'province',
                'province_code',
                'country',
                'country_code',
                'currency',
                'presentment_currencies',
                'money_format',
                'money_with_currency_format',
                'money_in_email_format',
                'money_with_currency_in_email_format',
                'timezone_offset',
                'iana_timezone',
                'shopify_plan_display_name',
                'shopify_partner',
                'shopify_plus',
                'shopify_scopes',
                'api_type',
                'api_key',
                'api_password',
                'api_secret',
                'created_at',
                'updated_at',
                'deleted_at',
            ])
        );
    }

    /**
     * @test
     */
    public function it_has_expected_columns_casted()
    {
        $expected = [
            'id' => 'int',
            'email_verified_at' => 'datetime',
            'presentment_currencies' => 'json',
            'shopify_scopes' => 'json',
            'shopify_partner' => 'boolean',
            'shopify_plus' => 'boolean',
        ];

        $actual = (new User())->getCasts();

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function it_has_expected_columns_casts_as_dates()
    {
        $expected = [
            'deleted_at',
            'created_at',
            'updated_at',
        ];

        $actual = (new User())->getDates();

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function it_has_expected_fillable_columns()
    {
        $expected = [];

        $actual = (new User())->getFillable();

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function it_has_expected_guarded_columns()
    {
        $expected = [];

        $actual = (new User())->getGuarded();

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function updateFromGraphApiResponse()
    {
        $shop = factory(User::class)->create([
            'contact_email' => 'some-email@example.com',
        ]);

        $email = $this->faker->safeEmail;

        $apiResponse = new ResponseAccess([
            'contactEmail' => $email,
        ]);

        $response = $shop->updateFromGraphApiResponse($apiResponse);

        $shop->refresh();

        $this->assertSame($response, 1);
        $this->assertSame($email, $shop->contact_email);
    }

    /**
     * @test
     */
    public function updateFromWebhook()
    {
        $shop = factory(User::class)->create([
            'contact_email' => 'some-email@example.com',
        ]);

        $email = $this->faker->safeEmail;

        $webhookResponse = (object) [
            'customer_email' => $email,
        ];

        $response = $shop->updateFromWebhook($webhookResponse);

        $shop->refresh();

        $this->assertSame($response, 1);
        $this->assertSame($email, $shop->contact_email);
    }

    /**
     * @test
     */
    public function isUsingPublicApp()
    {
        $shop = factory(User::class)->make([
            'api_type' => 'public',
        ]);

        $this->assertTrue($shop->isUsingPublicApp());

        $shop->api_type = 'some-invalid-value';
        $this->assertTrue($shop->isUsingPublicApp());

        $shop->api_type = 'private';
        $shop->api_key = 'value';
        $shop->api_secret = 'value';
        $shop->api_password = 'value';
        $this->assertFalse($shop->isUsingPublicApp());
    }

    /**
     * @test
     */
    public function isUsingPrivateApp()
    {
        $shop = factory(User::class)->make([
            'api_type' => 'private',
            'api_key' => 'some-key',
            'api_secret' => 'some-secret',
            'api_password' => 'some-password',
        ]);

        $this->assertTrue($shop->isUsingPrivateApp());

        $shop->api_key = null;
        $this->assertFalse($shop->isUsingPrivateApp());

        $shop->api_type = 'public';
        $this->assertFalse($shop->isUsingPrivateApp());
    }

    /**
     * @test
     */
    public function isUsingCustomApp()
    {
        $shop = factory(User::class)->make([
            'api_type' => 'custom',
            'api_key' => 'some-key',
            'api_secret' => 'some-secret',
        ]);

        $this->assertTrue($shop->isUsingCustomApp());

        $shop->api_key = null;
        $this->assertFalse($shop->isUsingCustomApp());

        $shop->api_type = 'public';
        $this->assertFalse($shop->isUsingCustomApp());
    }

    /**
     * @test
     */
    public function it_has_one_tester()
    {
        $shop = factory(User::class)->create([
            'name' => 'shop-1.myshopify.com',
        ]);

        $this->assertEquals(0, $shop->tester()->count());

        $tester = factory(Tester::class)->create([
            'shopify_domain' => $shop->name,
        ]);

        $this->assertEquals(1, $shop->tester()->count());
        $this->assertTrue($tester->is($shop->tester));
    }

    /**
     * @test
     */
    public function isTester_by_plan_name()
    {
        $shop1 = factory(User::class)->create([
            'shopify_plan_display_name' => 'plan1',
        ]);

        $shop2 = factory(User::class)->create([
            'shopify_plan_display_name' => 'plan2',
        ]);

        config()->set('shark.tester_plans', ['plan1', 'plan3']);

        $this->assertTrue($shop1->isTester());
        $this->assertFalse($shop2->isTester());

        config()->set('shark.tester_plans', ['plan2', 'plan3']);

        $this->assertFalse($shop1->isTester());
        $this->assertTrue($shop2->isTester());
    }

    /**
     * @test
     */
    public function isTester_by_shopify_domain_in_config()
    {
        $shop1 = factory(User::class)->create([
            'name' => 'my-shop1.myshopify.com',
        ]);

        $shop2 = factory(User::class)->create([
            'name' => 'my-test-shop.myshopify.com',
        ]);

        config()->set('shark.tester_shops', ['my-test-shop.myshopify.com']);

        $this->assertFalse($shop1->isTester());
        $this->assertTrue($shop2->isTester());

        config()->set('shark.tester_shops', ['my-shop1.myshopify.com']);

        $this->assertTrue($shop1->isTester());
        $this->assertFalse($shop2->isTester());
    }

    /**
     * @test
     */
    public function isTester_by_shopify_partner_as_test()
    {
        $shop1 = factory(User::class)->create([
            'shopify_partner' => true,
        ]);

        $shop2 = factory(User::class)->create([
            'shopify_partner' => false,
        ]);

        config()->set('shark.shopify_partner_as_tester', true);

        $this->assertTrue($shop1->isTester());
        $this->assertFalse($shop2->isTester());

        config()->set('shark.shopify_partner_as_tester', false);

        $this->assertFalse($shop1->isTester());
        $this->assertFalse($shop2->isTester());
    }

    /**
     * @test
     */
    public function isTester_by_record_in_testers_table()
    {
        $shop1 = factory(User::class)->create([
            'name' => 'shop1.myshopify.com',
        ]);

        $shop2 = factory(User::class)->create([
            'name' => 'shop2.myshopify.com',
        ]);

        $shop3 = factory(User::class)->create([
            'name' => 'shop3.myshopify.com',
        ]);

        $this->assertFalse($shop1->isTester());
        $this->assertFalse($shop2->isTester());
        $this->assertFalse($shop3->isTester());

        // Create tester record
        factory(Tester::class)->create([
            'shopify_domain' => 'shop1.myshopify.com',
        ]);

        factory(Tester::class)->create([
            'shopify_domain' => 'shop2.myshopify.com',
            'expires_at' => Carbon::tomorrow(),
        ]);

        factory(Tester::class)->create([
            'shopify_domain' => 'shop3.myshopify.com',
            'expires_at' => Carbon::yesterday(),
        ]);

        $this->assertTrue($shop1->isTester());
        $this->assertTrue($shop2->isTester());
        $this->assertFalse($shop3->isTester());
    }

    /**
     * @test
     */
    public function apiHelper()
    {
        $publicShop = factory(User::class)->make();
        $this->assertInstanceOf(IApiHelper::class, $publicShop->apiHelper());

        //
        $privateAppShop = factory(User::class)->make([
            'api_type' => 'private',
            'api_key' => 'api-key',
            'api_secret' => 'api-secret',
            'api_password' => 'api-password',
        ]);

        $privateAppShopApiHelper = $privateAppShop->apiHelper();
        $privateAppShopApiHelperOptions = $privateAppShopApiHelper->getApi()->getOptions();
        $this->assertInstanceOf(IApiHelper::class, $privateAppShopApiHelper);
        $this->assertSame('api-key', $privateAppShopApiHelperOptions->getApiKey());
        $this->assertSame('api-secret', $privateAppShopApiHelperOptions->getApiSecret());
        $this->assertSame('api-password', $privateAppShopApiHelperOptions->getApiPassword());
        $this->assertTrue($privateAppShopApiHelperOptions->isPrivate());

        //
        $customAppShop = factory(User::class)->make([
            'api_type' => 'custom',
            'api_key' => 'api-key',
            'api_secret' => 'api-secret',
        ]);

        $customAppShopApiHelper = $customAppShop->apiHelper();
        $customAppShopApiHelperOptions = $customAppShopApiHelper->getApi()->getOptions();
        $this->assertInstanceOf(IApiHelper::class, $customAppShopApiHelper);
        $this->assertSame('api-key', $customAppShopApiHelperOptions->getApiKey());
        $this->assertSame('api-secret', $customAppShopApiHelperOptions->getApiSecret());
        $this->assertTrue($customAppShopApiHelperOptions->isPublic());
    }
}
