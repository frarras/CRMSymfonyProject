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
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use PHPExcel;
use PHPExcel_IOFactory;




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
            ->add('feedback', 'textarea',  array('required' => false,'attr' => array('rows' => '3', 'cols'=>'127', 'placeholder' => 'Inserire data di oggi e Feedback')))
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
     * @Route("/admin/modifica_operatore/{id}", name="modifica_operatore")
     */
    public function modificaOperatoreAdminAction(Request $request, $id)
    {
      //$id = $_GET['id'];
        //var_dump($id);
      $em = $this->getDoctrine()->getEntityManager();
      $operatore = $em->getRepository('AppBundle:Operatori')->find($id);
      $form=$this->createFormBuilder($operatore)
          ->add('name', 'text',array( 'attr' => array('rows' => '1', 'cols'=>'50','class'=>'form-control')))
          ->add('surname', 'text',array(  'attr' => array('rows' => '1', 'cols'=>'50','class'=>'form-control')))
          ->add('email', 'text',array(  'attr' => array('rows' => '1', 'cols'=>'50','class'=>'form-control')))
          ->add('phone', 'text',array(  'attr' => array('rows' => '1', 'cols'=>'50','class'=>'form-control')))
          ->add('Salva', 'submit', array('attr'=> array('class'=>'btn btn-lg crm-button')))
          ->add('indietro','submit',array('attr'=> array('class'=>'btn btn-lg btn-primary back-btn')))
          ->getForm();

          $form->handleRequest($request);

              if ($form->get('indietro')->isClicked()) {
                  return $this->redirectToRoute('gestione_operatori');
                }
                if ($form->isValid() && $form->get('Salva')->isClicked()) {
                  $em = $this->getDoctrine()->getManager();
                  $em->persist($operatore);
                  $em->flush();
                  return $this->redirectToRoute('gestione_operatori');
                }

        // replace this example code with whatever you need
        return $this->render('AppBundle::default/modifica_operatore.html.twig', array(
          'operatore' =>$operatore,
          'form'=>$form->createView(),
        ));
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
             $phpExcelObject = $this->get('phpexcel')->createPHPExcelObject();

        // replace this example code with whatever you need
        return $this->render('AppBundle::default/dati_campagne.html.twig', array(
          'countChiamate'           =>$countChiamate,
          'countConversioni'        =>$countConversioni,
          'tassoConversioneChiamate'=>$tassoConversioneChiamate,
          'tassoConversioneUtenti'  =>$tassoConversioneUtenti,
          'countUtenti'             =>$countUtenti,
          'countNonInteressati'     =>$countNonInteressati
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

    /**
    * @Route("/excel", name="excel")
    */
    public function excelAction(Request $request)
    {
              $em = $this->getDoctrine()->getManager();
              $elencoChiamate = $this->getDoctrine()->getManager()->getRepository('AppBundle:Chiamate')
                ->createQueryBuilder('e')
                ->getQuery()
                ->getResult();

              $objPHPExcel = $this->get('phpexcel')->createPHPExcelObject();
              // Set document properties
              $objPHPExcel->getProperties()->setCreator("fra")
              							 ->setLastModifiedBy("fra")
              							 ->setTitle("Office 2007 XLSX Test Document")
              							 ->setSubject("Office 2007 XLSX Test Document")
              							 ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
              							 ->setKeywords("office 2007 openxml php")
              							 ->setCategory("Test result file");
              // Add some data
              $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A1', 'Id')
                        ->setCellValue('B1', 'Operatore')
                        ->setCellValue('C1', 'Nome contatto')
                        ->setCellValue('D1', 'Cognome contatto')
                        ->setCellValue('E1', 'StatusUtente')
                        ->setCellValue('F1', 'DataRichiamare')
                        ->setCellValue('G1', 'inizioChiamata')
                        ->setCellValue('H1', 'fineChiamata');
              // Miscellaneous glyphs, UTF-8

              // Rename worksheet
              $objPHPExcel->getActiveSheet()->setTitle('Simple');
              // Set active sheet index to the first sheet, so Excel opens this as the first sheet
              $objPHPExcel->setActiveSheetIndex(0);

              $row = 3;
              foreach ($elencoChiamate as $item) {
                  $objPHPExcel->setActiveSheetIndex(0)
                      ->setCellValue('A'.$row, $item->getId())
                      ->setCellValue('B'.$row, $item->getOperatore())
                      ->setCellValue('C'.$row, $item->getUtente()->getName())
                      ->setCellValue('D'.$row, $item->getUtente()->getSurname())
                      ->setCellValue('E'.$row, $item->getStatusUtente())
                      ->setCellValue('F'.$row, $item->getDataRichiamare())
                      ->setCellValue('G'.$row, $item->getInizioChiamata())
                      ->setCellValue('H'.$row, $item->getFineChiamata());

                  $row++;
              }

              // Redirect output to a client’s web browser (Excel2007)
              header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
              header('Content-Disposition: attachment;filename="01simple.xlsx"');
              header('Cache-Control: max-age=0');
              // If you're serving to IE 9, then the following may be needed
              header('Cache-Control: max-age=1');
              // If you're serving to IE over SSL, then the following may be needed
              header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
              header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
              header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
              header ('Pragma: public'); // HTTP/1.0
              $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
              $objWriter->save('php://output');
              exit;
              return $this->render('AppBundle::default/excel.html.twig', array(
                'excel'                   =>$excelStatistiche));
    }
}
