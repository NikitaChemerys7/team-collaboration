<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Conference;
use App\Models\Subpage;

class ConferenceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $conf1 = Conference::create([
            'title' => '32nd International Symposium Animal Science Days',
            'year' => 2024,
            'date' => '2024-10-02',
            'location' => 'Oberaichwald, Carinthia, Austria',
            'description' => 'The 32nd International Symposium Animal Science Days (ASD) is a prestigious annual event bringing together scientists, professionals, and students from the field of animal science. The 2024 edition will be hosted in the scenic region of Oberaichwald, near Faaker See, from October 2 to 4, 2024.',
        ]);

        $conf1->subpages()->createMany([
            [
                'title' => 'About the Symposium',
                'slug' => 'about',
                'content' => <<<HTML
<p>The Animal Science Days Symposium was established in 1993 as a collaborative effort between universities in Central and Eastern Europe. Over the years, it has grown into a respected international event attracting researchers and practitioners from across the globe.</p>
<p>The ASD 2024 will include keynote lectures, poster sessions, oral presentations, and networking events aimed at fostering collaboration and innovation in the field of animal breeding, welfare, and sustainable production systems.</p>
HTML,
                'order' => 0,
            ],
            [
                'title' => 'Organizing Committee',
                'slug' => 'organizing-committee',
                'content' => <<<HTML
<ul>
<li>Prof. Birgit Fürst-Waltl (BOKU, Austria) – Chair</li>
<li>Prof. Johann Sölkner (BOKU, Austria)</li>
<li>Prof. Werner Zollitsch (BOKU, Austria)</li>
<li>Dr. Peter Dovč (University of Ljubljana, Slovenia)</li>
<li>Dr. Radovan Kasarda (Slovak University of Agriculture, Slovakia)</li>
</ul>
HTML,
             'order' => 1,
            ],
            [
                'title' => 'Scientific Committee',
                'slug' => 'scientific-committee',
                'content' => <<<HTML
<p>The scientific committee ensures the academic quality of the symposium. Members include:</p>
<ul>
<li>Prof. Martino Cassandro (University of Padova, Italy)</li>
<li>Prof. Ino Čurik (University of Zagreb, Croatia)</li>
<li>Prof. Peter Dovč (University of Ljubljana, Slovenia)</li>
<li>Prof. Nina Moravčíková (Slovakia)</li>
<li>Prof. Wilhelm Knaus (Austria)</li>
</ul>
HTML,
            'order' => 2,
            ],
            [
                'title' => 'Venue & Accommodation',
                'slug' => 'venue-accommodation',
                'content' => <<<HTML
<p><strong>Conference venue:</strong> Naturel Hotels & Resorts Dorf SCHÖNLEITN, Dorfstraße 26, 9582 Oberaichwald, Austria.</p>
<p>Accommodation is available at special symposium rates. Booking details and discount codes are included in the accommodation file.</p>
<p>The venue offers scenic views of the mountains and is located close to Faaker See. Free Wi-Fi and conference shuttle services will be available.</p>
HTML,
            'order' => 3,
            ],
            [
                'title' => 'Registration',
                'slug' => 'registration',
                'content' => <<<HTML
<p><strong>Registration fee:</strong></p>
<ul>
<li>Early Bird (until July 31, 2024): 220 EUR</li>
<li>Regular (August 1 – September 15): 270 EUR</li>
<li>Student rate: 150 EUR</li>
</ul>
<p>Registration includes access to all sessions, conference materials, coffee breaks, and the official dinner.</p>
<p>Register online via the <a href="/register">registration form</a>. Payment details will be provided after submission.</p>
HTML,
            'order' => 4,
            ],
        ]);

        $conf2 = Conference::create([
            'title' => '31st International Symposium Animal Science Days',
            'year' => 2023,
            'date' => '2023-09-20',
            'location' => 'Lipica, Slovenia',
            'description' => 'The 31st International Symposium ASD 2023 was successfully held in Lipica, Slovenia. It brought together experts and students from all over Europe to discuss innovations and sustainability in livestock production.',
        ]);

        $conf2->subpages()->createMany([
            [
                'title' => 'Symposium Overview',
                'slug' => 'overview',
                'content' => <<<HTML
<p>The 31st ASD symposium focused on animal health, welfare, and sustainable farming methods. It was attended by over 200 participants and featured more than 50 scientific contributions.</p>
HTML,
                'order' => 0,
            ],
            [
                'title' => 'Scientific Sessions',
                'slug' => 'scientific-sessions',
                'content' => <<<HTML
<p>Key topics included:</p>
<ul>
<li>Genomic selection and animal breeding</li>
<li>Sustainable ruminant nutrition</li>
<li>Precision livestock farming</li>
<li>Animal welfare and behavior</li>
</ul>
HTML,
                'order' => 1,
            ],
            [
                'title' => 'Photo Gallery',
                'slug' => 'gallery',
                'content' => '<p>Check out highlights from the event in our <a href="/gallery/2023">photo gallery</a>.</p>',
                'order' => 2,
            ],
             [
                'title' => 'Sponsors',
                'slug' => 'sponsors',
                'content' => '<p>We gratefully acknowledge the support of Agromarket, VetMed, and BioFeed.</p>',
                'order' => 3,
            ],
            [
                'title' => 'Contact & Feedback',
                'slug' => 'contact',
                'content' => <<<HTML
<p>If you have any questions or wish to provide feedback, please contact us at:</p>
<ul>
<li>Email: admin@mail.com</li>
<li>Phone: +386 1 234 5678</li>
</ul>
HTML,
                'order' => 4,
            ],
        ]);
    }
}


