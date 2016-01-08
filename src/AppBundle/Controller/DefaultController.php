<?php

namespace AppBundle\Controller;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Entity\Operatori;
use AppBundle\Entity\Utenti;
use AppBundle\Entity\Chiamate;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Codemaster\MailUp\HttpClient\MailUpClient;
use Codemaster\MailUp\HttpClient\HttpClient;




class DefaultController extends Controller
{

    /**
     * @Route("/", name="index")
     */
    public function indexshowAction()
    {
        if (($this->container->get('security.context')->isGranted('ROLE_ADMIN')))
        {
            return $this->redirectToRoute('admin');
        }
        /*
        $joinChiamateUtenti = $this->getDoctrine()->getEntityManager()->getRepository('AppBundle:Chiamate');
        $matchChiamateUtenti = $joinChiamateUtenti->createQueryBuilder('c')
        ->add('select', 'c, u')
        ->add('from', 'AppBundle:Chiamate c')
        ->innerJoin('AppBundle:Utenti', 'u')
        ->where('c.utente=u.id')
        ->getQuery()
        ->getArrayResult();
        /*foreach ($matchChiamateUtenti as  $matchChiamateUtente) {
          foreach ($matchChiamateUtente as  $matchChiamateUtente) {
                                    if($matchChiamateUtente instanceof DateTime){
                        		 $string = $matchChiamateUtente->getTimestamp();
                        	} else {
                            //$inizioChiamata = strtotime($matchChiamateUtente['inizioChiamata']);
                            $obj= $matchChiamateUtente['inizioChiamata'];
                          }
                          //$statusUtente =$matchChiamateUtente['statusUtente'];
                          //echo $statusUtente;
          }
        }*/

        $username = 'm76488';
        $password = 'codemaster1';

        // Create a new mailup client
        $client = new MailUpClient($username, $password);


        $elencoUtenti = $client->getListRecipients(3, 'Subscribed');

        foreach ($elencoUtenti->Items as $Items) {
          $nuovoUtente= new Utenti();
          $email=$Items->Email;
          $nuovoUtente->setEmail($email);
          foreach ($Items -> Fields as $Fields) {
            $description = $Fields->Description;
              switch ($description):
              case 'surname':
                  $surname= $Fields->Value;
                  $nuovoUtente->setSurname($surname);
                  break;
              case 'name':
                  $name= $Fields->Value;
                  $nuovoUtente->setName($name);
                  break;
              case 'phone':
                  $phone= $Fields->Value;
                  $nuovoUtente->setPhone($phone);
                  break;
              case 'Campagne':
                  $campagna= $Fields->Value;
                  $nuovoUtente->setCampagna($campagna);
                  endswitch;
        }

            $em = $this->getDoctrine()->getManager();
            $utenteCheck = $em->getRepository('AppBundle:Utenti')->findOneByEmail($email);
            if (!$utenteCheck) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($nuovoUtente);
            $em->flush();
            }

            }

         $em = $this->getDoctrine()->getEntityManager();
         $Chiamate = $em->getRepository('AppBundle:Chiamate')->findAll();
         $utenti = $em->getRepository('AppBundle:Utenti')->findAll();

        if (!$utenti) {
            throw $this->createNotFoundException('Unable to find lista Utenti .');
        }

        return $this->render('AppBundle::default/index-user.html.twig', array(
            'utenti'      => $utenti,
            'chiamate'    =>$Chiamate,
            //'ma'          =>$matchChiamateUtenti,
        ));
    }


    /**
     * @Route("/campagne", name="campagne_operatore")
     */
    public function campagneAction(Request $request)
    {

            $elenco = $this->getDoctrine()->getManager()->getRepository('AppBundle:Utenti');
            $campagnaKimbo='1';
            $qbKimbo = $elenco->createQueryBuilder('u')
            ->select('COUNT(u.id)')
            ->where('u.campagna = :campagnaKimbo')
            ->setParameter('campagnaKimbo', 'kimbo');

            $campagnaVodafone='2';
            $qbVodafone = $elenco->createQueryBuilder('u')
            ->select('COUNT(u.id)')
            ->where('u.campagna = :campagnaVodafone')
            ->setParameter('campagnaVodafone', 'vodafone');

            $campagnaWind='3';
            $qbWind = $elenco->createQueryBuilder('u')
            ->select('COUNT(u.id)')
            ->where('u.campagna = :campagnaWind')
            ->setParameter('campagnaWind', 'wind');


            $countKimbo = $qbKimbo->getQuery()->getSingleScalarResult();
            $countVodafone=$qbVodafone->getQuery()->getSingleScalarResult();
            $countWind=$qbWind->getQuery()->getSingleScalarResult();


        // replace this example code with whatever you need
        return $this->render('AppBundle::default/campagne.html.twig', array(
            'countKimbo' => $countKimbo ,
            'countVodafone' => $countVodafone,
            'countWind'=>$countWind,
            ));
    }



     /**
     * @Route("/gestione_chiamata/{id}", name="gestione_chiamata")
     */
    public function createNuovaChiamataAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $utentesingolo = $em->getRepository('AppBundle:Utenti')->find($id);
        if (!$utentesingolo)
        {
            throw $this->createNotFoundException(
                'No utente found for id '.$id);
        }


        $em = $this->getDoctrine()->getManager();
        $chiamata = $em->getRepository('AppBundle:Chiamate')->findByUtente($id);



        $nuovachiamata= new Chiamate();
        $iniziochiamata=$nuovachiamata->setInizioChiamata(new \DateTime('now'));


        $form=$this->createFormBuilder($nuovachiamata)
            ->add('feedback', 'textarea',  array('required' => false,'attr' => array('rows' => '3', 'cols'=>'125', 'placeholder' => 'Inserire data di oggi e Feedback')))
            ->add('dataRichiamare', 'datetime', array(
                    'required' => false,
                    'input'  => 'string',
                    'widget' => 'choice'
                     ))


            ->add('statusUtente', 'choice', array(
                    'choices'  => array(
                        'Richiamare' => 'richiamare',
                        'Confermato' => 'confermato',
                        'Non interessato' => 'non_interessato',
                    ),

                    'label'=>'dropdownMenu1',

                    'choices_as_values' => true,
                    ))
            ->add('Salva', 'submit', array('attr'=> array('class'=>'btn btn-lg crm-button')))
            ->add('indietro','submit',array('attr'=> array('class'=>'btn btn-lg btn-primary back-btn')))

            ->getForm();


        $form->handleRequest($request);

        if ($form->isValid())
        {
            if ($form->get('indietro')->isClicked()) {
                return $this->redirectToRoute('index');

        }
            $nuovachiamata->setFineChiamata(new \DateTime('now'));
            $nuovachiamata->setUtente($utentesingolo);


            $operatore = $em->getRepository('AppBundle:Operatori')->find($this->getUser()->getId());
            if (!$operatore)
            {
                throw $this->createNotFoundException(
                    'No operatore found for id '.$id);
            }

            $nuovachiamata->setOperatore($operatore);

            $em = $this->getDoctrine()->getManager();
                            $chiamataCheck = $em->getRepository('AppBundle:Chiamate')->findOneByUtente($utentesingolo);
                            if ($chiamataCheck==null) {
                              $em = $this->getDoctrine()->getManager();
                            $em->persist($nuovachiamata);
                            $em->flush();
                            }
                            $statusCheck = $em->getRepository('AppBundle:Chiamate')->findOneByUtente($utentesingolo)->getStatusUtente();
                            $richiamareCheck = $em->getRepository('AppBundle:Chiamate')->findOneByUtente($utentesingolo)->getDataRichiamare();
                            if ($statusCheck=='confermato') {

                            } elseif ($statusCheck==null) {
                             $em = $this->getDoctrine()->getManager();
                            $em->persist($nuovachiamata);
                            $em->flush();
                            } elseif ($statusCheck=='non_interessato') {

                            } elseif ($richiamareCheck!=null && $statusCheck!='non_interessato'&& $statusCheck!='confermato') {
                             $em = $this->getDoctrine()->getManager();
                            $em->persist($nuovachiamata);
                            $em->flush();
                          }


            return $this->redirectToRoute('index');
        }

        return $this->render('AppBundle::default/operatore_gestione_chiamate.html.twig',
        array(
            'form'=>$form->createView(),
            'utente'=>$utentesingolo,
            'chiamata'=>$chiamata ));
    }


     /**
     * @Route("/gestione_contatti", name="gestione_contatti")
     */
    public function gestioneContattiAction(Request $request)
    {
        $em = $this->getDoctrine()->getEntityManager();

         $utenti = $em->getRepository('AppBundle:Utenti')->findAll();

        if (!$utenti) {
            throw $this->createNotFoundException('Unable to find lista Utenti .');
        }

        return $this->render('AppBundle::default/gestione_contatti.html.twig', array(
            'utenti'      => $utenti,
        ));
    }

     /**
     * @Route("/storico_chiamate/{id}", name="storico_chiamate")
     */
    public function storicoChiamateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getEntityManager();
         $utente = $em->getRepository('AppBundle:Utenti')->find($id);
        if (!$utente) {
            throw $this->createNotFoundException('Unable to find lista Utenti .');
        }

        $em = $this->getDoctrine()->getManager();
        $chiamata = $em->getRepository('AppBundle:Chiamate')->findByUtente($id);
        // replace this example code with whatever you need
        return $this->render('AppBundle::default/storico_chiamate.html.twig', array(
            'chiamata'=>$chiamata,
            'utente'=> $utente));
    }


      /**
     * @Route("/admin", name="admin")
     */
    public function indexadminAction(Request $request)
    {
        $elencoChiamate = $this->getDoctrine()->getManager()->getRepository('AppBundle:Chiamate');
        $elencoUtenti = $this->getDoctrine()->getManager()->getRepository('AppBundle:Utenti');

            $qbCountChiamate = $elencoChiamate->createQueryBuilder('c')
            ->select('COUNT(c.id)');

            $qbConversioni = $elencoChiamate->createQueryBuilder('c')
            ->select('COUNT(c.id)')
            ->where('c.statusUtente = :confermato')
            ->setParameter('confermato', 'confermato');

             $countChiamate = $qbCountChiamate->getQuery()->getSingleScalarResult();
             $countConversioni = $qbConversioni ->getQuery()->getSingleScalarResult();

             if ($countConversioni<= 0) {
               $tassoConversioneChiamate=0;
             } else {
             $tassoConversioneChiamate= ($countConversioni/$countChiamate)*100;
           }
            $qbCountUtenti = $elencoUtenti->createQueryBuilder('u')
              ->select('COUNT(u.id)');
               $countUtenti = $qbCountUtenti->getQuery()->getSingleScalarResult();

               if ($countChiamate<= 0) {
               $tassoConversioneUtenti=0;
             } else {
             $tassoConversioneUtenti= ($countConversioni/$countUtenti)*100;
           }


        // replace this example code with whatever you need
        return $this->render('AppBundle::default/index-admin.html.twig', array(
          'countChiamate' => $countChiamate,
          'countConversioni' =>$countConversioni,
          'tassoConversioneChiamate' =>$tassoConversioneChiamate,
          'tassoConversioneUtenti' =>$tassoConversioneUtenti,
          ));
    }


     /**
     * @Route("/admin/gestione_operatori", name="gestione_operatori")
     */
      public function showAction()
    {
         $em = $this->getDoctrine()->getEntityManager();

         $operatori = $em->getRepository('AppBundle:Operatori')->findAll();

        if (!$operatori) {
            throw $this->createNotFoundException('Unable to find lista .');
        }

        return $this->render('AppBundle::default/gestione_operatori.html.twig', array(
            'operatori'=> $operatori,
        ));
    }

    /**
     * @Route("/admin/modifica_operatore", name="modifica_operatore")
     */
    public function modificaOperatoreAdminAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('AppBundle::default/modifica_operatore.html.twig');
    }

     /**
     * @Route("/admin/gestione_contatti_admin", name="gestione_contatti_admin")
     */
    public function gestioneContattiAdminAction(Request $request)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $utenti = $em->getRepository('AppBundle:Utenti')->findAll();

        if (!$utenti) {
            throw $this->createNotFoundException('Unable to find lista Utenti .');
        }

        // replace this example code with whatever you need
        return $this->render('AppBundle::default/gestione_contatti_admin.html.twig', array(
            'utenti'      => $utenti,
            ));
    }

     /**
     * @Route("/admin/dati_campagne", name="dati_campagne")
     */
    public function datiCampagneAction(Request $request)
    {
        $elencoChiamate = $this->getDoctrine()->getManager()->getRepository('AppBundle:Chiamate');
        $elencoUtenti = $this->getDoctrine()->getManager()->getRepository('AppBundle:Utenti');

            $qbCountChiamate = $elencoChiamate->createQueryBuilder('c')
            ->select('COUNT(c.id)');

            $qbConversioni = $elencoChiamate->createQueryBuilder('c')
            ->select('COUNT(c.id)')
            ->where('c.statusUtente = :confermato')
            ->setParameter('confermato', 'confermato');

            $qbCountNonInteressati = $elencoChiamate->createQueryBuilder('c')
            ->select('COUNT(c.id)')
            ->where('c.statusUtente = :non_interessato')
            ->setParameter('non_interessato', 'non_interessato');


             $countChiamate = $qbCountChiamate->getQuery()->getSingleScalarResult();
             $countConversioni = $qbConversioni ->getQuery()->getSingleScalarResult();
             $countNonInteressati = $qbCountNonInteressati ->getQuery()->getSingleScalarResult();


             if ($countConversioni<= 0) {
               $tassoConversioneChiamate=0;
             } else {
             $tassoConversioneChiamate= ($countConversioni/$countChiamate)*100;
           }
            $qbCountUtenti = $elencoUtenti->createQueryBuilder('u')
              ->select('COUNT(u.id)');
               $countUtenti = $qbCountUtenti->getQuery()->getSingleScalarResult();

               if ($countChiamate<= 0) {
               $tassoConversioneUtenti=0;
             } else {
             $tassoConversioneUtenti= ($countConversioni/$countUtenti)*100;
           }


        // replace this example code with whatever you need
        return $this->render('AppBundle::default/dati_campagne.html.twig', array(
          'countChiamate'           =>$countChiamate,
          'countConversioni'        =>$countConversioni,
          'tassoConversioneChiamate'=>$tassoConversioneChiamate,
          'tassoConversioneUtenti'  =>$tassoConversioneUtenti,
          'countUtenti'             =>$countUtenti,
          'countNonInteressati'     =>$countNonInteressati,
          ));
    }

     /**
     * @Route("/admin/dati_operatori", name="dati_operatori")
     */
    public function datiOperatoriAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('AppBundle::default/dati_operatori.html.twig');
    }

      /**
     * @Route("/admin/impostazione_obiettivi", name="impostazione_obiettivi")
     */
    public function impostazioneObiettiviAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('AppBundle::default/impostazione_obiettivi.html.twig');
    }

     /**
     * @Route("/admin/visualizza_storico/{id}", name="visualizza_storico")
     */
    public function visualizzaStoricoAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getEntityManager();
         $utente = $em->getRepository('AppBundle:Utenti')->find($id);
        if (!$utente) {
            throw $this->createNotFoundException('Unable to find lista Utenti .');
        }

        $em = $this->getDoctrine()->getManager();
        $chiamata = $em->getRepository('AppBundle:Chiamate')->findByUtente($id);
        // replace this example code with whatever you need
        return $this->render('AppBundle::default/visualizza_storico_admin.html.twig', array(
            'chiamata'=>$chiamata,
            'utente'=> $utente));
    }

     /**
     * @Route("/impostazioni", name="impostazioni")
     */
    public function impostazioniAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('AppBundle::default/impostazioni.html.twig');
    }






}
