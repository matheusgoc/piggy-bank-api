<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    private $groups = [
        'Income' => [
            'Paycheck',
            'Investment',
            'Returned Purchase',
            'Bonus',
            'Interest Income',
            'Reimbursement',
            'Rental Income'
        ],
        'Auto and Transport' => [
            'Transport',
            'Vehicle',
            'Car',
            'Cars',
            'Truck',
            'Gas',
            'Boat',
            'Mileage',
            'Car Repairs',
            'Car Insurance',
            'Car Licensing',
            'Vehicle Repairs',
            'Vehicle Insurance',
            'Vehicle Licensing',
            'Truck Repairs',
            'Truck Insurance',
            'Truck Licensing',
            'Boat Repairs',
            'Boat Insurance',
            'Boat Licensing',
            'Service and Auto Parts',
            'Service',
            'Auto Parts',
            'Auto Payment',
            'Auto Insurance',
            'Auto Parking',
        ],
        'Education' => [
            'Education',
            'Training',
            'Tuition',
            'Student Loan',
            'Loan',
            'Books and Supplies',
            'Books',
        ],
        'Insurance' => [
            'Accident Insurance',
            'Health Insurance',
            'Life Insurance',
            'Business Insurance',
            'Truck Insurance',
            'Car Insurance',
            'Boat Insurance',
            'Auto Insurance',
        ],
        'Entertainment' => [
            'Arts',
            'Music',
            'Movies & DVDs',
            'Newspaper and Magazines'
        ],
        'Shopping' => [
            'Clothes and Accessories',
            'Clothes',
            'Accessories',
            'Books',
            'Electronics and Software',
            'Electronics',
            'Software',
            'Hobbies',
            'Sporting Goods',
        ],
        'Food & Dining' => [
            'Dining',
            'Restaurants',
            'Groceries',
            'Coffee shops',
            'Fast Food',
            'Alcohol',
        ],
        'Office' => [
            'Business Services',
            'Office Supplies',
            'Hardware',
            'Packaging',
            'Postage',
            'Printing',
            'Shipping',
            'Software',
            'Stationery',
            'Printing',
        ],
        'Others' => [
            'Bank Fees',
            'Commissions',
            'Depreciation',
            'Online Services',
            'Materials',
            'Maintenance',
            'Subscriptions Dues Memberships',
            'Licenses',
            'Wages',
            'Miscellaneous',

        ],
        'Professional Services' => [
            'Accounting',
            'Legal Fees',
        ],
        'Rent' => [
            'Equipment',
            'Machinery',
            'Office Space',
            'Vehicles',
        ],
        'Supplies' => [
            'Supplies',
        ],
        'Travel' => [
            'Airfare',
            'Hotel',
            'Lodging',
            'Accommodation',
            'Travel Taxi',
            'Travel Parking',
            'Air Travel',
            'Vacation',
            'Rental Car and Taxi',
            'Rental Car',
        ],
        'Utilities and Bills' => [
            'Utilities',
            'Bills',
            'Gas',
            'Gas and Electrical',
            'Electricity',
            'Phone',
            'Cell',
            'Cellphone',
            'Cable / Satellite TV',
            'Home Phone',
            'Internet',
            'Mobile Phone',
        ],
        'Personal Care' => [
            'Laundry',
            'Hair',
            'Spa & Massage',
        ],
        'Health and Fitness' => [
            'Fitness',
            'Health',
            'Dentist',
            'Doctor',
            'Eyecare',
            'Pharmacy',
            'Health Insurance',
            'Gym',
            'Sports',
        ],
        'Kids' => [
            'Kids Activities',
            'Kids Allowance',
            'Baby Supplies',
            'Babysitter and Daycare',
            'Babysitter',
            'Daycare',
            'Child Support',
            'Toys',
        ],
        'Taxes' => [
            'Tax',
            'Federal Tax',
            'State Tax',
            'Local Tax',
            'Sales Tax',
            'Property Tax',
        ],
        'Gifts and Donations' => [
            'Gifts',
            'Donations',
            'Charities',
            'Church',
        ],
        'Investments' => [
            'Deposit',
            'Withdrawal',
            'Dividends & Cap Gains',
            'Dividents',
            'Caps Gains',
            'Buy',
            'Sell',
        ],
        'Fees and Charges' => [
            'Fees',
            'Charges',
            'Late Fee',
            'Finance Charge',
            'ATM Fee',
            'Bank Fee',
            'Commissions',
        ],
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::beginTransaction();

        try {

            foreach ($this->groups as $parent => $subs) {

                $parentCategory = new Category();
                $parentCategory->name = $parent;
                $parentCategory->is_default = true;
                $parentCategory->is_suggestion = true;
                $parentCategory->save();

                $arrSubsToAttach = [];
                foreach ($subs as $sub) {

                    $subCategory = Category::where('name', $sub)->firstOrNew();

                    if (in_array($subCategory->id, $arrSubsToAttach)) {
                        continue;
                    }

                    $subCategory->name = $sub;
                    $subCategory->is_default = true;
                    $subCategory->save();

                    $arrSubsToAttach[] = $subCategory->id;
                }

                if (count($arrSubsToAttach) > 0) {

                    $parentCategory->subs()->attach($arrSubsToAttach);
                }
            }

            DB::commit();

        } catch(\Exception $ex) {

            DB::rollBack();
            throw $ex;
        }
    }
}
