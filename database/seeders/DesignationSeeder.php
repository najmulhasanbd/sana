<?php

namespace Database\Seeders;

use App\Models\Designation;
use Illuminate\Database\Seeder;

class DesignationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $designations = [
            ['name' => 'ম্যানেজার', 'description' => 'সার্বিক কার্যক্রম ও হিসাব পরিচালনা'],
            ['name' => 'অপারেটর', 'description' => 'নার্সিং পয়েন্ট পরিচালনা ও টেকনিক্যাল কাজ'],
            ['name' => 'নার্সিং কর্মী', 'description' => 'পোনা পরিচর্যা ও ঘেরের কাজ'],
            ['name' => 'পোনা গোনা ও প্যাকেজিং কর্মী', 'description' => 'বক্স, পলি, মীর গোনা ও প্যাকেট তৈরি'],
            ['name' => 'কেয়ারটেকার / শ্রমিক', 'description' => 'পুকুর ও নার্সিং পয়েন্ট পাহারা ও রক্ষণাবেক্ষণ'],
            ['name' => 'ড্রাইভার / ডেলিভারি', 'description' => 'পোনা পরিবহন ও কাস্টমার ডেলিভারি'],
        ];

        foreach ($designations as $d) {
            Designation::firstOrCreate(['name' => $d['name']], $d);
        }
    }
}
