<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/* Form */
use App\Entity\Search;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;


class mapController extends Controller
{
    public $geodata;

    public function init(Request $request)
    {
        $mySearch = new Search();
        $mySearch->setLocation('Paris 15e');
        $mySearch->setMarkerType('default');
        $mySearch->setMarkerColor('default');

        $form = $this->createFormBuilder($mySearch)
            ->add('location', TextType::class)
            ->add('markerType', ChoiceType::class, array(
                'choices' => array(
                    'Classic' => 'default',
                    'Custom1' => 'custom1',
                    'Custom2' => 'custom2',
                ),
            ))
            ->add('markerColor', ChoiceType::class, array(
                'choices' => array(
                    'Red' => 'default',
                    'Green' => 'custom1',
                    'Blue' => 'custom2',
                ),
            ))
            ->add('btn-search', SubmitType::class, array('label' => 'Search'))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $searchText = $form["location"]->getData();

            // Getting geolocation
            $this->geodata = $this->geocoding($searchText);

            $this->console_log($this->geodata);

            return $this->render('base.html.twig', array(
                'form' => $form->createView(),
                'location' => $this->geodata['location'],
                'lat' => $this->geodata['lat'],
                'lng' => $this->geodata['lng'],
            ));
            // return $this->redirectToRoute('app_mymap');
        }

        return $this->render('base.html.twig', array(
            'form' => $form->createView(),
            'location' => $this->geodata['location'],
            'lat' => $this->geodata['lat'],
            'lng' => $this->geodata['lng'],
        ));
    }

    public function console_log($data) {
        echo '<script>';
        echo 'console.log('. json_encode($data) .')';
        echo '</script>';
    }


    /*
     * Copyright notice
     * (c) 2013 Yohann CERDAN <cerdanyohann@yahoo.fr>
     * All rights reserved
     */
    public function geocoding($address) {
        $url = 'http://maps.googleapis.com/maps/api/geocode/json?address=' . urlencode($address) . '&sensor=true';

        if (function_exists('curl_init')) {
            $data = $this->getContent($url);
        } else {
            $data = file_get_contents($url);
        }

        $response = json_decode($data, TRUE);
        $status = $response['status'];

        if ($status == 'OK') {
            $return = array(
                'status' => $status,
                'location' => $response['results'][0]['formatted_address'],
                'lat' => $response['results'][0]['geometry']['location']['lat'],
                'lng' => $response['results'][0]['geometry']['location']['lng']
            ); // successful geocode
        } else {
            echo '<!-- geocoding : failure to geocode : ' . $status . " -->\n";
            $return = NULL; // failure to geocode
        }

        return $return;
    }
    public function getContent($url) {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_TIMEOUT, 10);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($curl, CURLOPT_URL, $url);
        $data = curl_exec($curl);
        curl_close($curl);
        return $data;
    }
}
