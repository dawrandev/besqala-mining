<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\ApplicationStatus;
use App\Enums\ApplicationType;
use App\Enums\DocumentKind;
use App\Enums\DocumentStatus;
use App\Enums\ExpertVerdict;
use App\Enums\NewsCategory;
use App\Enums\Region;
use App\Enums\ResidentStatus;
use App\Enums\UserRole;
use App\Models\Application;
use App\Models\ApplicationEvent;
use App\Models\Deficiency;
use App\Models\Document;
use App\Models\ExpertConclusion;
use App\Models\Inspection;
use App\Models\Leader;
use App\Models\NewsArticle;
use App\Models\Resident;
use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * Reproduces the original platform's demo content: five role accounts, the
 * public registry, applications spread across the whole pipeline, issued
 * documents, inspections, news, leadership and the about page.
 */
class DemoSeeder extends Seeder
{
    private const PASSWORD = 'demo1234';

    public function run(): void
    {
        $users = $this->seedUsers();
        $residents = $this->seedResidents($users);
        $this->seedApplications($users, $residents);
        $this->seedInspections($users, $residents);
        $this->seedContent($users['admin']);
    }

    /**
     * @return array<string, User>
     */
    private function seedUsers(): array
    {
        $password = Hash::make(self::PASSWORD);

        $definitions = [
            'applicant' => ['aral@besqala.uz', 'Azamat Qalbaev', UserRole::Applicant],
            'direksiya' => ['direksiya@besqala.uz', 'Dilnoza Yusupova', UserRole::Direksiya],
            'expert' => ['expert@besqala.uz', 'Rustam Nurmuxamedov', UserRole::Expert],
            'authority' => ['authority@besqala.uz', 'Dmitriy Li', UserRole::Authority],
            'admin' => ['admin@besqala.uz', 'Kontent administratori', UserRole::Admin],
        ];

        $users = [];

        foreach ($definitions as $key => [$email, $name, $role]) {
            $users[$key] = User::query()->create([
                'name' => $name,
                'email' => $email,
                'password' => $password,
                'role' => $role,
                'phone' => '+998 61 222 '.random_int(10, 99).' '.random_int(10, 99),
                'email_verified_at' => now(),
            ]);
        }

        return $users;
    }

    /**
     * @param  array<string, User>  $users
     * @return array<string, Resident>
     */
    private function seedResidents(array $users): array
    {
        $aral = Resident::query()->create([
            'owner_id' => $users['applicant']->id,
            'company_name' => '«Aral Digital Mining» MChJ',
            'stir' => '305412876',
            'region' => Region::Qoraozak,
            'legal_form' => 'MChJ (Mas’uliyati cheklangan jamiyat)',
            'status' => ResidentStatus::Active,
            'address' => 'Qoraqalpog‘iston Respublikasi, Nukus shahri, Doslik ko‘chasi 14-uy',
            'location_address' => 'Qorao‘zak tumani, Sanoat zonasi, 3-uchastka',
            'cadastre_number' => '35:05:0106003:78',
            'btc_address' => 'bc1qar4l...d9k1',
            'email' => 'office@araldigital.uz',
            'phone' => '+998 61 222 14 55',
            'founders' => [
                ['name' => 'Qalbaev Azamat Polatovich', 'share' => 60],
                ['name' => '«Green Energy Invest» MChJ', 'share' => 40],
            ],
            'power_mw' => 18.50,
            'energy_source' => 'Quyosh',
            'mined_total_btc' => 9.7400,
            'registered_at' => now()->subMonths(6),
        ]);

        $kungrad = Resident::query()->create([
            'company_name' => '«Kungrad DataFarm» MChJ',
            'stir' => '306788221',
            'region' => Region::Qongirot,
            'legal_form' => 'MChJ (Mas’uliyati cheklangan jamiyat)',
            'status' => ResidentStatus::Suspended,
            'address' => 'Qoraqalpog‘iston Respublikasi, Qo‘ng‘irot tumani, Energetika ko‘chasi 2',
            'location_address' => 'Qo‘ng‘irot tumani, Energetika ko‘chasi 2',
            'cadastre_number' => '35:08:0204001:12',
            'btc_address' => 'bc1qkun...7h2a',
            'email' => 'info@kungraddata.uz',
            'phone' => '+998 61 333 22 11',
            'founders' => [['name' => 'Seytov Murat Kadirovich', 'share' => 100]],
            'power_mw' => 12.00,
            'energy_source' => 'Gidro',
            'mined_total_btc' => 4.2100,
            'registered_at' => now()->subMonths(9),
        ]);

        // Certificates for registered residents.
        $this->issueCertificate($aral, 'G-2026-0001', $users['authority']);
        $this->issueCertificate($kungrad, 'G-2026-0002', $users['authority']);

        // Active permit for Aral, so the authority dashboard shows one.
        Document::query()->create([
            'number' => 'R-2026-0001',
            'kind' => DocumentKind::Permit,
            'status' => DocumentStatus::Active,
            'resident_id' => $aral->id,
            'issued_by' => $users['authority']->id,
            'payload' => ['scope' => 'Kripto-mayning faoliyati'],
            'issued_at' => now()->subMonths(6),
            'expires_at' => now()->addYears(5),
        ]);

        return ['aral' => $aral, 'kungrad' => $kungrad];
    }

    private function issueCertificate(Resident $resident, string $number, User $issuer): Document
    {
        return Document::query()->create([
            'number' => $number,
            'kind' => DocumentKind::Certificate,
            'status' => DocumentStatus::Active,
            'resident_id' => $resident->id,
            'issued_by' => $issuer->id,
            'payload' => ['companyName' => $resident->company_name],
            'template_version' => 'v1',
            'issued_at' => $resident->registered_at,
            'expires_at' => $resident->registered_at?->copy()->addYears(5),
        ]);
    }

    /**
     * @param  array<string, User>  $users
     * @param  array<string, Resident>  $residents
     */
    private function seedApplications(array $users, array $residents): void
    {
        $applicant = $users['applicant'];
        $aral = $residents['aral'];

        // Five drafts owned by the applicant (dashboard shows A-2026-0013..0017).
        foreach (range(13, 17) as $i) {
            Application::query()->create([
                'number' => 'A-2026-00'.$i,
                'applicant_id' => $applicant->id,
                'resident_id' => $aral->id,
                'type' => ApplicationType::Amendment,
                'status' => ApplicationStatus::Draft,
                'company_name' => $aral->company_name,
                'stir' => $aral->stir,
                'region' => $i === 13 ? Region::Qoraozak : Region::NukusCity,
                'legal_form' => $aral->legal_form,
                'address' => $aral->address,
                'location_address' => $aral->location_address,
                'cadastre_number' => $aral->cadastre_number,
                'property_right' => 'Mulk huquqi',
                'email' => $aral->email,
                'phone' => $aral->phone,
                'founders' => $aral->founders,
                'power_mw' => 18.50,
                'energy_source' => 'Quyosh',
                'bank_account' => '202080001007788990'.$i,
                'bank_name' => '«Mikrokreditbank»',
                'btc_address' => $aral->btc_address,
            ]);
        }

        // Amendment from Aral currently in completeness check (Direksiya queue).
        $this->pipeline('A-2026-0008', $applicant, $aral, Region::Qoraozak, ApplicationStatus::CompletenessCheck, ApplicationType::Amendment);

        // Chimboy GreenHash — completeness check.
        $this->pipeline('A-2026-0012', $applicant, null, Region::Chimboy, ApplicationStatus::CompletenessCheck, companyName: '«Chimboy GreenHash» MChJ');

        // Ellikqala Mining — deficiency notice sent.
        $ellik = $this->pipeline('A-2026-0011', $applicant, null, Region::Ellikqala, ApplicationStatus::Deficiency, companyName: '«Ellikqala Mining» MChJ');
        Deficiency::query()->create([
            'application_id' => $ellik->id,
            'issued_by' => $users['direksiya']->id,
            'items' => ['2 ta kamchilik aniqlandi', 'Yangi mayning uskunasi hujjati yetishmaydi'],
            'status' => 'open',
        ]);

        // Two applications awaiting an authority permit decision (positive expert conclusion).
        $amudaryo = $this->pipeline('A-2026-0009', $applicant, null, Region::Amudaryo, ApplicationStatus::AuthorityReview, companyName: '«Amudaryo Compute» MChJ');
        $this->addConclusion($amudaryo, $users['expert'], ExpertVerdict::Positive);

        $nukus = $this->pipeline('A-2026-0007', $applicant, null, Region::NukusDistrict, ApplicationStatus::AuthorityReview, companyName: '«Nukus HashPower» MChJ');
        $this->addConclusion($nukus, $users['expert'], ExpertVerdict::Positive);

        // Historic conclusions so the expert dashboard shows 4 positive + 1 negative.
        foreach (range(1, 2) as $i) {
            $done = $this->pipeline('A-2026-000'.$i, $applicant, null, Region::NukusCity, ApplicationStatus::Registered, companyName: '«Registered '.$i.'» MChJ');
            $this->addConclusion($done, $users['expert'], ExpertVerdict::Positive);

            // Issued certificate linked to the application (drives the QR preview card).
            Document::query()->create([
                'number' => 'G-2026-'.str_pad((string) (10 + $i), 4, '0', STR_PAD_LEFT),
                'kind' => DocumentKind::Certificate,
                'status' => DocumentStatus::Active,
                'application_id' => $done->id,
                'issued_by' => $users['authority']->id,
                'payload' => ['companyName' => $done->company_name],
                'template_version' => 'v1',
                'issued_at' => now()->subMonths(2),
                'expires_at' => now()->addYears(5),
            ]);
            $done->update(['decided_at' => now()->subMonths(2)]);
        }
        $rejected = $this->pipeline('A-2026-0006', $applicant, null, Region::Beruniy, ApplicationStatus::Rejected, companyName: '«Rad etilgan» MChJ');
        $this->addConclusion($rejected, $users['expert'], ExpertVerdict::Negative);
    }

    /**
     * Create an application in a given status and append a basic timeline.
     */
    private function pipeline(
        string $number,
        User $applicant,
        ?Resident $resident,
        Region $region,
        ApplicationStatus $status,
        ApplicationType $type = ApplicationType::Registration,
        ?string $companyName = null,
    ): Application {
        $company = $companyName;

        if ($company === null && $resident !== null) {
            $company = $resident->company_name;
        }

        $application = Application::query()->create([
            'number' => $number,
            'applicant_id' => $applicant->id,
            'resident_id' => $resident?->id,
            'type' => $type,
            'status' => $status,
            'company_name' => $company ?? '«Noma’lum» MChJ',
            'stir' => (string) random_int(300000000, 399999999),
            'region' => $region,
            'legal_form' => 'MChJ (Mas’uliyati cheklangan jamiyat)',
            'address' => $region->label().', Sanoat zonasi',
            'location_address' => $region->label().', Agrosanoat hududi',
            'cadastre_number' => '35:'.random_int(10, 20).':010'.random_int(1000, 9999).':'.random_int(10, 99),
            'property_right' => 'Mulk huquqi',
            'email' => Str::slug($companyName ?? 'company').'@besqala.uz',
            'phone' => '+998 61 '.random_int(200, 399).' '.random_int(10, 99).' '.random_int(10, 99),
            'founders' => [['name' => 'Ta’sischi '.random_int(1, 99), 'share' => 100]],
            'power_mw' => random_int(5, 40),
            'energy_source' => 'Quyosh',
            'bank_account' => '20208000'.random_int(100000000000, 999999999999),
            'bank_name' => '«Mikrokreditbank»',
            'btc_address' => 'bc1q'.Str::random(6).'...'.Str::random(4),
            'submitted_at' => now()->subDays(random_int(5, 40)),
        ]);

        ApplicationEvent::query()->create([
            'application_id' => $application->id,
            'actor_id' => $applicant->id,
            'type' => 'submitted',
            'title' => 'Ariza topshirildi',
        ]);

        return $application;
    }

    private function addConclusion(Application $application, User $expert, ExpertVerdict $verdict): void
    {
        ExpertConclusion::query()->create([
            'application_id' => $application->id,
            'expert_id' => $expert->id,
            'verdict' => $verdict,
            'comment' => $verdict === ExpertVerdict::Positive ? 'Talablarga mos' : 'Talablarga mos emas',
            'members' => ['R. Nurmuxamedov (rais)', 'D. Li', 'S. Allambergenova'],
            'concluded_at' => now()->subDays(random_int(1, 20)),
        ]);

        ApplicationEvent::query()->create([
            'application_id' => $application->id,
            'actor_id' => $expert->id,
            'type' => $verdict === ExpertVerdict::Positive ? 'expert_positive' : 'expert_negative',
            'title' => $verdict === ExpertVerdict::Positive ? 'Ijobiy ekspert xulosasi' : 'Salbiy ekspert xulosasi',
        ]);
    }

    /**
     * @param  array<string, User>  $users
     * @param  array<string, Resident>  $residents
     */
    private function seedInspections(array $users, array $residents): void
    {
        Inspection::query()->create([
            'act_number' => 'D-2026-0001',
            'resident_id' => $residents['kungrad']->id,
            'inspector_id' => $users['authority']->id,
            'subject' => 'Elektr energiyasini hisobga olish tizimi',
            'status' => 'completed',
            'result' => 'violation',
            'has_violation' => true,
            'findings' => 'Hisoblagich ko‘rsatkichlari mos kelmadi.',
            'conducted_at' => now()->subDays(12),
        ]);

        Inspection::query()->create([
            'act_number' => 'D-2026-0002',
            'resident_id' => $residents['aral']->id,
            'inspector_id' => $users['authority']->id,
            'subject' => 'Ruxsat berish talablari va shartlariga rioya etilishi',
            'status' => 'completed',
            'result' => 'clean',
            'has_violation' => false,
            'findings' => 'Buzilishlar aniqlanmadi.',
            'conducted_at' => now()->subDays(20),
        ]);
    }

    private function seedContent(User $admin): void
    {
        $this->seedNews($admin);
        $this->seedLeadership();
        $this->seedAbout();
    }

    private function seedNews(User $admin): void
    {
        $news = [
            [
                'cat' => NewsCategory::Official,
                'uz' => '«Besqala Mining Valley» maxsus zonasi rasman ish boshladi',
                'ru' => 'Специальная зона «Besqala Mining Valley» официально начала работу',
                'en' => '«Besqala Mining Valley» special zone officially launched',
                'excerpt' => 'Qoraqalpog‘istonda tashkil etilgan maxsus mayning zonasi rezidentlarni qabul qilishni boshladi.',
            ],
            [
                'cat' => NewsCategory::Residents,
                'uz' => 'Zona rezidentlari uchun soliq imtiyozlari joriy etildi',
                'ru' => 'Введены налоговые льготы для резидентов зоны',
                'en' => 'Tax incentives introduced for zone residents',
                'excerpt' => 'Rezidentlar uchun maxsus soliq rejimi amal qila boshladi.',
            ],
            [
                'cat' => NewsCategory::Energy,
                'uz' => 'Quyosh energiyasi asosidagi yangi mayning markazi ochildi',
                'ru' => 'Открыт новый майнинг-центр на солнечной энергии',
                'en' => 'New solar-powered mining center opened',
                'excerpt' => 'Qayta tiklanuvchi energiya manbalariga asoslangan markaz ishga tushdi.',
            ],
            [
                'cat' => NewsCategory::Official,
                'uz' => 'Hujjatlarni QR-kod orqali tekshirish tizimi ishga tushdi',
                'ru' => 'Запущена система проверки документов по QR-коду',
                'en' => 'QR-code document verification system launched',
                'excerpt' => 'Barcha guvohnoma va ruxsatnomalar QR-kod orqali tekshiriladi.',
            ],
            [
                'cat' => NewsCategory::Energy,
                'uz' => 'Zonada birinchi ekologik audit o‘tkazildi',
                'ru' => 'В зоне проведён первый экологический аудит',
                'en' => 'First environmental audit conducted in the zone',
                'excerpt' => 'Rezidentlar faoliyati ekologik normalarga muvofiq baholandi.',
            ],
        ];

        foreach ($news as $i => $item) {
            NewsArticle::query()->create([
                'slug' => Str::slug($item['en']),
                'category' => $item['cat'],
                'title' => ['uz' => $item['uz'], 'ru' => $item['ru'], 'en' => $item['en']],
                'excerpt' => ['uz' => $item['excerpt'], 'ru' => $item['excerpt'], 'en' => $item['excerpt']],
                'body' => [
                    'uz' => $item['excerpt']."\n\nBarcha jarayonlar to‘liq elektron shaklda, QR-kodli hujjatlar orqali amalga oshiriladi.",
                    'ru' => $item['excerpt'],
                    'en' => $item['excerpt'],
                ],
                'author_id' => $admin->id,
                'published_at' => now()->subDays(($i + 1) * 7),
            ]);
        }
    }

    private function seedLeadership(): void
    {
        $leaders = [
            ['Li Dmitriy Romanovich', 'Ли Дмитрий Романович', 'Direktor', 'Директор', 'O‘zbekiston Respublikasi Istiqbolli loyihalar milliy agentligi'],
            ['Nurmuxamedov Rustam', 'Нурмухамедов Рустам', 'Ekspert komissiyasi raisi', 'Председатель экспертной комиссии', 'Ekspert komissiyasi'],
            ['Yusupova Dilnoza', 'Юсупова Дилноза', 'Direksiya boshlig‘i', 'Начальник дирекции', 'Zona direksiyasi'],
            ['Allambergenova Saule', 'Алламбергенова Сауле', 'Bosh ekspert', 'Главный эксперт', 'Ekspert komissiyasi'],
            ['Qalbaev Azamat', 'Калбаев Азамат', 'Nazorat bo‘limi boshlig‘i', 'Начальник отдела надзора', 'Vakolatli organ'],
        ];

        foreach ($leaders as $i => [$nameUz, $nameRu, $posUz, $posRu, $dept]) {
            Leader::query()->create([
                'full_name' => ['uz' => $nameUz, 'ru' => $nameRu, 'en' => $nameUz],
                'position' => ['uz' => $posUz, 'ru' => $posRu, 'en' => $posUz],
                'department' => ['uz' => $dept, 'ru' => $dept, 'en' => $dept],
                'bio' => [
                    'uz' => 'Kripto-aktivlar aylanmasi va maxsus mayning zonasi faoliyatiga rahbarlik qiladi.',
                    'ru' => 'Руководит оборотом крипто-активов и деятельностью специальной майнинг-зоны.',
                    'en' => 'Oversees crypto-asset turnover and the special mining zone.',
                ],
                'sort_order' => $i,
            ]);
        }
    }

    private function seedAbout(): void
    {
        SiteSetting::put('about', [
            'title' => [
                'uz' => '«Besqala Mining Valley» maxsus mayning zonasi',
                'ru' => 'Специальная майнинг-зона «Besqala Mining Valley»',
                'en' => '«Besqala Mining Valley» Special Mining Zone',
            ],
            'intro' => [
                'uz' => '«Besqala Mining Valley» — Qoraqalpog‘iston Respublikasi hududida tashkil etilgan maxsus mayning zonasi bo‘lib, kripto-aktivlar mayningini qonuniy, shaffof va ekologik toza energiya manbalari asosida amalga oshirish uchun barcha shart-sharoitlarni yaratadi. Zona 2026-yil 17-apreldagi PQ–143-son qaror asosida tashkil etilgan.',
                'ru' => '«Besqala Mining Valley» — специальная майнинг-зона на территории Республики Каракалпакстан.',
                'en' => '«Besqala Mining Valley» is a special mining zone in the Republic of Karakalpakstan.',
            ],
            'stats' => [
                ['label' => ['uz' => 'Rezidentlar', 'ru' => 'Резиденты', 'en' => 'Residents'], 'value' => '2'],
                ['label' => ['uz' => 'Umumiy quvvat', 'ru' => 'Общая мощность', 'en' => 'Total power'], 'value' => '30 MW'],
                ['label' => ['uz' => 'Qazib olingan', 'ru' => 'Добыто', 'en' => 'Mined'], 'value' => '13.95 BTC'],
            ],
        ]);
    }
}
