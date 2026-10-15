<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminProductManagementTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->admin()->create();
    }

    public function test_admin_can_view_product_list(): void
    {
        Product::factory()->count(2)->create();

        $this->actingAs($this->admin())
            ->get(route('admin.products.index'))
            ->assertOk()
            ->assertViewHas('products');
    }

    public function test_admin_can_create_product(): void
    {
        $this->actingAs($this->admin())
            ->post(route('admin.products.store'), [
                'name' => 'Risol Mayo Spesial',
                'category_name' => 'Cemilan',
                'price' => 12000,
                'description' => 'Enak banget',
                'stock' => 30,
                'min_stock' => 5,
            ])
            ->assertRedirect(route('admin.products.index'));

        $this->assertDatabaseHas('products', [
            'name' => 'Risol Mayo Spesial',
            'category' => 'CEMILAN', // dinormalisasi dari nama display "Cemilan"
            'price' => 12000,
            'stock' => 30,
        ]);
    }

    public function test_new_product_is_unavailable_when_stock_is_zero(): void
    {
        $this->actingAs($this->admin())->post(route('admin.products.store'), [
            'name' => 'Stok Kosong',
            'category_name' => 'CEMILAN',
            'price' => 10000,
            'stock' => 0,
        ]);

        $this->assertDatabaseHas('products', ['name' => 'Stok Kosong', 'available' => false]);
    }

    public function test_create_product_validates_input(): void
    {
        $this->actingAs($this->admin())
            ->post(route('admin.products.store'), [])
            ->assertSessionHasErrors(['name', 'category_name', 'price']);
    }

    public function test_admin_can_update_product(): void
    {
        $product = Product::factory()->create(['name' => 'Nama Lama', 'price' => 10000]);

        $this->actingAs($this->admin())
            ->put(route('admin.products.update', $product->id), [
                'name' => 'Nama Baru',
                'price' => 15000,
            ])
            ->assertRedirect(route('admin.products.index'));

        $this->assertDatabaseHas('products', ['id' => $product->id, 'name' => 'Nama Baru', 'price' => 15000]);
    }

    public function test_admin_can_delete_product_without_orders(): void
    {
        $product = Product::factory()->create();

        $this->actingAs($this->admin())
            ->delete(route('admin.products.destroy', $product->id))
            ->assertRedirect(route('admin.products.index'));

        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }

    public function test_product_with_orders_cannot_be_deleted(): void
    {
        $product = Product::factory()->create();

        \App\Models\Order::create([
            'order_number' => 'ORD-TEST-001',
            'customer_name' => 'Budi',
            'customer_phone' => '08123',
            'shipping_address' => 'Jl. Test',
            'product_id' => $product->id,
            'product_name' => $product->name,
            'unit_price' => $product->price,
            'items_count' => 1,
            'shipping_fee' => 10000,
            'service_fee' => 1000,
            'total_amount' => $product->price + 11000,
        ]);

        $this->actingAs($this->admin())
            ->delete(route('admin.products.destroy', $product->id))
            ->assertRedirect(route('admin.products.index'))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('products', ['id' => $product->id]);
    }

    public function test_admin_can_upload_product_image(): void
    {
        Storage::fake('public');

        $this->actingAs($this->admin())->post(route('admin.products.store'), [
            'name' => 'Produk Bergambar',
            'category_name' => 'KUE',
            'price' => 10000,
            'image' => UploadedFile::fake()->image('kue.webp'),
        ]);

        $product = Product::where('name', 'Produk Bergambar')->firstOrFail();

        $this->assertNotNull($product->image_path);
        $this->assertStringStartsWith('storage/products/', $product->image_path);
        Storage::disk('public')->assertExists(str_replace('storage/', '', $product->image_path));
    }

    public function test_upload_rejects_files_over_2mb(): void
    {
        Storage::fake('public');

        $this->actingAs($this->admin())->post(route('admin.products.store'), [
            'name' => 'Gambar Gede',
            'category_name' => 'KUE',
            'price' => 10000,
            'image' => UploadedFile::fake()->image('besar.jpg')->size(3000),
        ])->assertSessionHasErrors('image');
    }

    public function test_guest_cannot_manage_products(): void
    {
        $this->post(route('admin.products.store'), [])->assertRedirect(route('login'));
    }

    public function test_list_can_be_filtered_by_search(): void
    {
        Product::factory()->create(['name' => 'Kopi Susu']);
        Product::factory()->create(['name' => 'Donat Kentang']);

        $response = $this->actingAs($this->admin())->get(route('admin.products.index', ['search' => 'Kopi']));

        $response->assertOk();
        $this->assertCount(1, $response->viewData('products')->items());
    }
}