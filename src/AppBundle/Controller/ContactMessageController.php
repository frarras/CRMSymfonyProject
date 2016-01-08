<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

class ContactMessageController extends Controller
{
	/**
	*@Route("/contact")
	*@Template("AppBundle:ContactMessageController:contact.html.twig")
	**/
    public function contactMailAction(Request $request)
    {
        $form = $this->createFormBuilder()  //posso identificare l entita a cui si riferisce 
        ->add('name', 'text')
        ->add('from','email')
        ->add('surname', 'text')
        ->add('phone','text')
        ->add('birthday', 'birthday')
        ->add('admin', 'checkbox')
        ->add('send', 'submit' , ['label'=>'Submit'])
        ->getForm(); 
    
    $form->handleRequest($request);
    if($form->isValid()){
    	$this->addFlash('notice','Richiesta ricevuta');
        $mailAdmin=$this->getParameter('emailciccoplus');
        $data=$form->getData(); //per richiamare i dati

         $mailer=$this->get('mailer');

         $message=\Swift_Message::newInstance();//$message è  una classe, istanza del messaggio
         $message->addTo($mailAdmin);
         $message->setFrom($data['from']);
         $message->setSubject($data['name']);
         $message->setBody($data['surname']);

         $mailer->send($message);
    }

    return [
    'form_di_contatto'=>$form->createView(),
    	];
     }   
}
