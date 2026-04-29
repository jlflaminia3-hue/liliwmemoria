<?php

namespace App\Http\Controllers;

class ServiceController extends Controller
{
    protected array $services = [
        'narra-lots' => [
            'id' => 'narra-lots',
            'title' => 'NARRA LOTS',
            'img' => 'frontend/assets/images/service/narra.jpg',
            'text' => 'A serene and well-kept resting place option with a dignified layout for family visits.',
            'details' => 'Our Narra Lots are located in a quiet area of the memorial park and maintained regularly to ensure a clean and dignified setting for remembrance.',
            'price' => 'Price: Please inquire for current rates.',
            'reason' => 'reservation_inquiry',
        ],
        'garden-lots' => [
            'id' => 'garden-lots',
            'title' => 'GARDEN LOTS',
            'img' => 'frontend/assets/images/service/garden-lot.jpg',
            'text' => 'Peaceful garden settings that support quiet reflection, remembrance, and comfort.',
            'details' => 'Garden Lots offer a calm, landscaped environment ideal for families who value a serene space for visits, reflection, and ongoing care.',
            'price' => 'Price: Please inquire for current rates.',
            'reason' => 'reservation_inquiry',
        ],
        'lot-phases' => [
            'id' => 'lot-phases',
            'title' => 'LOT PHASES',
            'img' => 'frontend/assets/images/service/lot-phases.jpg',
            'text' => 'Multiple development phases offering various lot options across different sections of the memorial park.',
            'details' => 'Our memorial park is developed in carefully planned phases, offering families a variety of lot options across different sections, each maintained to the highest standards.',
            'price' => 'Price: Please inquire for current rates.',
            'reason' => 'reservation_inquiry',
        ],
        'mausoleum' => [
            'id' => 'mausoleum',
            'title' => 'MAUSOLEUM',
            'img' => 'frontend/assets/images/service/mausoleum.jpg',
            'text' => 'A dignified space for interment designed for lasting protection, privacy, and respect.',
            'details' => 'Our Mausoleum option provides a protected, dignified resting place designed for lasting respect, security, and family privacy.',
            'price' => 'Price: Please inquire for current rates.',
            'reason' => 'reservation_inquiry',
        ],
        'interment' => [
            'id' => 'interment',
            'title' => 'INTERMENT',
            'img' => 'frontend/assets/images/service/interment.jpg',
            'text' => 'Professional interment assistance handled with respect, care, and proper coordination.',
            'details' => 'We assist families through the interment process with clear guidance, scheduling support, and on-site coordination to ensure a dignified service.',
            'price' => 'Price: Please inquire for current rates.',
            'reason' => 'interment',
        ],
        'back-office-lots' => [
            'id' => 'back-office-lots',
            'title' => 'BACK OFFICE LOTS',
            'img' => 'frontend/assets/images/service/backoffice.jpg',
            'text' => 'Convenient lot options close to service areas while maintaining a calm memorial environment.',
            'details' => 'Back Office Lots provide a practical option with convenient access while still offering a peaceful, well-maintained memorial setting.',
            'price' => 'Price: Please inquire for current rates.',
            'reason' => 'reservation_inquiry',
        ],
    ];

    public function index()
    {
        $services = array_slice($this->services, 0, 4, true);
        $otherServices = array_slice($this->services, 4, 2, true);

        return view('home.services', compact('services', 'otherServices'));
    }

    public function show(string $service)
    {
        if (! array_key_exists($service, $this->services)) {
            abort(404);
        }

        return view('home.services.' . $service);
    }
}
