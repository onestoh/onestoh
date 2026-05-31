<?php

namespace Database\Seeders;

use App\Models\Auction;
use App\Models\Bid;
use App\Models\Booking;
use App\Models\HotelRoom;
use App\Models\DeveloperProject;
use App\Models\EscrowTransaction;
use App\Models\Inspection;
use App\Models\Lease;
use App\Models\MaintenanceRequest;
use App\Models\Message;
use App\Models\NotificationLog;
use App\Models\Property;
use App\Models\Referral;
use App\Models\RentPayment;
use App\Models\User;
use App\Models\Valuation;
use App\Models\Verification;
use App\Models\VerificationDocument;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    private array $usersByRole = [];

    public function run(): void
    {
        $this->seedUsers();
        $this->seedProperties();
        $this->seedLeases();
        $this->seedRentPayments();
        $this->seedAuctions();
        $this->seedBids();
        $this->seedEscrowTransactions();
        $this->seedReferrals();
        $this->seedVerifications();
        $this->seedMaintenanceRequests();
        $this->seedInspections();
        $this->seedDeveloperProjects();
        $this->seedMessages();
        $this->seedNotifications();
        $this->seedHotelRoomsAndBookings();

        $this->printCounts();
    }

    private function makeReferralCode(): string
    {
        do {
            $code = strtoupper(Str::random(6));
        } while (User::where('referral_code', $code)->exists());
        return $code;
    }

    private function seedUsers(): void
    {
        $roles = [
            'admin', 'landlord', 'broker_licensed', 'broker_unlicensed', 'tenant',
            'developer', 'valuer', 'surveyor', 'auctioneer', 'investor',
            'corporate', 'property_manager', 'finance',
        ];

        $kenyaNames = [
            'James Kamau', 'Grace Wanjiku', 'Peter Otieno', 'Mary Njeri', 'John Mwangi',
            'Alice Achieng', 'Samuel Kipchoge', 'Ruth Wangari', 'David Odhiambo', 'Faith Chege',
            'Michael Njoroge', 'Esther Mutua', 'Daniel Kimani', 'Priscilla Waweru', 'Joseph Mugo',
            'Lydia Karanja', 'Stephen Gitau', 'Beatrice Mbugua', 'Charles Njuguna', 'Mercy Maina',
            'Andrew Wakiumu', 'Jane Wairimu', 'Patrick Ndungu', 'Susan Mureithi', 'George Kibet',
            'Agnes Chebet', 'Francis Gacheru', 'Hannah Muthoni', 'Robert Ngugi', 'Catherine Waruguru',
            'Eric Muriuki', 'Eunice Chepkoech', 'Vincent Karimi', 'Tabitha Waithira', 'Kevin Njeru',
            'Rose Auma', 'Brian Kamande', 'Irene Waceke', 'Paul Kiragu', 'Lucy Nyambura',
            'Simon Ochieng', 'Doris Wekesa', 'Timothy Gichuki', 'Winnie Mwende', 'Moses Kirui',
            'Gladys Adhiambo', 'Isaac Muigai', 'Florence Wanjiru', 'Solomon Maina', 'Helen Chepchumba',
            'Philip Wainaina', 'Judith Kananu', 'Geoffrey Mutisya', 'Carolyne Nduta', 'Mark Musyoka',
            'Purity Nyawira', 'Anthony Njomo', 'Martha Wambua', 'Henry Oloo', 'Zipporah Mutheu',
            'Gabriel Macharia', 'Diana Kerubo', 'Elijah Muchangi', 'Vivian Njoki', 'Caleb Mwiti',
        ];

        $nameIndex = 0;

        foreach ($roles as $role) {
            $this->usersByRole[$role] = [];
            for ($i = 1; $i <= 5; $i++) {
                $name = $kenyaNames[$nameIndex % count($kenyaNames)];
                $nameIndex++;

                $user = User::create([
                    'name'              => $name,
                    'email'             => "{$role}{$i}@estateyard.co.ke",
                    'password'          => Hash::make('password123'),
                    'role'              => $role,
                    'phone'             => '+2547' . rand(10000000, 99999999),
                    'is_verified'       => $i <= 3,
                    'verification_tier' => $i === 1 ? 'elite' : ($i <= 3 ? 'professional' : 'none'),
                    'referral_code'     => $this->makeReferralCode(),
                    'is_active'         => true,
                    'bio'               => "Experienced {$role} professional based in Nairobi, Kenya.",
                ]);

                $this->usersByRole[$role][] = $user;
            }
        }

        // Admin super user
        User::create([
            'name'              => 'Super Admin',
            'email'             => 'admin@estateyard.co.ke',
            'password'          => Hash::make('admin123!'),
            'role'              => 'admin',
            'referral_code'     => $this->makeReferralCode(),
            'is_verified'       => true,
            'verification_tier' => 'elite',
            'is_active'         => true,
        ]);
    }

    private function seedProperties(): void
    {
        $counties = ['Nairobi', 'Nairobi', 'Nairobi', 'Kiambu', 'Machakos'];
        $areas = [
            'Karen', 'Westlands', 'Kilimani', 'Lavington', 'Runda',
            'Muthaiga', 'Upper Hill', 'Parklands', 'Spring Valley', 'Gigiri',
            'Langata', 'South C', 'Hurlingham', 'Riverside', 'Milimani',
            'Kitisuru', 'Rosslyn', 'Loresho', 'Brookside', 'Kileleshwa',
        ];

        $types = ['house', 'apartment', 'land', 'commercial', 'villa', 'office'];
        $listingTypes = ['sale', 'rent', 'sale', 'rent', 'sale'];

        $landlords = array_merge(
            $this->usersByRole['landlord'],
            $this->usersByRole['developer'],
            $this->usersByRole['corporate']
        );

        $amenitiesSets = [
            ['Swimming Pool', 'Gym', 'Security', 'Parking', 'Balcony'],
            ['Borehole', 'Generator', 'CCTV', 'Garden', 'Servant Quarter'],
            ['Solar Power', 'Fibre Internet', 'Air Conditioning', 'Elevator'],
            ['Gated Community', 'Playground', 'Basketball Court', 'Tennis Court'],
        ];

        $propertyTitles = [
            '5-Bedroom Villa with Pool in Karen',
            'Modern 3-Bedroom Apartment in Kilimani',
            '0.5 Acre Land for Sale in Runda',
            'Commercial Office Space in Upper Hill',
            'Luxury Penthouse in Westlands',
            '4-Bedroom Townhouse in Lavington',
            'Studio Apartment in Parklands',
            '2-Bedroom Flat in Hurlingham',
            'Commercial Building in Upper Hill',
            '3-Bedroom Maisonette in South C',
            'Prime Land in Muthaiga 1 Acre',
            'Serviced Apartment in Gigiri',
            'Bungalow in Langata',
            'Modern Villa in Spring Valley',
            'Office Suite in Riverside Drive',
            '6-Bedroom Mansion in Muthaiga',
            '1-Bedroom Apartment in Kileleshwa',
            'Retail Space in Westlands CBD',
            'Semi-Detached House in Loresho',
            '4-Bedroom Apartment in Brookside',
            'Warehouse Space in Industrial Area',
            'Beach Plot in Nyali Mombasa',
            '3-Bedroom Townhouse in Kitisuru',
            'Prime Corner Plot in Karen',
            'Grade A Office in Upper Hill Towers',
            '2-Bedroom Apartment Gigiri UN Area',
            'Detached Villa in Runda Estate',
            'Commercial Plot Thika Road',
            '5-Bedroom Home in Rosslyn',
            'Furnished 2BR Apartment Kilimani',
            '1-Acre Farm in Limuru',
            'Modern Duplex in Lavington',
            'Hostel Investment in Westlands',
            'Prime Land in Ruiru 2 Acres',
            '3-Bedroom House in South B',
            'Office Block in Upper Hill',
            'Luxury Maisonette in Karen',
            'Affordable Apartment in Rongai',
            '4-Bedroom Bungalow in Athi River',
            'Industrial Shed in Mombasa Road',
            'Studio Apartment in Kilimani',
            '5BR Detached House Nyari Estate',
            'Corner Apartment in Westlands',
            'Plot in Ruaka Township',
            '3BR Flat in Buruburu',
            'Commercial Unit in Ngong Road',
            'Townhouse Complex in Karen',
            'Student Hostel Kenyatta University',
            'Duplex in Kitengela',
            'Penthouse Nairobi CBD',
        ];

        for ($i = 0; $i < 50; $i++) {
            $owner = $landlords[$i % count($landlords)];
            $type = $types[$i % count($types)];
            $listingType = $listingTypes[$i % count($listingTypes)];
            $area = $areas[$i % count($areas)];
            $county = $counties[$i % count($counties)];

            $isLand = $type === 'land';
            $isCommercial = in_array($type, ['commercial', 'office']);

            if ($listingType === 'sale') {
                $price = $isLand ? rand(3, 50) * 1_000_000 : rand(5, 150) * 1_000_000;
            } else {
                $price = $isCommercial ? rand(50, 300) * 1_000 : rand(30, 250) * 1_000;
            }

            $title = $propertyTitles[$i];
            $slug = Str::slug($title) . '-' . ($i + 1);

            Property::create([
                'user_id'      => $owner->id,
                'title'        => $title,
                'slug'         => $slug,
                'description'  => "Beautiful {$type} located in {$area}, {$county}. This property offers excellent value and modern amenities in a prime Nairobi location.",
                'type'         => $type,
                'listing_type' => $listingType,
                'status'       => $i < 40 ? 'active' : ($i < 45 ? 'pending' : 'draft'),
                'price'        => $price,
                'price_period' => $listingType === 'rent' ? ($i % 2 === 0 ? 'month' : 'year') : null,
                'bedrooms'     => $isLand || $isCommercial ? null : rand(1, 6),
                'bathrooms'    => $isLand || $isCommercial ? null : rand(1, 5),
                'area_sqft'    => rand(500, 5000),
                'floors'       => $isLand ? null : rand(1, 4),
                'year_built'   => rand(2000, 2024),
                'county'       => $county,
                'constituency' => $area,
                'location'     => "{$area}, {$county}",
                'latitude'     => -1.2921 + (rand(-500, 500) / 10000),
                'longitude'    => 36.8219 + (rand(-500, 500) / 10000),
                'amenities'    => $amenitiesSets[$i % count($amenitiesSets)],
                'images'       => [
                    "https://source.unsplash.com/800x600/?house,kenya&sig={$i}a",
                    "https://source.unsplash.com/800x600/?interior,modern&sig={$i}b",
                ],
                'is_featured'  => $i < 8,
                'view_count'   => rand(0, 500),
                'save_count'   => rand(0, 50),
            ]);
        }
    }

    private function seedLeases(): void
    {
        $landlords  = $this->usersByRole['landlord'];
        $tenants    = $this->usersByRole['tenant'];
        $properties = Property::where('listing_type', 'rent')->take(20)->get();

        foreach ($properties as $i => $property) {
            $landlord  = $landlords[$i % count($landlords)];
            $tenant    = $tenants[$i % count($tenants)];
            $startDate = now()->subMonths(rand(1, 18));

            Lease::create([
                'property_id'  => $property->id,
                'landlord_id'  => $landlord->id,
                'tenant_id'    => $tenant->id,
                'monthly_rent' => $property->price,
                'deposit'      => $property->price * 2,
                'start_date'   => $startDate->format('Y-m-d'),
                'end_date'     => $startDate->copy()->addYear()->format('Y-m-d'),
                'status'       => $i < 15 ? 'active' : ($i < 18 ? 'expired' : 'pending'),
                'terms'        => 'Standard residential lease terms apply. Tenant responsible for utility bills. No subletting without landlord consent.',
                'signed_at'    => $i < 18 ? $startDate : null,
            ]);
        }
    }

    private function seedRentPayments(): void
    {
        $leases  = Lease::with(['tenant', 'landlord'])->take(15)->get();
        $methods = ['mpesa', 'bank', 'cash', 'card'];

        foreach ($leases as $lease) {
            for ($month = 0; $month < 2; $month++) {
                $dueDate = now()->subMonths($month);
                $status  = $month === 0 ? 'paid' : (rand(0, 1) ? 'paid' : 'overdue');

                RentPayment::create([
                    'lease_id'        => $lease->id,
                    'tenant_id'       => $lease->tenant_id,
                    'landlord_id'     => $lease->landlord_id,
                    'amount'          => $lease->monthly_rent,
                    'month_year'      => $dueDate->format('Y-m'),
                    'payment_method'  => $methods[array_rand($methods)],
                    'transaction_ref' => $status === 'paid' ? 'QHJ' . strtoupper(Str::random(8)) : null,
                    'status'          => $status,
                    'paid_at'         => $status === 'paid' ? $dueDate->subDays(rand(0, 5)) : null,
                    'due_date'        => $dueDate->format('Y-m-d'),
                ]);
            }
        }
    }

    private function seedAuctions(): void
    {
        $auctioneers = $this->usersByRole['auctioneer'];
        $properties  = Property::take(10)->get();

        $auctionTitles = [
            'Prime Karen Mansion Auction',
            'Westlands Commercial Block Sale',
            'Distressed Sale Kilimani Apartment',
            'Bank-Seized Property Runda Villa',
            'Government Surplus Muthaiga Land',
            'Probate Sale Lavington Estate',
            'Developer Closeout Upper Hill Offices',
            'Foreclosure Parklands Townhouse',
            'Heritage Property Gigiri Residence',
            'Agricultural Land Limuru Farm',
        ];

        foreach ($properties as $i => $property) {
            $auctioneer = $auctioneers[$i % count($auctioneers)];
            $isLive     = $i < 3;
            $isUpcoming = $i < 7 && !$isLive;
            $isEnded    = !$isLive && !$isUpcoming;

            $startsAt = $isLive
                ? now()->subHours(rand(1, 5))
                : ($isUpcoming ? now()->addDays(rand(1, 14)) : now()->subDays(rand(1, 30)));
            $endsAt = $isLive
                ? now()->addHours(rand(2, 12))
                : $startsAt->copy()->addDays(1);

            $reservePrice = $property->price * 0.8;
            $startingBid  = $property->price * 0.6;
            $currentBid   = ($isEnded || $isLive) ? $startingBid * (1 + rand(5, 30) / 100) : null;

            Auction::create([
                'property_id'   => $property->id,
                'auctioneer_id' => $auctioneer->id,
                'title'         => $auctionTitles[$i],
                'description'   => "Court-ordered sale of prime property. All bids subject to reserve price. Viewing by appointment only.",
                'reserve_price' => $reservePrice,
                'starting_bid'  => $startingBid,
                'current_bid'   => $currentBid,
                'bid_increment' => 50000,
                'starts_at'     => $startsAt,
                'ends_at'       => $endsAt,
                'status'        => $isLive ? 'live' : ($isUpcoming ? 'upcoming' : 'ended'),
                'winner_id'     => $isEnded ? $this->usersByRole['investor'][0]->id : null,
            ]);
        }
    }

    private function seedBids(): void
    {
        $auctions  = Auction::whereIn('status', ['live', 'ended'])->get();
        $investors = $this->usersByRole['investor'];
        $tenants   = $this->usersByRole['tenant'];
        $bidders   = array_merge($investors, $tenants);

        foreach ($auctions as $auction) {
            $bidCount      = rand(3, 6);
            $currentAmount = $auction->starting_bid;

            for ($j = 0; $j < $bidCount; $j++) {
                $bidder = $bidders[($j + $auction->id) % count($bidders)];
                $currentAmount += $auction->bid_increment * rand(1, 3);
                $isWinning = ($j === $bidCount - 1) && $auction->isEnded();

                Bid::create([
                    'auction_id' => $auction->id,
                    'bidder_id'  => $bidder->id,
                    'amount'     => $currentAmount,
                    'is_winning' => $isWinning,
                ]);
            }
        }
    }

    private function seedEscrowTransactions(): void
    {
        $properties = Property::where('listing_type', 'sale')->take(20)->get();
        $buyers     = $this->usersByRole['investor'];
        $sellers    = $this->usersByRole['landlord'];

        foreach ($properties as $i => $property) {
            $buyer  = $buyers[$i % count($buyers)];
            $seller = $sellers[$i % count($sellers)];

            EscrowTransaction::create([
                'property_id' => $property->id,
                'buyer_id'    => $buyer->id,
                'seller_id'   => $seller->id,
                'amount'      => $property->price * 0.1,
                'type'        => $i < 10 ? 'deposit' : ($i < 15 ? 'sale' : 'refund'),
                'status'      => $i < 8 ? 'held' : ($i < 14 ? 'released' : ($i < 17 ? 'disputed' : 'refunded')),
                'reference'   => 'ESC-' . strtoupper(Str::random(8)),
                'notes'       => 'Deposit held pending title deed transfer and legal clearance.',
                'released_at' => ($i >= 8 && $i < 14) ? now()->subDays(rand(1, 30)) : null,
            ]);
        }
    }

    private function seedReferrals(): void
    {
        $brokers   = $this->usersByRole['broker_licensed'];
        $promoters = $this->usersByRole['broker_unlicensed'];
        $referrers = array_merge($brokers, $promoters);
        $tenants   = $this->usersByRole['tenant'];

        foreach ($referrers as $i => $referrer) {
            $referred = $i < count($tenants) ? $tenants[$i] : null;

            Referral::create([
                'referrer_id'      => $referrer->id,
                'referred_user_id' => $referred?->id,
                'referral_code'    => $referrer->referral_code,
                'click_count'      => rand(5, 100),
                'conversion_count' => rand(1, 10),
                'total_earned'     => rand(5, 50) * 1000,
                'expires_at'       => now()->addMonths(6),
            ]);
        }
    }

    private function seedVerifications(): void
    {
        $tiers    = ['basic', 'professional', 'elite'];
        $statuses = ['pending', 'approved', 'approved', 'rejected', 'expired'];
        $admin    = User::where('email', 'admin@estateyard.co.ke')->first();

        $usersToVerify = array_merge(
            $this->usersByRole['landlord'],
            $this->usersByRole['broker_licensed'],
            $this->usersByRole['valuer']
        );

        foreach (array_slice($usersToVerify, 0, 10) as $i => $user) {
            $tier   = $tiers[$i % count($tiers)];
            $status = $statuses[$i % count($statuses)];

            $verification = Verification::create([
                'user_id'      => $user->id,
                'tier'         => $tier,
                'status'       => $status,
                'submitted_at' => now()->subDays(rand(5, 60)),
                'approved_at'  => $status === 'approved' ? now()->subDays(rand(1, 30)) : null,
                'expires_at'   => $status === 'approved' ? now()->addYear() : null,
                'notes'        => $status === 'rejected' ? 'Documents not clear. Please resubmit with clearer copies.' : null,
                'reviewed_by'  => $status !== 'pending' ? $admin?->id : null,
            ]);

            VerificationDocument::create([
                'verification_id' => $verification->id,
                'document_type'   => 'national_id',
                'file_path'       => "verifications/{$user->id}/national_id.pdf",
                'status'          => $status === 'approved' ? 'approved' : ($status === 'rejected' ? 'rejected' : 'pending'),
            ]);

            if ($tier !== 'basic') {
                VerificationDocument::create([
                    'verification_id' => $verification->id,
                    'document_type'   => 'kra_pin',
                    'file_path'       => "verifications/{$user->id}/kra_pin.pdf",
                    'status'          => $status === 'approved' ? 'approved' : 'pending',
                ]);
            }
        }
    }

    private function seedMaintenanceRequests(): void
    {
        $tenants    = $this->usersByRole['tenant'];
        $managers   = $this->usersByRole['property_manager'];
        $properties = Property::where('listing_type', 'rent')->take(20)->get();

        $titles = [
            'Leaking Roof Urgent Repair',
            'Broken Water Heater',
            'Electrical Fault in Kitchen',
            'Clogged Drainage System',
            'Paint Peeling in Living Room',
            'Broken Window Lock',
            'AC Unit Not Working',
            'Faulty Gate Motor',
            'Mold in Bathroom',
            'Burst Water Pipe',
            'Sewage Overflow',
            'Broken Staircase Railing',
            'Power Outage Generator Fault',
            'Fallen Boundary Wall',
            'Rodent Infestation',
            'Damaged Floor Tiles',
            'Broken Door Hinge',
            'Water Tank Malfunction',
            'Broken CCTV Camera',
            'Faulty Elevator',
        ];

        $priorities = ['low', 'medium', 'high', 'urgent'];
        $statuses   = ['open', 'in_progress', 'resolved', 'closed'];

        foreach ($properties as $i => $property) {
            $tenant  = $tenants[$i % count($tenants)];
            $manager = $managers[$i % count($managers)];

            MaintenanceRequest::create([
                'property_id' => $property->id,
                'tenant_id'   => $tenant->id,
                'title'       => $titles[$i % count($titles)],
                'description' => "Tenant reported issue: {$titles[$i % count($titles)]}. Requires prompt attention.",
                'priority'    => $priorities[$i % count($priorities)],
                'status'      => $statuses[$i % count($statuses)],
                'assigned_to' => $i < 15 ? $manager->id : null,
                'resolved_at' => in_array($statuses[$i % count($statuses)], ['resolved', 'closed'])
                    ? now()->subDays(rand(1, 10)) : null,
            ]);
        }
    }

    private function seedInspections(): void
    {
        $properties = Property::take(15)->get();
        $tenants    = $this->usersByRole['tenant'];
        $surveyors  = $this->usersByRole['surveyor'];
        $statuses   = ['pending', 'confirmed', 'completed', 'cancelled'];

        foreach ($properties as $i => $property) {
            $requester = $tenants[$i % count($tenants)];
            $inspector = $surveyors[$i % count($surveyors)];

            Inspection::create([
                'property_id'  => $property->id,
                'inspector_id' => $i < 12 ? $inspector->id : null,
                'requester_id' => $requester->id,
                'scheduled_at' => now()->addDays(rand(-10, 20)),
                'status'       => $statuses[$i % count($statuses)],
                'notes'        => "Property inspection requested by prospective tenant. Access arranged with caretaker.",
                'report_url'   => $statuses[$i % count($statuses)] === 'completed'
                    ? "reports/inspection_{$i}.pdf" : null,
            ]);
        }
    }

    private function seedDeveloperProjects(): void
    {
        $developers   = $this->usersByRole['developer'];
        $projectNames = [
            'Garden City Residences',
            'Kilimani Heights',
            'Runda Forest Estate',
            'Westlands Prime Towers',
            'Karen Golf Villas',
            'Lavington Green Park',
            'Parklands Skyline Apartments',
            'Upper Hill Business Hub',
            'Muthaiga Royal Manors',
            'Riverside Executive Suites',
        ];
        $statuses = ['planning', 'construction', 'completed', 'selling'];

        foreach ($projectNames as $i => $name) {
            $developer = $developers[$i % count($developers)];

            DeveloperProject::create([
                'developer_id'   => $developer->id,
                'name'           => $name,
                'description'    => "Premium residential development offering modern units at {$name} in prime Nairobi location.",
                'location'       => $name . ', Nairobi',
                'total_units'    => rand(20, 200),
                'sold_units'     => rand(0, 50),
                'reserved_units' => rand(0, 20),
                'price_from'     => rand(5, 15) * 1_000_000,
                'price_to'       => rand(20, 80) * 1_000_000,
                'status'         => $statuses[$i % count($statuses)],
                'completion_date' => now()->addMonths(rand(6, 36))->format('Y-m-d'),
                'images'         => ["projects/{$i}/cover.jpg"],
            ]);
        }
    }

    private function seedMessages(): void
    {
        $landlords = $this->usersByRole['landlord'];
        $tenants   = $this->usersByRole['tenant'];

        $subjects = [
            'Rent Payment Confirmation',
            'Maintenance Request Follow-up',
            'Lease Renewal Inquiry',
            'Property Viewing Request',
            'Utility Bill Query',
        ];

        $bodies = [
            'Hi, I wanted to confirm my rent payment for this month has been sent via MPESA.',
            'I submitted a maintenance request last week regarding a leaking pipe. Any update on this?',
            'My lease expires next month. Are you open to renewal at the same terms?',
            'I am interested in viewing your property. What time works for you this weekend?',
            'Could you please clarify the utility bill charges reflected on my last statement?',
        ];

        for ($i = 0; $i < 20; $i++) {
            $sender    = $tenants[$i % count($tenants)];
            $recipient = $landlords[$i % count($landlords)];

            Message::create([
                'sender_id'    => $sender->id,
                'recipient_id' => $recipient->id,
                'subject'      => $subjects[$i % count($subjects)],
                'body'         => $bodies[$i % count($bodies)],
                'is_read'      => $i % 3 === 0,
                'read_at'      => $i % 3 === 0 ? now()->subHours(rand(1, 48)) : null,
            ]);
        }
    }

    private function seedNotifications(): void
    {
        $allUsers = User::take(20)->get();
        $types    = ['payment', 'inspection', 'document', 'auction', 'system', 'message'];
        $notifs   = [
            ['title' => 'Rent Payment Received', 'body' => 'Your rent payment of KES 45,000 has been received successfully.'],
            ['title' => 'Inspection Scheduled', 'body' => 'Property inspection has been confirmed for tomorrow at 10am.'],
            ['title' => 'Document Verified', 'body' => 'Your National ID has been successfully verified.'],
            ['title' => 'Auction Starting Soon', 'body' => 'The Karen Mansion auction starts in 2 hours. Place your bid!'],
            ['title' => 'Account Verified', 'body' => 'Congratulations! Your professional verification has been approved.'],
            ['title' => 'New Message', 'body' => 'You have a new message from your landlord regarding the lease.'],
        ];

        foreach ($allUsers as $i => $user) {
            $notif = $notifs[$i % count($notifs)];

            NotificationLog::create([
                'user_id' => $user->id,
                'title'   => $notif['title'],
                'body'    => $notif['body'],
                'type'    => $types[$i % count($types)],
                'is_read' => $i % 2 === 0,
                'url'     => '/dashboard',
            ]);
        }
    }

    private function printCounts(): void
    {
        $this->command->info('');
        $this->command->info('=== EstateYard Seeding Complete ===');
        $this->command->info('Users:                 ' . User::count());
        $this->command->info('Properties:            ' . Property::withTrashed()->count());
        $this->command->info('Leases:                ' . Lease::count());
        $this->command->info('Rent Payments:         ' . RentPayment::count());
        $this->command->info('Auctions:              ' . Auction::count());
        $this->command->info('Bids:                  ' . Bid::count());
        $this->command->info('Escrow Transactions:   ' . EscrowTransaction::count());
        $this->command->info('Referrals:             ' . Referral::count());
        $this->command->info('Verifications:         ' . Verification::count());
        $this->command->info('Maintenance Requests:  ' . MaintenanceRequest::count());
        $this->command->info('Inspections:           ' . Inspection::count());
        $this->command->info('Developer Projects:    ' . DeveloperProject::count());
        $this->command->info('Messages:              ' . Message::count());
        $this->command->info('Notifications:         ' . NotificationLog::count());
    }
}
