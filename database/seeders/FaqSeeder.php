<?php

namespace Database\Seeders;

use App\Models\Faq;
use Illuminate\Database\Seeder;

class FaqSeeder extends Seeder
{
    public function run(): void
    {
        Faq::insert([
            ['id' => 1, 'question' => 'How do I book an appointment?', 'answer' => 'Use our online booking form, call our 24/7 helpline, or visit the patient portal. Most appointments are confirmed within 30 minutes.', 'category' => null, 'order' => 0],
            ['id' => 2, 'question' => 'Do you accept international insurance?', 'answer' => 'Yes. We are empaneled with most major international and domestic insurance providers. See our insurance partners page for the full list.', 'category' => null, 'order' => 0],
            ['id' => 3, 'question' => 'Where can I access my medical reports online?', 'answer' => 'Lab and imaging reports are available on the patient portal within 24 hours. You can also download them from the report verification page using your Patient ID.', 'category' => null, 'order' => 0],
            ['id' => 4, 'question' => 'Is there an ambulance service?', 'answer' => 'Yes. Our advanced life support ambulances are available 24/7. Call 1-800-VALOR-ER for emergency response within the city.', 'category' => null, 'order' => 0],
            ['id' => 5, 'question' => 'How can international patients plan their visit?', 'answer' => 'Our International Patient Services team coordinates visas, travel, accommodation, and treatment scheduling end-to-end. Contact us at international@example.com.', 'category' => null, 'order' => 0],
        ]);
    }
}
