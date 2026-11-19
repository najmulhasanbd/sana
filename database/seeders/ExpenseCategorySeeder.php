<?php

namespace Database\Seeders;

use App\Models\ExpenseCategory;
use Illuminate\Database\Seeder;

class ExpenseCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'বিদ্যুৎ বিল', 'description' => 'হ্যাচারি, নার্সিং পয়েন্ট ও অফিস বিদ্যুৎ বিল'],
            ['name' => 'মাছের খাবার ও নার্সিং মেডিসিন', 'description' => 'পোনা মাছের খাদ্য, নার্সিং ভিটামিন ও মেডিসিন'],
            ['name' => 'পরিবহন ও গাড়িভাড়া', 'description' => 'পোনা ডেলিভারি, তেল খরচ ও পরিবহন ভাড়া'],
            ['name' => 'প্যাকেজিং ও অক্সিজেন গ্যাস', 'description' => 'পলি, কর্কশিট বক্স, অক্সিজেন বোতল রিফিল'],
            ['name' => 'অফিস ও নার্সিং সামগ্রী', 'description' => 'অফিস সরঞ্জাম ও নার্সিং সাইট সামগ্রী'],
            ['name' => 'আপ্যায়ন ও নাস্তা', 'description' => 'কাস্টমার ও স্টাফ আপ্যায়ন ও চা-নাস্তা'],
            ['name' => 'যন্ত্রপাতি মেরামত ও রক্ষণাবেক্ষণ', 'description' => 'মটর, পাম্প, জেনারেটর সার্ভিসিং ও মেরামত'],
            ['name' => 'অন্যান্য আনুষঙ্গিক খরচ', 'description' => 'দৈনিক বিবিধ আনুষঙ্গিক ব্যয়'],
        ];

        foreach ($categories as $cat) {
            ExpenseCategory::firstOrCreate(
                ['name' => $cat['name']],
                [
                    'description' => $cat['description'],
                    'status' => true,
                ]
            );
        }
    }
}
