<?php
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;

$collection = new RouteCollection();
$collection->add('contact_mail', new Route('@Route("/contact/mail")', array(
    '_controller' => 'AppBundle:ContactMessageController:contactMail',
)));

return $collection;
