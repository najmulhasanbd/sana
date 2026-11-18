<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            'business_name' => 'মেসার্স সানা ফিস এন্ড নার্সিং পয়েন্ট',
            'site_tagline' => 'এখানে সকল প্রকার বাগদার পোনা নার্সিং করা হয় ও বিক্রয়ের নির্ভরযোগ্য প্রতিষ্ঠান।',
            'business_type' => 'বাগদার পোনা নার্সিং ও মৎস্য হ্যাচারি',
            'memo_business_name' => 'মেসার্স সানা ফিস এন্ড নার্সিং পয়েন্ট',
            'memo_subtitle' => 'এখানে সকল প্রকার বাগদার পোনা নার্সিং করা হয় ও বিক্রয়ের নির্ভরযোগ্য প্রতিষ্ঠান।',
            'proprietor_name' => 'আলহাজ্ব নজরুল ইসলাম সানা',
            'proprietor_mobile' => '01737 908309',
            'contact_numbers' => '01352 383531, 01352 383532',
            'mobile' => '01737 908309',
            'alt_mobile' => '01352 383531, 01352 383532',
            'operator_name' => 'মোমিনুর রহমান',
            'operator_contact' => '01751-722350 NG/BK',
            'whatsapp' => '01751-722350',
            'nursing_point_address' => 'দক্ষিণ পুঁইজালা গেটের ধার, শ্রীউলা, আশাশুনি, সাতক্ষীরা।',
            'office_address' => 'পুঁইজালা বাল্লা বাজার, আশাশুনি, সাতক্ষীরা।',
            'address' => 'অফিস: পুঁইজালা বাল্লা বাজার, আশাশুনি, সাতক্ষীরা।',
            'memo_slogan' => 'ধন্যবাদ আবার আসবেন | সততাই ব্যবসার মূল লক্ষ্য',
            'memo_authority_signature' => 'পক্ষে - মেসার্স সানা ফিস এন্ড নার্সিং পয়েন্ট',
        ];

        foreach ($settings as $key => $value) {
            Setting::set($key, $value);
        }
    }
}
