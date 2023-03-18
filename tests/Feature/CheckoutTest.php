<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Enums\PaymentMethod;

class CheckoutTest extends TestCase
{
    use RefreshDatabase;
    /**
     * POST 401.
     * User must be logged in.
     */
    public function test_checkout_1():void
    {
        $this->postJson('/api/checkout', [])
        ->assertStatus(401);
    }
    /**
     * POST 422.
     * Specific payload required.
     */
    public function test_checkout_2():void
    {
        $u = User::factory()->create();

        $this->actingAs($u);
        $this->postJson('/api/checkout', [])
        ->assertStatus(422)
        ->assertInvalid(['email', 'full-name', 'tel', 'cart-id', 'payment-method']);
    }
    /**
     * POST 422.
     * Invalid payload provided.
     */
    public function test_checkout_3():void
    {
        $u = User::factory()->create();

        $this->actingAs($u);
        $this->postJson('/api/checkout', [
            'email'=>'foo@baz.azaza',
            'full-name'=>'F00 1st',
            'tel'=>'kjsdfk3234',
            'cart-id'=>'foo',
            'payment-method'=>'azaza',
        ])
        ->assertStatus(422)
        ->assertInvalid(['email', /*'full-name',*/ 'tel', 'cart-id', 'payment-method']);
    }
    /**
     * POST 201.
     * Payload OK.
     */
    public function test_checkout_4():void
    {
        $u = User::factory()->create();

        $this->actingAs($u);
        $this->postJson('/api/checkout', [
            'email'=>'test@gmail.com',
            'full-name'=>'Baz Fooman',
            'tel'=>'2101111111',
            'cart-id'=>'1',
            'payment-method'=>PaymentMethod::OnDelivery->value,
        ])
        ->assertStatus(201)
        ->assertValid(['email', 'full-name', 'tel', 'cart-id', 'payment-method']);
    }
    /**
     * POST 422.
     * Card number is required when payment method is Card.
     */
    public function test_checkout_5():void
    {
        $u = User::factory()->create();

        $this->actingAs($u);
        $this->postJson('/api/checkout', [
            'email'=>'test@gmail.com',
            'full-name'=>'Baz Fooman',
            'tel'=>'2101111111',
            'cart-id'=>'1',
            'payment-method'=>PaymentMethod::Card->value,
        ])
        ->assertStatus(422)
        ->assertValid(['email', 'full-name', 'tel', 'cart-id', 'payment-method'])
        ->assertInvalid(['card-number']);
    }
    /**
     * POST 422.
     * Card is invalid.
     */
    public function test_checkout_6():void
    {
        $u = User::factory()->create();

        $this->actingAs($u);
        $this->postJson('/api/checkout', [
            'email'=>'test@gmail.com',
            'full-name'=>'Baz Fooman',
            'tel'=>'2101111111',
            'cart-id'=>'1',
            'payment-method'=>PaymentMethod::Card->value,
            'card-number'=>'4242 4242 424',
            'cvv'=>'1234',
            'card-expiration-date'=>'13/10',
        ])
        ->assertStatus(422)
        ->assertValid(['email', 'full-name', 'tel', 'cart-id', 'payment-method'])
        ->assertInvalid(['card-number', 'cvv', 'card-expiration-date']);
    }
    /**
     * POST 422.
     * Expiration date invalid.
     */
    public function test_checkout_7():void
    {
        $u = User::factory()->create();

        $this->actingAs($u);
        $this->postJson('/api/checkout', [
            'email'=>'test@gmail.com',
            'full-name'=>'Baz Fooman',
            'tel'=>'2101111111',
            'cart-id'=>'1',
            'payment-method'=>PaymentMethod::Card->value,
            'card-number'=>'4242 4242 4242 4242',
            'cvv'=>'123',
            'card-expiration-date'=>'12/21',
        ])
        ->assertStatus(422)
        ->assertValid([
            'email', 
            'full-name', 
            'tel', 
            'cart-id', 
            'payment-method', 
            'card-number', 
            'cvv'
        ])
        ->assertInvalid(['card-expiration-date']);
    }
    /**
     * POST 201.
     * Card is valid.
     */
    public function test_checkout_8():void
    {
        $u = User::factory()->create();

        $this->actingAs($u);
        $this->postJson('/api/checkout', [
            'email'=>'test@gmail.com',
            'full-name'=>'Baz Fooman',
            'tel'=>'2101111111',
            'cart-id'=>'1',
            'payment-method'=>PaymentMethod::Card->value,
            'card-number'=>'4242 4242 4242 4242',
            'cvv'=>'123',
            'card-expiration-date'=>'12/99',
        ])
        ->assertStatus(201)
        ->assertValid([
            'email', 
            'full-name', 
            'tel', 
            'cart-id', 
            'payment-method', 
            'card-number', 
            'cvv', 
            'card-expiration-date'
        ]);
    }
}
