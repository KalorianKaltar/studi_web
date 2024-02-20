<?php

namespace App\EventSubscriber;

use App\Repository\SejourRepository;
use App\Repository\EDTRepository;
use App\Repository\TypeUtilisateurRepository;
use CalendarBundle\CalendarEvents;
use CalendarBundle\Entity\Event;
use CalendarBundle\Event\CalendarEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\HttpFoundation\RequestStack;
//use CalendarBundle\Controller\CalendarController;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\RequestEvent;

class CalendarSubscriber implements EventSubscriberInterface
{
    private static $id;
    private $user;
    
    public function __construct(
        private SejourRepository $sejourRepository,
        private EDTRepository $edtRepository,
        private TypeUtilisateurRepository $typeUtilisateurRepository,
        private RequestStack $requestStack,
//            private CalendarController $calendarController,
        private TokenStorageInterface $tokenStorage,
        private UrlGeneratorInterface $router
    ){}

    public static function getSubscribedEvents()
    {
        return [
            CalendarEvents::SET_DATA => 'onCalendarSetData',
            KernelEvents::REQUEST => [['onKernelRequest', 20]],
        ];
    }

     public function onKernelRequest(RequestEvent $event): void
    {
//         var_dump('LOL !');
        $request = $event->getRequest();
        if($request->attributes->get('id')) {
            self::$id = $request->attributes->get('id');
        } else {
            $referer = $this->requestStack->getCurrentRequest()->headers->get('referer');
            if($referer) {
                if(is_numeric(basename($referer))) {
                    self::$id = basename($referer);
                }
            }
        }
        
//        var_dump(self::$id);
        
        
//        if (!$request->hasPreviousSession()) {
//            return;
//        }

//        // try to see if the locale has been set as a _locale routing parameter
//        if ($locale = $request->attributes->get('_locale')) {
//            $request->getSession()->set('_locale', $locale);
//        } else {
//            // if no explicit locale has been set on this request, use one from the session
//            $request->setLocale($request->getSession()->get('_locale', $this->defaultLocale));
//        }
    }
    
    public function onCalendarSetData(CalendarEvent $calendar)
    {
        if (!$token = $this->tokenStorage->getToken()) {
            return ;
        }
        
//        if (!$token->isAuthenticated()) {
//            return ;
//        }

        if (!$this->user = $token->getUser()) {
            return ;
        }
        
//        $start = $calendar->getStart();
//        $end = $calendar->getEnd();
//        $filters = $calendar->getFilters();

        
        // Modify the query to fit to your entity and needs
        // Change booking.beginAt by your start date property
        
        $typeutilisateur = $this->typeUtilisateurRepository->find($this->user->getIdType());
        
        $sejours = array();
        if($typeutilisateur->getLabel() == 'Patient') {
            return $this->onCalendarSetDataPatient($calendar);
        } elseif($typeutilisateur->getLabel() == 'Administrateur') {
            if(self::$id) {
                return $this->onCalendarSetDataAdministrateurID($calendar);
            } else {
                return $this->onCalendarSetDataAdministrateur($calendar);
            }
        } else {
            throw new \Exception("Non implémenté pour les utilisateurs : " .$typeutilisateur->getLabel());
        }
    }
    
    private function onCalendarSetDataPatient($calendar) {
        $start = $calendar->getStart();
        $end = $calendar->getEnd();
        $filters = $calendar->getFilters();
        
        $sejours = $this->sejourRepository
            ->createQueryBuilder('sejour')
            ->where('sejour.id_patient = :patient AND (sejour.date_debut BETWEEN :start and :end OR sejour.date_fin BETWEEN :start and :end)')
            ->setParameter('patient', $this->user->getId())
            ->setParameter('start', $start->format('Y-m-d H:i:s'))
            ->setParameter('end', $end->format('Y-m-d H:i:s'))
            ->getQuery()
            ->getResult()
        ;



        foreach ($sejours as $sejour) {
            // this create the events with your data (here booking data) to fill calendar
            $sejourEvent = new Event(
                $sejour->getMotif(),
                $sejour->getDateDebut(),
                $sejour->getDateFin() // If the end date is null or not defined, a all day event is created.
            );

            /*
             * Add custom options to events
             *
             * For more information see: https://fullcalendar.io/docs/event-object
             * and: https://github.com/fullcalendar/fullcalendar/blob/master/src/core/options.ts
             */

            $sejourEvent->setOptions([
                'backgroundColor' => 'red',
                'borderColor' => 'red',
                'allDay' => true,
            ]);
            $sejourEvent->addOption(
                'url',
                $this->router->generate('app_sejour_show', [
                    'id' => $sejour->getId(),
                ])
            );

            // finally, add the event to the CalendarEvent to fill the calendar
            $calendar->addEvent($sejourEvent);
        }
    }
    
    private function onCalendarSetDataAdministrateur($calendar) {
        $start = $calendar->getStart();
        $end = $calendar->getEnd();
        $filters = $calendar->getFilters();
        
        $edtQueryBuilder = $this->edtRepository->createQueryBuilder('edt');
                
        $edts = $this->edtRepository->createQueryBuilder('edt')
            ->where('(edt.date BETWEEN :start and :end)')
            ->setParameter('start', $start->format('Y-m-d H:i:s'))
            ->setParameter('end', $end->format('Y-m-d H:i:s'))
            ->getQuery()
            ->getResult()
        ;

        $sejourQueryBuilder = $this->sejourRepository->createQueryBuilder('sejour');
        $sejourQuery = $sejourQueryBuilder
            ->where('sejour.date_debut BETWEEN :start and :end OR sejour.date_fin BETWEEN :start and :end')
                ->andWhere($sejourQueryBuilder->expr()->notIn('sejour.id', $edtQueryBuilder->select('IDENTITY(edt.id_sejour)')->getDQL()))
            ->setParameter('start', $start->format('Y-m-d H:i:s'))
            ->setParameter('end', $end->format('Y-m-d H:i:s'))
        ;

        $sejours = $sejourQuery->getQuery()->getResult();

        foreach ($sejours as $sejour) {
            // this create the events with your data (here booking data) to fill calendar
            $sejourEvent = new Event(
                $sejour->getIdSpecialite()->getLabel() . ' - Dr. ' . substr($sejour->getIdMedecin()->getPrenom(), 0, 1) . '. ' . $sejour->getIdMedecin()->getNom() . ' :> ' .  $sejour->getIdPatient()->getPreNom() . ' ' . $sejour->getIdPatient()->getNom() . ' - ' . $sejour->getMotif(),
                $sejour->getDateDebut(),
                $sejour->getDateFin() // If the end date is null or not defined, a all day event is created.
            );

//                var_dump($sejour);

            /*
             * Add custom options to events
             *
             * For more information see: https://fullcalendar.io/docs/event-object
             * and: https://github.com/fullcalendar/fullcalendar/blob/master/src/core/options.ts
             */

            $sejourEvent->setOptions([
                'backgroundColor' => 'red',
                'borderColor' => 'red',
                'allDay' => true,
                'groupId' => $sejour->getId(),
                'id' => $sejour->getId(),
                'customType' => 'sejour',
            ]);
            $sejourEvent->addOption(
                'url',
                $this->router->generate('edtPrepare', [
                    'id' => $sejour->getId(),
                ])
            );

//                var_dump($sejourEvent);

            // finally, add the event to the CalendarEvent to fill the calendar
            $calendar->addEvent($sejourEvent);
        }

        foreach ($edts as $edt) {
            // this create the events with your data (here booking data) to fill calendar
            $edtEvent = new Event(
                $edt->getIdSejour()->getIdSpecialite()->getLabel() . ' - Dr. ' . substr($edt->getIdMedecin()->getPreNom(), 0, 1) . '. ' . $edt->getIdMedecin()->getNom() . ' :> ' .  ' - ' . $edt->getIdSejour()->getMotif(),
                $edt->getDate(),
                 // If the end date is null or not defined, a all day event is created.
            );

            /*
             * Add custom options to events
             *
             * For more information see: https://fullcalendar.io/docs/event-object
             * and: https://github.com/fullcalendar/fullcalendar/blob/master/src/core/options.ts
             */

            $edtEvent->setOptions([
                'backgroundColor' => 'green',
                'borderColor' => 'green',
                'allDay' => true,
                'groupId' => $edt->getIdSejour()->getId(),
                'id' => $edt->getId(),
                'description' => $edt->getIdSejour()->getIdPatient()->getPreNom() . ' ' . $edt->getIdSejour()->getIdPatient()->getNom(),
                'customType' => 'edt',
            ]);
            $edtEvent->addOption(
                'url',
                $this->router->generate('app_e_d_t_show', [
                    'id' => $edt->getId(),
                ])
            );

            // finally, add the event to the CalendarEvent to fill the calendar
            $calendar->addEvent($edtEvent);
        }     
    }
    
    private function onCalendarSetDataAdministrateurID($calendar) {
        $start = $calendar->getStart();
        $end = $calendar->getEnd();
        $filters = $calendar->getFilters();
        
        $edtQueryBuilder = $this->edtRepository->createQueryBuilder('edt');
                
        $edts = $this->edtRepository->createQueryBuilder('edt')
            ->where('edt.id_medecin = :medecin AND (edt.date BETWEEN :start and :end)')
            ->setParameter('medecin', self::$id)
            ->setParameter('start', $start->format('Y-m-d H:i:s'))
            ->setParameter('end', $end->format('Y-m-d H:i:s'))
            ->getQuery()
            ->getResult()
        ;

        $sejourQueryBuilder = $this->sejourRepository->createQueryBuilder('sejour');
        $sejourQuery = $sejourQueryBuilder
//                    ->where('sejour.id_medecin = :medecin AND (sejour.date_debut BETWEEN :start and :end OR sejour.date_fin BETWEEN :start and :end) AND sejour.id NOT IN (SELECT edt.id_sejour FROM edt edt)')
            ->where('sejour.id_medecin = :medecin')
                ->andWhere('sejour.date_debut BETWEEN :start and :end OR sejour.date_fin BETWEEN :start and :end')
                ->andWhere($sejourQueryBuilder->expr()->notIn('sejour.id', $edtQueryBuilder->select('IDENTITY(edt.id_sejour)')->getDQL()))
            ->setParameter('medecin', self::$id)
            ->setParameter('start', $start->format('Y-m-d H:i:s'))
            ->setParameter('end', $end->format('Y-m-d H:i:s'))
        ;

        $sejours = $sejourQuery->getQuery()->getResult();

        foreach ($sejours as $sejour) {
            // this create the events with your data (here booking data) to fill calendar
            $sejourEvent = new Event(
                $sejour->getIdSpecialite()->getLabel() . ' - ' . substr($sejour->getIdPatient()->getPreNom(), 0, 1) . '. ' . $sejour->getIdPatient()->getNom() . ' - ' . $sejour->getMotif(),
                $sejour->getDateDebut(),
                $sejour->getDateFin() // If the end date is null or not defined, a all day event is created.
            );

//                var_dump($sejour);

            /*
             * Add custom options to events
             *
             * For more information see: https://fullcalendar.io/docs/event-object
             * and: https://github.com/fullcalendar/fullcalendar/blob/master/src/core/options.ts
             */

            $sejourEvent->setOptions([
                'backgroundColor' => 'red',
                'borderColor' => 'red',
                'allDay' => true,
                'groupId' => $sejour->getId(),
                'id' => $sejour->getId(),
                'customType' => 'sejour',
            ]);
            $sejourEvent->addOption(
                'url',
                $this->router->generate('edtPrepare', [
                    'id' => $sejour->getId(),
                ])
            );

//                var_dump($sejourEvent);

            // finally, add the event to the CalendarEvent to fill the calendar
            $calendar->addEvent($sejourEvent);
        }

        foreach ($edts as $edt) {
            // this create the events with your data (here booking data) to fill calendar
            $edtEvent = new Event(
                $edt->getIdSejour()->getIdSpecialite()->getLabel() . ' - ' . substr($edt->getIdSejour()->getIdPatient()->getPrenom(), 0, 1) . '. ' . $edt->getIdSejour()->getIdPatient()->getNom() . ' - ' . $edt->getIdSejour()->getMotif(),
                $edt->getDate(),
                 // If the end date is null or not defined, a all day event is created.
            );

            /*
             * Add custom options to events
             *
             * For more information see: https://fullcalendar.io/docs/event-object
             * and: https://github.com/fullcalendar/fullcalendar/blob/master/src/core/options.ts
             */

            $edtEvent->setOptions([
                'backgroundColor' => 'green',
                'borderColor' => 'green',
                'allDay' => true,
                'groupId' => $edt->getIdSejour()->getId(),
                'id' => $edt->getId(),
                'customType' => 'edt',
            ]);
            $edtEvent->addOption(
                'url',
                $this->router->generate('app_e_d_t_show', [
                    'id' => $edt->getId(),
                ])
            );

            // finally, add the event to the CalendarEvent to fill the calendar
            $calendar->addEvent($edtEvent);
        }     
    }
}