    <?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class DatabaseSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Model::unguard();

        $this->call('RolesTableSeeder');
	}
}

    class ShippingMethodTableSeeder extends Seeder
    {
        public function run()
        {
            DB::table('shipping_methods')->truncate();

            \SimpleOMS\Shipping_Method::create(['description' => 'AIR 21']);
            \SimpleOMS\Shipping_Method::create(['description' => 'LBC']);
            \SimpleOMS\Shipping_Method::create(['description' => 'By hand']);
        }
    }

    class EmployeeTableSeeder extends Seeder
    {
        public function run()
        {
            DB::table('employees')->truncate();

            \SimpleOMS\Employee::create([
                'first_name' => 'Jennifer',
                'middle_name' => 'Brooks',
                'last_name' => 'Lawrence',
                'title' => 'Ms',
                'birth_date' => '1992-09-04',
                'hire_date' => '2013-05-14',
                'address' => 'Diliman',
                'city' => 'Quezon',
                'region' => 'NCR',
                'postal_code' => null,
                'country' => 'Philippines',
                'home_phone' => '5690348',
                'photo' => null,
                'notes' => 'Best Employee of the Year 2014',
                'reports_to' => null
            ]);

            $this->command->info('Customer table seeded!');
        }
    }

    class CustomerTableSeeder extends Seeder
    {
        public function run()
        {
            DB::table('customers')->truncate();

            \SimpleOMS\Customer::create(['first_name' => 'El John', 'middle_name' => 'Mondala', 'last_name' => 'Perez', 'company_name' => 'Haystack Business Intelligence Solutions Inc.', 'address' => 'Norzagaray', 'city' => 'Bulacan', 'region' => 'Region 3', 'postal_code' => '3013', 'country' => 'Philippines', 'phone' => '1235678', 'fax' => null, 'title' => 'Mr']);
            \SimpleOMS\Customer::create(['first_name' => 'Jessa', 'middle_name' => 'Naldoza', 'last_name' => 'Acaso', 'company_name' => 'IBM Solutions Delivery Inc.', 'address' => 'Santolan', 'city' => 'Pasig', 'region' => 'NCR', 'postal_code' => '4052', 'country' => 'Philippines', 'phone' => '4859238', 'fax' => null, 'title' => 'Ms']);

            $this->command->info('Customer table seeded!');
        }
    }

    class ProductTableSeeder extends Seeder {

        public function run()
        {
            DB::table('products')->truncate();

            \SimpleOMS\Product::create(['category_id' => 1, 'name' => 'Regular Burger', 'unit_price' => '25', 'uom' => 'piece']);
            \SimpleOMS\Product::create(['category_id' => 1, 'name' => 'Cheese Burger', 'unit_price' => '28', 'uom' => 'piece']);
            \SimpleOMS\Product::create(['category_id' => 1, 'name' => 'Ham and Cheese Burger', 'unit_price' => '20', 'uom' => 'piece']);
            \SimpleOMS\Product::create(['category_id' => 1, 'name' => 'Bart Burger', 'unit_price' => '35', 'uom' => 'piece']);
            \SimpleOMS\Product::create(['category_id' => 1, 'name' => 'Ham and Egg Burger', 'unit_price' => '32', 'uom' => 'piece']);

            \SimpleOMS\Product::create(['category_id' => 2, 'name' => 'Coke', 'unit_price' => '25', 'uom' => 'can']);
            \SimpleOMS\Product::create(['category_id' => 2, 'name' => 'Sprite', 'unit_price' => '25', 'uom' => 'can']);
            \SimpleOMS\Product::create(['category_id' => 2, 'name' => 'Royal', 'unit_price' => '26', 'uom' => 'can']);
            \SimpleOMS\Product::create(['category_id' => 2, 'name' => 'Water', 'unit_price' => '15', 'uom' => 'bottle']);

            \SimpleOMS\Product::create(['category_id' => 3, 'name' => 'Regular French Fries', 'unit_price' => '10', 'uom' => 'pack']);
            \SimpleOMS\Product::create(['category_id' => 3, 'name' => 'Medium French Fries', 'unit_price' => '20', 'uom' => 'pack']);
            \SimpleOMS\Product::create(['category_id' => 3, 'name' => 'Large French Fries', 'unit_price' => '30', 'uom' => 'pack']);
            \SimpleOMS\Product::create(['category_id' => 3, 'name' => 'X-Large French Fries', 'unit_price' => '40', 'uom' => 'pack']);

            $this->command->info('Product category table seeded!');
        }

    }

    class ProductCategoryTableSeeder extends Seeder {

        public function run()
        {
            DB::table('product_category')->truncate();

            \SimpleOMS\Product_Category::create(['name' => 'Hamburgers', 'description' => 'Bread with patty in between']);
            \SimpleOMS\Product_Category::create(['name' => 'Drinks', 'description' => 'Something you drink']);
            \SimpleOMS\Product_Category::create(['name' => 'Side Dishes', 'description' => 'Small foods']);

            $this->command->info('Product category table seeded!');
        }

    }

    class RolesTableSeeder extends Seeder {

        public function run()
        {
            DB::table('roles')->truncate();

            \SimpleOMS\Role::create(['name' => 'Administrator', 'description' => 'Overall access']);
            \SimpleOMS\Role::create(['name' => 'Sales', 'description' => 'Creates orders']);
            \SimpleOMS\Role::create(['name' => 'Approver', 'description' => 'Approves or disapproves orders']);

            $this->command->info('Roles table seeded!');
        }

    }